# Code Changes - MTBF Dashboard Integration

## File 1: DashboardController.php

### Change 1: Added Import
**Location**: Line 5-7 (Imports section)

```php
use App\Models\LaporanHarian;
use App\Models\Machine;  // ← NEW
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
```

### Change 2: Added MTBF Metrics Calculation
**Location**: departmentHeadDashboard() method, after Machine Performance section

```php
// MTBF Metrics dari Machine Model
$machines = Machine::where('status', 'active')->with('line')->get();
$mtbfData = [];
$totalMTBFHours = 0;
$mtbfMachineCount = 0;

foreach ($machines as $machine) {
    $mtbf = $machine->calculateMTBF();
    if ($mtbf['failure_count'] > 0) {
        $mtbfData[] = $mtbf;
        $totalMTBFHours += $mtbf['mtbf_hours'];
        $mtbfMachineCount++;
    }
}

// Sort by MTBF descending
usort($mtbfData, function ($a, $b) {
    return $b['mtbf_hours'] <=> $a['mtbf_hours'];
});

// Average MTBF dari actual calculation
$avgMTBFHours = $mtbfMachineCount > 0 ? $totalMTBFHours / $mtbfMachineCount : 0;

// Get top machines by reliability
$topReliableMachines = array_slice($mtbfData, 0, 5);
$worstMachines = array_slice(array_reverse($mtbfData), 0, 5);
```

### Change 3: Added Variables to View Compact
**Location**: return view() statement, in compact() call

```php
return view('dashboard.department-head', compact(
    // ... existing variables ...
    'mtbfData',              // ← NEW
    'avgMTBFHours',          // ← NEW
    'topReliableMachines',   // ← NEW
    'worstMachines'          // ← NEW
));
```

---

## File 2: department-head.blade.php

### Change: Added MTBF Section
**Location**: After Summary Cards (line ~360), before "Top 10 & Top 7 Tables Row 1"

**Full Code Section**:

```blade
<!-- MTBF Metrics Section -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="bi bi-speedometer2" style="color: var(--primary-color);"></i> 
            <span style="color: var(--text-dark); font-weight: 600;">MTBF (Mean Time Between Failures) Analysis</span>
        </h5>
    </div>
    
    <!-- MTBF Statistics -->
    <div class="col-md-4">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-graph-up"></i></div>
                <div class="performance-label">Average MTBF</div>
                <div class="performance-value">{{ number_format($avgMTBFHours, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-check-circle"></i></div>
                <div class="performance-label">Machines with Data</div>
                <div class="performance-value">{{ count($mtbfData) }}</div>
                <div class="performance-unit">mesin</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-link-45deg"></i></div>
                <div class="performance-label">View Full</div>
                <a href="{{ route('mtbf.index') }}" class="btn btn-sm btn-primary mt-2">
                    MTBF Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Top Reliable & Worst Machines -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="bi bi-trophy"></i> Top 5 Most Reliable Machines
            </div>
            <div class="card-body">
                @if(count($topReliableMachines) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mesin</th>
                                    <th class="text-center">MTBF (hrs)</th>
                                    <th class="text-center">Failures</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topReliableMachines as $machine)
                                    @php
                                        if ($machine['mtbf_hours'] >= 168) {
                                            $badgeClass = 'bg-success';
                                            $status = 'Excellent';
                                        } elseif ($machine['mtbf_hours'] >= 72) {
                                            $badgeClass = 'bg-info';
                                            $status = 'Good';
                                        } else {
                                            $badgeClass = 'bg-warning';
                                            $status = 'Fair';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $machine['machine_name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $machine['line_name'] ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ number_format($machine['mtbf_hours'], 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $machine['failure_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">Tidak ada data MTBF untuk mesin</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> Bottom 5 Worst Performing Machines
            </div>
            <div class="card-body">
                @if(count($worstMachines) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mesin</th>
                                    <th class="text-center">MTBF (hrs)</th>
                                    <th class="text-center">Failures</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($worstMachines as $machine)
                                    @php
                                        if ($machine['mtbf_hours'] < 24) {
                                            $badgeClass = 'bg-danger';
                                            $status = 'Poor';
                                        } elseif ($machine['mtbf_hours'] < 72) {
                                            $badgeClass = 'bg-warning';
                                            $status = 'Fair';
                                        } else {
                                            $badgeClass = 'bg-info';
                                            $status = 'Good';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $machine['machine_name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $machine['line_name'] ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ number_format($machine['mtbf_hours'], 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $machine['failure_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">Tidak ada data MTBF untuk mesin</div>
                @endif
            </div>
        </div>
    </div>
</div>
```

---

## Summary of Changes

### Total Files Modified: 2
- DashboardController.php
- department-head.blade.php

### Lines Added
- **DashboardController**: ~30 lines of MTBF calculation code
- **department-head.blade.php**: ~130 lines of MTBF display code

### Key Logic
1. **Data Retrieval**: Gets all active machines from database
2. **Calculation**: Calls Machine::calculateMTBF() for each machine
3. **Filtering**: Only includes machines with actual failure records
4. **Sorting**: Orders machines by MTBF (highest first = most reliable)
5. **Extraction**: Gets top 5 and bottom 5 machines
6. **Display**: Shows metrics and tables with color-coded status badges

### Blade Template Features
- Responsive 3-column layout for metrics
- 2-column layout for machine tables (top and worst)
- Color-coded status badges (4-level system)
- Empty state handling
- Links to detailed MTBF analysis

---

## Verification

### Check Code Syntax
```bash
php -l app/Http/Controllers/DashboardController.php
# Should output: No syntax errors detected
```

### Verify Routes
```bash
php artisan route:list | grep dashboard
# Should show: GET dashboard route
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Testing Instructions

1. **Start Server**: `php artisan serve`
2. **Login**: Use department_head account
3. **Navigate**: Go to Dashboard (home page)
4. **Look For**: "MTBF (Mean Time Between Failures) Analysis" section
5. **Verify**: 
   - 3 statistics cards display
   - Top 5 machines table shows data
   - Bottom 5 machines table shows data
   - "MTBF Dashboard" link works
