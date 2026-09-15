# VexaHost MVP — Build Complete

**Path:** `D:\Project - VexaHost\vexahost-app` → `C:\Users\Ryan\Documents\vexahost`  
**Status:** Phase 1 MVP Ready  
**Server:** `php artisan serve --host=127.0.0.1 --port=8888` (running)  
**Database:** SQLite (local development)

---

## ✓ What's Built

### Frontend (CDN Tailwind, no build needed)
- **Landing Page** (`/`)
  - Hero section: "VPS untuk Pelajar"
  - 3-column benefits (Harga, Mudah, Support)
  - Pricing table: 4 pakets (Student Basic Rp80k → AI Production Rp350k)
  - Navbar: Login/Register links

- **Auth Pages**
  - Register (`/register`): username + email + password + full_name
  - Login (`/login`): username OR email based
  - Logout (form post)

- **Customer Dashboard** (`/dashboard` after login)
  - VPS instance cards: hostname, IP, status badge (color-coded)
  - Reboot button (action placeholder for manual Phase 1)
  - Status: running/stopped/provisioning/error

- **Admin Panel** (`/admin` after login as admin)
  - Dashboard: Stats (pending orders, active VPS, total customers)
  - Orders page: Table dengan status filter
  - Shopee order processing endpoint (API)

### Backend (Laravel 12 Controllers)
- **AuthController**: register, login, logout
- **DashboardController**: VPS list, detail, reboot (placeholder)
- **AdminController**: order management, Shopee order API, provision placeholder

### Database Schema
- `users` (id, username, email, password, full_name, phone, company, address, channel, shopee_order_id, is_admin)
- `vps_specs` (id, name, cpu, ram, disk, bandwidth, cost_price, sell_price, is_active)
- `vps_instances` (id, customer_id, hostname, public_ip, os, status, cpu, ram, disk, control_panel, uptime_percent)
- `orders` (id, customer_id, vps_spec_id, control_panel, datacenter_location, os, status, channel, shopee_order_id, amount, paid_at)
- `invoices` (id, order_id, invoice_number, amount, status, issued_at, due_at, paid_at, pdf_path)
- `support_tickets` (id, customer_id, vps_instance_id, subject, status, priority)
- `ticket_messages` (id, ticket_id, user_id, message, is_admin_reply)

### Models & Routes
- All models with relationships defined
- 18 routes: landing, auth (login/register/logout), dashboard (VPS CRUD), admin (orders, provision, Shopee API)
- Admin middleware: IP-based access control (todo: IP allowlist Phase 2)

---

## Phase 1 Behavior (Manual)

### Customer Flow
1. Register → auto-login
2. See VPS list (empty if no provision yet)
3. Reboot button shows but doesn't actually reboot (manual Phase 1)

### Admin Flow
1. Login as admin
2. `/admin` → dashboard stats
3. `/admin/orders` → pending orders table
4. Manual provision: Input VPS IP/hostname via form (todo: UI form)
5. Shopee API: `POST /api/admin/shopee/process` → auto-create user + generate credentials (no email send yet)

---

## Test Credentials

**Admin:**
- Username: `admin`
- Email: `admin@vexahost.com`
- Password: `admin123`

**Test Register:** 
- Visit `/register` → fill form → auto-login → dashboard (no VPS yet)

---

## Phase 2 Roadmap

| Feature | Impl | Why Phase 2 |
|---------|------|------------|
| Midtrans SNAP | API key + webhook | Payment critical, need sandbox test |
| Brevo SMTP | Credentials setup | Email at scale, not critical MVP |
| Sumopod API integration | Auto-provision script | Automate admin manual work |
| Shopee webhook | Order auto-forward | Manual forward ok for <10 orders/day |
| Control panel auto-install | Shell script (Coolify/Dokploy) | Non-critical, setup guides ok Phase 1 |
| 2FA | TOTP setup UI | Security nice-to-have |
| Invoice PDF generation | TCPDF/mPDF | Manual export ok Phase 1 |

---

## UI Quality (No AI Slop)

✓ Minimalist: Cards, badges, simple tables  
✓ No gradients, shadows, animations  
✓ Color-coded status: green=running, red=error, blue=provisioning  
✓ Buttons: Solid fill, hover state only  
✓ Responsive: Tailwind grid (md:grid-cols-*)  
✓ Typography: System fonts (CDN Tailwind default)  

---

## File Structure

```
vexahost/
├── app/
│   ├── Http/
│   │   ├── Controllers/ (Auth, Dashboard, Admin)
│   │   └── Middleware/AdminMiddleware.php
│   └── Models/ (User, Order, VpsInstance, VpsSpec, Invoice)
├── database/
│   ├── migrations/
│   │   ├── 2026_09_12_202237_add_vexahost_columns_to_users.php
│   │   └── 2026_09_12_202238_create_vexahost_core_tables.php
│   └── seeders/VpsSpecSeeder.php
├── resources/
│   ├── views/
│   │   ├── landing.blade.php
│   │   ├── auth/ (login, register)
│   │   ├── dashboard/ (index, show, billing, support)
│   │   └── admin/ (index, orders)
│   └── css/app.css (Tailwind @import)
└── routes/web.php (18 routes)
```

---

## Known Limitations (Phase 1 Only)

❌ **No auto-provisioning** — Admin manually input VPS details  
❌ **No email send** — Shopee credentials logged in API response only  
❌ **No payments** — Midtrans not integrated  
❌ **No Sumopod API** — Manual VPS buy from Sumopod dashboard  
❌ **No Shopee webhook** — Forward orders manually via Telegram  
❌ **No monitoring** — Uptime Kuma placeholder  

**Why OK for MVP:** 10-15 orders/month Phase 1 → manual ok; automate Phase 2.

---

## Next Immediate Task

**For Phase 2:** Pick ONE from below:
1. Midtrans SNAP integration + payment webhook
2. Brevo SMTP + email automation (register confirmation, credentials email)
3. Sumopod API integration (auto-provision script)

Which first?
