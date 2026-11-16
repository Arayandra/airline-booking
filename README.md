# Airline Booking

![CI](https://github.com/Arayandra/airline-booking/actions/workflows/ci.yml/badge.svg?branch=main)

Aplikasi pemesanan tiket pesawat berbasis **Laravel 9**. Pengguna dapat melihat dan mencari jadwal penerbangan, melakukan pemesanan tiket, memantau status tiket, serta mengelola profil. Admin dapat mengelola data maskapai, pesawat, bandara, penerbangan, tiket, dan pelanggan melalui dashboard.

Repository: **[Arayandra/airline-booking](https://github.com/Arayandra/airline-booking)**

## Fitur

### Pengguna
- Registrasi dan login.
- Melihat daftar penerbangan.
- Memfilter penerbangan berdasarkan maskapai, bandara asal, bandara tujuan, tanggal keberangkatan, dan tanggal kedatangan.
- Memesan penerbangan.
- Melihat daftar tiket/pemesanan milik akun yang sedang login.
- Membatalkan tiket milik sendiri.
- Mengubah profil dan kata sandi.

### Admin
- Dashboard administrasi.
- Mengelola data maskapai.
- Mengelola data pesawat dan kapasitasnya.
- Mengelola data bandara.
- Mengelola jadwal dan data penerbangan.
- Melihat data pelanggan.
- Melihat tiket dan mengubah status tiket (pending, approved, atau cancelled).

Halaman administrasi dilindungi oleh middleware autentikasi dan middleware admin.

### Aturan pemesanan kursi

One ticket represents one passenger and reserves exactly one seat. The current UI/API accepts one seat per booking request. Multi-passenger booking is intentionally not enabled until the data model supports multiple passenger/ticket records per reservation. Booking and cancellation operations lock the flight row and update available seats transactionally. When an administrator restores a cancelled ticket, the system allocates a currently free seat number again instead of reusing the old reservation number blindly. Cancelled tickets are not shown as active bookings in the flight list.

## Teknologi

| Komponen | Teknologi |
| --- | --- |
| Backend framework | Laravel 9 |
| Bahasa backend | PHP (batas versi pada composer.json: ^7.3 atau ^8.0) |
| Database | MySQL |
| Frontend framework | Bootstrap 5 |
| Asset bundler | Laravel Mix 6 / Webpack |
| Data table | Yajra Laravel DataTables |
| Media dan gambar | Spatie Laravel Media Library |
| JavaScript | jQuery, Axios, dan paket UI pendukung |
| Testing | PHPUnit 9 |

## Persyaratan

- PHP yang kompatibel dengan Laravel 9 dan semua ekstensi yang dibutuhkan paket Composer.
- Composer.
- MySQL atau MariaDB.
- Node.js dan npm yang kompatibel dengan Laravel Mix 6.
- Git.

Pastikan ekstensi PHP yang dibutuhkan Laravel aktif, terutama BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, dan XML. Aktifkan juga driver PDO untuk database yang digunakan (misalnya pdo_mysql).

## Instalasi lokal

### 1. Clone repository

    git clone https://github.com/Arayandra/airline-booking.git
    cd airline-booking

### 2. Instal dependency PHP

    composer install

### 3. Siapkan environment

Salin file contoh environment.

**Windows CMD:**

    copy .env.example .env

**Linux/macOS/Git Bash:**

    cp .env.example .env

Buat application key:

    php artisan key:generate

Edit file .env dan sesuaikan koneksi database MySQL. Contoh konfigurasi:

    APP_NAME="Airline Booking"
    APP_ENV=local
    APP_DEBUG=true
    APP_URL=http://127.0.0.1:8000

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=airline_booking
    DB_USERNAME=root
    DB_PASSWORD=

Buat database bernama airline_booking di MySQL terlebih dahulu. Sesuaikan nama database, username, dan password dengan konfigurasi lokal Anda.

### 4. Jalankan migration dan seeder

    php artisan migrate --seed

Seeder akan membuat data contoh untuk pengguna, maskapai, kota, bandara, pesawat, dan penerbangan. Data seeder ditujukan untuk pengembangan atau demonstrasi lokal.

### 5. Instal dependency frontend

    npm install

Build asset untuk pengembangan:

    npm run dev

Untuk build production:

    npm run prod

Untuk memantau perubahan asset selama pengembangan:

    npm run watch

### 6. Jalankan aplikasi

    php artisan serve

Buka aplikasi melalui:

<http://127.0.0.1:8000>

## Akun demo lokal

Seeder saat ini membuat akun admin contoh:

| Field | Nilai |
| --- | --- |
| Email | admin@airline.com |
| Password | password |

> **Penting:** akun dan password tersebut hanya untuk demonstrasi lokal. Segera ganti password setelah login. Jangan gunakan kredensial demo pada server publik atau production. Seeder juga membuat beberapa akun pelanggan contoh dengan password demo yang sama.

## Struktur direktori

    app/
    ├── Http/
    │   ├── Controllers/
    │   │   ├── Admin/       # Controller dashboard dan pengelolaan data
    │   │   └── Auth/        # Controller autentikasi
    │   ├── Middleware/      # Middleware autentikasi dan admin
    │   └── Requests/        # Validasi request
    ├── Models/              # Model Airline, Airport, Flight, Ticket, dan lainnya
    └── Providers/
    config/                  # Konfigurasi Laravel
    database/
    ├── migrations/          # Struktur tabel database
    └── seeders/             # Data contoh
    public/                  # Asset publik
    resources/
    ├── lang/                # Resource bahasa
    └── views/               # Blade templates
    routes/
    └── web.php              # Route aplikasi
    tests/                   # Pengujian otomatis

## Route utama

| Kegunaan | URL |
| --- | --- |
| Halaman depan | / |
| Login | /login |
| Registrasi | /register |
| Dashboard | /dashboard |
| Profil pengguna | /dashboard/profile |
| Daftar penerbangan untuk pengguna | /dashboard/tickets/show-flights |
| Tiket milik pengguna | /dashboard/tickets/user-tickets |

Route dashboard memerlukan autentikasi. Fitur administrasi di dalam dashboard juga memerlukan akun dengan hak admin.

## Pengujian

Jalankan pengujian yang tersedia dengan:

    php artisan test

## Catatan keamanan dan penggunaan

- Jangan commit file .env. Simpan kredensial database, mail, Pusher, AWS, dan application key hanya di environment lokal atau secret manager.
- Jangan gunakan APP_DEBUG=true di production.
- Ganti password admin demo dan jangan gunakan akun contoh pada server publik.
- Gunakan HTTPS pada deployment production.
- Verifikasi ulang alur pemesanan, ketersediaan kursi, pembatalan, dan perubahan status tiket sebelum digunakan untuk transaksi nyata.
- Source ini mengelola tiket dan status pemesanan. Konfigurasi payment gateway tidak terlihat pada daftar dependency dan route yang diperiksa; jangan menganggap pembayaran online sudah tersedia tanpa integrasi dan pengujian terpisah.
- Sesuaikan data maskapai, bandara, pesawat, jadwal, harga, serta timezone dengan kebutuhan deployment.

## Lisensi

File composer.json mendeklarasikan lisensi MIT. Pastikan seluruh aset dan dependency pihak ketiga yang digunakan tetap mengikuti lisensi masing-masing.


## Automated checks

GitHub Actions runs the PHPUnit suite and validates registered Laravel routes on pushes and pull requests to `main`. The seat allocator has unit tests for first-free-seat selection, full flights, invalid capacity, and numeric/string seat values.


## Maintenance status

This repository currently uses Laravel 9 and legacy Laravel UI / DataTables packages. Laravel 9 no longer receives security fixes. Do not deploy this version as a public production service.

A framework upgrade has intentionally not been applied as a blind version bump. The next maintenance release should first move the runtime to PHP 8.3+, review package compatibility (especially Laravel UI, Spatie Media Library, Yajra DataTables, and the localization/theme packages), then upgrade Laravel in stages, rebuild frontend assets, and run the complete booking/cancellation regression suite. The current CI checks the existing codebase; it does not certify the legacy framework as supported.


## Rencana upgrade Laravel

**Baseline saat ini:** Laravel 9 dengan constraint PHP `^7.3|^8.0`. Security support Laravel 9 berakhir pada **6 Februari 2024**. Ini adalah prioritas upgrade tertinggi di antara proyek Laravel pada portofolio ini.

**Target:** Laravel 13 dan PHP 8.3+. Upgrade belum diterapkan pada `composer.json` atau `composer.lock`.

Sebelum upgrade, audit kompatibilitas paket langsung yang berisiko tinggi: `fruitcake/laravel-cors`, `laravel/ui`, `spatie/laravel-medialibrary`, `yajra/laravel-datatables-oracle`, `psr/simple-cache`, `spatie/laravel-ignition`, PHPUnit, dan Collision. Periksa juga Laravel Mix 6/Webpack serta dependensi JavaScript secara terpisah; jangan mencampur migrasi framework dengan refactor frontend besar dalam satu perubahan.

Urutan validasi:

1. Buat branch `upgrade/laravel-13`, dokumentasikan versi PHP/Composer/Node yang dipakai, dan simpan hasil CI baseline.
2. Naikkan runtime ke PHP 8.3+, lalu perbarui paket satu per satu sesuai constraint yang mendukung Laravel 13. Gunakan `composer why-not laravel/framework ^13.0` untuk menemukan blocker.
3. Periksa kompatibilitas autentikasi, middleware admin, upload/media, DataTables, booking/cancellation, transactional seat allocation, dan pengelolaan status tiket.
4. Jalankan test suite pada database sementara, migration + seeder, `php artisan route:list`, build frontend, serta smoke test untuk alur pencarian penerbangan dan pemesanan.
5. Jalankan `composer validate`, `composer audit`, dan seluruh workflow CI. Commit lockfile yang diperbarui hanya setelah seluruh test lulus.

File `airline.sql` dan aset frontend yang dipakai aplikasi tetap dipertahankan; keduanya bukan artefak runtime yang boleh dihapus tanpa mengganti alur instalasi atau referensi asset.

Referensi: [Laravel release/support policy](https://laravel.com/framework/docs/releases) · [Laravel 13 upgrade guide](https://laravel.com/docs/13.x/upgrade).


## Bukti visual aplikasi

Screenshot browser dari aplikasi yang sedang berjalan belum tersedia di repository. Aset pesawat, ikon, dan gambar frontend tidak dianggap sebagai screenshot aplikasi. Setelah menjalankan demo dengan database seed, tambahkan tangkapan layar halaman pencarian penerbangan, detail/pemesanan tiket, dan dashboard admin ke folder `docs/screenshots/`, lalu tampilkan file tersebut di bagian ini.
