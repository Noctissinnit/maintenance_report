<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Laporan Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #6c8cff;
            --primary-dark: #1e40af;
            --secondary-color: #7b9fff;
            --accent-color: #ff9f1c;
            --text-dark: #2c3e50;
            --text-light: #ecf0f1;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            transition: var(--transition);
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 50%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            color: var(--text-dark);
        }

        .login-wrapper {
            width: 100%;
            padding: 2rem;
        }

        .login-container {
            background: var(--bg-white);
            border-radius: 0.875rem;
            box-shadow: var(--shadow-lg);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 0.75rem;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
            color: white;
            box-shadow: var(--shadow-md);
        }

        .login-header h1 {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #999;
            font-size: 0.95rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid #e8ecf1;
            border-radius: 0.625rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            background: #f8f9fa;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control::placeholder {
            color: #bbb;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: var(--bg-white);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.875rem 1rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 0.625rem;
            color: white;
            width: 100%;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s;
        }

        .btn-login:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            border: 2px solid #e8ecf1;
            border-radius: 0.3rem;
            cursor: pointer;
            margin-top: 0.2rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .form-check-label {
            color: var(--text-dark);
            font-weight: 500;
            cursor: pointer;
            font-size: 0.95rem;
            margin-left: 0.5rem;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.4rem;
            font-weight: 500;
        }

        .alert {
            border: none;
            border-radius: 0.625rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            font-size: 0.9rem;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-info {
            background: rgba(67, 97, 238, 0.1);
            border-left: 4px solid var(--primary-color);
            color: #004085;
        }

        .alert-close {
            opacity: 0.5;
        }

        .alert-close:hover {
            opacity: 0.75;
        }

        .demo-credentials {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(124, 179, 255, 0.05));
            border-left: 4px solid var(--primary-color);
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 0.85rem;
            margin-top: 1.5rem;
            line-height: 1.6;
        }

        .demo-credentials strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .demo-credentials code {
            background: rgba(0, 0, 0, 0.05);
            padding: 0.2rem 0.5rem;
            border-radius: 0.3rem;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.8rem;
        }

        .demo-credentials ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.25rem;
            list-style: none;
        }

        .demo-credentials li {
            margin-bottom: 0.4rem;
        }

        .demo-credentials li::before {
            content: '→ ';
            color: var(--primary-color);
            font-weight: 700;
            margin-right: 0.5rem;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-header-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="login-header-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <h1>Maintenance</h1>
                <p>Sistem Laporan Operasional & Maintenance</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <strong>Login Gagal!</strong>
                    @foreach($errors->all() as $error)
                        <div style="margin-top: 0.5rem;">{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close alert-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close alert-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="Masukkan email Anda"
                        required 
                        autofocus>
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan password Anda"
                        required>
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="remember" 
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

            {{-- <div class="demo-credentials">
                <strong><i class="bi bi-info-circle"></i> Akun Demo:</strong>
                <ul>
                    <li><code>admin@maintenance.com</code></li>
                    <li><code>departmenthead@maintenance.com</code></li>
                    <li><code>operator1@maintenance.com</code></li>
                </ul>
                <strong style="margin-top: 0.75rem;">Password: <code>password123</code></strong>
            </div> --}}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
