# TaskFlow Pelatihan

TaskFlow Pelatihan adalah prototype **Mini Dashboard Manajemen Tugas** berbasis Laravel untuk membantu instruktur membuat dan memonitor tugas, serta membantu peserta melihat dan mengumpulkan tugas pelatihan.

## Fitur Utama

### Admin / Instruktur

- Login dan logout.
- Melihat ringkasan tugas, peserta, dan pengumpulan pada dashboard.
- Membuat, melihat, mengubah, dan menghapus tugas.
- Mengatur status tugas menjadi draf, aktif, atau ditutup.
- Memonitor peserta yang sudah, belum, atau terlambat mengumpulkan.
- Melihat detail pengumpulan, mengunduh file, dan membuka tautan tugas.
- Melakukan pencarian, filter, pagination, dan export monitoring ke CSV.
- Melihat ringkasan aktivitas peserta.

### Peserta

- Login dan logout.
- Melihat dashboard dan daftar tugas.
- Melihat detail, instruksi, deadline, dan status tugas.
- Mengumpulkan tugas melalui file, tautan, atau keduanya.
- Memperbarui pengumpulan yang sudah dikirim.
- Melihat status pengumpulan tepat waktu atau terlambat.
- Melihat riwayat tugas pada halaman **Pengumpulan Saya**.

## Teknologi

- PHP 8.3
- Laravel 13
- Laravel Blade
- Tailwind CSS 4
- Vite
- MySQL
- Laravel Eloquent ORM
- Laravel Session Authentication
- Laravel Storage
- PHPUnit

## Struktur Data

Aplikasi menggunakan tiga tabel utama:

- `users`: menyimpan akun admin dan peserta.
- `tasks`: menyimpan tugas yang dibuat oleh instruktur.
- `submissions`: menyimpan pengumpulan tugas peserta.

Satu peserta hanya memiliki satu pengumpulan untuk setiap tugas melalui `unique constraint` pada kombinasi `task_id` dan `user_id`.

## Persyaratan Sistem

Pastikan perangkat telah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- MySQL
- Node.js dan NPM
- Git

## Instalasi

Clone repository dan masuk ke direktori proyek:

```bash
git clone https://github.com/Whyjuhari/task-management.git
cd task-management
```

Instal dependency PHP dan frontend:

```bash
composer install
npm install
```

Salin konfigurasi environment dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

Untuk Windows PowerShell, gunakan:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

## Konfigurasi Database

Masuk ke MySQL, kemudian buat database:

```sql
CREATE DATABASE taskflow_pelatihan
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Buka file `.env`, kemudian sesuaikan konfigurasi berikut:

```dotenv
APP_NAME="TaskFlow Pelatihan"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow_pelatihan
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL pada perangkat.

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Untuk menghapus seluruh tabel dan membuat ulang data pengembangan:

```bash
php artisan migrate:fresh --seed
```

> **Perhatian:** Perintah `migrate:fresh` akan menghapus seluruh data pada database yang dipilih.

## Menjalankan Aplikasi

Jalankan server pengembangan:

```bash
composer run dev
```

Alternatifnya, jalankan Laravel dan Vite pada dua terminal yang berbeda:

```bash
php artisan serve
```

```bash
npm run dev
```

Aplikasi dapat dibuka melalui:

```text
http://127.0.0.1:8000
```

Untuk membuat build frontend production:

```bash
npm run build
```

## Akun Demo

| Role               | Email                   | Password   |
| ------------------ | ----------------------- | ---------- |
| Admin / Instruktur | `admin@taskflow.test`   | `password` |
| Peserta            | `peserta@taskflow.test` | `password` |

Akun demo dibuat melalui seeder dan hanya digunakan untuk pengembangan serta pengujian.

## Menjalankan Test

Jalankan pengujian aplikasi dengan:

```bash
php artisan test
```

Alternatif:

```bash
composer test
```

## Penyimpanan File

File pengumpulan disimpan secara privat melalui Laravel Storage pada direktori:

```text
storage/app/private/submissions/
```

Ketentuan upload:

- Format yang diperbolehkan: PDF, DOC, DOCX, ZIP, PNG, JPG, dan JPEG.
- Ukuran file maksimal 5 MB.
- File hanya dapat diunduh admin melalui route yang telah dilindungi.
- File lama dihapus ketika peserta mengganti file pengumpulan.
- File terkait ikut dibersihkan ketika tugas dihapus.

## Keterbatasan

- Belum tersedia registrasi mandiri.
- Belum tersedia fitur lupa password dan verifikasi email.
- Belum tersedia notifikasi email atau realtime.
- Belum tersedia sistem penilaian dan umpan balik tugas.
- Penyimpanan file masih menggunakan server lokal dan belum menggunakan cloud storage.

## Pengembangan Berikutnya

- Pengelolaan akun peserta oleh admin.
- Notifikasi tugas dan deadline.
- Penilaian serta umpan balik instruktur.
- Penyimpanan file berbasis cloud.
- Pengujian end-to-end.

## Developer

**Juhari**

Proyek Tes Praktik Web Developer

**Arsitektur:** Laravel MVC, Blade, Tailwind CSS, dan MySQL
