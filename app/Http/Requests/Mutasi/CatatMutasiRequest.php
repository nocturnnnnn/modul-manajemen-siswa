<?php

namespace App\Http\Requests\Mutasi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CatatMutasiRequest extends FormRequest
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
            'kd_siswa' => ['required', 'integer', 'exists:siswa,kd_siswa'],
            'jenis_mutasi' => ['required', 'in:Masuk,Keluar,Internal'],
            'tgl_mutasi' => ['required', 'date'],
            'no_surat' => ['required', 'string', 'max:50'],
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
            'kd_siswa.required' => 'Siswa bersangkutan wajib dipilih.',
            'kd_siswa.exists' => 'Data siswa tidak valid.',
            'jenis_mutasi.required' => 'Jenis mutasi wajib ditentukan.',
            'jenis_mutasi.in' => 'Jenis mutasi harus berupa Masuk, Keluar, atau Internal.',
            'tgl_mutasi.required' => 'Tanggal mutasi wajib diisi.',
            'tgl_mutasi.date' => 'Format tanggal mutasi tidak valid.',
            'no_surat.required' => 'Nomor surat mutasi resmi wajib diisi.',
            'no_surat.max' => 'Nomor surat maksimal 50 karakter.',
        ];
    }
}
