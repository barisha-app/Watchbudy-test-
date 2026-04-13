<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$items = [];

foreach (HLS_STREAMS as $stream) {
    $items[] = [
        'id' => $stream['id'],
        'title' => $stream['title'],
        'thumbnail' => $stream['poster'] ?? '',
        'url' => $stream['url'],
        'type' => 'm3u8',
        'category' => $stream['category'] ?? 'live',
    ];
}

jsonResponse([
    'success' => true,
    'site' => SITE_NAME,
    'count' => count($items),
    'items' => $items,
]);
