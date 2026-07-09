<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordRequest extends FormRequest
{
    /**
     * Uthibitisho wa ruhusa unafanywa na Policy kwenye Controller,
     * kwa hiyo hapa tunaruhusu ombi lipite kwenye ukaguzi wa data.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sheria za ukaguzi wa data (Validation rules).
     */
    public function rules(): array
    {
        // Wakati wa ku-update, tunapata rekodi husika ili tusipige marufuku pig_code yake yenyewe
        $recordId = $this->route('record')?->id;

        return [
            'record_type' => ['required', Rule::in(['pig', 'litter'])],
            'pig_code' => [
                'required', 'string', 'max:255',
                Rule::unique('records', 'pig_code')->ignore($recordId),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'castration_status' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'age_manual' => ['nullable', 'string', 'max:255'],
            'weaning_date' => ['nullable', 'date'],
            'pen_number' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'litter_size' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sire_code' => ['nullable', 'string', 'max:255'],
            'dam_code' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Ujumbe wa makosa kwa lugha ya Kiswahili.
     */
    public function messages(): array
    {
        return [
            'record_type.required' => 'Aina ya rekodi inahitajika.',
            'record_type.in' => 'Aina ya rekodi lazima iwe nguruwe au kundi.',
            'pig_code.required' => 'ID ya nguruwe inahitajika.',
            'pig_code.unique' => 'ID hii (:input) tayari imetumika kwa nguruwe mwingine.',
            'birth_date.required' => 'Tarehe ya kuzaliwa inahitajika.',
            'birth_date.date' => 'Tarehe ya kuzaliwa si sahihi.',
            'litter_size.min' => 'Idadi ya watoto lazima iwe angalau mmoja.',
        ];
    }
}
