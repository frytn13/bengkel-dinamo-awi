# Sistem Informasi Akuntansi & ERP - Bengkel Dinamo Awi
**Aplikasi Point of Sales (POS), Inventory Control, dan Financial Audit Berskala Enterprise**

<p>
  <img src="https://img.shields.io/badge/Framework-Laravel_11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/UI-Bootstrap_5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Status-Production_Ready-success?style=for-the-badge" alt="Status">
</p>

---

## 📖 Deskripsi Proyek
Sistem Informasi Akuntansi (SIA) Bengkel Dinamo Awi adalah solusi perangkat lunak berbasis web yang dirancang untuk mendigitalisasi dan mengotomatisasi proses bisnis bengkel otomotif. Dikembangkan menggunakan *framework* Laravel, sistem ini mengintegrasikan berbagai modul krusial mulai dari manajemen rantai pasok (*Supply Chain*), manajemen inventaris, operasional kasir (*Point of Sales*), hingga pembuatan laporan keuangan dan audit secara *real-time*.

Proyek ini berfokus pada keandalan data (*Data Integrity*), efisiensi operasional, serta penerapan standar pelaporan yang akurat guna mendukung pengambilan keputusan strategis bagi manajemen.

## ✨ Fitur Utama (Core Modules)

### 1. Master Data Management
Modul pengelolaan data fundamental yang terstruktur dengan relasi *database* yang ketat (*Strict Foreign Key Constraints*).
- Manajemen Produk & Sparepart (Kategori, Satuan, Multi-Lokasi Rak).
- Manajemen Data Vendor / Pemasok.

### 2. Supply Chain & Procurement
- **Purchase Order (PO):** Pencatatan pesanan pembelian kepada vendor dengan visibilitas status dokumen (*Pending, Parsial, Selesai, Ditutup Paksa*).
- **Receive Order (RO):** Modul penerimaan barang yang secara otomatis memperbarui kuantitas stok gudang dan mencatat rekapitulasi hutang dagang (Hutang Vendor).

### 3. Point of Sales (POS) & Manajemen Piutang
- Antarmuka transaksi kasir dinamis yang mendukung layanan Jasa Servis, Penjualan Sparepart, maupun kombinasi keduanya.
- Integrasi metode pembayaran Tunai (Kas Masuk) dan Tempo (Pencatatan Piutang Pelanggan).
- Sistem proteksi stok otomatis untuk mencegah manipulasi atau insiden *negative stock*.

### 4. Inventory Control & Asset Tracking
- Pencatatan pengeluaran barang untuk kebutuhan operasional bengkel (Pemakaian Internal).
- Pencatatan barang rusak/hilang (*Write-off*) yang terintegrasi dengan laporan pengeluaran aset.
- Fungsionalitas *Void* untuk membatalkan transaksi dengan mekanisme *rollback* stok yang dijamin keamanannya oleh *Database Transactions*.

### 5. Financial Reporting & Audit
Modul pelaporan *Enterprise-grade* yang memproses kalkulasi finansial kompleks secara akurat:
- Pemantauan metrik keuangan utama: **Kas Masuk, Kas Keluar, Total Piutang Pelanggan**, dan **Hutang Vendor**.
- Audit rekonsiliasi barang (identifikasi sisa PO yang berstatus *Backorder*).
- Fitur cetak laporan *Real-Time* berformat PDF presisi tinggi (menggunakan *engine client-side rendering*) dengan desain tata letak A4 yang terstandarisasi.

---

## 💻 Arsitektur & Teknologi

- **Backend:** PHP 8.x, Laravel 11.x
- **Frontend:** HTML5, CSS3, Bootstrap 5, Vanilla JavaScript, jQuery
- **Database:** MySQL (Relational Database Design with Strict ACID Compliance)
- **PDF Engine:** html2pdf.js (Client-side Document Rendering)

---

## 🚀 Panduan Instalasi (Development Setup)

Untuk menjalankan proyek ini di lingkungan pengembangan lokal (*Localhost*), silakan ikuti instruksi berikut:

**1. Kloning Repositori**
```bash
git clone [https://github.com/username-anda/bengkel-dinamo-awi.git](https://github.com/username-anda/bengkel-dinamo-awi.git)
cd bengkel-dinamo-awi
```

**2. Instalasi Dependensi**
```bash
composer install
npm install && npm run build
```

**3. Konfigurasi Environment**
Salin file `.env.example` menjadi `.env`, lalu sesuaikan kredensial koneksi *database* (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
```bash
cp .env.example .env
php artisan key:generate
```

**4. Migrasi Database & Data Seeder**
Sistem ini menyertakan *seeder* komprehensif (Master Data, PO, RO, Sales) untuk keperluan pengujian fungsionalitas pelaporan dan pembuktian struktur relasi.
```bash
php artisan migrate:fresh --seed
```

**5. Jalankan Server Lokal**
```bash
php artisan serve
```
Akses aplikasi melalui peramban web di `http://127.0.0.1:8000`

---

## 👨‍💻 Informasi Pengembang

**Yulianus Febry Tri Nugroho**  
*Informatics Student at Universitas Musi Charitas (UKMC), Palembang*

Let's connect:
- **LinkedIn:** [Profil LinkedIn Anda](https://linkedin.com/in/username)
- **Email:** [Alamat Email Anda](mailto:email@domain.com)

<br>
<p align="center"><i>© 2026 Yulianus Febry Tri Nugroho. All Rights Reserved.</i></p>
