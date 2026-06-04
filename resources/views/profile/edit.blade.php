@extends('layouts.app')

@section('title', 'Edit Profil - Sistem Laporan Maintenance')

@section('extra-css')
<style>
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 1rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .profile-avatar-lg {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
    }

    .profile-header-info {
        position: relative;
        z-index: 1;
    }

    .profile-header-info h2 {
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
    }

    .profile-header-info .role-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 0.3rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .profile-card {
        border: none;
        border-radius: 1rem;
        box-shadow: var(--shadow-sm);
        background: white;
        overflow: hidden;
        border-top: none;
    }

    .profile-card:hover {
        box-shadow: var(--shadow-md);
    }

    .profile-card .card-header {
        background: white;
        color: var(--text-dark);
        padding: 1.5rem 1.75rem;
        border-bottom: 2px solid #f0f2f7;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .profile-card .card-header i {
        font-size: 1.3rem;
        color: var(--primary-color);
    }

    .profile-card .card-header h5 {
        font-weight: 700;
        margin: 0;
        font-size: 1.05rem;
    }

    .profile-card .card-body {
        padding: 2rem 1.75rem;
    }

    .form-floating-custom {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-floating-custom label {
        font-weight: 600;
        color: #555;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .form-floating-custom label i {
        color: var(--primary-color);
        font-size: 0.95rem;
    }

    .form-floating-custom .form-control {
        border: 2px solid #e8ecf1;
        border-radius: 0.625rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }

    .form-floating-custom .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12);
    }

    .form-floating-custom .form-control.is-invalid {
        border-color: #e74c3c;
    }

    .form-floating-custom .form-control[readonly] {
        background-color: #f8f9fb;
        color: #888;
    }

    .form-hint {
        font-size: 0.8rem;
        color: #999;
        margin-top: 0.35rem;
    }

    .password-section {
        background: #f8f9fb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 0.5rem;
        border: 1px dashed #e0e0e0;
    }

    .password-section-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-save-profile {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .btn-save-profile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        color: white;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    }

    .btn-cancel-profile {
        background: white;
        border: 2px solid #e0e0e0;
        color: #666;
        padding: 0.75rem 1.5rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-cancel-profile:hover {
        background: #f5f5f5;
        border-color: #ccc;
        color: #444;
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #aaa;
        cursor: pointer;
        padding: 0.25rem;
        transition: color 0.2s;
    }

    .password-toggle-btn:hover {
        color: var(--primary-color);
    }

    .input-with-toggle {
        position: relative;
    }

    .input-with-toggle .form-control {
        padding-right: 2.5rem;
    }

    .profile-success-alert {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #6ee7b7;
        color: #065f46;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideDown 0.4s ease-out;
        margin-bottom: 1.5rem;
    }

    .profile-success-alert i {
        font-size: 1.3rem;
        color: #10b981;
    }
</style>
@endsection

@section('content')
<div class="container-fluid" style="max-width: 850px;">
    {{-- Profile Header --}}
    <div class="profile-header">
        <div class="d-flex align-items-center gap-4">
            <div class="profile-avatar-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="profile-header-info">
                <h2>{{ $user->name }}</h2>
                <p class="mb-2" style="opacity: 0.85;">{{ $user->email }}</p>
                <span class="role-badge">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ ucfirst(str_replace('_', ' ', $user->getRoleNames()[0] ?? 'User')) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="profile-success-alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Informasi Akun --}}
            <div class="col-12">
                <div class="profile-card card">
                    <div class="card-header">
                        <i class="bi bi-person-circle"></i>
                        <h5>Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <label for="name">
                                        <i class="bi bi-person"></i> Nama Lengkap
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name"
                                        value="{{ old('name', $user->name) }}"
                                        placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <label for="email">
                                        <i class="bi bi-envelope"></i> Alamat Email
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email"
                                        value="{{ old('email', $user->email) }}"
                                        placeholder="Masukkan email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="bi bi-info-circle"></i> Email digunakan untuk login ke sistem.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ubah Password --}}
            <div class="col-12">
                <div class="profile-card card">
                    <div class="card-header">
                        <i class="bi bi-shield-lock"></i>
                        <h5>Ubah Kata Sandi</h5>
                    </div>
                    <div class="card-body">
                        <div class="password-section">
                            <div class="password-section-title">
                                <i class="bi bi-info-circle"></i>
                                Kosongkan jika tidak ingin mengubah kata sandi
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating-custom">
                                        <label for="password">
                                            <i class="bi bi-key"></i> Password Baru
                                        </label>
                                        <div class="input-with-toggle">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password"
                                                placeholder="Masukkan password baru"
                                                autocomplete="new-password">
                                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                                        @enderror
                                        <div class="form-hint">Minimal 6 karakter.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating-custom">
                                        <label for="password_confirmation">
                                            <i class="bi bi-key-fill"></i> Konfirmasi Password Baru
                                        </label>
                                        <div class="input-with-toggle">
                                            <input type="password" class="form-control"
                                                id="password_confirmation" name="password_confirmation"
                                                placeholder="Ulangi password baru"
                                                autocomplete="new-password">
                                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('dashboard') }}" class="btn-cancel-profile">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn-save-profile">
                        <i class="bi bi-check2-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('extra-js')
<script>
    function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
