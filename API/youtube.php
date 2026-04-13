<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$allVideos = [];

foreach (YOUTUBE_CHANNELS as $channelUrl) {
    $videos = fetchChannelVideos($channelUrl, 10);
    $allVideos = array_merge($allVideos, $videos);
}

jsonResponse([
    'success' => true,
    'site' => SITE_NAME,
    'count' => count($allVideos),
    'items' => $allVideos,
]);
