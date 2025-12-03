<p align="center">
  <a href="http://immanuel-store.test" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Immanuel Store Logo">
  </a>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/PHP-8.5-777BB4.svg?style=flat-square&logo=php&logoColor=white" alt="PHP 8.5">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
    <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
    <a href="./LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square" alt="License"></a>
</p>

# 🛒 Immanuel Store

**Immanuel Store** adalah aplikasi **Point of Sales (POS)** dan manajemen toko berbasis web yang modern, efisien, dan mudah digunakan. Dibangun dengan **Laravel 12** dan **PHP 8.5**, aplikasi ini membantu pengelolaan inventaris, transaksi, hingga laporan keuangan secara *real-time*.

---

## 🚀 Fitur Utama

- **Dashboard Interaktif**: Ringkasan performa toko dalam satu tampilan.
- **Manajemen Barang (Items)**:
  - CRUD data barang.
  - Ekspor data ke **Excel** & **PDF**.
- **Transaksi Penjualan**:
  - Pencatatan transaksi cepat & mudah.
  - Cetak struk & laporan transaksi (Excel/PDF).
- **Laporan Keuangan**:
  - Pantau pemasukan & pengeluaran.
  - Ekspor laporan lengkap untuk pembukuan.
- **Riwayat Aktivitas (History)**: Log aktivitas untuk keamanan & audit.
- **Manajemen Pengguna (Users)**: Kelola akun admin & staf.
- **Otentikasi Aman**: Login/registrasi dengan **Laravel Breeze**.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: [Laravel 12](https://laravel.com) + [PHP 8.5](https://php.net)
- **Frontend**: Blade Templates + [Tailwind CSS](https://tailwindcss.com)
- **Database**: MySQL / MariaDB
- **Packages**:
  - [`maatwebsite/excel`](https://github.com/Maatwebsite/Laravel-Excel) → Ekspor Excel
  - [`barryvdh/laravel-dompdf`](https://github.com/barryvdh/laravel-dompdf) → Cetak PDF

---

## ⚙️ Persyaratan Sistem

- **PHP**: >= 8.5  
- **Composer**: Versi terbaru  
- **Node.js & NPM**: Untuk kompilasi aset frontend  
- **Database**: MySQL, PostgreSQL, atau SQLite  

---

## 📦 Instalasi & Penggunaan

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