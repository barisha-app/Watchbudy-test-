<?php
$url = $_GET['url'] ?? '';
$title = $_GET['title'] ?? 'Yayın';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body {
            margin: 0;
            background: #111;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .wrap {
            max-width: 960px;
            margin: 30px auto;
            padding: 16px;
        }
        video {
            width: 100%;
            background: #000;
            border-radius: 12px;
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1><?= htmlspecialchars($title) ?></h1>
    <video id="video" controls autoplay></video>
</div>

<script>
const video = document.getElementById('video');
const src = <?= json_encode($url) ?>;

if (Hls.isSupported()) {
    const hls = new Hls();
    hls.loadSource(src);
    hls.attachMedia(video);
} else if (video.canPlayType('application/vnd.apple.mpegurl')) {
    video.src = src;
}
</script>
</body>
</html>
