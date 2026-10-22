<div align="center">

  <img src="public/images/logo.png" alt="Logo SMP Kristen 1 Wonosobo" width="120" height="120" />

  # SISTEM INFORMASI MANAJEMEN SISWA (SIAKAD)
  ### SMP KRISTEN 1 WONOSOBO

  [![Laravel 12+](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![Inertia.js v3](https://img.shields.io/badge/Inertia.js-v3-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
  [![Vue 3](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)
  [![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
  [![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
  [![Pest Tests](https://img.shields.io/badge/Tests-140%20Passed-10B981?style=for-the-badge&logo=pest&logoColor=white)](https://pestphp.com)

  <p align="center">
    Aplikasi manajemen data siswa, evaluasi akademik, mutasi, kelulusan, dan arsip dokumen digital resmi SMP Kristen 1 Wonosobo berbasis Single Page Application (SPA).
  </p>

</div>

---

## 📌 Deskripsi Singkat Proyek

**Sistem Informasi Manajemen Siswa (SIAKAD)** SMP Kristen 1 Wonosobo adalah modul sistem informasi terpadu yang dirancang untuk mengelola seluruh siklus hidup data siswa dari tahap pendaftaran hingga kelulusan dan pelacakan alumni. 

Aplikasi ini dibangun menggunakan arsitektur **Modular Monolith** dengan pendekatan *Component-Driven UI*, menjamin kode bersih (*zero-bloat*), terketik ketat (*strict TypeScript*), dan aman dari eskalasi hak akses (*Role-Based Access Control*).

### Fitur Utama:
1. **Otorisasi & Autentikasi Pengguna**: Login berbasis **Username & Password**, pencegahan serangan brute-force, dan proteksi sesi.
2. **Data Induk Siswa (Master Siswa)**: Pendataan identitas lengkap, nomor identitas kependudukan (NIS, NISN, NIK), data keluarga, orang tua/wali, serta kontak.
3. **Dokumen Digital & Verifikasi TU**: Pengunggahan berkas persyaratan (KK, Akta Lahir, KIP/KKS) beserta antarmuka verifikasi staf Tata Usaha.
4. **Riwayat Akademik & Kenaikan Kelas**: Evaluasi naik kelas dan tinggal kelas per rombel belajar tingkat 7, 8, dan 9.
5. **Mutasi & Pindah Sekolah**: Pencatatan mutasi internal/eksternal dan penerbitan surat keterangan pindah sekolah.
6. **Kelulusan & Portal Alumni**: Rekapitulasi calon kelulusan tingkat 9, penomoran ijazah, dan pelacakan kegiatan lanjutan alumni.
7. **Pelaporan & Cetak Dokumen**: Laporan rekapitulasi data siswa dan rombel siap cetak dengan kop surat resmi sekolah.
8. **Profil Mandiri & Administrasi Pengguna**: Pembaruan profil staf tanpa eskalasi peran (*Zero Role Escalation*) serta CRUD pengguna bagi Superadmin dengan proteksi anti-hapus akun sendiri.
9. **Penanganan Error Dinamis**: Halaman error (404, 403, 500, 503, 505, 419) bertema terpadu dengan navigasi pintar berdasarkan status login.

---

## 🛠️ Kebutuhan Sistem (Prerequisites)

Pastikan lingkungan pengembang Anda telah memenuhi spesifikasi berikut:

- **PHP**: Versi `^8.2` atau lebih tinggi
- **Composer**: Versi `^2.x`
- **Node.js**: Versi `^20.x` atau lebih baru
- **NPM / PNPM**: Pengelola paket JavaScript
- **Database**: MySQL `^8.0` / MariaDB `^10.4` atau SQLite `^3.x`
- **Ekstensi PHP**: `pdo`, `pdo_mysql` / `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`

---

## 🚀 Panduan Inisialisasi Proyek (Clone to Run)

Ikuti langkah-langkah di bawah ini untuk menginisialisasi proyek setelah melakukan clone dari repository:

### 1. Clone Repository & Masuk ke Direktori

```bash
git clone https://github.com/username/modul-manajemen-siswa.git
cd modul-manajemen-siswa
```

### 2. Konfigurasi Environment (`.env`)

Salin file contoh konfigurasi dan sesuaikan pengaturan lingkungan Anda:

```bash
cp .env.example .env
```

Buka file `.env` dan pastikan konfigurasi basis data telah sesuai:
```env
APP_NAME="SIAKAD SMP Kristen 1 Wonosobo"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Atau jika menggunakan MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_manajemen_siswa
# DB_USERNAME=root
# DB_PASSWORD=
```

### 3. Instalasi Dependensi Backend (PHP / Composer)

```bash
composer install
```

### 4. Generate Kunci Aplikasi

```bash
php artisan key:generate
```

### 5. Buat Tautan Penyimpanan Berkas (Storage Symlink)

```bash
php artisan storage:link
```

### 6. Migrasi Skema Basis Data & Isi Data Awal (Seeders)

Jalankan migrasi basis data (15 tabel inti + tabel pendukung) beserta seeder default:

```bash
php artisan migrate --seed
```

> [!NOTE]
> Jika menggunakan SQLite, pastikan file `database/database.sqlite` telah dibuat jika diminta oleh sistem (`touch database/database.sqlite`).

### 7. Instalasi Dependensi Frontend (JavaScript / Vite)

```bash
npm install
```

### 8. Menjalankan Server Pengembangan (Local Dev Server)

Jalankan backend Laravel dan dev server Vite secara bersamaan:

**Terminal 1 (Backend Server):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Hot Reload Vite):**
```bash
npm run dev
```

Aplikasi kini siap diakses melalui peramban di: **`http://localhost:8000`**

---

## 👤 Akun Pengguna Default (Hasil Seeder)

Setelah menjalankan `php artisan migrate --seed`, sistem secara otomatis menyediakan dua akun default untuk pengujian:

| Peran Akun | Username | Kata Sandi | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Superadministrator** | `superadmin` | `password123` | Akses penuh seluruh modul + Menu Administrasi Sistem (`/admin/users`) |
| **Admin Tata Usaha** | `admin` | `password123` | Manajemen siswa, dokumen, akademik, mutasi, kelulusan, dan laporan |

---

## 🧪 Pengujian Otomatis & Standar Kualitas (QA)

Repositori ini dilengkapi dengan rangkaian pengujian otomatis lengkap dan strict static analysis:

```bash
# Menjalankan seluruh pengujian Pest PHP (140 Unit & Feature Tests)
php artisan test

# Menjalankan type-checking TypeScript pada komponen Vue
npm run types:check

# Menjalankan linter dan formatter kode frontend
npm run check:fix

# Menjalankan code style fixer PHP (Laravel Pint)
./vendor/bin/pint
```

---

## 📂 Struktur Direktori Utama

```
modul-manajemen-siswa/
├── .docs/                    # Diagram analisis sistem (ERD, DFD, Activity Diagram, Blueprint)
├── app/
│   ├── Http/Controllers/     # Thin Controller (Siswa, Dokumen, Mutasi, Users, dll.)
│   ├── Http/Requests/        # Form Request Validasi Terpusat
│   ├── Models/               # 15 Eloquent Models sistem
│   └── Providers/            # Service Providers & Gate Otorisasi
├── database/
│   ├── migrations/           # Skema 15 tabel relasional terurut FK
│   └── seeders/              # Master referensi dan data dummy realistis
├── public/
│   └── images/               # Asset publik (Logo sekolah SMP Kristen 1 Wonosobo)
├── resources/
│   ├── css/app.css           # Token desain tema Tailwind CSS v4 (Biru-Putih)
│   ├── js/
│   │   ├── components/
│   │   │   ├── ui/           # Komponen UI primitif atomik (Button, Input, Modal, Badge, dll.)
│   │   │   ├── common/       # AppLayout, Navbar, Sidebar
│   │   │   └── modules/      # Komponen sub-fitur modular (< 150-200 baris)
│   │   ├── pages/            # View halaman utama Inertia.js SPA
│   │   └── types/            # Definisi TypeScript model & data kontrak
│   └── views/
│       └── app.blade.php     # Root template SPA
├── routes/
│   └── web.php               # Rute terproteksi sesi & otorisasi Gate
└── tests/
    ├── Feature/              # Pengujian integrasi modul bisnis
    └── Unit/                 # Pengujian unit model & relasi
```

---

## 📄 Lisensi & Hak Cipta

Dikembangkan untuk **SMP Kristen 1 Wonosobo**  
Jl. Pasukan Ronggolawe No. 8, Wonosobo 56311, Jawa Tengah.  
*Hak Cipta Dilindungi Undang-Undang.*
