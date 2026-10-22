<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nm_kelas' => ['required', 'string', 'max:10', 'unique:kelas,nm_kelas'],
            'tingkat' => ['required', 'integer', 'in:7,8,9'],
        ];
    }

    public function messages(): array
    {
        return [
            'nm_kelas.required' => 'Nama rombel kelas wajib diisi.',
            'nm_kelas.string' => 'Nama kelas harus berupa teks.',
            'nm_kelas.max' => 'Nama kelas maksimal 10 karakter.',
            'nm_kelas.unique' => 'Nama kelas ini sudah terdaftar.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
            'tingkat.integer' => 'Tingkat kelas harus berupa angka.',
            'tingkat.in' => 'Tingkat kelas harus bernilai 7, 8, atau 9.',
        ];
    }
}
