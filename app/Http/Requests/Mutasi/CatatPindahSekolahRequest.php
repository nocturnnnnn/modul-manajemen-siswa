<?php

namespace App\Http\Requests\Mutasi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CatatPindahSekolahRequest extends FormRequest
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
            'sekolah_tujuan' => ['required', 'string', 'max:100'],
            'tgl_pindah' => ['required', 'date'],
            'alasan' => ['required', 'string', 'max:255'],
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
            'kd_siswa.required' => 'Siswa yang pindah sekolah wajib dipilih.',
            'kd_siswa.exists' => 'Data siswa tidak valid.',
            'sekolah_tujuan.required' => 'Nama sekolah tujuan wajib diisi.',
            'sekolah_tujuan.max' => 'Nama sekolah tujuan maksimal 100 karakter.',
            'tgl_pindah.required' => 'Tanggal pindah sekolah wajib diisi.',
            'tgl_pindah.date' => 'Format tanggal pindah tidak valid.',
            'alasan.required' => 'Alasan pindah sekolah wajib diisi.',
            'alasan.max' => 'Alasan pindah maksimal 255 karakter.',
        ];
    }
}
