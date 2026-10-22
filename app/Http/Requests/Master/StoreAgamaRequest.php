<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgamaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nm_agama' => ['required', 'string', 'max:30', 'unique:agama,nm_agama'],
        ];
    }

    public function messages(): array
    {
        return [
            'nm_agama.required' => 'Nama agama wajib diisi.',
            'nm_agama.string' => 'Nama agama harus berupa teks.',
            'nm_agama.max' => 'Nama agama maksimal 30 karakter.',
            'nm_agama.unique' => 'Nama agama ini sudah terdaftar.',
        ];
    }
}
