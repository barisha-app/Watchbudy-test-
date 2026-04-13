<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$items = [];

foreach (YOUTUBE_CHANNELS as $channelUrl) {
    $videos = fetchChannelVideos($channelUrl, 8);
    $items = array_merge($items, $videos);
}

jsonResponse([
    'success' => true,
    'site' => SITE_NAME,
    'count' => count($items),
    'items' => $items,
]);
