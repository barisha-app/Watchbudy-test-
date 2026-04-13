<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Akadememi</title>
    <style>
        body{margin:0;background:#111;color:#fff;font-family:Arial,sans-serif}
        .container{max-width:1200px;margin:0 auto;padding:24px}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px}
        .card{background:#1c1c1c;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.25)}
        .card img{width:100%;height:160px;object-fit:cover;display:block}
        .content{padding:14px}
        .type{display:inline-block;background:#333;border-radius:999px;padding:4px 8px;font-size:12px;margin-bottom:10px}
        .title{font-size:16px;line-height:1.4;min-height:46px}
        .meta{font-size:13px;color:#bbb;margin-top:8px}
        .btn{display:inline-block;margin-top:12px;padding:10px 14px;background:#2563eb;color:#fff;text-decoration:none;border-radius:10px}
    </style>
</head>
<body>
<div class="container">
    <h1>Haber Akadememi</h1>
    <div id="grid" class="grid"></div>
</div>

<script>
async function loadData() {
    const res = await fetch('/api/all.php');
    const data = await res.json();
    const grid = document.getElementById('grid');

    if (!data.items || !data.items.length) {
        grid.innerHTML = '<p>İçerik bulunamadı.</p>';
        return;
    }

    grid.innerHTML = data.items.map(item => {
        const image = item.thumbnail || 'https://via.placeholder.com/640x360?text=No+Image';

        let button = '';
        if (item.type === 'youtube') {
            button = `<a class="btn" href="${item.url}" target="_blank" rel="noopener noreferrer">YouTube'da Aç</a>`;
        } else if (item.type === 'm3u8') {
            const title = encodeURIComponent(item.title || 'Yayın');
            const url = encodeURIComponent(item.url || '');
            button = `<a class="btn" href="/player.php?title=${title}&url=${url}" target="_blank" rel="noopener noreferrer">Yayını Aç</a>`;
        }

        return `
            <div class="card">
                <img src="${image}" alt="">
                <div class="content">
                    <div class="type">${item.type}</div>
                    <div class="title">${item.title || 'Başlıksız'}</div>
                    <div class="meta">${item.published_text || item.category || ''}</div>
                    ${button}
                </div>
            </div>
        `;
    }).join('');
}

loadData().catch(() => {
    document.getElementById('grid').innerHTML = '<p>Veri alınamadı.</p>';
});
</script>
</body>
</html>
