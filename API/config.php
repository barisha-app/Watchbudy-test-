<?php
declare(strict_types=1);

const SITE_NAME = 'Barisha Media';

const YOUTUBE_CHANNELS = [
    'https://youtube.com/@sinemood',
    'https://youtube.com/@patlamismisirofficial',
    'https://youtube.com/@sinekeyif',
    'https://youtube.com/@filmadresi',
    'https://youtube.com/@turkfilmleri-tv',
    'https://youtube.com/@taff-pictures',
    'https://youtube.com/@mostproductionofficial',
];

/*
 | BURAYA SADECE SANA AIT VEYA KULLANIM IZNI OLAN M3U8 YAYINLARINI KOY
 | Örnek:
 | 'url' => 'https://seninsite.com/live/yayin1.m3u8'
*/
const HLS_STREAMS = [
    [
        'id' => 'live_1',
        'title' => 'Canlı Yayın 1',
        'url' => 'https://example.com/path/to/your-stream.m3u8',
        'poster' => 'https://via.placeholder.com/640x360?text=Canli+Yayin+1',
        'type' => 'm3u8',
        'category' => 'live'
    ],
];
