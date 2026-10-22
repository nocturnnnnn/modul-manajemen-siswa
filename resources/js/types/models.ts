export interface Agama {
    kd_agama: number;
    nm_agama: string;
    created_at?: string;
    updated_at?: string;
}

export interface JenisDokumen {
    kd_jenis: number;
    kode: 'KK' | 'AKTA' | 'KIP' | 'KKS' | 'LAINNYA';
    nm_jenis: string;
    wajib: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface TahunAjaran {
    kd_tahun: number;
    nm_tahun: string; // e.g. "2026/2027"
    semester: 'Ganjil' | 'Genap';
    aktif: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface Kelas {
    kd_kelas: number;
    nm_kelas: string; // e.g. "7A"
    tingkat: 7 | 8 | 9;
    created_at?: string;
    updated_at?: string;
}

export type StatusSiswa = 'aktif' | 'mutasi' | 'pindah' | 'lulus' | 'alumni';
export type JenisKelamin = 'Laki-laki' | 'Perempuan';

export interface Siswa {
    kd_siswa: number;
    nis: string;
    nisn: string;
    nik: string;
    nm_siswa: string;
    tempat_lahir: string;
    tgl_lahir: string;
    jenis_kelamin: JenisKelamin;
    kd_agama: number;
    alamat: string;
    foto?: string | null;
    status_siswa: StatusSiswa;
    created_at?: string;
    updated_at?: string;
    agama?: Agama;
    keluarga?: Keluarga;
    orang_tua_wali?: OrangTuaWali[];
    kontak?: KontakSiswa[];
    dokumen?: DokumenSiswa[];
    pendaftaran?: Pendaftaran[];
    kenaikan_kelas?: KenaikanKelas[];
    mutasi?: Mutasi[];
    pindah_sekolah?: PindahSekolah[];
    kelulusan?: Kelulusan;
}

export interface Keluarga {
    kd_keluarga: number;
    kd_siswa: number;
    no_kk: string;
    anak_ke: number;
    jumlah_saudara: number;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export type HubunganOrtu = 'ayah' | 'ibu' | 'wali';

export interface OrangTuaWali {
    kd_ortu: number;
    kd_siswa: number;
    hubungan: HubunganOrtu;
    nm_ortu: string;
    nik?: string | null;
    pekerjaan?: string | null;
    pendidikan?: string | null;
    no_hp?: string | null;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export interface KontakSiswa {
    kd_kontak: number;
    kd_siswa: number;
    jenis_kontak: 'HP' | 'WhatsApp' | 'Rumah' | 'Lainnya';
    nomor: string;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export type StatusVerifikasiDokumen = 'belum' | 'terverifikasi' | 'ditolak';

export interface DokumenSiswa {
    kd_dokumen: number;
    kd_siswa: number;
    kd_jenis: number;
    nomor_dokumen?: string | null;
    file_path: string;
    status_verifikasi: StatusVerifikasiDokumen;
    catatan_verifikasi?: string | null;
    tgl_upload: string;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
    jenis?: JenisDokumen;
}

export interface Pendaftaran {
    kd_daftar: number;
    kd_siswa: number;
    no_pendaftaran: string;
    tgl_daftar: string;
    jalur: 'Zonasi' | 'Prestasi' | 'Afirmasi' | 'Lainnya';
    asal_sekolah: string;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export interface KenaikanKelas {
    kd_kenaikan: number;
    kd_siswa: number;
    kd_tahun: number;
    kd_kelas_asal: number;
    kd_kelas_tujuan: number;
    status: 'naik' | 'tinggal';
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
    tahun_ajaran?: TahunAjaran;
    kelas_asal?: Kelas;
    kelas_tujuan?: Kelas;
}

export interface Mutasi {
    kd_mutasi: number;
    kd_siswa: number;
    jenis_mutasi: 'Masuk' | 'Keluar' | 'Internal';
    tgl_mutasi: string;
    no_surat: string;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export interface PindahSekolah {
    kd_pindah: number;
    kd_siswa: number;
    sekolah_tujuan: string;
    tgl_pindah: string;
    alasan: string;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
}

export interface Kelulusan {
    kd_lulus: number;
    kd_siswa: number;
    kd_tahun: number;
    tgl_lulus: string;
    no_ijazah?: string | null;
    created_at?: string;
    updated_at?: string;
    siswa?: Siswa;
    tahun_ajaran?: TahunAjaran;
    alumni?: Alumni;
}

export interface Alumni {
    kd_alumni: number;
    kd_lulus: number;
    kegiatan_lanjutan: string; // e.g. "Melanjutkan SMA", "Melanjutkan SMK", "Bekerja"
    instansi: string;
    kontak?: string | null;
    created_at?: string;
    updated_at?: string;
    kelulusan?: Kelulusan;
}
