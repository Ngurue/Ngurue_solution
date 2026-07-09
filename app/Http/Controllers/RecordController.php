<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordRequest;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecordController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base query kulingana na ruhusa (admin = zote, mtumiaji = zake)
        $baseQuery = fn () => Record::query()->visibleTo($user);

        $statsPigQuery = fn () => $baseQuery()->where('record_type', 'pig');

        // Takwimu kuu — hazihesabu waliokufa kwenye jumla ya nguruwe hai
        $stats = [
            'total_pigs' => $baseQuery()
                ->whereIn('record_type', ['pig', 'litter'])
                ->where('status', '!=', 'Aliekufa')
                ->count(),
            'boars' => $statsPigQuery()->where('gender', 'Dume')->where('status', '!=', 'Aliekufa')->count(),
            'sows' => $statsPigQuery()->where('gender', 'Jike')->where('status', '!=', 'Aliekufa')->count(),
            'weaners' => $baseQuery()
                ->where('status', '!=', 'Aliekufa')
                ->where(function ($query) {
                    $query->where('status', 'Mtoto')->orWhere('record_type', 'litter');
                })
                ->count(),
            'deceased' => $baseQuery()->where('status', 'Aliekufa')->count(),
        ];

        // Dropdowns kwa ajili ya kuchagua wazazi kwenye fomu ya watoto
        $dropdownSires = $baseQuery()
            ->where('record_type', 'pig')
            ->where('gender', 'Dume')
            ->select('id', 'pig_code', 'title', 'breed')
            ->get();

        $dropdownDams = $baseQuery()
            ->where('record_type', 'pig')
            ->where('gender', 'Jike')
            ->select('id', 'pig_code', 'title', 'breed')
            ->get();

        $paginatedRecords = $baseQuery()
            ->with(['father:id,pig_code,breed', 'mother:id,pig_code,breed'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Records/Index', [
            'records' => $paginatedRecords,
            'sires' => $dropdownSires,
            'dams' => $dropdownDams,
            'stats' => $stats,
            'isAdmin' => (bool) $user->is_admin,
            'flash' => ['message' => session('flash_message') ?? session('message')],
        ]);
    }

    public function store(RecordRequest $request)
    {
        Record::create($this->buildAttributes($request));

        $label = $request->record_type === 'litter' ? 'Kundi' : 'Nguruwe';

        return redirect()->back()->with('flash_message', "Rekodi ya {$label} imehifadhiwa kikamilifu!");
    }

    public function update(RecordRequest $request, Record $record)
    {
        $this->authorize('update', $record);

        $record->update($this->buildAttributes($request));

        return redirect()->back()->with('flash_message', 'Rekodi imesasishwa kwa mafanikio!');
    }

    public function destroy(Record $record)
    {
        $this->authorize('delete', $record);

        $record->delete();

        return redirect()->back()->with('flash_message', 'Rekodi ya nguruwe imefutwa kikamilifu!');
    }

    public function updateWeight(Request $request, Record $record)
    {
        $this->authorize('update', $record);

        $validated = $request->validate([
            'new_weight' => ['required', 'numeric', 'min:1', 'max:1000'],
        ]);

        $history = is_array($record->weight_history) ? $record->weight_history : [];

        $history[] = [
            'date' => now()->toDateString(),
            'weight' => (float) $validated['new_weight'],
        ];

        $record->update([
            'value' => $validated['new_weight'],
            'weight_history' => $history,
        ]);

        return redirect()->back()->with('flash_message', 'Uzito mpya umesajiliwa vyema!');
    }

    /**
     * Andaa data iliyosafishwa kwa ajili ya kuhifadhi/kusasisha rekodi.
     * Hapa ndipo tunapoweka thamani chaguo-msingi (defaults) na kutafuta breed ya mzazi.
     */
    private function buildAttributes(RecordRequest $request): array
    {
        $data = $request->validated();
        $isLitter = $data['record_type'] === 'litter';

        // Breed kiotomatiki: kama ni kundi na breed haijawekwa, tumia breed ya mama
        $breed = $data['breed'] ?? null;
        if ($isLitter && ! $breed && ! empty($data['dam_code'])) {
            $mother = Record::where('pig_code', $data['dam_code'])->first();
            $breed = $mother?->breed;
        }

        return [
            'record_type' => $data['record_type'],
            'pig_code' => $data['pig_code'],
            'title' => $data['title'] ?? ($isLitter ? 'Kundi '.$data['pig_code'] : 'Hana Jina'),
            'gender' => $isLitter ? ($data['gender'] ?? 'Changanyiko') : ($data['gender'] ?? 'Jike'),
            'breed' => $breed ?? 'Mchanganyiko',
            'castration_status' => $data['castration_status'] ?? null,
            'birth_date' => $data['birth_date'],
            'age_manual' => $data['age_manual'] ?? null,
            'weaning_date' => $data['weaning_date'] ?? null,
            'pen_number' => $data['pen_number'] ?? ($isLitter ? 'Banda la Uzazi' : 'Banda A'),
            'status' => $data['status'] ?? ($isLitter ? 'Mtoto' : 'Anakuwa'),
            'litter_size' => $isLitter ? ($data['litter_size'] ?? 1) : null,
            'sire_code' => $data['sire_code'] ?? null,
            'dam_code' => $data['dam_code'] ?? null,
            'user_id' => Auth::id(),
        ];
    }
}
