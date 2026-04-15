# MoizPayment

MoizPayment adalah aplikasi manajemen invoice, quotation, dan keuangan berbasis `CodeIgniter 3`, `PHP 7.4`, dan `MariaDB`.

Status saat ini:
- Login dan session autentikasi
- Dashboard statistik dan grafik cashflow
- CRUD klien
- CRUD quotation dengan item dinamis
- CRUD invoice dengan item dinamis
- Konversi quotation ke invoice
- Input pembayaran invoice / uang masuk
- Input pengeluaran / uang keluar
- Laporan ringkas pemasukan, pengeluaran, dan piutang
- Pengaturan profil perusahaan, bank, SMTP, dan WhatsApp gateway

## Default Login

- Username: `admin`
- Password: `admin12345`

Segera ganti password setelah deployment pertama.

## Struktur Penting

- `application/` kode utama aplikasi
- `assets/` CSS, JS, upload, PDF
- `database/schema.sql` schema database + seed awal
- `MoizPayment_Blueprint.docx` blueprint awal proyek

## Instalasi Lokal

1. Buat database dan user MariaDB.
2. Import `database/schema.sql`.
3. Pastikan document root mengarah ke root project ini.
4. Pastikan `mod_rewrite` aktif dan `.htaccess` terbaca.
5. Pastikan folder berikut writable:
   - `application/cache`
   - `application/logs`
   - `assets/img/uploads`
   - `assets/pdf`
6. Jalankan dengan `PHP 7.4+`.

Default config database di `application/config/database.php`:
- database: `moizpayment`
- user: `moizpay`
- password: `MoizPayment@2026!`

Anda juga bisa override dengan environment variable:
- `MP_DB_HOST`
- `MP_DB_NAME`
- `MP_DB_USER`
- `MP_DB_PASS`

## Verifikasi yang Sudah Dilakukan

- Syntax check seluruh controller, model, view, helper, dan core file
- Import schema ke MariaDB lokal
- Login HTTP end-to-end berhasil
- Dashboard ter-render
- Submit form klien via HTTP berhasil dan tersimpan ke database

## Catatan

Integrasi kirim email SMTP, WhatsApp gateway, dan generator PDF masih disiapkan pada level konfigurasi/settings dan belum saya sambungkan penuh ke tombol pengiriman dokumen.
