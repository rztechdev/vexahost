<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VexaHost Business Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi bisnis VexaHost. Ubah nilai-nilai ini sesuai dengan
    | data bisnis real Anda sebelum go-live ke production.
    |
    */

    // Nomor WhatsApp customer service (format internasional tanpa +)
    // Ganti dengan nomor WhatsApp Business real Anda
    'whatsapp' => env('VEXAHOST_WHATSAPP', '6285774410978'),

    // Email support
    'support_email' => env('VEXAHOST_SUPPORT_EMAIL', 'support@vexahostcloud.my.id'),

    // Email admin/billing
    'admin_email' => env('VEXAHOST_ADMIN_EMAIL', 'admin@vexahostcloud.my.id'),

    // SLA Uptime guarantee (%)
    'sla_uptime' => 99.9,

    // Secret API Key untuk mengamankan endpoint /api/admin/*
    'admin_api_key' => env('ADMIN_API_KEY'),
];
