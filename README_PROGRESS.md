# VexaHost - VPS Reseller Platform (MVP)

## Status: Phase 1 MVP Complete

**Path:** `C:\Users\Ryan\Documents\vexahost`

**Server:** http://127.0.0.1:8888

---

## ✓ Completed

### Database
- Schema: users, vps_specs, vps_instances, orders, invoices, support_tickets
- Migration: ✓ Run
- Seeder: VPS specs (Student Basic, Standard, Premium, AI Production)

### Auth System
- Register (website channel, username self-picked)
- Login (username OR email)
- Logout
- Admin middleware

### Landing Page
- Hero: "VPS untuk Pelajar"
- Why Us: 3 benefits (Harga, Mudah, Support Lokal)
- Pricing: 4 paket dengan spec
- Footer: Contact info
- Tailwind CSS via CDN

### Dashboard (Customer)
- VPS list: Cards dengan status badge (running/stopped/provisioning/error)
- VPS detail view
- Reboot action (placeholder untuk manual Phase 1)
- Billing placeholder
- Support placeholder

### Admin Panel
- Dashboard: Stats (pending orders, active VPS, total customers)
- Orders list: Table dengan filter status
- Shopee order processing API endpoint (`/api/admin/shopee/process`):
  - Auto-generate username (`vx_xxxxxxxx`)
  - Auto-generate password
  - Create user account
  - Send credentials (manual email Phase 1)
- Provision endpoint (manual input IP, hostname)

### Models & Relations
- User (hasMany VpsInstance, Order)
- Order (belongsTo User, VpsSpec)
- VpsInstance (belongsTo User)
- VpsSpec, Invoice

---

## Phase 1 Limitations (Manual)

- **Provisioning:** Admin input VPS details manually dari Sumopod
- **Email:** Credentials dikirim manual (Brevo belum integrated)
- **Payment:** Midtrans belum integrated (manual confirm)
- **Shopee:** Order forward manual via Telegram, process lewat admin panel

---

## Next Steps (Phase 2)

1. Midtrans SNAP integration
2. Brevo SMTP setup (email automation)
3. Sumopod API integration (auto-provision)
4. Shopee API webhook
5. Control panel auto-install script (Coolify/Dokploy)

---

## Test Flow

### Register & Login
1. http://127.0.0.1:8888/register
2. Fill form → auto-login
3. Dashboard shows "Belum ada VPS"

### Admin Flow
1. Create admin user: `php artisan tinker` → `User::create(['username'=>'admin','email'=>'admin@vexahost.com','password'=>bcrypt('password'),'full_name'=>'Admin','channel'=>'website','is_admin'=>true])`
2. Login as admin → http://127.0.0.1:8888/admin
3. View orders, process Shopee orders

---

## UI Notes (Non-Slop)

- Status badges: Color-coded (green=running, blue=provisioning, red=error)
- Cards: Border outline, no shadows
- Buttons: Solid fill, no gradients
- Tables: Simple borders, hover:bg-gray-50
- No animations, no skeletons, no spinners (Phase 1)
- CDN Tailwind → Production: build dengan Vite

---

**Build Time:** ~30 min  
**Lines of Code:** ~500 (controllers + views + migrations)  
**Dependencies:** Laravel 12, Tailwind CDN, SQLite
