<?php

namespace App\Http\Requests\Dokumen;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadDokumenRequest extends FormRequest
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
            'kd_jenis' => ['required', 'integer', 'exists:jenis_dokumen,kd_jenis'],
            'nomor_dokumen' => ['nullable', 'string', 'max:50'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:3072'],
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
            'kd_siswa.required' => 'Siswa pemilik dokumen wajib dipilih.',
            'kd_siswa.exists' => 'Data siswa yang dipilih tidak valid.',
            'kd_jenis.required' => 'Jenis dokumen wajib dipilih.',
            'kd_jenis.exists' => 'Jenis dokumen yang dipilih tidak valid.',
            'file.required' => 'Berkas dokumen wajib diunggah.',
            'file.mimes' => 'Format berkas harus berupa PDF, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran berkas maksimal adalah 3MB (3072 KB).',
        ];
    }
}
