<?php
declare(strict_types=1);

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
    );
    exit;
}

function httpGet(string $url): string
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept-Language: tr-TR,tr;q=0.9,en;q=0.8',
        ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('HTTP hata: ' . $error);
    }

    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status >= 400) {
        throw new RuntimeException('HTTP durum kodu: ' . $status);
    }

    return $response;
}

function normalizeYouTubeUrl(string $url): string
{
    $url = trim($url);
    $url = preg_replace('/\?.*$/', '', $url);
    return rtrim($url, '/');
}

function extractHandleFromUrl(string $url): ?string
{
    $path = parse_url($url, PHP_URL_PATH);

    if (!$path) {
        return null;
    }

    $parts = explode('/', trim($path, '/'));
    if (!isset($parts[0])) {
        return null;
    }

    if (str_starts_with($parts[0], '@')) {
        return $parts[0];
    }

    return null;
}

function extractYtInitialData(string $html): ?array
{
    if (!preg_match('/var ytInitialData = (.*?);<\/script>/s', $html, $matches)) {
        if (!preg_match('/ytInitialData"\]\s*=\s*(\{.*?\});/s', $html, $matches)) {
            return null;
        }
    }

    $json = trim($matches[1]);
    $data = json_decode($json, true);

    return is_array($data) ? $data : null;
}

function findVideosRecursive(mixed $node, array &$results): void
{
    if (is_array($node)) {
        if (isset($node['videoRenderer']) && is_array($node['videoRenderer'])) {
            $video = $node['videoRenderer'];

            $videoId = $video['videoId'] ?? null;
            $title = $video['title']['runs'][0]['text'] ?? null;
            $thumb = $video['thumbnail']['thumbnails'] ?? [];
            $published = $video['publishedTimeText']['simpleText'] ?? '';
            $length = $video['lengthText']['simpleText'] ?? '';

            if ($videoId && $title) {
                $results[] = [
                    'id' => 'yt_' . $videoId,
                    'source_id' => $videoId,
                    'title' => $title,
                    'thumbnail' => end($thumb)['url'] ?? '',
                    'url' => 'https://www.youtube.com/watch?v=' . $videoId,
                    'embed' => 'https://www.youtube.com/embed/' . $videoId,
                    'published_text' => $published,
                    'duration' => $length,
                    'type' => 'youtube',
                ];
            }
        }

        foreach ($node as $child) {
            findVideosRecursive($child, $results);
        }
    }
}

function fetchChannelVideos(string $channelUrl, int $limit = 12): array
{
    $channelUrl = normalizeYouTubeUrl($channelUrl);
    $handle = extractHandleFromUrl($channelUrl);

    if (!$handle) {
        return [];
    }

    $videosUrl = 'https://www.youtube.com/' . $handle . '/videos';

    try {
        $html = httpGet($videosUrl);
        $data = extractYtInitialData($html);

        if (!$data) {
            return [];
        }

        $videos = [];
        findVideosRecursive($data, $videos);

        $seen = [];
        $clean = [];

        foreach ($videos as $video) {
            if (isset($seen[$video['source_id']])) {
                continue;
            }

            $seen[$video['source_id']] = true;
            $video['channel_handle'] = $handle;
            $video['channel_url'] = 'https://www.youtube.com/' . $handle;

            $clean[] = $video;

            if (count($clean) >= $limit) {
                break;
            }
        }

        return $clean;
    } catch (Throwable $e) {
        return [];
    }
}
