# Dashboard Migration Guide

## Refactored Dashboards Ready

Semua dashboard telah di-refactor menggunakan component-based approach yang reuseable. Berikut adalah file-file yang sudah disiapkan:

### ✅ New Refactored Dashboards

1. **Department Head Dashboard**
   - File: `resources/views/dashboard/department-head-refactored.blade.php`
   - Status: ✅ Ready to use
   - Lines: ~130 (sebelumnya 600+)

2. **Admin Dashboard**
   - File: `resources/views/dashboard/admin.blade.php` (Updated)
   - Status: ✅ Refactored in place
   - Lines: ~90 (sebelumnya 150+)

3. **Supervisor Dashboard**
   - File: `resources/views/dashboard/supervisor-refactored.blade.php`
   - Status: ✅ Ready to use
   - Lines: ~110 (sebelumnya 400+)

4. **Operator Dashboard**
   - File: `resources/views/dashboard/operator-refactored.blade.php`
   - Status: ✅ Ready to use
   - Lines: ~130 (sebelumnya 180+)

---

## 🚀 How to Activate Refactored Dashboards

### Option 1: Replace in Place (Recommended)

Edit `app/Http/Controllers/DashboardController.php`:

```php
// departmentHeadDashboard() method
return view('dashboard.department-head-refactored', compact(...));

// supervisorDashboard() method
return view('dashboard.supervisor-refactored', compact(...));

// operatorDashboard() method
return view('dashboard.operator-refactored', compact(...));
```

### Option 2: Rename Files

```bash
# Backup old files
mv resources/views/dashboard/department-head.blade.php resources/views/dashboard/department-head.blade.php.bak
mv resources/views/dashboard/supervisor.blade.php resources/views/dashboard/supervisor.blade.php.bak
mv resources/views/dashboard/operator.blade.php resources/views/dashboard/operator.blade.php.bak

# Use refactored versions
mv resources/views/dashboard/department-head-refactored.blade.php resources/views/dashboard/department-head.blade.php
mv resources/views/dashboard/supervisor-refactored.blade.php resources/views/dashboard/supervisor.blade.php
mv resources/views/dashboard/operator-refactored.blade.php resources/views/dashboard/operator.blade.php
```

---

## 📊 Comparison

| Dashboard | Before (lines) | After (lines) | Reduction |
|-----------|----------------|---------------|-----------|
| Department Head | 600+ | 130 | 78% ↓ |
| Admin | 150+ | 90 | 40% ↓ |
| Supervisor | 400+ | 110 | 72% ↓ |
| Operator | 180+ | 130 | 28% ↓ |
| **TOTAL** | **1330+** | **460** | **65% ↓** |

---

## 📦 Components Used

Semua dashboard sekarang menggunakan:

- `<x-dashboard.page-header>` - Page header dengan title/subtitle
- `<x-dashboard.section>` - Section wrapper dengan icon
- `<x-dashboard.stat-card>` - Stat card dengan label/value
- `<x-dashboard.data-table>` - Reusable table

---

## 🎨 Stylesheet

Global stylesheet: `resources/views/css/dashboard-styles.css`

Features:
- ✅ Consistent color scheme
- ✅ Responsive design (Mobile/Tablet/Desktop)
- ✅ Hover effects & animations
- ✅ Professional UI styling
- ✅ Used by all dashboards

---

## 🔧 Customization

### Menambah Stat Card
```blade
<x-dashboard.stat-card 
    label="Custom Label" 
    value="100" 
    unit="items"
    subtitle="Optional subtitle"
/>
```

### Menambah Section
```blade
<x-dashboard.section title="Section Title" icon="bi-icon-name">
    {{-- Content here --}}
</x-dashboard.section>
```

### Menambah Table
```blade
<x-dashboard.data-table :headers="['Col1', 'Col2', 'Col3']">
    @foreach($data as $item)
        <tr>
            <td>{{ $item->col1 }}</td>
            <td>{{ $item->col2 }}</td>
            <td>{{ $item->col3 }}</td>
        </tr>
    @endforeach
</x-dashboard.data-table>
```

---

## ✨ Benefits

✅ **Cleaner Code** - 65% reduction in total lines  
✅ **Maintainable** - Update styling in one place  
✅ **Reuseable** - Components can be used anywhere  
✅ **Consistent** - Same styling across all dashboards  
✅ **Responsive** - Built-in mobile support  
✅ **Professional** - Modern UI design  

---

## 📝 Next Steps

1. ✅ Review refactored dashboards in browser
2. ✅ Test all filters and functionality
3. ✅ Confirm data displays correctly
4. ✅ Activate in DashboardController (update view names)
5. ✅ Remove old dashboard files (after testing)
6. ✅ Update any custom styling if needed

---

## 📞 Troubleshooting

**Components not found?**
- Ensure components are in: `resources/views/components/dashboard/`
- Clear view cache: `php artisan view:clear`

**Stylesheet not loading?**
- Check if CSS file exists: `resources/views/css/dashboard-styles.css`
- Verify asset path in blade: `{{ asset('resources/views/css/dashboard-styles.css') }}`

**Data not displaying?**
- Verify controller is passing required variables to compact()
- Check blade syntax in refactored view
- Use `php artisan tinker` to debug query results

---

## 💡 Tips

- Components accept optional props - only pass what you need
- Use Bootstrap grid: `<div class="row">` for stat cards
- All badges have CSS classes: `.badge-green`, `.badge-red`, etc.
- Tables automatically responsive
- Filter bar styling included in global CSS

