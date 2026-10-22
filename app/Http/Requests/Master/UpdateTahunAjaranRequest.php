<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nm_tahun' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nm_tahun.required' => 'Nama tahun ajaran wajib diisi.',
            'nm_tahun.regex' => 'Format tahun ajaran harus berupa TTTT/TTTT (contoh: 2026/2027).',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester harus Ganjil atau Genap.',
            'aktif.boolean' => 'Status aktif harus bernilai boolean.',
        ];
    }
}
