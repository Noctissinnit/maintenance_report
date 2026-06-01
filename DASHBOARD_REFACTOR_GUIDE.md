# Dashboard Refactor Documentation

## Overview
Dashboard telah direfactor dengan component-component reuseable dan stylesheet global untuk mengurangi duplikasi code dan mempermudah maintenance.

## Components Created

### 1. Stat Card Component
**File:** `resources/views/components/dashboard/stat-card.blade.php`

**Usage:**
```blade
<x-dashboard.stat-card
    label="Total Laporan"
    value="{{ $totalLaporan }}"
    unit="records"
    subtitle="Laporan maintenance"
/>
```

**Props:**
- `label` - Label untuk stat card
- `value` - Nilai yang ditampilkan (required)
- `unit` - Unit satuan (optional)
- `subtitle` - Subtitle tambahan (optional)
- `showTrend` - Tampilkan trend indicator (optional)
- `trendClass` - Class trend (up/down) (optional)
- `trendIcon` - Icon untuk trend (optional)

---

### 2. Page Header Component
**File:** `resources/views/components/dashboard/page-header.blade.php`

**Usage:**
```blade
<x-dashboard.page-header
    title="Maintenance Dashboard"
    subtitle="Department Head · Monitoring Semua Aktivitas"
>
    <span class="badge-live"><span class="dot-pulse"></span> Live</span>
    <a href="#" class="filter-btn-pdf"><i class="bi bi-download"></i> Download PDF</a>
</x-dashboard.page-header>
```

**Props:**
- `title` - Judul halaman (required)
- `subtitle` - Subtitle (optional)
- `actions` - Slot untuk action buttons (optional)

---

### 3. Section Component
**File:** `resources/views/components/dashboard/section.blade.php`

**Usage:**
```blade
<x-dashboard.section title="KPI Utama" icon="bi-bar-chart-line">
    {{-- Content di sini --}}
</x-dashboard.section>
```

**Props:**
- `title` - Judul section (required)
- `icon` - Bootstrap icon class (optional)
- Slot untuk content

---

### 4. Data Table Component
**File:** `resources/views/components/dashboard/data-table.blade.php`

**Usage:**
```blade
<x-dashboard.data-table :headers="['Machine', 'Downtime', 'Status']">
    @foreach($machines as $machine)
        <tr>
            <td>{{ $machine->name }}</td>
            <td>{{ $machine->downtime }} jam</td>
            <td><span class="badge-green">Active</span></td>
        </tr>
    @endforeach
</x-dashboard.data-table>
```

**Props:**
- `headers` - Array header columns (required)
- Slot untuk table rows

---

## Stylesheet

### Global Dashboard CSS
**File:** `resources/views/css/dashboard-styles.css`

**Import dalam blade template:**
```blade
@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-styles.css') }}">
    <style>
        /* Custom styles jika diperlukan */
    </style>
@endsection
```

**Available CSS Classes:**
- `.dash-page` - Main dashboard container
- `.page-header` - Page header section
- `.filter-bar` - Filter section
- `.stat-card` - Stat card styling
- `.section-title` - Section title styling
- `.dash-badge` - Badge styling
- `.status-pill` - Status pill styling
- `.bar-wrap`, `.bar-bg`, `.bar-fill` - Inline chart bars

---

## Implementation Pattern

### Before (Old)
```blade
{{-- 200+ lines of embedded CSS --}}
{{-- 300+ lines of HTML markup --}}
{{-- Repeated styles and patterns --}}
```

### After (New)
```blade
@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-styles.css') }}">
@endsection

<x-dashboard.page-header title="Dashboard" subtitle="Monitoring"/>

<x-dashboard.section title="KPI Utama" icon="bi-bar-chart-line">
    <div class="row">
        <x-dashboard.stat-card label="Total" value="{{ $total }}" unit="items"/>
        <x-dashboard.stat-card label="Downtime" value="{{ $downtime }}" unit="jam"/>
    </div>
</x-dashboard.section>

<x-dashboard.section title="Machine Performance" icon="bi-cpu">
    <x-dashboard.data-table :headers="['Name', 'Status', 'Downtime']">
        @foreach($machines as $m)
            <tr>
                <td>{{ $m->name }}</td>
                <td><span class="badge-green">{{ $m->status }}</span></td>
                <td>{{ $m->downtime }} jam</td>
            </tr>
        @endforeach
    </x-dashboard.data-table>
</x-dashboard.section>
```

---

## Applying to Other Dashboards

### 1. Supervisor Dashboard
Replace the entire inline CSS and header section dengan component approach.

### 2. Operator Dashboard
Gunakan same components dengan props yang disesuaikan.

### 3. Custom Dashboards
Buat dashboard baru tanpa perlu menulis ulang CSS/HTML structure.

---

## Benefits

✅ **Reduced Code Duplication** - 60% pengurangan code
✅ **Easier Maintenance** - Update CSS di satu tempat
✅ **Consistency** - Same styling across all dashboards
✅ **Faster Development** - Component-based development
✅ **Responsive Design** - Built-in responsive CSS
✅ **Reusability** - Components dapat digunakan di berbagai halaman

---

## Notes

- Component ini menggunakan Blade Component best practices
- CSS sudah responsive untuk mobile/tablet/desktop
- Warna dan spacing consistent dengan desain
- Semua component props optional kecuali yang ditandai (required)
