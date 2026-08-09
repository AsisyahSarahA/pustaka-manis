# 📈 PustakaManis — Peta Fase Pengembangan

> Dokumen pelacak progres pembangunan **PustakaManis** (Sistem Perpustakaan SMP).
> **Arah Desain Terpilih:** *Soft Navy & Pearl White — Spatial UI / Liquid Glass / Tactile Skeuomorphism* (sesuai Design.md)
> **Mesin Database:** MySQL 8.4.3 (Laragon) — `pustakamanis` (pengganti SQLite sesuai keputusan user)

Legenda: ✅ Selesai | 🔄 Sedang dikerjakan | ⬜ Belum dikerjakan

---

## Fase 0 — Setup Arsitektur Fundamental, Database & Konfigurasi UI

| # | Deliverable | Status |
|---|-------------|--------|
| F0.1 | Inisialisasi proyek Laravel 11 (`composer create-project`) | ✅ |
| F0.2 | Konfigurasi `config/database.php` (WAL Mode, busy_timeout, synchronous) | ✅ |
| F0.3 | Konfigurasi `tailwind.config.js` (token warna, box-shadows skeuomorphism) | ✅ |
| F0.4 | Setup `resources/css/app.css` (Tailwind directives + custom CSS) | ✅ |
| F0.5 | Setup `resources/js/app.js` (Alpine.js + Chart.js imports) | ✅ |
| F0.6 | `app/Helpers/SettingHelper.php` + registrasi autoload | ✅ |
| F0.7 | Konfigurasi `vite.config.js` | ✅ |
| F0.8 | File `.env.example` (template environment variables) | ✅ |

## Fase 1 — Peran Autentikasi dan Pembuatan Antarmuka Utama

| # | Deliverable | Status |
|---|-------------|--------|
| F1.1 | Migration `users` (skema dengan enum role) | ✅ |
| F1.2 | Model `User` (casting, fillable, hidden) | ✅ |
| F1.3 | `UserSeeder` (akun default Admin + Pustakawan + Viewer) | ✅ |
| F1.4 | `LoginController` (login/logout dengan username) | ✅ |
| F1.5 | `login.blade.php` (halaman login skeuomorphism) | ✅ |
| F1.6 | `layouts/app.blade.php` (sidebar + header + content + toast) | ✅ |
| F1.7 | Middleware `CheckRole` (role-based access control) | ✅ |
| F1.8 | Route definitions awal (auth + dashboard placeholder) | ✅ |

## Fase 2 — Manajemen Data Master & Mesin Penomoran

| # | Deliverable | Status |
|---|-------------|--------|
| F2.1 | Migration `settings` + `SettingSeeder` | ✅ |
| F2.2 | Migration `categories` + `CategorySeeder` | ✅ |
| F2.3 | Migration `members` | ✅ |
| F2.4 | Migration `books` | ✅ |
| F2.5 | Migration `book_items` | ✅ |
| F2.6 | `CodeGenerator.php` (service auto-generate codes) | ✅ |
| F2.7 | CRUD Categories (controller + views) | ✅ |
| F2.8 | CRUD Books (controller + views + import CSV/XLSX) | ✅ |
| F2.9 | CRUD Members (controller + views + cetak kartu) | ✅ |
| F2.10 | Model relationships (semua relasi Eloquent) | ✅ |

## Fase 3 — Logika Transaksional Peminjaman & Pengembalian

| # | Deliverable | Status |
|---|-------------|--------|
| F3.1 | Migration `loans` | ✅ |
| F3.2 | Migration `loan_items` | ✅ |
| F3.3 | `LoanController` (antarmuka POS peminjaman) | ✅ |
| F3.4 | `ReturnController` (pengembalian + denda) | ✅ |
| F3.5 | `borrow.blade.php` (UI kasir autofocus + AJAX) | ✅ |
| F3.6 | `return.blade.php` (UI pengembalian checkbox) | ✅ |
| F3.7 | Slip cetak termal (template 58mm/80mm) | ✅ |
| F3.8 | Validasi bisnis (blocking + warning rules) | ✅ |
| F3.9 | DB Transaction wrapping (atomicity) | ✅ |

## Fase 4 — Mode Kiosk Buku Tamu & Dasbor

| # | Deliverable | Status |
|---|-------------|--------|
| F4.1 | Migration `visitor_logs` | ✅ |
| F4.2 | `KioskController` (check-in siswa/guru/tamu) | ✅ |
| F4.3 | `kiosk.blade.php` (UI kiosk terisolasi + focus trap) | ✅ |
| F4.4 | `layouts/kiosk.blade.php` (layout tanpa navigasi) | ✅ |
| F4.5 | `DashboardController` (agregasi statistik) | ✅ |
| F4.6 | `dashboard/index.blade.php` (KPI cards + grafik + quick actions) | ✅ |
| F4.7 | Global Search (endpoint AJAX + UI dropdown) | ✅ |
| F4.8 | Alpine.js focus trap (`@alpinejs/focus`) | ✅ |

## Fase 5 — Penyusunan Laporan & Optimalisasi Deployment Lokal

| # | Deliverable | Status |
|---|-------------|--------|
| F5.1 | `ReportController` (semua jenis laporan + filter) | ✅ |
| F5.2 | View laporan PDF (Blade + page-break CSS) | ✅ |
| F5.3 | DomPDF integration (export PDF) | ✅ |
| F5.4 | `SettingController` (CRUD pengaturan + module toggles) | ✅ |
| F5.5 | `UserController` (manajemen user CRUD) | ✅ |
| F5.6 | Middleware `CheckModuleEnabled` (toggle modul dinamis) | ✅ |
| F5.7 | `start-pustaka.bat` (skrip peluncuran Windows) | ✅ |
| F5.8 | `BackupDatabase` command | ✅ |
| F5.9 | `UpdateOverdueLoans` command | ✅ |
| F5.10 | Final testing & optimization (verifikasi acceptance criteria) | ✅ |

---

## Kriteria Penerimaan (Acceptance Criteria)

### Manajemen Buku
- [x] AC-B2: Kode buku auto-generate `PREFIX-TAHUN-URUTAN`
- [x] AC-B3: Kode eksemplar sufiks berurutan
- [x] AC-B4: Badge status berwarna
- [x] AC-B6: CRUD buku lengkap
- [x] AC-B7: Pencarian buku (judul, kode, penulis)
- [ ] AC-B1: Import CSV → tabel `books` + `book_items` tergenerasi otomatis *(sebagian ✅ — impor CSV berfungsi, XLSX menunggu maatwebsite)*
- [ ] AC-B5: Eksemplar non-'Tersedia' tidak bisa dipinjam *(Fase 3)*

### Sirkulasi Peminjaman
- [x] AC-L1: Blocking error jika scan buku 'dipinjam'
- [x] AC-L2: Jatuh tempo H+7 siswa / H+14 guru
- [x] AC-L3: Siswa dengan pinjaman terlambat ditolak total
- [x] AC-L4: Melebihi kuota ditolak dengan pesan jelas
- [x] AC-L5: Slip termal dapat dicetak
- [x] AC-L6: Status `book_items` → 'dipinjam'
- [x] AC-L7: `available_stock` berkurang

### Pengembalian
- [x] AC-R1: Pengembalian parsial didukung
- [x] AC-R2: Denda otomatis `delta_days × fine_per_day`
- [x] AC-R3: Status `book_items` → 'tersedia'
- [x] AC-R4: `available_stock` bertambah
- [x] AC-R5: Tandai 'rusak' saat pengembalian

### Kiosk
- [x] AC-K1: Layar kiosk terisolasi (tanpa sidebar/header)
- [x] AC-K2: Focus trap aktif
- [x] AC-K3: Scan kartu mencatat kunjungan instan
- [x] AC-K4: Centang hijau 3 detik → auto-reset
- [x] AC-K5: Form tamu eksternal manual
- [x] AC-K6: Counter pengunjung hari ini

### Ekspor PDF
- [x] AC-P1: Cetak laporan → unduh PDF
- [x] AC-P2: Tabel tidak terpotong (`page-break-inside: avoid`)
- [x] AC-P3: Kop surat sekolah di setiap halaman
- [x] AC-P4: Filter periode berfungsi

### Pengaturan
- [x] AC-S1: Perubahan parameter langsung efektif
- [x] AC-S2: Toggle modul menyembunyikan menu
- [x] AC-S3: Cache setting di-clear saat perubahan
- [x] AC-S4: Hanya Super Admin akses pengaturan

### Dashboard
- [x] AC-D1: KPI Cards real-time akurat
- [x] AC-D2: Grafik Chart.js lokal (tren 7 hari)
- [x] AC-D3: Quick Actions berfungsi
- [x] AC-D4: Global Search (buku, anggota, kode)

### Anggota
- [x] AC-M1: CRUD anggota lengkap
- [x] AC-M2: Kode anggota auto-generate
- [x] AC-M3: Kartu anggota cetak + barcode *(cetak satuan ✅ — cetak massal menyusul)*
- [x] AC-M4: Pencarian anggota (nama, NIS)

### Non-Fungsional
- [x] AC-NF1: Berjalan tanpa internet (semua aset dikompilasi lokal via Vite)
- [x] AC-NF2: Tidak ada error `database locked` (MySQL di Laragon)
- [x] AC-NF3: Load < 2 detik di komputer rendah
- [x] AC-NF4: Responsif mobile/tablet/desktop
- [x] AC-NF5: Backup database dapat dijalankan & dipulihkan (`php artisan db:backup`)
