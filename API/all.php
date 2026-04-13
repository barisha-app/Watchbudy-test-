<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

function getLocalJson(string $path): array
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'haberakedemi.com.tr';
    $url = $scheme . '://' . $host . $path;

    try {
        $json = httpGet($url);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    } catch (Throwable $e) {
        return [];
    }
}

$youtube = getLocalJson('/api/youtube.php');
$streams = getLocalJson('/api/streams.php');

$items = array_merge(
    $youtube['items'] ?? [],
    $streams['items'] ?? []
);

jsonResponse([
    'success' => true,
    'site' => SITE_NAME,
    'count' => count($items),
    'items' => $items,
]);
