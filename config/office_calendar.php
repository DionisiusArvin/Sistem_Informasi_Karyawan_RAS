<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Office Holidays (Specific Date)
    |--------------------------------------------------------------------------
    | Format: YYYY-MM-DD
    | Isi dengan hari libur kantor sesuai keputusan internal/per tahun.
    */
    'holidays' => [
        // Contoh:
        // '2026-03-19', // Nyepi (contoh)
        // '2026-03-20', // Cuti Bersama (contoh)
    ],

    /*
    |--------------------------------------------------------------------------
    | Recurring Holidays (Fixed Date Every Year)
    |--------------------------------------------------------------------------
    | Format: MM-DD
    | Umumnya hari libur nasional yang tanggalnya tetap tiap tahun.
    */
    'recurring_holidays' => [
        '01-01', // Tahun Baru Masehi
        '05-01', // Hari Buruh Internasional
        '06-01', // Hari Lahir Pancasila
        '08-17', // Hari Kemerdekaan RI
        '12-25', // Hari Raya Natal
    ],
];
