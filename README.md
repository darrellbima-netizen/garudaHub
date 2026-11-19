# GarudaHub

GarudaHub adalah aplikasi web lelang proyek IT yang memungkinkan klien mempublikasikan proyek dan developer mengajukan penawaran. Aplikasi ini dikembangkan sebagai bagian dari tugas mata kuliah Pemrograman Berbasis Web.

## Teknologi

- **Backend:** Laravel
- **Bahasa:** PHP
- **Database:** PostgreSQL
- **ORM:** Laravel Eloquent ORM
- **API:** RESTful API
- **Version Control:** Git dan GitHub

## Fitur Utama

- Pengelolaan data proyek (CRUD).
- Pengelolaan data penawaran atau bid (CRUD).
- Relasi antara pengguna, proyek, dan penawaran.
- Validasi data menggunakan Laravel.
- Penggunaan foreign key dan constraint pada PostgreSQL.

## Struktur Database

GarudaHub menggunakan tiga tabel utama:

- `users`: menyimpan data pengguna dan perannya.
- `projects`: menyimpan informasi proyek yang dipublikasikan klien.
- `bids`: menyimpan penawaran developer terhadap proyek.

Relasi database:
- Satu pengguna dengan peran klien dapat memiliki banyak proyek.
- Satu proyek dapat memiliki banyak penawaran.
- Satu pengguna dengan peran developer dapat memiliki banyak penawaran.
- Setiap penawaran terhubung dengan satu proyek dan satu developer.

## Instalasi

1. Clone repository:
   ```bash
   git clone <URL_REPOSITORY>
   ```
2. Masuk ke direktori project:
   ```bash
   cd garudaHub-git
   ```
3. Install dependency:
   ```bash
   composer install
   ```
4. Salin `.env.example` menjadi `.env`.
5. Atur koneksi PostgreSQL pada `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=garuda_hub_pt2
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```
6. Generate application key:
   ```bash
   php artisan key:generate
   ```
7. Jalankan migration:
   ```bash
   php artisan migrate
   ```
8. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```

Aplikasi dapat diakses melalui `http://127.0.0.1:8000`.

## API Endpoints

### Projects

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/projects` | Menampilkan semua proyek |
| GET | `/api/projects/{id}` | Menampilkan detail proyek |
| POST | `/api/projects` | Membuat proyek |
| PUT/PATCH | `/api/projects/{id}` | Memperbarui proyek |
| DELETE | `/api/projects/{id}` | Menghapus proyek |

### Bids

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/bids` | Menampilkan semua penawaran |
| GET | `/api/bids/{id}` | Menampilkan detail penawaran |
| POST | `/api/bids` | Membuat penawaran |
| PUT/PATCH | `/api/bids/{id}` | Memperbarui penawaran |
| DELETE | `/api/bids/{id}` | Menghapus penawaran |

## Tujuan Pengembangan

Project ini berfokus pada:
1. Redesain arsitektur database.
2. Refactoring aplikasi menggunakan pola MVC Laravel.
3. Implementasi ORM Eloquent untuk operasi CRUD.

