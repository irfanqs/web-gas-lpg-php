# Website Gas LPG

Aplikasi web penjualan gas LPG dengan fitur pembayaran online menggunakan Midtrans.

## Persyaratan

- PHP 8.4+
- MySQL / MariaDB
- Web server (Apache/Nginx) atau PHP built-in server
- Akun Midtrans (untuk payment gateway)[^1]
- Akun Google reCAPTCHA v2[^2]

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/irfanqs/web-gas-lpg-php.git
cd web-gas-lpg-php
```

### 2. Import database

Import file `gas-lpg/gas_lpg.sql` ke MySQL:

```bash
mysql -u root -p gas_lpg < gas-lpg/gas_lpg.sql
```

Atau import manual via phpMyAdmin.

### 3. Jalankan aplikasi

```bash
cd gas-lpg
php -S localhost:8000
```

Buka browser: http://localhost:8000

## Struktur User

| Role | Akses |
|------|-------|
| Admin | Kelola produk, pesanan, pegawai, laporan |
| Pegawai | Kelola pesanan, transaksi |
| Kurir | Lihat & update status pengantaran |
| Pembeli | Pesan produk, pembayaran |

## Catatan
- Daftar Midtrans di https://midtrans.com untuk mendapatkan Server Key dan Client Key
- Daftar reCAPTCHA di https://www.google.com/recaptcha untuk mendapatkan Site Key dan Secret Key
