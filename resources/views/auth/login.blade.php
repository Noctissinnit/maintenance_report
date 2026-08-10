<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Laporan Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-rgb: 67, 97, 238;
            --primary-dark: #304ffe;
            --primary-light: #4895ef;
            --secondary: #7209b7;
            --dark-blue: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: var(--transition);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .brand-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* Split Screen Container */
        .login-page {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* 1. Left Side: Hero Section */
        .hero-section {
            flex: 1.2;
            background-color: var(--dark-blue);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem;
            overflow: hidden;
            z-index: 1;
        }

        /* Glowing Mesh Background Blobs */
        .hero-gradient-blobs {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.45;
            animation: moveBlobs 20s infinite alternate;
        }

        .blob-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(67, 97, 238, 0.8) 0%, rgba(67, 97, 238, 0) 70%);
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(114, 9, 183, 0.7) 0%, rgba(114, 9, 183, 0) 70%);
            bottom: -150px;
            right: -100px;
            animation-duration: 25s;
        }

        .blob-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(72, 149, 239, 0.6) 0%, rgba(72, 149, 239, 0) 70%);
            top: 40%;
            left: 30%;
            animation-duration: 18s;
        }

        @keyframes moveBlobs {
            0% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(30px, -50px) scale(1.1);
            }
            100% {
                transform: translate(-20px, 30px) scale(0.9);
            }
        }

        /* Branding */
        .app-branding {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo-img {
            height: 54px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.25));
        }

        .brand-name {
            font-size: 1.6rem;
            color: white;
            letter-spacing: -0.5px;
            display: block;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.55);
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Hero Middle Content */
        .hero-content {
            margin: auto 0;
            max-width: 540px;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero-text h2 {
            font-size: 2.3rem;
            line-height: 1.3;
            color: white;
            margin-top: 2rem;
            margin-bottom: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .hero-text p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        /* Glassmorphic Stats Panel */
        .glass-stats-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-item .text-primary .stat-icon-wrapper {
            color: var(--primary-light) !important;
        }

        .stat-item .text-success .stat-icon-wrapper {
            color: #4ade80 !important;
        }

        .stat-val {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            line-height: 1.2;
        }

        .stat-lbl {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
        }

        .stat-divider {
            width: 1px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
        }

        .hero-footer {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }

        /* 2. Right Side: Login Form Section */
        .form-section {
            flex: 1;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 3rem;
            position: relative;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Mobile Branding Header */
        .mobile-header {
            display: none;
            margin-bottom: 2.5rem;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 1.85rem;
            color: var(--dark-blue);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Form Styling */
        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-field-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            pointer-events: none;
            z-index: 5;
        }

        .form-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            font-size: 0.95rem;
            background-color: var(--slate-100);
            border: 2px solid transparent;
            border-radius: var(--border-radius);
            color: var(--text-dark);
            font-weight: 500;
            outline: none;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            background-color: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        /* Toggle Password Eye */
        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.2rem;
            font-size: 1.1rem;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        /* Error/Invalid Feedback */
        .feedback-invalid {
            color: #ef4444;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Remember Me & Options */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--slate-700);
            user-select: none;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid var(--slate-200);
            border-radius: 6px;
            background-color: var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        input[type="checkbox"] {
            display: none;
        }

        input[type="checkbox"]:checked + .checkbox-custom {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        input[type="checkbox"]:checked + .checkbox-custom::after {
            content: "\F26E";
            font-family: "bootstrap-icons";
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* Login Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            padding: 0.95rem;
            border-radius: var(--border-radius);
            border: none;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.25);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-submit:hover {
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.35);
            transform: translateY(-2px);
        }

        .btn-submit:hover::after {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert styling override */
        .alert-custom {
            border: none;
            border-left: 4px solid #ef4444;
            background-color: #fef2f2;
            color: #991b1b;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        /* Keyframes Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.03);
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .hero-section {
                display: none;
            }

            .form-section {
                flex: 1;
                padding: 3rem 1.5rem;
                background-color: #f8fafc;
            }

            .form-wrapper {
                background-color: white;
                padding: 2.5rem 2rem;
                border-radius: 16px;
                box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
            }

            .mobile-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .mobile-header .brand-logo-img {
                margin-bottom: 1rem;
            }

            .mobile-header .brand-name {
                color: var(--dark-blue);
            }
        }

        @media (max-width: 480px) {
            .form-section {
                padding: 1rem;
            }

            .form-wrapper {
                padding: 2rem 1.25rem;
            }

            .form-header h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- Sisi Kiri: Hero Banner (Desktop Only) -->
        <div class="hero-section">
            <div class="hero-gradient-blobs">
                <div class="blob blob-1"></div>
                <div class="blob blob-2"></div>
                <div class="blob blob-3"></div>
            </div>
            
            <div class="app-branding">
                <img src="{{ asset('images/ahs.png') }}" alt="Logo AHS" class="brand-logo-img">
                <div>
                    <span class="brand-name">AHS</span>
                    <span class="brand-sub">Maintenance Hub</span>
                </div>
            </div>
            
            <div class="hero-content">
                <div class="hero-text">
                    <h2>Optimalkan Kinerja Mesin & Efisiensi Operasional</h2>
                    <p>Sistem pencatatan laporan harian maintenance, analisis keandalan mesin (MTBF), serta kolaborasi tim operator & supervisor yang terpadu.</p>
                </div>
                
                <div class="glass-stats-card">
                    <div class="stat-item text-primary">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div>
                            <div class="stat-val">Real-Time</div>
                            <div class="stat-lbl">Monitoring & Log</div>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item text-success">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="stat-val">MTBF & MTTR</div>
                            <div class="stat-lbl">Analytics Platform</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="hero-footer">
                <p>&copy; 2026 AHS Maintenance System. All rights reserved.</p>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="form-section">
            <div class="form-wrapper">
                <!-- Branding Header untuk Tampilan Mobile -->
                <div class="mobile-header">
                    <img src="{{ asset('images/ahs.png') }}" alt="Logo AHS" class="brand-logo-img">
                    <div>
                        <span class="brand-name">AHS</span>
                        <span class="brand-sub">Maintenance Hub</span>
                    </div>
                </div>

                <div class="form-header">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk menggunakan akun terdaftar Anda.</p>
                </div>

                <!-- Alert Validation Errors -->
                @if($errors->any())
                    <div class="alert-custom" role="alert">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Login Gagal!</strong>
                        </div>
                        @foreach($errors->all() as $error)
                            <div style="font-size: 0.85rem; padding-left: 1.3rem;">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-custom" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" novalidate id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="input-group-custom">
                        <label for="email" class="input-label">Alamat Email</label>
                        <div class="input-field-wrapper">
                            <span class="input-icon"><i class="bi bi-envelope"></i></span>
                            <input 
                                type="email" 
                                class="form-input @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="nama@perusahaan.com"
                                required 
                                autofocus>
                        </div>
                        @error('email')
                            <div class="feedback-invalid">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="input-group-custom">
                        <label for="password" class="input-label">Kata Sandi</label>
                        <div class="input-field-wrapper">
                            <span class="input-icon"><i class="bi bi-lock"></i></span>
                            <input 
                                type="password" 
                                class="form-input @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••"
                                required>
                            <button type="button" class="toggle-password" id="togglePasswordBtn" title="Tampilkan sandi">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="feedback-invalid">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Options -->
                    <div class="form-options">
                        <label class="checkbox-container" for="remember">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <div class="checkbox-custom"></div>
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <span>Masuk ke Dashboard</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.25rem;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility Logic
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                this.setAttribute('title', 'Sembunyikan sandi');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                this.setAttribute('title', 'Tampilkan sandi');
            }
        });
    </script>
</body>
</html>
