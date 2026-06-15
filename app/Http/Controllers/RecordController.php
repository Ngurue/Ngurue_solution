<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecordController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Base Query kulingana na Role ya mtumiaji
        $baseQuery = $user->is_admin 
            ? Record::query() 
            : Record::where('user_id', $user->id);

        // 2. REKEBISHO KUBWA: Hesabu takwimu (Stats) ikijumuisha na Litter!
        $statsPigQuery = (clone $baseQuery)->where('record_type', 'pig');
        
        $stats = [
            'total_pigs' => (clone $baseQuery)->whereIn('record_type', ['pig', 'litter'])->count(),
            'boars'      => (clone $statsPigQuery)->where('gender', 'Dume')->count(),
            'sows'       => (clone $statsPigQuery)->where('gender', 'Jike')->count(),
            
            // TIBA YA SUMMARY: Hapa sasa inahesabu nguruwe wenye status 'Mtoto' PAMOJA na rekodi zote za aina ya 'litter'
            'weaners'    => (clone $baseQuery)->where(function($query) {
                                $query->where('status', 'Mtoto')
                                      ->orWhere('record_type', 'litter');
                            })->count(),
        ];

        // 3. Dropdowns kwa ajili ya Fomu ya Watoto
        $dropdownSires = (clone $baseQuery)
            ->where('record_type', 'pig')
            ->where('gender', 'Dume')
            ->select('id', 'pig_code', 'title', 'breed')
            ->get();

        $dropdownDams = (clone $baseQuery)
            ->where('record_type', 'pig')
            ->where('gender', 'Jike')
            ->select('id', 'pig_code', 'title', 'breed')
            ->get();

        // 4. Paginated Records zinazoenda kwenye List kuu ya Dashboard
        $paginatedRecords = $baseQuery->with(['father', 'mother'])
            ->latest()
            ->paginate(10); 

        return Inertia::render('Records/Index', [
            'records' => $paginatedRecords, 
            'sires'   => $dropdownSires,          
            'dams'    => $dropdownDams,            
            'stats'   => $stats,
            'isAdmin' => (bool) $user->is_admin,
            'flash'   => ['message' => session('flash_message') ?? session('message')]
        ]);
    }

    public function store(Request $request)
    {
        // Validation thabiti ili kuzuia makosa ya SQLite
        $request->validate([
            'record_type' => 'required|string',
            'pig_code'    => 'required|string',
            'birth_date'  => 'required|date',
        ]);

        // SAKATA LA BREED KIOTOMATIKI:
        // Kama ni litter na mtumiaji hajaweka breed, tunatafuta breed ya MAMA yake na kuwapa watoto.
        $breed = $request->breed;
        if ($request->record_type === 'litter' && !$breed && $request->dam_code) {
            $mother = Record::where('pig_code', $request->dam_code)->first();
            if ($mother) {
                $breed = $mother->breed;
            }
        }

        Record::create([
            'record_type'  => $request->record_type,
            'pig_code'     => $request->pig_code,
            'title'        => $request->title ?? ($request->record_type === 'litter' ? 'Kundi ' . $request->pig_code : 'Hana Jina'),
            'gender'       => $request->record_type === 'litter' ? 'Dume/Jike' : $request->gender, 
            'breed'        => $breed ?? 'Mchanganyiko', // Kama hakuna kabisa, mfumo unaweka Mchanganyiko
            'birth_date'   => $request->birth_date,
            'pen_number'   => $request->pen_number ?? 'Banda la Uzazi',
            'status'       => $request->status ?? ($request->record_type === 'litter' ? 'Mtoto' : 'Mkubwa'),
            'litter_size'  => $request->litter_size ?? 1, // Default iwe mtoto 1 kama haikujazwa
            'weaning_date' => $request->weaning_date,
            'sire_code'    => $request->sire_code,
            'dam_code'     => $request->dam_code,
            'user_id'      => Auth::id(),
        ]);

        return redirect()->back()->with('message', 'Rekodi ya kundi imehifadhiwa na kuingizwa kwenye summary!');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $record = $user->is_admin 
            ? Record::findOrFail($id) 
            : Record::where('user_id', $user->id)->findOrFail($id);

        $record->delete();

        return redirect()->back()->with('flash_message', 'Rekodi ya nguruwe imefutwa kikamilifu!');
    }

    public function updateWeight(Request $request, Record $record)
    {
        $user = Auth::user();

        if (!$user->is_admin && $record->user_id !== $user->id) {
            abort(403, 'Huna ruhusa ya kubadilisha rekodi hii.');
        }

        $request->validate(['new_weight' => 'required|integer|min:1']);
        
        $history = is_array($record->weight_history) 
            ? $record->weight_history 
            : (json_decode($record->weight_history, true) ?? []);
            
        $history[] = [
            'date'   => date('Y-m-d'), 
            'weight' => (int) $request->new_weight
        ];
        
        $record->update([
            'value'          => $request->new_weight,
            'weight_history' => $history 
        ]);

        return redirect()->back()->with('flash_message', 'Uzito mpya umesajiliwa vyema!');
    }
}