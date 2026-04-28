<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif;background:#0b1120;color:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0; }
        .box { text-align:center;padding:40px; }
        .code { font-size:80px;font-weight:800;background:linear-gradient(135deg,#3b82f6,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent; }
        h1 { font-size:22px;margin:8px 0 12px; }
        p { color:#64748b;margin-bottom:28px; }
        a { background:#3b82f6;color:white;padding:10px 22px;border-radius:8px;font-weight:600;text-decoration:none; }
    </style>
</head>
<body>
    <div class="box">
        <div class="code">403</div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url()->previous() }}">← Kembali</a>
    </div>
</body>
</html>
