# Deploy ke aaPanel

Dokumen ini menjelaskan deploy MoizPayment ke server aaPanel.

## 1. Upload Source

Upload isi repo ini ke web root aaPanel Anda, misalnya:

- `/www/wwwroot/moizpayment`

Atau clone langsung dari GitHub bila repo sudah dipush.

## 2. Buat Database

Di aaPanel:

1. Buka `Database`
2. Buat database `moizpayment`
3. Buat user database
4. Import file `database/schema.sql`

Jika Anda ingin memakai credential selain default, ubah:

- `application/config/database.php`

Atau set environment variable:

- `MP_DB_HOST`
- `MP_DB_NAME`
- `MP_DB_USER`
- `MP_DB_PASS`

## 3. Set PHP Version

Gunakan:

- `PHP 7.4` atau `PHP 8.1`

Untuk CodeIgniter 3, `PHP 7.4` adalah pilihan paling aman.

## 4. Set Permission

Pastikan writable:

- `application/cache`
- `application/logs`
- `assets/img/uploads`
- `assets/pdf`

Contoh:

```bash
chmod -R 775 application/cache application/logs assets/img/uploads assets/pdf
chown -R www-data:www-data application/cache application/logs assets/img/uploads assets/pdf
```

Sesuaikan user webserver jika bukan `www-data`.

## 5. Apache / Nginx Rewrite

Repo sudah menyertakan `.htaccess` untuk Apache.

Jika memakai Nginx di aaPanel, gunakan rewrite model CI3:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 6. Login Awal

- Username: `admin`
- Password: `admin12345`

Setelah login:

1. Isi profil perusahaan
2. Ubah credential admin
3. Isi data klien
4. Buat quotation atau invoice
5. Mulai catat pembayaran dan pengeluaran

## 7. Publikasi Domain

Setelah site dan database terpasang di aaPanel:

1. Bind domain/subdomain Anda ke site
2. Pasang SSL
3. Arahkan document root ke folder project

Contoh hasil akses nantinya:

- `https://billing.domainanda.com/`

## 8. Langkah Setelah Ini

Setelah Anda memberi akses aaPanel atau path target web root, deployment bisa dilanjutkan ke:

- copy source ke web root final
- import/update database
- set virtual host / domain
- smoke test endpoint publik
