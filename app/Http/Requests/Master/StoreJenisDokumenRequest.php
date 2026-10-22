<?php

namespace App\Http\Requests\Master;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJenisDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', 'unique:jenis_dokumen,kode'],
            'nm_jenis' => ['required', 'string', 'max:50'],
            'wajib' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode jenis dokumen wajib diisi.',
            'kode.string' => 'Kode jenis dokumen harus berupa teks.',
            'kode.max' => 'Kode jenis dokumen maksimal 10 karakter.',
            'kode.unique' => 'Kode jenis dokumen ini sudah digunakan.',
            'nm_jenis.required' => 'Nama jenis dokumen wajib diisi.',
            'nm_jenis.string' => 'Nama jenis dokumen harus berupa teks.',
            'nm_jenis.max' => 'Nama jenis dokumen maksimal 50 karakter.',
        ];
    }
}
