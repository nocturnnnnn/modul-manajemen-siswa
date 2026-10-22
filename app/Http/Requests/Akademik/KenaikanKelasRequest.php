<?php

namespace App\Http\Requests\Akademik;

use App\Models\Kelas;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class KenaikanKelasRequest extends FormRequest
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
            'kd_tahun' => ['required', 'integer', 'exists:tahun_ajaran,kd_tahun'],
            'kd_kelas_asal' => ['required', 'integer', 'exists:kelas,kd_kelas'],
            'evaluasi' => ['required', 'array', 'min:1'],
            'evaluasi.*.kd_siswa' => ['required', 'integer', 'exists:siswa,kd_siswa'],
            'evaluasi.*.status' => ['required', 'in:naik,tinggal'],
            'evaluasi.*.kd_kelas_tujuan' => ['required', 'integer', 'exists:kelas,kd_kelas'],
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
            'kd_tahun.required' => 'Tahun ajaran evaluasi wajib dipilih.',
            'kd_tahun.exists' => 'Tahun ajaran tidak valid.',
            'kd_kelas_asal.required' => 'Kelas rombel asal wajib dipilih.',
            'kd_kelas_asal.exists' => 'Kelas asal tidak valid.',
            'evaluasi.required' => 'Daftar evaluasi siswa tidak boleh kosong.',
            'evaluasi.min' => 'Minimal terdapat 1 siswa untuk dievaluasi.',
            'evaluasi.*.kd_siswa.required' => 'Siswa yang dievaluasi wajib ditentukan.',
            'evaluasi.*.status.required' => 'Keputusan kenaikan (naik/tinggal) wajib dipilih.',
            'evaluasi.*.status.in' => 'Keputusan kenaikan harus bernilai naik atau tinggal.',
            'evaluasi.*.kd_kelas_tujuan.required' => 'Kelas tujuan wajib ditentukan.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $kdKelasAsal = $this->input('kd_kelas_asal');
            $evaluasi = $this->input('evaluasi', []);

            if (! $kdKelasAsal || ! is_array($evaluasi)) {
                return;
            }

            $kelasAsal = Kelas::where('kd_kelas', $kdKelasAsal)->first();
            if (! $kelasAsal) {
                return;
            }

            $kelasTujuanIds = collect($evaluasi)->pluck('kd_kelas_tujuan')->filter()->unique();
            $kelasMap = Kelas::whereIn('kd_kelas', $kelasTujuanIds)->get()->keyBy('kd_kelas');

            foreach ($evaluasi as $index => $item) {
                $status = $item['status'] ?? null;
                $kdKelasTujuan = $item['kd_kelas_tujuan'] ?? null;
                $kelasTujuan = $kelasMap->get($kdKelasTujuan);

                if (! $kelasTujuan instanceof Kelas) {
                    continue;
                }

                if ($status === 'naik') {
                    if ($kelasAsal->tingkat >= 9) {
                        $validator->errors()->add(
                            "evaluasi.{$index}.status",
                            'Siswa kelas tingkat 9 tidak dapat dinaikkan jenjang kelas (harus diproses melalui modul kelulusan).'
                        );
                    } elseif ($kelasTujuan->tingkat !== $kelasAsal->tingkat + 1) {
                        $validator->errors()->add(
                            "evaluasi.{$index}.kd_kelas_tujuan",
                            'Kelas tujuan untuk siswa yang naik harus satu tingkat lebih tinggi (Tingkat '.($kelasAsal->tingkat + 1).').'
                        );
                    }
                } elseif ($status === 'tinggal') {
                    if ($kelasTujuan->tingkat !== $kelasAsal->tingkat) {
                        $validator->errors()->add(
                            "evaluasi.{$index}.kd_kelas_tujuan",
                            "Kelas tujuan untuk siswa yang tinggal kelas harus pada tingkat yang sama (Tingkat {$kelasAsal->tingkat})."
                        );
                    }
                }
            }
        });
    }
}
