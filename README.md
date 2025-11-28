<p align="center">
  <a href="http://immanuel-store.test" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Immanuel Store Logo">
  </a>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/PHP-8.3-777BB4.svg?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
    <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
    <a href="./LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square" alt="License"></a>
</p>

# Immanuel Store

**Immanuel Store** adalah aplikasi manajemen toko dan Point of Sales (POS) berbasis web yang modern dan efisien. Dibangun menggunakan framework Laravel terbaru, aplikasi ini dirancang untuk membantu pengelolaan inventaris barang, pencatatan transaksi, hingga pelaporan keuangan secara *real-time*.

## 🚀 Fitur Utama

Berdasarkan modul yang tersedia, aplikasi ini memiliki fitur unggulan sebagai berikut:

-   **Dashboard Interaktif**: Ringkasan performa toko dalam satu pandangan.
-   **Manajemen Barang (Items)**:
    -   CRUD (Create, Read, Update, Delete) data barang.
    -   **Ekspor Data**: Mendukung unduhan data barang ke format **Excel** dan **PDF**.
-   **Transaksi Penjualan**:
    -   Pencatatan transaksi yang mudah.
    -   Cetak struk atau laporan transaksi ke **Excel** dan **PDF**.
-   **Laporan Keuangan**:
    -   Pantau pemasukan dan pengeluaran toko.
    -   Fitur ekspor laporan lengkap (PDF/Excel) untuk pembukuan.
-   **Riwayat Aktivitas (History)**: Log aktivitas untuk memantau perubahan data dan keamanan sistem.
-   **Manajemen Pengguna (Users)**: Pengelolaan akun admin dan staf toko.
-   **Otentikasi Aman**: Menggunakan sistem login/registrasi yang aman (Laravel Breeze).

## 🛠️ Teknologi yang Digunakan

-   **Backend**: [Laravel 12](https://laravel.com) & [PHP 8.3](https://php.net)
-   **Frontend**: Blade Templates & [Tailwind CSS](https://tailwindcss.com)
-   **Database**: MySQL / MariaDB
-   **Packages Pendukung**:
    -   `maatwebsite/excel`: Untuk ekspor laporan ke Excel.
    -   `barryvdh/laravel-dompdf`: Untuk cetak laporan ke PDF.

## ⚙️ Persyaratan Sistem

Pastikan server atau lingkungan lokal Anda memenuhi persyaratan berikut:

-   **PHP**: >= 8.3
-   **Composer**: Versi terbaru
-   **Node.js & NPM**: Untuk mengompilasi aset frontend
-   **Database**: MySQL, PostgreSQL, atau SQLite

## 📦 Instalasi & Penggunaan

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username-anda/immanuel-store.git](https://github.com/username-anda/immanuel-store.git)
    cd immanuel-store
    ```

2.  **Instal Dependensi PHP & JavaScript**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Lingkungan (.env)**
    Salin file contoh `.env` dan sesuaikan dengan konfigurasi database Anda.
    ```bash
    cp .env.example .env
    ```
    *Buka file `.env` dan atur `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai database lokal Anda.*

4.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi Database**
    Jalankan migrasi untuk membuat tabel yang diperlukan.
    ```bash
    php artisan migrate
    ```
    *(Opsional) Jika ingin mengisi data dummy untuk pengetesan:*
    ```bash
    php artisan db:seed
    ```

6.  **Jalankan Aplikasi**
    Anda perlu menjalankan server Laravel dan Vite (untuk aset) secara bersamaan (atau di terminal terpisah).
    ```bash
    npm run dev
    php artisan serve
    ```

7.  **Akses Aplikasi**
    Buka browser dan kunjungi: `http://localhost:8000`

## 📄 Lisensi

Aplikasi ini adalah perangkat lunak open-source di bawah lisensi [MIT](https://opensource.org/licenses/MIT).