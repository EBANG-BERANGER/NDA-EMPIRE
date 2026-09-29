<?php

return [
    'name' => 'NDA EMPIRE',
    'signature' => 'by Niomba',
    'city' => 'Kigali',
    'currency' => 'RWF',
    'phone' => '+250 791 700 902',
    'whatsapp' => '250791700902',
    'instagram' => env('SALON_INSTAGRAM'),
    'tiktok' => env('SALON_TIKTOK'),

    // Opening hours per ISO weekday (1 = Monday ... 7 = Sunday). null = closed.
    'hours' => [
        1 => ['09:00', '19:00'],
        2 => ['09:00', '19:00'],
        3 => ['09:00', '19:00'],
        4 => ['09:00', '19:00'],
        5 => ['09:00', '19:00'],
        6 => ['09:00', '19:00'],
        7 => null,
    ],
    'slot_minutes' => 30,
    'booking_days_ahead' => 60,

    // AI try-on (Google Gemini image model).
    'gemini_key' => env('GEMINI_API_KEY'),
    'gemini_model' => env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image'),
    'tryons_per_day' => (int) env('TRYONS_PER_DAY', 8),
];
