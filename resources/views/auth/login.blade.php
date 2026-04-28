<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Buku Tamu Digital SMKN 2 Jakarta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #0b1120;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        .bg-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        .bg-glow {
            position: fixed;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: float 8s ease-in-out infinite;
        }
        .bg-glow2 {
            position: fixed;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139,92,246,0.06) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            animation: float 10s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translate(0,0); }
            50% { transform: translate(30px, 20px); }
        }

        /* Card */
        .login-wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 420px;
            padding: 16px;
        }
        .login-card {
            background: rgba(17,24,39,0.95);
            border: 1px solid rgba(31,45,74,0.8);
            border-radius: 20px;
            padding: 40px 36px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: slideUp .4s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center; margin-bottom: 32px;
        }
        .login-logo .icon-wrap {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(59,130,246,0.3);
        }
        .login-logo h1 { font-size: 20px; font-weight: 700; color: #f1f5f9; }
        .login-logo p  { font-size: 13px; color: #64748b; margin-top: 4px; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 12.5px; font-weight: 600;
            color: #94a3b8; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #4b6074; font-size: 14px;
        }
        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid #1f2d4a;
            border-radius: 10px;
            padding: 11px 14px 11px 40px;
            color: #f1f5f9;
            font-size: 14px; font-family: 'Inter', sans-serif;
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .form-input::placeholder { color: #374151; }
        .error-msg {
            font-size: 12px; color: #fb7185;
            margin-top: 5px; display: flex; align-items: center; gap: 4px;
        }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 22px;
        }
        .remember-row input { accent-color: #3b82f6; width: 15px; height: 15px; }
        .remember-row label { font-size: 12.5px; color: #64748b; cursor: pointer; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none; border-radius: 10px;
            color: white; font-size: 14px; font-weight: 600;
            padding: 13px; cursor: pointer;
            transition: opacity .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        .login-footer {
            text-align: center; margin-top: 24px;
            font-size: 12px; color: #374151;
        }
        .login-footer span { color: #4b6074; }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-glow"></div>
    <div class="bg-glow2"></div>

    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">
                <div class="icon-wrap">🏫</div>
                <h1>Buku Tamu Digital</h1>
                <p>SMK Negeri 2 Jakarta</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            value="{{ old('email') }}"
                            placeholder="nama@smkn2jakarta.sch.id"
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <div class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                        >
                    </div>
                    @error('password')
                        <div class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                </button>
            </form>

            <div class="login-footer">
                <span>© {{ date('Y') }} SMKN 2 Jakarta — Sistem Buku Tamu Digital</span>
            </div>
        </div>
    </div>
</body>
</html>
