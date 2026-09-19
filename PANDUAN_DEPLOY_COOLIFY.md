# 🚀 Panduan Lengkap Deploy VexaHost ke Coolify

Panduan resmi deployment aplikasi **VexaHost Cloud Platform** (`vexahostcloud.my.id`) ke server VPS menggunakan **Coolify**, mengikuti standar arsitektur ekosistem Flustra.

---

## 📌 Ringkasan Proyek

| Item | Nilai |
| --- | --- |
| **Aplikasi** | VexaHost Cloud Platform |
| **Repository** | `rztechdev/vexahost` |
| **Stack** | Laravel 12 · PHP 8.2+ · Vite · Tailwind CSS · MySQL · Redis |
| **Build Pack** | Nixpacks (Default Coolify, tanpa Dockerfile) |
| **Port Internal** | `80` |

### Pemetaan Environment

| Environment | Branch Git | Domain Publik | Nama Database | File Template Env |
| --- | --- | --- | --- | --- |
| **Production** | `main` | `https://vexahostcloud.my.id` | `vexahost` | `.env.production` |
| **Staging** | `staging` | `https://staging.vexahostcloud.my.id` | `vexahost_staging` | `.env.staging` |
| **Development** | `dev` | `https://dev.vexahostcloud.my.id` | `vexahost_dev` | `.env.development` |

---

## 📍 LANGKAH 1: Pengaturan DNS Domain

Arahkan domain ke IP server VPS Coolify Anda melalui DNS Management (Cloudflare / Registrar Domain):

| Tipe | Nama Record | Target / Nilai | Proxy Status | Keterangan |
| --- | --- | --- | --- | --- |
| **A** | `@` | `<IP_VPS_COOLIFY>` | DNS Only / Proxied | Domain utama production (`vexahostcloud.my.id`) |
| **A** | `www` | `<IP_VPS_COOLIFY>` | DNS Only / Proxied | Alias www production |
| **A** | `staging` | `<IP_VPS_COOLIFY>` | DNS Only / Proxied | Subdomain staging (`staging.vexahostcloud.my.id`) |
| **A** | `dev` | `<IP_VPS_COOLIFY>` | DNS Only / Proxied | Subdomain development (`dev.vexahostcloud.my.id`) |

> ⚠️ **Penting untuk Cloudflare SSL:**
> Bila menggunakan Cloudflare Proxy (awan oranye), pastikan SSL/TLS encryption mode disetel ke **Full** atau **Full (Strict)** agar tidak terjadi redirect loop `ERR_TOO_MANY_REDIRECTS`.
>
> Pastikan juga di dashboard Cloudflare Turnstile, domain `vexahostcloud.my.id` dan subdomains telah didaftarkan ke daftar **Allowed Domains**.

---

## 📍 LANGKAH 2: Persiapan Database MySQL di Coolify

Setiap environment disarankan memiliki database terpisah agar data development/testing tidak tercampur dengan production:

1. Buka layanan database MySQL di Coolify atau melalui phpMyAdmin / DB GUI.
2. Jalankan perintah SQL berikut untuk membuat database:

```sql
-- Database Production
CREATE DATABASE `vexahost` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Database Staging
CREATE DATABASE `vexahost_staging` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Database Development
CREATE DATABASE `vexahost_dev` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📍 LANGKAH 3: Pembuatan Resource Aplikasi di Coolify

Ulangi langkah berikut untuk setiap environment (`production`, `staging`, `development`):

1. Masuk ke dashboard Coolify ➔ Pilih Project ➔ Klik **`+ Add New Resource`**.
2. Pilih **`Private Repository (with GitHub App)`** (atau Public Repository jika repo public).
3. Pilih repository: **`rztechdev/vexahost`**.
4. Atur informasi dasar:
   - **Branch**: `main` (untuk Production), `staging` (untuk Staging), atau `dev` (untuk Dev).
   - **Name**: `vexahost-app` / `vexahost-staging` / `vexahost-dev`.
   - **Build Pack**: **Nixpacks** (default).
   - **Domains**: Masukkan URL HTTPS lengkap, contoh:
     - Production: `https://vexahostcloud.my.id`
     - Staging: `https://staging.vexahostcloud.my.id`
     - Dev: `https://dev.vexahostcloud.my.id`
   - **Port**: `80`
   - **Healthcheck**: **Matikan / Uncheck** *Enable Healthcheck* (mengikuti standar Flustra).

### Konfigurasi Command di General Settings:

| Kolom | Nilai | Penjelasan |
| --- | --- | --- |
| **Install Command** | *(kosongkan)* | Nixpacks otomatis mendeteksi `composer.json` & `package.json` untuk menjalankan install. |
| **Build Command** | `npm run build` | Melakukan build aset CSS & JS via Vite untuk mode production. |
| **Start Command** | `php artisan serve --host=0.0.0.0 --port=80` | Menjalankan server aplikasi di port container 80. |

---

## 📍 LANGKAH 4: Konfigurasi Environment Variables

1. Buka resource aplikasi di Coolify ➔ Tab **Environment Variables**.
2. Buka file environment yang sesuai di laptop Anda:
   - Untuk Production: buka file [`.env.production`](file:///d:/Project%20-%20Rz%20digital%20creative/vexahost/.env.production)
   - Untuk Staging: buka file [`.env.staging`](file:///d:/Project%20-%20Rz%20digital%20creative/vexahost/.env.staging)
   - Untuk Development: buka file [`.env.development`](file:///d:/Project%20-%20Rz%20digital%20creative/vexahost/.env.development)
3. Salin (**Copy**) seluruh isinya dan tempel (**Paste**) ke kolom environment di Coolify.
4. **Periksa & Sesuaikan Koneksi Database:**
   - Bila MySQL berada di dalam satu server Coolify yang sama (Docker network Coolify), isi `DB_HOST` dengan IP internal / service name MySQL Coolify.
   - Isi `DB_USERNAME` dan `DB_PASSWORD` sesuai kredensial MySQL Coolify.
   - Pastikan `DB_DATABASE` sesuai dengan environment:
     - Production: `DB_DATABASE=vexahost`
     - Staging: `DB_DATABASE=vexahost_staging`
     - Dev: `DB_DATABASE=vexahost_dev`
5. **Periksa Cloudflare Turnstile:**
   ```env
   TURNSTILE_SITE_KEY="0x4AAAAAAE5Ryx_iTQzttsDQ"
   TURNSTILE_SECRET_KEY="<isi-di-coolify-jangan-ditulis-di-sini>"
   ```
6. **Periksa Akun Admin Seeder:**
   ```env
   ADMIN_NAME="Ryan Rizki"
   ADMIN_USERNAME="mryanrizki11"
   ADMIN_EMAIL="vexahostcloudtech@gmail.com"
   ADMIN_PASSWORD="12345678"
   ADMIN_PHONE="6285808749131"
   ADMIN_COMPANY="VexaHost Cloud Indonesia"
   ADMIN_API_KEY="<isi-di-coolify-jangan-ditulis-di-sini>"
   ```
7. Klik **Save**.

---

## 📍 LANGKAH 5: Persistent Storage (Uploads & Invoices)

Tanpa persistent storage, file gambar dan invoice PDF yang diunggah/dibuat akan hilang setiap kali container di-redeploy.

1. Di menu resource aplikasi Coolify ➔ Buka tab **Persistent Storage** ➔ Klik **`+ Add`**.
2. Masukkan pengaturan berikut:
   - **Name**: `vexahost-storage`
   - **Mount Path**: `/app/storage/app/public`
3. Klik **Save**.

---

## 📍 LANGKAH 6: Post-Deployment Commands

Tab **Pre/Post Deployment Commands** ➔ Bagian **Post-deployment Commands**:

```bash
php artisan optimize:clear && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Mengapa Urutan Ini Penting?
1. **`php artisan optimize:clear`**: Menghapus cache konfigurasi lama agar nilai environment baru (seperti kredensial database/API) langsung terbaca.
2. **`php artisan migrate --force`**: Menjalankan migrasi database otomatis setiap ada perubahan schema.
3. **`php artisan db:seed --force`**: Menjalankan seeder produk VPS, akun admin (dari `.env`), dan struktur permission secara *idempotent* (aman dijalankan berkali-kali tanpa duplikasi).
4. **`php artisan storage:link --force`**: Memastikan symlink `public/storage` ke storage persistent terhubung kembali setelah container dibuat ulang.
5. **`php artisan config:cache && php artisan route:cache && php artisan view:cache`**: Mengoptimalkan performa response aplikasi.

---

## 📍 LANGKAH 7: Background Queue Worker (Opsional / Disarankan)

VexaHost menggunakan antrean database (`QUEUE_CONNECTION=database`) untuk tugas-tugas background seperti:
- Auto-provisioning VPS (`ProvisionVpsJob`)
- Pengiriman email invoice pembayaran via Brevo SMTP

Jika ingin antrean diproses di background:
1. Di Coolify, buat resource worker tambahan atau gunakan tab Cron / Worker.
2. Gunakan perintah:
   ```bash
   php artisan queue:work --sleep=3 --tries=3
   ```

---

## 📍 LANGKAH 8: Integrasi Eksternal (Google OAuth, Lynk.id, Turnstile)

### 1. Google OAuth (Google Cloud Console)
Buka Google Cloud Console ➔ Credentials ➔ Edit OAuth 2.0 Client ID ➔ Tambahkan pada **Authorized redirect URIs**:
- `https://vexahostcloud.my.id/auth/google/callback`
- `https://staging.vexahostcloud.my.id/auth/google/callback`
- `https://dev.vexahostcloud.my.id/auth/google/callback`

OAuth client yang sama dipakai VexaHost WA Gateway, jadi tambahkan juga:
- `https://wa.vexahostcloud.my.id/auth/google/callback`
- `https://wa-staging.vexahostcloud.my.id/auth/google/callback`
- `https://wa-dev.vexahostcloud.my.id/auth/google/callback`

### 2. Lynk.id Webhook (Dashboard Lynk)
Buka pengaturan Webhook di Lynk.id ➔ Set Callback URL:
- Production: `https://vexahostcloud.my.id/api/webhooks/lynk`
- Pastikan secret merchant key sama dengan `LYNK_MERCHANT_KEY="<isi-di-coolify-jangan-ditulis-di-sini>"`.

### 3. Cloudflare Turnstile
Buka Dashboard Cloudflare ➔ Turnstile ➔ Pilih Widget:
- Pastikan **Allowed Domains** memuat:
  - `vexahostcloud.my.id`
  - `staging.vexahostcloud.my.id`
  - `dev.vexahostcloud.my.id`
  - `localhost` (untuk pengetesan lokal)

### 4. Akun Tertaut dengan VexaHost WA Gateway
Satu akun untuk dua aplikasi — **hanya autentikasi** (email, kata sandi, status verifikasi). Daftar atau ganti kata sandi di salah satunya ikut berlaku di yang lain. VPS, tagihan, organisasi, 2FA, dan peran admin tidak dibagi; akun admin tidak pernah diubah dari seberang.

```env
LINKED_ACCOUNTS_URL=https://wa.vexahostcloud.my.id   # staging: https://wa-staging.vexahostcloud.my.id, dev: https://wa-dev.vexahostcloud.my.id
LINKED_ACCOUNTS_SECRET=<isi-di-coolify>             # IDENTIK dengan milik WA Gateway, BERBEDA tiap tahap
```

- Salah satunya kosong = penautan mati di kedua arah (endpoint `POST /api/internal/akun-tertaut` menjawab 503).
- **Scheduler wajib jalan** — `akun-tertaut:kirim` tiap menit mengulang kiriman yang gagal saat WA Gateway sedang deploy.
- Setelah pertama kali dinyalakan, jalankan sekali di kedua aplikasi: `php artisan akun-tertaut:tautkan`.
- Protokol lengkapnya di `docs/AKUN_TERTAUT.md` milik repo `vexahost-wa`; kodenya harus sama persis di kedua sisi.

---

## ✅ Checklist Verifikasi Setelah Deploy

- [ ] Domain utama `https://vexahostcloud.my.id` dapat diakses dengan HTTPS valid (SSL gembok hijau).
- [ ] Tampilan halaman landing tampil sempurna dengan CSS Tailwind & aset JS (bukan plain HTML).
- [ ] Widget Cloudflare Turnstile muncul di halaman login (`/login`) dan dapat diverifikasi.
- [ ] Login admin menggunakan kredensial seeder (`vexahostcloudtech@gmail.com` / `12345678`) berhasil masuk ke dashboard admin (`/admin`).
- [ ] Tombol "Masuk dengan Google" membuka consent screen Google tanpa pesan `redirect_uri_mismatch`.
- [ ] Pengiriman email notifikasi via Brevo SMTP berfungsi normal.

---

## 🛠️ Panduan Troubleshooting Coolify

| Kendala | Penyebab Kemungkinan | Solusi |
| --- | --- | --- |
| **Tampilan Polos / CSS Tidak Muncul** | File aset Vite belum ter-build atau mixed content HTTP. | Pastikan `Build Command` diisi `npm run build`. Pastikan `APP_URL` menggunakan `https://` dan jalankan `php artisan optimize:clear`. |
| **Error 500: Database Connection Refused** | `DB_HOST` atau `DB_PASSWORD` salah di Coolify. | Pastikan host MySQL Coolify (IP internal container atau host) sudah benar dan nama database telah dibuat sebelumnya (Langkah 2). |
| **Turnstile Gagal Verifikasi / Invalid** | Domain belum masuk Allowed Domains Cloudflare. | Masukkan `vexahostcloud.my.id` ke daftar domain yang diizinkan di panel Cloudflare Turnstile. |
| **Login Google `redirect_uri_mismatch`** | Redirect URI di Google Cloud Console belum didaftarkan. | Tambahkan URL callback sesuai Langkah 8 di Google Cloud Console. |
| **File / Gambar Upload Hilang saat Redeploy** | Persistent storage belum di-mount. | Tambahkan persistent storage `/app/storage/app/public` di Coolify sesuai Langkah 5. |
