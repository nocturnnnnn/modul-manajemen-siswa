<?php

namespace App\Http\Requests\Siswa;

use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
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
        $siswa = $this->route('siswa');
        $kdSiswa = $siswa instanceof Siswa ? (string) $siswa->kd_siswa : (is_scalar($siswa) ? (string) $siswa : '0');

        return [
            // Identitas Pokok Siswa
            'nis' => ['required', 'string', 'max:20', 'unique:siswa,nis,'.$kdSiswa.',kd_siswa'],
            'nisn' => ['required', 'string', 'size:10', 'unique:siswa,nisn,'.$kdSiswa.',kd_siswa'],
            'nik' => ['required', 'string', 'size:16', 'unique:siswa,nik,'.$kdSiswa.',kd_siswa'],
            'nm_siswa' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'kd_agama' => ['required', 'integer', 'exists:agama,kd_agama'],
            'alamat' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status_siswa' => ['required', 'in:aktif,mutasi,pindah,lulus,alumni'],

            // Data Keluarga
            'keluarga' => ['required', 'array'],
            'keluarga.no_kk' => ['required', 'string', 'size:16'],
            'keluarga.anak_ke' => ['required', 'integer', 'min:1'],
            'keluarga.jumlah_saudara' => ['required', 'integer', 'min:0'],

            // Data Orang Tua / Wali
            'orang_tua' => ['required', 'array', 'min:1'],
            'orang_tua.*.kd_ortu' => ['nullable', 'integer'],
            'orang_tua.*.hubungan' => ['required', 'in:ayah,ibu,wali'],
            'orang_tua.*.nm_ortu' => ['required', 'string', 'max:100'],
            'orang_tua.*.nik' => ['nullable', 'string', 'size:16'],
            'orang_tua.*.pekerjaan' => ['nullable', 'string', 'max:50'],
            'orang_tua.*.pendidikan' => ['nullable', 'string', 'max:30'],
            'orang_tua.*.no_hp' => ['nullable', 'string', 'max:15'],

            // Data Kontak Siswa
            'kontak' => ['nullable', 'array'],
            'kontak.*.kd_kontak' => ['nullable', 'integer'],
            'kontak.*.jenis_kontak' => ['required', 'string', 'max:20'],
            'kontak.*.nomor' => ['required', 'string', 'max:15'],

            // Data Pendaftaran
            'pendaftaran' => ['nullable', 'array'],
            'pendaftaran.no_pendaftaran' => ['nullable', 'string', 'max:20'],
            'pendaftaran.tgl_daftar' => ['nullable', 'date'],
            'pendaftaran.jalur' => ['nullable', 'string', 'max:30'],
            'pendaftaran.asal_sekolah' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar pada siswa lain.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.size' => 'NISN harus tepat 10 digit.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada siswa lain.',
            'nik.required' => 'NIK siswa wajib diisi.',
            'nik.size' => 'NIK siswa harus tepat 16 digit.',
            'nik.unique' => 'NIK siswa ini sudah terdaftar pada sistem.',
            'nm_siswa.required' => 'Nama lengkap siswa wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kd_agama.required' => 'Agama siswa wajib dipilih.',
            'kd_agama.exists' => 'Pilihan agama tidak valid.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'foto.image' => 'File foto harus berupa format gambar (JPG/PNG).',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
            'keluarga.no_kk.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'keluarga.no_kk.size' => 'Nomor KK harus tepat 16 digit.',
            'keluarga.anak_ke.required' => 'Urutan anak ke berapa wajib diisi.',
            'keluarga.jumlah_saudara.required' => 'Jumlah saudara wajib diisi.',
            'orang_tua.required' => 'Minimal salah satu data orang tua/wali wajib diisi.',
            'orang_tua.min' => 'Minimal salah satu data orang tua/wali wajib diisi.',
            'orang_tua.*.nm_ortu.required' => 'Nama orang tua/wali wajib diisi.',
        ];
    }
}
