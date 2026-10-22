<?php

namespace App\Http\Requests\Dokumen;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyDokumenRequest extends FormRequest
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
            'status_verifikasi' => ['required', 'in:terverifikasi,ditolak'],
            'catatan_verifikasi' => ['nullable', 'string', 'max:255', 'required_if:status_verifikasi,ditolak'],
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
            'status_verifikasi.required' => 'Status verifikasi wajib ditentukan.',
            'status_verifikasi.in' => 'Status verifikasi harus berupa terverifikasi atau ditolak.',
            'catatan_verifikasi.required_if' => 'Alasan atau catatan penolakan wajib diisi jika dokumen ditolak.',
            'catatan_verifikasi.max' => 'Catatan verifikasi maksimal 255 karakter.',
        ];
    }
}
