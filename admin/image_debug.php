<?php
include 'config.php';

function normalize_name($val) {
    $val = trim((string)$val);
    if ($val === '') return '';
    $val = str_replace('\\', '/', $val);
    $val = rawurldecode($val);
    if (strpos($val, '/') !== false) $val = basename($val);
    return $val;
}

function first_existing_image($filename) {
    $filename = normalize_name($filename);
    if ($filename === '') return ['fs' => null, 'rel' => null];

    $candidates = [
        ['fs' => __DIR__ . '/../ashion-master/img/shop/' . $filename,    'rel' => '../ashion-master/img/shop/' . rawurlencode($filename)],
        ['fs' => __DIR__ . '/../ashion-master/img/product/' . $filename, 'rel' => '../ashion-master/img/product/' . rawurlencode($filename)],
        ['fs' => __DIR__ . '/img/' . $filename,                          'rel' => 'img/' . rawurlencode($filename)],
    ];
    foreach ($candidates as $c) {
        if (file_exists($c['fs'])) return $c;
    }
    return ['fs' => null, 'rel' => $candidates[0]['rel']]; // default expected
}

$rows = mysqli_query($con, "SELECT p_id, p_name, p_image FROM product ORDER BY p_id DESC LIMIT 100");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Image Debug</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:16px;background:#fafafa}
        table{width:100%;border-collapse:collapse;background:#fff}
        th,td{border:1px solid #e5e7eb;padding:8px;vertical-align:top;font-size:13px}
        th{background:#f3f4f6;text-align:left}
        img{width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
        code{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:12px}
        .bad{color:#b91c1c;font-weight:600}
        .ok{color:#065f46;font-weight:600}
    </style>
</head>
<body>
    <h2 style="margin:0 0 12px">Product image debug (latest 100)</h2>
    <div style="margin:0 0 12px;color:#374151">
        This page tells whether `product.p_image` matches a real file in `ashion-master/img/shop/` (or old folders).
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:90px">Preview</th>
                <th style="width:60px">ID</th>
                <th>Name</th>
                <th style="width:260px">p_image (raw → normalized)</th>
                <th style="width:260px">Resolved</th>
            </tr>
        </thead>
        <tbody>
        <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)):
            $raw = $r['p_image'] ?? '';
            $norm = normalize_name($raw);
            $hit = first_existing_image($raw);
            $exists = $hit['fs'] ? true : false;
            $url = $hit['rel'] ?: '';
        ?>
            <tr>
                <td>
                    <?php if($norm !== ''): ?>
                        <img src="<?= htmlspecialchars($url) ?>" alt="" onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=bad>404</span>';">
                    <?php else: ?>
                        <span class="bad">empty</span>
                    <?php endif; ?>
                </td>
                <td><?= (int)$r['p_id'] ?></td>
                <td><?= htmlspecialchars($r['p_name'] ?? '') ?></td>
                <td>
                    <div><code><?= htmlspecialchars($raw) ?></code></div>
                    <div style="margin-top:6px;color:#6b7280"><code><?= htmlspecialchars($norm) ?></code></div>
                </td>
                <td>
                    <div class="<?= $exists ? 'ok' : 'bad' ?>"><?= $exists ? 'FOUND' : 'NOT FOUND' ?></div>
                    <div style="margin-top:6px"><code><?= htmlspecialchars($url) ?></code></div>
                    <?php if($hit['fs']): ?>
                        <div style="margin-top:6px;color:#6b7280"><code><?= htmlspecialchars($hit['fs']) ?></code></div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="5">No products.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

