<?php

/**
 * PHASE 2 - Identitas versi naskah legal.
 *
 * Versi ini disimpan bersama setiap persetujuan pelanggan di tabel
 * terms_acceptances. Tanpa versi, kita tidak dapat membuktikan naskah
 * mana yang disetujui pelanggan ketika isinya berubah di kemudian hari.
 *
 * NAIKKAN versi setiap kali isi Pasal 4 (Ketentuan Penggunaan) atau
 * klausul tanggung jawab diubah secara substansial.
 */
return [

    'terms_version' => env('TERMS_VERSION', 'TOS-2026-V3'),

    'terms_effective_date' => env('TERMS_EFFECTIVE_DATE', '2026-09-18'),

    /*
     * Larangan yang wajib selaras dengan Acceptable Use Policy Supplier.
     * Empat butir terakhir ditambahkan pada V3 karena sebelumnya tidak
     * tercantum, sehingga pelanggan secara kontraktual boleh melakukannya
     * padahal tindakan tersebut melanggar aturan supplier.
     */
    'prohibited_additions' => [
        'vpn_proxy',
        'scraping',
        'torrent',
        'automated_bots',
    ],

];
