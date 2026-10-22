<?php

namespace App\Http\Requests\Kelulusan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SimpanAlumniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kd_lulus' => ['required', 'integer', 'exists:kelulusan,kd_lulus'],
            'kegiatan_lanjutan' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kd_lulus.required' => 'Data kelulusan siswa wajib dipilih.',
            'kd_lulus.exists' => 'Data kelulusan tidak valid.',
            'kegiatan_lanjutan.required' => 'Pilihan kegiatan lanjutan wajib diisi.',
            'kegiatan_lanjutan.max' => 'Kegiatan lanjutan maksimal 100 karakter.',
            'instansi.required' => 'Nama sekolah lanjutan atau instansi wajib diisi.',
            'instansi.max' => 'Nama instansi maksimal 100 karakter.',
            'kontak.max' => 'Kontak maksimal 50 karakter.',
        ];
    }
}
