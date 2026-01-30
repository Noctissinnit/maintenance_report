<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Laporan Maintenance')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
        
        html, body {
            height: 100%;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        }
        
        /* Navbar Styling */
        .navbar {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary-color) 50%, var(--primary-light) 100%);
            box-shadow: 0 2px 12px rgba(45, 80, 22, 0.2);
            padding: 0.6rem 0;
            border-bottom: none;
            position: fixed;
            top: 0;
            right: 0;
            left: 270px;
            z-index: 990;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Navbar adjustment when sidebar is collapsed */
        .navbar.navbar-collapsed {
            left: 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-light) !important;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0;
            margin: 0;
        }
        
        .navbar-brand i {
            font-size: 1.35rem;
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
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            backdrop-filter: blur(10px);
        }
        
        .navbar-user-name {
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .navbar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: var(--text-light) !important;
            padding: 0.45rem 0.9rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            transform: translateY(-2px);
        }
        
        .btn-logout i {
            font-size: 0.95rem;
        }
        
        /* Sidebar Styling */
        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: linear-gradient(180deg, #ffffff 0%, #f5f7fb 100%);
            color: var(--text-dark);
            padding: 1.5rem 1.25rem;
            box-shadow: var(--shadow-lg);
            overflow-y: auto;
            z-index: 1001;
            transform: translateX(0);
            border-right: 2px solid #e8ecf1;
        }
        
        .sidebar-wrapper.collapsed {
            transform: translateX(-100%);
            width: 0;
            padding: 0;
        }
        
        .sidebar-toggle {
            position: fixed;
            top: 75px;
            left: 290px;
            z-index: 989;
            background: var(--primary-color);
            border: none;
            color: white;
            width: 42px;
            height: 42px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }
        
        .sidebar-toggle:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .sidebar-wrapper.collapsed ~ .sidebar-toggle {
            left: 20px;
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
            color: #999;
            margin-top: 1.75rem;
            margin-bottom: 0.875rem;
            padding-left: 0.5rem;
        }
        
        .sidebar-nav-title:first-child {
            margin-top: 0;
        }
        
        .sidebar-nav-link {
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.875rem 1.125rem;
            border-radius: 0.625rem;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            cursor: pointer;
            font-weight: 500;
            position: relative;
            transition: var(--transition);
        }
        
        .sidebar-nav-link i {
            width: 1.2rem;
            margin-right: 0.875rem;
            text-align: center;
            font-size: 1.1rem;
            color: #999;
        }
        
        .sidebar-nav-link:hover {
            background-color: #f0f2f7;
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            padding-left: 1.25rem;
        }
        
        .sidebar-nav-link:hover i {
            color: var(--primary-color);
        }
        
        .sidebar-nav-link.active {
            background: linear-gradient(90deg, rgba(67, 97, 238, 0.1), transparent);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
            padding-left: 1.25rem;
        }
        
        .sidebar-nav-link.active i {
            color: var(--primary-color);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 270px;
            margin-top: 60px;
            padding: 2.5rem;
            min-height: calc(100vh - 60px);
            background-color: var(--bg-light);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-wrapper.collapsed ~ .main-content {
            margin-left: 0;
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
                left: 0;
            }
            
            .sidebar-wrapper {
                width: 100%;
                padding: 1rem;
                height: 100vh;
                top: 0;
            }
            
            .sidebar-toggle {
                display: flex !important;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1002;
            }
            
            .main-content {
                margin-left: 0;
                margin-top: 70px;
                padding: 1.5rem;
            }
            
            .sidebar-wrapper.collapsed {
                display: none;
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
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-gear-fill"></i>
                <span>Maintenance</span>
            </a>
            
            <div class="navbar-content ms-auto">
                <div class="navbar-user-info">
                    <div class="navbar-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="navbar-user-name mb-0">{{ Auth::user()->name }}</p>
                        <small style="color: rgba(255,255,255,0.7);">
                            {{ ucfirst(Auth::user()->getRoleNames()[0] ?? 'User') }}
                        </small>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="sidebar-wrapper" id="sidebarWrapper">
        <div class="sidebar-nav">
            {{-- Dashboard untuk Admin, Department Head, dan Supervisor --}}
            @if(Auth::user()->hasAnyRole(['admin', 'department_head', 'supervisor']))
                <div class="sidebar-nav-title">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'dashboard') active @endif">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            @endif

            {{-- Dashboard untuk Operator --}}
            @if(Auth::user()->hasRole('operator'))
                <div class="sidebar-nav-title">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'dashboard') active @endif">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            @endif

            {{-- Laporan untuk Operator, Supervisor, dan Admin --}}
            @if(Auth::user()->hasAnyRole(['operator', 'supervisor','admin']))
                <div class="sidebar-nav-title">Laporan</div>
                <a href="{{ route('laporan.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'laporan.index') active @endif">
                    <i class="bi bi-file-earmark-text"></i> Daftar Laporan
                </a>
                <a href="{{ route('laporan.create') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'laporan.create') active @endif">
                    <i class="bi bi-plus-circle"></i> Input Laporan
                </a>
            @endif

            {{-- Management Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_employees'))
                <div class="sidebar-nav-title">Management</div>
                <a href="{{ route('employees.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'employees.index') active @endif">
                    <i class="bi bi-people"></i> Operator
                </a>
            @endif

            {{-- Produksi Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_machines'))
                <div class="sidebar-nav-title">Produksi</div>
                <a href="{{ route('lines.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'lines.index') active @endif">
                    <i class="bi bi-diagram-3"></i> Line
                </a>
                <a href="{{ route('machines.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'machines.index') active @endif">
                    <i class="bi bi-gear"></i> Mesin
                </a>
            @endif

            {{-- Inventory Menu untuk Admin saja --}}
            @if(Auth::user()->can('manage_spare_parts'))
                <div class="sidebar-nav-title">Inventory</div>
                <a href="{{ route('spare-parts.index') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'spare-parts.index') active @endif">
                    <i class="bi bi-box-seam"></i> Spare Part
                </a>
                <a href="{{ route('spare-parts.monitoring') }}" class="sidebar-nav-link @if(Route::current()->getName() === 'spare-parts.monitoring') active @endif">
                    <i class="bi bi-graph-up"></i> Monitoring Sparepart
                </a>
            @endif
        </div>
    </div>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebarWrapper');
            const navbar = document.querySelector('.navbar');
            sidebar.classList.toggle('collapsed');
            navbar.classList.toggle('navbar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Restore sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            const sidebar = document.getElementById('sidebarWrapper');
            const navbar = document.querySelector('.navbar');
            sidebar.classList.add('collapsed');
            navbar.classList.add('navbar-collapsed');
        }
        
        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sidebar-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    const sidebar = document.getElementById('sidebarWrapper');
                    const navbar = document.querySelector('.navbar');
                    sidebar.classList.add('collapsed');
                    navbar.classList.add('navbar-collapsed');
                }
            });
        });
        
        // Select2 Initialization
        $(document).ready(function() {
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
        });
    </script>
    @yield('extra-js')
</body>
</html>

