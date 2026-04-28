<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Aplikasi — Buku Tamu Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #0b1120;
            --card: #111827;
            --accent: #3b82f6;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #1f2d4a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
            background-image: radial-gradient(circle at 50% 50%, #1e293b 0%, #0b1120 100%);
        }
        .install-card {
            width: 100%; max-width: 440px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 16px;
            margin: 0 auto 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        p { color: var(--text-muted); font-size: 14px; margin-bottom: 32px; line-height: 1.5; }
        
        .form-group { text-align: left; margin-bottom: 24px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); }
        input {
            width: 100%;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: white;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }
        input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        
        .btn {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px; font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn:hover { background: #2563eb; transform: translateY(-1px); }
        
        .alert {
            padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;
            text-align: left; display: flex; align-items: flex-start; gap: 10px;
        }
        .alert-error { background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #fb7185; }

        .footer-note { margin-top: 32px; font-size: 12px; color: var(--text-muted); }
        .footer-note a { color: var(--accent); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="install-card">
    <div class="logo-icon">🔑</div>
    <h1>Aktivasi Aplikasi</h1>
    <p>Silakan masukkan Secret Key untuk mengaktifkan Buku Tamu Digital SMKN 2 Jakarta di server Anda.</p>

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('install.activate') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Secret Key / License Key</label>
            <input type="text" name="license_key" placeholder="BUKUTAMU-SMKN2-xxxx-xxxx" required autocomplete="off">
            @error('license_key')
                <div style="color: #fb7185; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">
            Aktifkan Sekarang <i class="fas fa-bolt"></i>
        </button>
    </form>

    <div class="footer-note">
        Belum punya key? <br>
        Hubungi pengembang: <a href="https://wa.me/6281234567890" target="_blank">Rio Widyatmoko</a>
    </div>
</div>

</body>
</html>
