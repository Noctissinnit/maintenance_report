<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Laporan Maintenance')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #6c8cff;
            --primary-dark: #1e40af;
            --secondary-color: #7209b7;
            --accent-color: #ff9f1c;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
        }
        
        * {
            transition: var(--transition);
        }
        
        html, body {
            height: 100%;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        }
        
        /* Navbar Styling */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 0.75rem 0;
            position: fixed;
            top: 0;
            right: 0;
            left: 270px;
            z-index: 990;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Navbar adjustment when sidebar is collapsed */
        .navbar.navbar-collapsed {
            left: 80px;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-dark) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0;
            margin: 0;
        }
        
        .navbar-logo-img {
            height: 32px;
            width: auto;
            object-fit: contain;
            margin-right: 0.25rem;
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
        
        .navbar-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .navbar-user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.45rem 1rem;
            background: rgba(241, 245, 249, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--border-radius);
            cursor: pointer;
        }
        
        .navbar-user-info:hover {
            background: rgba(226, 232, 240, 0.9) !important;
        }
        
        .navbar-user-name {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.875rem;
            margin: 0;
        }
        
        .navbar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: var(--shadow-sm);
        }
        
        .dropdown-menu .dropdown-item {
            font-weight: 500;
            border-radius: 6px;
            margin: 0.2rem 0.5rem;
            width: calc(100% - 1rem);
        }
        
        .dropdown-menu .dropdown-item:hover {
            background-color: #f1f5f9;
        }
        
        .dropdown-menu .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
        }

        /* Sidebar Styling */
        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: var(--bg-white);
            color: var(--text-dark);
            padding: 1.5rem 1.15rem;
            box-shadow: var(--shadow-sm);
            overflow-y: auto;
            z-index: 1001;
            transform: translateX(0);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Sidebar Brand */
        .sidebar-brand-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-left: 0.5rem;
            overflow: hidden;
        }
        .sidebar-logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }
        .sidebar-brand-wrapper .brand-text {
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand-wrapper .brand-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.2rem;
            line-height: 1.1;
        }
        .sidebar-brand-wrapper .brand-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Desktop Collapse */
        @media (min-width: 768px) {
            .sidebar-wrapper.collapsed {
                width: 80px;
                padding: 1.5rem 0.5rem;
            }
            
            .sidebar-wrapper.collapsed .sidebar-brand-wrapper {
                padding-left: 0;
                justify-content: center;
            }
            
            .sidebar-wrapper.collapsed .sidebar-brand-wrapper .brand-text {
                display: none;
            }
            
            .sidebar-wrapper.collapsed ~ .main-content {
                margin-left: 80px;
            }
            
            .sidebar-wrapper.collapsed .nav-text {
                display: none;
            }
            
            .sidebar-wrapper.collapsed .sidebar-nav-title {
                text-align: center;
                font-size: 0;
                height: 1px;
                background: rgba(226, 232, 240, 0.8);
                margin: 1.5rem 0.5rem;
                padding: 0;
                letter-spacing: 0;
            }
            
            .sidebar-wrapper.collapsed .sidebar-nav-link {
                justify-content: center;
                padding: 0.75rem 0;
            }
            
            .sidebar-wrapper.collapsed .sidebar-nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            /* CSS Tooltip on Collapse */
            .sidebar-wrapper.collapsed .sidebar-nav-link::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 80px;
                top: 50%;
                transform: translateY(-50%) translateX(-10px);
                background-color: var(--text-dark);
                color: white;
                padding: 0.4rem 0.75rem;
                border-radius: 6px;
                font-size: 0.8rem;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                box-shadow: var(--shadow-md);
                z-index: 9999;
                transition: opacity 0.2s, transform 0.2s;
            }
            
            .sidebar-wrapper.collapsed .sidebar-nav-link:hover::after {
                opacity: 1;
                visibility: visible;
                transform: translateY(-50%) translateX(0);
            }
        }
        
        /* Sidebar Navigation */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .sidebar-nav-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            padding-left: 0.5rem;
            font-family: 'Outfit', sans-serif;
        }
        
        .sidebar-nav-title:first-child {
            margin-top: 0;
        }
        
        .sidebar-nav-link {
            color: var(--slate-700);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.925rem;
            cursor: pointer;
            font-weight: 500;
            position: relative;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }
        
        .sidebar-nav-link i {
            width: 1.25rem;
            margin-right: 0.85rem;
            text-align: center;
            font-size: 1.1rem;
            color: var(--text-muted);
        }
        
        .sidebar-nav-link:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
            padding-left: 1.15rem;
        }
        
        .sidebar-nav-link:hover i {
            color: var(--primary-color);
        }
        
        .sidebar-nav-link.active {
            background: rgba(67, 97, 238, 0.08);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
            padding-left: 1.15rem;
        }
        
        .sidebar-nav-link.active i {
            color: var(--primary-color);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 270px;
            margin-top: 66px;
            padding: 2.5rem;
            min-height: calc(100vh - 66px);
            background-color: var(--bg-light);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-wrapper.collapsed ~ .main-content {
            margin-left: 80px;
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            background-color: var(--bg-white);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border-top: 3px solid var(--primary-color);
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 1.5rem 1.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            font-size: 1.05rem;
        }
        
        .card-body {
            padding: 1.75rem;
        }
        
        .card-footer {
            background-color: var(--bg-light);
            border-top: 1px solid #e9ecef;
            padding: 1.25rem 1.75rem;
        }
        
        /* Table Styling */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-weight: 600;
            border-color: #e9ecef;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .table tbody td {
            border-color: #f0f0f0;
            padding: 0.85rem 0.75rem;
            vertical-align: middle;
        }
        
        .table tbody tr {
            background-color: var(--bg-white);
        }
        
        .table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        /* Button Styling */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border: none;
            cursor: pointer;
            text-transform: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-success {
            background-color: #48a55a;
        }
        
        .btn-success:hover {
            background-color: #3a8548;
        }
        
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #d68910;
            color: white;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-outline-warning,
        .btn-outline-danger {
            color: var(--text-dark);
            border-color: #ddd;
        }
        
        .btn-outline-warning:hover {
            background-color: #f39c12;
            border-color: #f39c12;
            color: white;
        }
        
        .btn-outline-danger:hover {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: white;
        }
        
        /* Form Styling */
        .form-control,
        .form-select {
            border: 1.5px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            background-color: var(--bg-white);
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 89, 0.15);
            background-color: var(--bg-white);
        }
        
        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 0.75rem;
            border-left: 4px solid;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }
        
        .alert-success {
            background-color: #ecf9f3;
            color: #1a664f;
            border-left-color: #48a55a;
        }
        
        .alert-danger {
            background-color: #fceded;
            color: #7a1a1a;
            border-left-color: #e74c3c;
        }
        
        /* Badge Styling */
        .badge {
            border-radius: 0.375rem;
            padding: 0.45rem 0.65rem;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .badge-success {
            background-color: #48a55a;
        }
        
        .badge-danger {
            background-color: #e74c3c;
        }
        
        .badge-warning {
            background-color: #f39c12;
        }
        
        .badge-info {
            background-color: var(--primary-light);
        }
        
        /* KPI Card */
        .kpi-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 1.75rem;
            border-radius: 0.75rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border-top: none;
            transition: var(--transition);
        }
        
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .kpi-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0.75rem 0;
        }
        
        .kpi-label {
            font-size: 0.85rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Select2 Custom Styling */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 0.95rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--text-dark);
            background-color: var(--bg-white);
            border: 1.5px solid #e0e0e0;
            border-radius: 0.5rem;
            transition: var(--transition);
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 89, 0.15);
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #e0e0e0;
            border-radius: 0.5rem;
            box-shadow: var(--shadow-md);
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #e0e0e0;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #e8f5f0;
            color: var(--text-dark);
        }
        
        /* Pagination */
        .pagination {
            margin-top: 1.5rem;
        }
        
        .page-link {
            color: var(--primary-color);
            border-color: #e0e0e0;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
        }
        
        .page-link:hover {
            background-color: var(--primary-light);
            color: white;
            border-color: var(--primary-light);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                left: 0 !important;
                padding: 0.5rem 0;
            }
            
            .sidebar-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                width: 270px;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1050;
                box-shadow: none;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .sidebar-wrapper:not(.collapsed) {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }
            
            .sidebar-wrapper.collapsed {
                transform: translateX(-100%);
                width: 270px;
                padding: 1.5rem 1.15rem;
                display: block;
            }
            
            .main-content {
                margin-left: 0 !important;
                margin-top: 70px;
                padding: 1.25rem;
            }
            
            .navbar-user-info {
                gap: 0.5rem;
                padding: 0.4rem 0.75rem;
            }
            
            .navbar-user-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .btn-logout span {
                display: none;
            }
            
            /* Backdrop overlay for mobile */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(15, 23, 42, 0.4);
                backdrop-filter: blur(4px);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: var(--transition);
            }
            
            .sidebar-wrapper:not(.collapsed) ~ .sidebar-overlay {
                opacity: 1;
                visibility: visible;
            }
        }
        
        /* Supervisor Notes Content - Image Sizing */
        .supervisor-notes-content {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .supervisor-notes-content img {
            max-width: 100%;
            height: auto;
            max-height: 500px;
            display: block;
            margin: 1rem 0;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .supervisor-notes-content p {
            margin-bottom: 1rem;
        }
    </style>
    @yield('extra-css')
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <!-- Sidebar Toggle Button inside Navbar -->
            <button class="btn btn-link me-3 p-0" id="sidebarToggle" title="Toggle Sidebar" style="font-size: 1.5rem; display: flex; align-items: center; color: var(--text-dark);">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/ahs.png') }}" alt="Logo AHS" class="navbar-logo-img">
                <span>Maintenance</span>
            </a>
            
            <div class="navbar-content ms-auto">
                <div class="dropdown">
                    <a href="#" class="navbar-user-info text-decoration-none" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="navbarUserDropdown">
                        <div class="navbar-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="d-none d-sm-block">
                            <p class="navbar-user-name mb-0">{{ Auth::user()->name }}</p>
                            <small class="navbar-user-role">
                                {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()[0] ?? 'User')) }}
                            </small>
                        </div>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.75rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 0.75rem; min-width: 220px; margin-top: 0.5rem; overflow: hidden;" aria-labelledby="navbarUserDropdown">
                        <li class="px-3 py-2" style="background: linear-gradient(135deg, #f8f9fb, #eef1f8); border-bottom: 1px solid #e8ecf1;">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-dark);">{{ Auth::user()->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3" href="{{ route('profile.edit') }}" style="font-size: 0.9rem;">
                                <i class="bi bi-person-gear" style="font-size: 1rem; color: var(--primary-color);"></i>
                                Edit Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 text-danger" style="font-size: 0.9rem;">
                                    <i class="bi bi-box-arrow-right" style="font-size: 1rem;"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar-wrapper" id="sidebarWrapper">
        <!-- Sidebar Brand Wrapper (only visible on desktop) -->
        <div class="sidebar-brand-wrapper">
            <img src="{{ asset('images/ahs.png') }}" alt="Logo AHS" class="sidebar-logo-img">
            <div class="brand-text">
                <span class="brand-name">AHS</span>
                <span class="brand-sub">Maintenance Hub</span>
            </div>
        </div>

        <div class="sidebar-nav">
            {{-- Dashboard untuk Admin, Department Head, dan Supervisor --}}
            @if(Auth::user()->hasAnyRole(['admin', 'department_head', 'supervisor']))
                <div class="sidebar-nav-title">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'dashboard') active @endif" data-tooltip="Dashboard">
                    <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
                </a>
            @endif

            {{-- Dashboard untuk Operator --}}
            @if(Auth::user()->hasRole('operator'))
                <div class="sidebar-nav-title">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'dashboard') active @endif" data-tooltip="Dashboard">
                    <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
                </a>
            @endif

            {{-- Laporan untuk Operator, Supervisor, dan Admin --}}
            @if(Auth::user()->hasAnyRole(['operator', 'supervisor','admin']))
                <div class="sidebar-nav-title">Laporan</div>
                <a href="{{ route('laporan.list') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'laporan.list') active @endif" data-tooltip="Laporan Saya">
                    <i class="bi bi-list-ul"></i> <span class="nav-text">Laporan Saya</span>
                </a>
              
                <a href="{{ route('laporan.create') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'laporan.create') active @endif" data-tooltip="Input Laporan">
                    <i class="bi bi-plus-circle"></i> <span class="nav-text">Input Laporan</span>
                </a>
            @endif

            {{-- MTBF Analysis untuk supervisor, department_head, dan admin --}}
            @if(Auth::user()->hasAnyRole(['admin', 'department_head', 'supervisor']))
                <div class="sidebar-nav-title">Analytics</div>
                <a href="{{ route('mtbf.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'mtbf.index' || Route::current()->getName() === 'mtbf.show') active @endif" data-tooltip="MTBF Analysis">
                    <i class="bi bi-speedometer2"></i> <span class="nav-text">MTBF Analysis</span>
                </a>
            @endif

            {{-- Planned Time Management untuk Department Head (PPIC) --}}
            @if(Auth::user()->hasRole('department_head'))
                <div class="sidebar-nav-title">PPIC Planning</div>
                <a href="{{ route('planned-times.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'planned-times.index' || Route::current()->getName() === 'planned-times.create' || Route::current()->getName() === 'planned-times.edit') active @endif" data-tooltip="Planned Time">
                    <i class="bi bi-calendar-event"></i> <span class="nav-text">Planned Time</span>
                </a>
            @endif

            {{-- Command Management untuk Department Head, Supervisor, dan Admin --}}
            @if(Auth::user()->hasAnyRole(['department_head', 'supervisor', 'admin']))
                <div class="sidebar-nav-title">Command Management</div>
                @if(Auth::user()->hasRole('department_head'))
                    <a href="{{ route('commands.list-department-head') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'commands.list-department-head' || Route::current()->getName() === 'commands.create' || Route::current()->getName() === 'commands.edit') active @endif" data-tooltip="Command Dept Head">
                        <i class="bi bi-list-check"></i> <span class="nav-text">Command Dept Head</span>
                    </a>
                @endif
               
                @if(Auth::user()->hasRole('supervisor'))
                    <a href="{{ route('commands.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'commands.index' || Route::current()->getName() === 'commands.edit-status') active @endif" data-tooltip="Command Supervision">
                        <i class="bi bi-clipboard-check"></i> <span class="nav-text">Command Supervision</span>
                    </a>
                @endif
                @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('commands.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'commands.index' || Route::current()->getName() === 'commands.edit-status') active @endif" data-tooltip="Supervisi Command">
                        <i class="bi bi-clipboard-check"></i> <span class="nav-text">Supervisi Command</span>
                    </a>
                @endif
            @endif

            {{-- Management Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_employees'))
                <div class="sidebar-nav-title">Management</div>
                <a href="{{ route('employees.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'employees.index') active @endif" data-tooltip="Operator">
                    <i class="bi bi-people"></i> <span class="nav-text">Operator</span>
                </a>
            @endif

            {{-- Produksi Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_machines'))
                <div class="sidebar-nav-title">Produksi</div>
                <a href="{{ route('lines.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'lines.index') active @endif" data-tooltip="Line">
                    <i class="bi bi-diagram-3"></i> <span class="nav-text">Line</span>
                </a>
                <a href="{{ route('machines.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'machines.index') active @endif" data-tooltip="Mesin">
                    <i class="bi bi-gear"></i> <span class="nav-text">Mesin</span>
                </a>
                <a href="{{ route('planned-times.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'planned-times.index' || Route::current()->getName() === 'planned-times.create') active @endif" data-tooltip="Planned Time">
                    <i class="bi bi-calendar-event"></i> <span class="nav-text">Planned Time</span>
                </a>
            @endif

            {{-- Inventory Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_spare_parts'))
                <div class="sidebar-nav-title">Inventory</div>
                <a href="{{ route('spare-parts.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'spare-parts.index') active @endif" data-tooltip="Spare Part">
                    <i class="bi bi-box-seam"></i> <span class="nav-text">Spare Part</span>
                </a>
                <a href="{{ route('spare-parts.monitoring') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'spare-parts.monitoring') active @endif" data-tooltip="Monitoring Sparepart">
                    <i class="bi bi-graph-up"></i> <span class="nav-text">Monitoring Sparepart</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Validasi Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Function untuk upload image - define di luar jQuery ready
        function uploadImage(file, $summernote) {
            const data = new FormData();
            data.append('image', file);
            data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                type: 'POST',
                url: '{{ route("commands.upload-image") }}',
                data: data,
                contentType: false,
                processData: false,
                success: function(response) {
                    $summernote.summernote('insertImage', response.url);
                },
                error: function(err) {
                    alert('Gagal upload gambar: ' + (err.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }
    </script>
    <script>
        // Sidebar Toggle Logic
        const sidebarToggleBtn = document.getElementById('sidebarToggle');
        const sidebarWrapper = document.getElementById('sidebarWrapper');
        const navbar = document.querySelector('.navbar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebarWrapper.classList.toggle('collapsed');
            navbar.classList.toggle('navbar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebarWrapper.classList.contains('collapsed'));
        }

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebarWrapper.classList.add('collapsed');
                navbar.classList.add('navbar-collapsed');
                localStorage.setItem('sidebarCollapsed', 'true');
            });
        }
        
        // Restore sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebarWrapper.classList.add('collapsed');
            navbar.classList.add('navbar-collapsed');
        } else {
            // Expand by default if not collapsed in storage
            sidebarWrapper.classList.remove('collapsed');
            navbar.classList.remove('navbar-collapsed');
        }
        
        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sidebar-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebarWrapper.classList.add('collapsed');
                    navbar.classList.add('navbar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            });
        });
    </script>    
    <script>
        // Select2 Initialization
        $(document).ready(function() {
            console.log('jQuery ready - Initializing Summernote and Select2');
            
            // Check if Summernote is loaded
            if (typeof $.summernote !== 'undefined') {
                console.log('Summernote library loaded successfully');
            } else {
                console.error('Summernote library NOT loaded!');
            }
            
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                width: '100%',
                dropdownParent: $(document.body),
                placeholder: 'Cari...',
                language: {
                    noResults: function() {
                        return 'Tidak ada hasil';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // Initialize Summernote
            if ($('.summernote').length > 0) {
                console.log('Initializing Summernote - found: ' + $('.summernote').length + ' element(s)');
                $('.summernote').summernote({
                    placeholder: 'Masukkan catatan di sini...',
                    tabsize: 2,
                    height: 300,
                    minHeight: 250,
                    maxHeight: 600,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            console.log('Image upload triggered');
                            uploadImage(files[0], $(this));
                        }
                    }
                });
                console.log('✓ Summernote initialized successfully');
            } else {
                if (typeof $.summernote === 'undefined') {
                    console.error('✗ Summernote library not found');
                } else {
                    console.warn('✗ No .summernote elements on page');
                }
            }
        });
    </script>
    @yield('extra-js')
    @livewireScripts
</body>
</html>

