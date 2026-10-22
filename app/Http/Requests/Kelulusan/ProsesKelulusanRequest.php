<?php

namespace App\Http\Requests\Kelulusan;

use App\Models\Kelulusan;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProsesKelulusanRequest extends FormRequest
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
        $kdSiswa = $this->input('kd_siswa');
        $kelulusanId = null;
        if ($kdSiswa) {
            $kelulusanId = Kelulusan::where('kd_siswa', $kdSiswa)->value('kd_lulus');
        }

        return [
            'kd_siswa' => ['required', 'integer', 'exists:siswa,kd_siswa'],
            'kd_tahun' => ['required', 'integer', 'exists:tahun_ajaran,kd_tahun'],
            'tgl_lulus' => ['required', 'date'],
            'no_ijazah' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('kelulusan', 'no_ijazah')->ignore($kelulusanId, 'kd_lulus'),
            ],
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
            'kd_siswa.required' => 'Siswa calon lulusan wajib dipilih.',
            'kd_siswa.exists' => 'Data siswa tidak valid.',
            'kd_tahun.required' => 'Tahun ajaran kelulusan wajib dipilih.',
            'kd_tahun.exists' => 'Tahun ajaran tidak valid.',
            'tgl_lulus.required' => 'Tanggal kelulusan resmi wajib diisi.',
            'tgl_lulus.date' => 'Format tanggal kelulusan tidak valid.',
            'no_ijazah.max' => 'Nomor ijazah maksimal 30 karakter.',
            'no_ijazah.unique' => 'Nomor ijazah ini sudah pernah digunakan untuk siswa lain.',
        ];
    }

    /**
     * Ensure the student is in grade 9.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $kdSiswa = $this->input('kd_siswa');
            if (! $kdSiswa) {
                return;
            }

            $siswa = Siswa::with('kelulusan')->find($kdSiswa);
            if (! $siswa) {
                return;
            }

            $latestKenaikan = $siswa->kenaikanKelas()->latest('kd_kenaikan')->first();
            $isGrade9 = $latestKenaikan && $latestKenaikan->kelasTujuan && $latestKenaikan->kelasTujuan->tingkat === 9;

            if (! $isGrade9 && ! $siswa->kelulusan) {
                $validator->errors()->add('kd_siswa', 'Hanya siswa tingkat 9 yang dapat disahkan kelulusannya.');
            }
        });
    }
}
