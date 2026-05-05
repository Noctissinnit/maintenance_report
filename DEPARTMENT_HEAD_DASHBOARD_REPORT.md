# LAPORAN DASHBOARD DEPARTMENT HEAD
## Sistem Laporan Maintenance

**File:** `resources/views/dashboard/department-head.blade.php`  
**Status:** Active  
**Tanggal Laporan:** 5 Mei 2026  

---

## 📋 DAFTAR ISI
1. [Gambaran Umum](#gambaran-umum)
2. [Fitur Filter Data](#fitur-filter-data)
3. [KPI Cards Section](#kpi-cards-section)
4. [Machine Performance Metrics](#machine-performance-metrics)
5. [Maintenance Analysis](#maintenance-analysis)
6. [Machine Reliability Analysis](#machine-reliability-analysis)
7. [Breakdown & Spare Parts Analysis](#breakdown--spare-parts-analysis)
8. [Data Visualization](#data-visualization)
9. [Styling & UI/UX](#styling--uiux)
10. [Data Flow & Calculations](#data-flow--calculations)

---

## 1. GAMBARAN UMUM

### Tujuan Dashboard
Dashboard Department Head dirancang untuk memberikan monitoring komprehensif terhadap **semua aktivitas maintenance** di fasilitas produksi. Fitur ini memungkinkan department head melihat performa mesin, breakdown analysis, dan resource planning dalam satu tampilan terintegrasi.

### Target User
- **Department Head** - Monitoring supervisors dan operators
- **Role Permissions:** `view_department_dashboard`

### Key Information
```
Title: Department Head Dashboard - Monitoring Semua Aktivitas
Alert Message: "Anda memiliki akses monitoring untuk melihat semua dashboard 
              kegiatan laporan harian dan supervisor."
```

---

## 2. FITUR FILTER DATA

### Filter Section
Lokasi: **Top of dashboard** dengan desain gradient blue

#### Filter Parameters:

| Filter | Type | Options | Fungsi |
|--------|------|---------|---------|
| **Bulan** | Dropdown | Jan-Dec (12 months) | Pilih bulan laporan |
| **Tahun** | Dropdown | 2024-2026 | Pilih tahun laporan |
| **Mesin** | Dropdown | Semua Mesin + List | Filter berdasarkan mesin |
| **Line** | Dropdown | Semua Line + List | Filter berdasarkan production line |
| **Semua Data** | Checkbox | Toggle On/Off | Tampilkan data all-time vs period tertentu |

#### Form Characteristics:
- **Method:** GET (URL-based filtering)
- **Action:** `route('dashboard')`
- **Auto-refresh:** Reload page saat submit
- **Responsive:** 6 column grid dengan mobile support

#### Dynamic Options:
```php
$allMesins  = LaporanHarian::distinct()->pluck('mesin_name')->sort()
$allLines   = LaporanHarian::distinct()->pluck('line')->sort()
```

### Filter Logic
```
Query Building:
├─ If NOT all_time:
│  ├─ whereYear('tanggal_laporan', $tahun)
│  └─ whereMonth('tanggal_laporan', $bulan)
├─ If mesin selected:
│  └─ where('mesin_name', $mesin)
└─ If line selected:
   └─ where('line', $line)
```

---

## 3. KPI CARDS SECTION

### Primary KPI Metrics (4 Cards)
Menampilkan 4 Key Performance Indicators utama:

#### Card 1: Availability
- **Label:** Availability
- **Metric:** `{{ number_format($availability, 2) }}%`
- **Calculation:** `100 - downtimePercent`
- **Insight:** Persentase waktu mesin operasional

#### Card 2: Downtime Percentage
- **Label:** Downtime
- **Metric:** `{{ number_format($downtimePercent, 2) }}%`
- **Calculation:** `(totalDowntimeMinutes / totalPlannedTime) × 100`
- **Insight:** Persentase downtime dari planned time

#### Card 3: Average MTTR (Mean Time To Repair)
- **Label:** Rata-rata MTTR
- **Metric:** `{{ number_format($avgMTTR, 2) }}` menit
- **Calculation:** `Average dari downtime_min (corrective maintenance only)`
- **Insight:** Rata-rata waktu untuk repair setelah breakdown

#### Card 4: Average MTBF (Mean Time Between Failures)
- **Label:** Rata-rata MTBF
- **Metric:** `{{ number_format($avgMTBFHours, 2) }}` menit
- **Calculation:** `Average MTBF dari active machines`
- **Insight:** Rata-rata waktu operasi antar breakdowns

### Card Styling
```css
.kpi-card {
  - Bootstrap card styling
  - Color-coded metrics
  - Bold typography for values
  - Responsive layout (md-3 = 25% width)
}
```

---

## 4. MACHINE PERFORMANCE METRICS

### Section Title
"🔧 Machine Performance" dengan icon speedometer

### Performance Cards (8 Cards Total)

#### Row 1: Operational Time Metrics

**Card 1: Planned Time**
- Icon: `bi-calendar-check` (Blue: #4361ee)
- Value: `totalPlannedTime / 60` (dalam jam)
- Formula: `daysInMonth × 24 × 60 × activeMachinesCount`
- Unit: jam

**Card 2: Down Time**
- Icon: `bi-exclamation-circle` (Red: #dc3545)
- Value: `totalDowntimeMinutes / 60` (dalam jam)
- Formula: Sum dari downtime_min (corrective maintenance)
- Unit: jam

**Card 3: Operation Time**
- Icon: `bi-play-circle` (Yellow: #ffc107)
- Value: `(Planned Time - Down Time) / 60`
- Formula: Calculated runtime
- Unit: jam

**Card 4: Breakdown**
- Icon: `bi-bug` (Pink: #e83e8c)
- Value: `totalBreakdown` (count)
- Formula: Count corrective maintenance dengan downtime > 0
- Unit: kejadian

#### Row 2: Maintenance Types

**Card 5: Corrective Maintenance**
- Icon: `bi-wrench` (Cyan: #17a2b8)
- Value: `totalCorrectiveMaint` (dalam jam)
- Description: Maintenance akibat breakdown
- Unit: jam

**Card 6: Preventive Maintenance**
- Icon: `bi-shield-check` (Green: #28a745)
- Value: `totalPreventiveMaint` (dalam jam)
- Description: Planned maintenance untuk prevent failure
- Unit: jam

**Card 7: Change Over Product**
- Icon: `bi-arrow-repeat` (Purple: #6610f2)
- Value: `totalChangeOver` (dalam jam)
- Description: Waktu untuk setup/change product
- Unit: jam

### Card Styling (Performance Cards)
```css
.performance-card {
  - Gradient top border (blue gradient)
  - Hover effect: lift up (-4px) + shadow
  - Icon color-coded per card
  - Smooth transitions (0.3s)
  - Text center alignment
}
```

---

## 5. MAINTENANCE ANALYSIS

### Summary Cards (3 Cards)

#### Card 1: Total Laporan
- **Title:** Total Laporan
- **Value:** `{{ $totalLaporan }}`
- **Color:** Primary Blue
- **Display:** Centered large number

#### Card 2: Total Downtime (Menit)
- **Title:** Total Downtime
- **Value:** `{{ number_format($totalDowntime) }}` menit
- **Color:** Secondary
- **Display:** Centered large number

#### Card 3: Jam Downtime
- **Title:** Jam Downtime
- **Value:** `{{ number_format($totalDowntime / 60, 2) }}` jam
- **Color:** Warning Orange
- **Display:** Centered large number

---

## 6. MACHINE RELIABILITY ANALYSIS

### MTBF (Mean Time Between Failures) Section

#### MTBF Statistics (3 Cards)

**Card 1: Average MTBF**
- Icon: `bi-graph-up`
- Value: `{{ number_format($avgMTBFHours, 2) }}` jam
- Calculation: Average dari all machines dengan failure_count > 0

**Card 2: Machines with Data**
- Icon: `bi-check-circle`
- Value: `{{ count($mtbfData) }}` mesin
- Description: Jumlah mesin yang memiliki data MTBF

**Card 3: View Full MTBF Dashboard**
- Icon: `bi-link-45deg`
- Action: Link ke `route('mtbf.index')`
- Button: "MTBF Dashboard"
- Purpose: Deep dive MTBF analysis

### Top 5 Most Reliable Machines
Header: Green background dengan "🏆 Top 5 Most Reliable Machines"

#### Table Columns:
| Column | Format | Description |
|--------|--------|-------------|
| Mesin | Text | Machine name + line name (small) |
| MTBF (hrs) | Number | Hours formatted to 2 decimals |
| Failures | Badge Red | Failure count |
| Status | Badge Color-Coded | Excellent/Good/Fair |

#### Status Logic:
```
Excellent: failureCount == 0 OR (failureCount == 1 AND downtimeHours < 1)
Good:      failureCount <= 2 AND downtimeHours < 4
Fair:      else
```

#### Data Sorting:
- Sorted by MTBF (highest first)
- Limited to top 5 machines

### Bottom 5 Worst Performing Machines
Header: Red background dengan "⚠️ Bottom 5 Worst Performing Machines"

#### Table Columns: (sama seperti Top 5)
| Column | Format | Description |
|--------|--------|-------------|
| Mesin | Text | Machine name + line name (small) |
| MTBF (hrs) | Number | Hours formatted to 2 decimals |
| Failures | Badge Red | Failure count |
| Status | Badge Color-Coded | Good/Fair/Poor |

#### Status Logic:
```
Good:  failureCount <= 2 AND downtimeHours < 4
Fair:  failureCount <= 5 AND downtimeHours < 12
Poor:  else
```

#### Data Sorting:
- Sorted by MTBF (lowest first)
- Limited to bottom 5 machines

---

## 7. BREAKDOWN & SPARE PARTS ANALYSIS

### Top 10 Mesin dengan Downtime Tertinggi
Header: Default styling

**Table Layout:**
```
| Mesin | Down Time (Jam) |
|-------|-----------------|
| data  | sum/60          |
```

**Data Source:**
```sql
SELECT mesin_name, SUM(downtime_min) as total_downtime
FROM laporan_harian
GROUP BY mesin_name
ORDER BY total_downtime DESC
LIMIT 10
```

### Top 7 Breakdown Per Line
Header: Default styling

**Table Layout:**
```
| Line | Total |
|------|-------|
| data | badge |
```

**Data Source:**
```sql
SELECT line, COUNT(*) as breakdown_count
FROM laporan_harian
GROUP BY line
ORDER BY breakdown_count DESC
LIMIT 7
```

### Top 7 Breakdown - Jenis Kerusakan
Header: Default styling

**Table Layout:**
```
| Kerusakan | Total |
|-----------|-------|
| data      | badge |
```

**Data Source:**
```sql
SELECT catatan, COUNT(*) as breakdown_count
FROM laporan_harian
WHERE catatan IS NOT NULL AND catatan != ''
GROUP BY catatan
ORDER BY breakdown_count DESC
LIMIT 7
```

### Monitoring Spare Part
Header: "Monitoring Spare Part (Bulan Tahun)" - Dynamic month/year

**Table Layout:**
```
| Spare Part | Total Qty |
|------------|-----------|
| data       | badge     |
```

**Data Source:**
```sql
SELECT sparepart, SUM(qty_sparepart) as total_qty
FROM laporan_harian
WHERE sparepart IS NOT NULL AND sparepart != ''
GROUP BY sparepart
ORDER BY total_qty DESC
LIMIT 10
```

**Features:**
- Responsive table
- Qty displayed in info badge
- Period-specific data (current bulan/tahun)

---

## 8. DATA VISUALIZATION

### Chart 1: Top 10 Mesin dengan Downtime Tertinggi
- **Type:** Bar Chart
- **Library:** Chart.js
- **Canvas ID:** `topDowntimeChart`
- **Container Height:** 400px

#### Data Processing:
```javascript
const topDowntimeMesinDataRaw = {downtime_min array}
const topDowntimeMesinData = topDowntimeMesinDataRaw.map(x => (x/60).toFixed(2))
// Convert menit to jam
```

#### Chart Options:
```javascript
{
  type: 'bar',
  responsive: true,
  maintainAspectRatio: false,
  backgroundColor: 'rgba(67, 97, 238, 0.8)',
  borderColor: '#4361ee',
  borderWidth: 2,
  borderRadius: 6,
  legend: false
}
```

### Chart 2: Machine Performance
- **Type:** Doughnut Chart
- **Library:** Chart.js
- **Canvas ID:** `machinePerformanceChart`
- **Container Height:** 400px

#### Data Processing:
```javascript
const machinePerformanceData = {count array}
const machinePerformanceLabels = {mesin_name array}
```

#### Chart Colors:
```javascript
[
  'rgba(67, 97, 238, 0.9)',      // Blue
  'rgba(107, 140, 255, 0.9)',    // Light Blue
  'rgba(52, 211, 153, 0.9)',     // Green
  'rgba(244, 63, 94, 0.9)',      // Red
  'rgba(255, 159, 28, 0.9)',     // Orange
  'rgba(99, 102, 241, 0.9)',     // Indigo
  'rgba(139, 92, 246, 0.9)',     // Purple
  'rgba(6, 182, 212, 0.9)',      // Cyan
  'rgba(34, 197, 94, 0.9)',      // Lime
  'rgba(59, 130, 246, 0.9)'      // Blue Alt
]
```

#### Chart Options:
```javascript
{
  type: 'doughnut',
  responsive: true,
  maintainAspectRatio: false,
  borderWidth: 2,
  borderColor: 'rgba(255, 255, 255, 0.8)',
  legend: { position: 'bottom' }
}
```

### Chart Initialization
```javascript
function waitForChart() {
  if (typeof Chart !== 'undefined') {
    initCharts()
  } else {
    setTimeout(waitForChart, 100)
  }
}
// Polling sampai Chart library tersedia
```

---

## 9. STYLING & UI/UX

### Color Scheme
```css
Primary Color: #4361ee (Blue)
Primary Light: #6b8cff
Primary Dark: (derived)
Secondary: (defined in layout)
Warning: (defined in layout)

Icon Colors:
├─ Card 2: #4361ee (Blue)
├─ Card 3: #dc3545 (Red)
├─ Card 4: #ffc107 (Yellow)
├─ Card 5: #e83e8c (Pink)
├─ Card 6: #17a2b8 (Cyan)
├─ Card 7: #28a745 (Green)
├─ Card 8: #6610f2 (Purple)
└─ Card 9: #fd7e14 (Orange)
```

### Filter Section Styling
```css
.filter-section {
  Background: Gradient blue (transparent)
  Padding: 25px
  Border-radius: 12px
  Border: 1px solid rgba(67, 97, 238, 0.2)
  Box styling: Subtle shadow and border
}

.filter-btn {
  Background: Linear gradient blue
  Hover: Darker gradient + lift effect (-2px) + shadow
  Border-radius: 0.625rem
}
```

### Performance Card Styling
```css
.performance-card {
  - Top border bar dengan gradient
  - Border: 1px solid #e8ecf1
  - Border-radius: 0.75rem
  - Box-shadow: 0 2px 8px rgba(0,0,0,0.04)
  
  Hover Effects:
  - Box-shadow: 0 8px 16px rgba(67,97,238,0.15)
  - Transform: translateY(-4px)
  - Border-color: primary-color
  
  Icon Animation:
  - Hover: Scale 1.15 + translateY(-2px) + opacity 1
}

.performance-value {
  Font-size: 2rem
  Font-weight: 700
  Color: primary-color
}
```

### Responsive Design
```css
Filter: col-md-2 (6 filters per row)
KPI Cards: col-md-3 (4 cards per row)
Performance Cards: col-md-3 (3-4 cards per row)
Tables: col-md-6 (2 per row) or col-md-4 (3 per row)
Charts: col-md-6 (2 per row)

Mobile Breakpoint: md (768px+)
Table: .table-responsive for horizontal scroll
```

### Badges & Indicators
```css
Status Badges:
├─ Success (Green): bg-success - Excellent/Good
├─ Info (Blue): bg-info - Good/Fair
├─ Warning (Yellow): bg-warning - Fair
└─ Danger (Red): bg-danger - Poor/Failures

Breakdown Count:
└─ Danger Badge (Red)

Spare Part Qty:
└─ Info Badge (Blue)
```

---

## 10. DATA FLOW & CALCULATIONS

### Data Source
**Model:** `LaporanHarian` (Daily Report)

**Key Fields:**
```php
- tanggal_laporan (date)
- mesin_name (string)
- line (string)
- jenis_pekerjaan (enum: corrective|preventive|change over product)
- downtime_min (integer)
- catatan (string - breakdown reason)
- sparepart (string)
- qty_sparepart (integer)
- user_id (foreign key)
```

### Calculation Formulas

#### 1. **Planned Time (Menit)**
```
if all_time:
  totalDays = (latestReport.date - earliestReport.date) + 1
  plannedTime = totalDays × 24 × 60 × activeMachinesCount
else:
  daysInMonth = Carbon::create(tahun, bulan)->daysInMonth
  plannedTime = daysInMonth × 24 × 60 × activeMachinesCount
```

#### 2. **Total Downtime (Menit)**
```
totalDowntime = SUM(downtime_min)
WHERE jenis_pekerjaan = 'corrective' 
AND downtime_min > 0
```

#### 3. **Downtime Percentage (%)**
```
downtimePercent = (totalDowntimeMinutes / totalPlannedTime) × 100
Capped at: 100%
```

#### 4. **Availability (%)**
```
availability = 100 - downtimePercent
```

#### 5. **Average MTTR (Menit)**
```
avgMTTR = AVG(downtime_min)
WHERE jenis_pekerjaan = 'corrective' 
AND downtime_min > 0
```

#### 6. **Maintenance Types (Jam)**
```
Corrective = SUM(downtime_min WHERE jenis_pekerjaan='corrective') / 60
Preventive = SUM(downtime_min WHERE jenis_pekerjaan='preventive') / 60
ChangeOver = SUM(downtime_min WHERE jenis_pekerjaan='change over product') / 60
```

#### 7. **Total Breakdown (Count)**
```
totalBreakdown = COUNT(*)
WHERE jenis_pekerjaan = 'corrective' 
AND downtime_min > 0
```

#### 8. **Average MTBF (Jam)**
```
Per Machine:
  MTBF = operationTime / failureCount
  
Average:
  avgMTBFHours = SUM(MTBF) / machineCountWithData
  
Source: Machine::calculateMTBF($tahun, $bulan)
        or Machine::calculateMTBFAllTime()
```

### Data Filtering Logic
```php
$baseQuery = function() {
  $q = LaporanHarian::query();
  
  if (!$showAllTime) {
    $q->whereYear('tanggal_laporan', $tahun)
      ->whereMonth('tanggal_laporan', $bulan);
  }
  
  if ($mesin) {
    $q->where('mesin_name', $mesin);
  }
  
  if ($line) {
    $q->where('line', $line);
  }
  
  return $q;
}
```

---

## 📊 SUMMARY DATA STRUCTURE

### Data Passed to View
```php
compact(
  'totalLaporan',              // Total reports count
  'totalDowntime',             // In minutes
  'totalDowntimeMinutes',      // In minutes
  'avgMTTR',                   // In minutes
  'avgMTBF',                   // Unused in view
  'availability',              // In percentage
  'downtimePercent',          // In percentage
  'topDowntimeMesin',         // Collection
  'topBreakdownLine',         // Collection
  'topBreakdownCatatan',      // Collection
  'spareParts',               // Collection
  'machinePerformance',       // Collection
  'totalPlannedTime',         // In minutes
  'totalBreakdown',           // Count
  'totalCorrectiveMaint',     // In hours
  'totalPreventiveMaint',     // In hours
  'totalChangeOver',          // In hours
  'bulan',                    // Month number
  'tahun',                    // Year
  'mesin',                    // Selected machine
  'line',                     // Selected line
  'allMesins',                // List
  'allLines',                 // List
  'mtbfData',                 // Array
  'avgMTBFHours',            // In hours
  'topReliableMachines',      // Top 5
  'worstMachines'             // Bottom 5
)
```

---

## 🔧 TECHNICAL DETAILS

### Framework & Dependencies
- **Framework:** Laravel (Blade Templating)
- **Chart Library:** Chart.js
- **Icon Library:** Bootstrap Icons (bi)
- **CSS Framework:** Bootstrap 5
- **Database:** Eloquent ORM

### File Size & Performance
- **View File:** ~750 lines
- **Custom CSS:** ~200 lines
- **JavaScript:** ~50 lines
- **Load Time:** Depends on data volume

### Known Issues & Fixes
1. **Month Duplication Bug (FIXED)**
   - Previous: `Carbon::createFromFormat('n', $m)->format('F')`
   - Fixed: Hardcoded month array to prevent formatting issues

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## 📝 RECOMMENDATIONS

### Potential Improvements
1. **Export Functionality** - Add CSV/PDF export for reports
2. **Real-time Updates** - Implement WebSocket for live data
3. **Advanced Analytics** - Add trend analysis charts
4. **Custom Date Range** - Allow specific date range selection
5. **Machine Comparison** - Side-by-side machine comparison tool
6. **Predictive Maintenance** - ML-based failure prediction
7. **Alert System** - Notification untuk anomalies
8. **Data Pagination** - For large datasets in tables

### Performance Optimization
1. Cache frequently accessed queries
2. Implement lazy loading for charts
3. Add database indexes on filter fields
4. Consider pagination for large result sets
5. Optimize image and icon loading

### Security Considerations
1. ✅ Role-based access control implemented
2. ✅ Permission checking enforced
3. Consider: CSRF token validation (should be auto-handled by Laravel)
4. Implement: Rate limiting on dashboard access
5. Add: Audit logging for data access

---

**Report Generated:** 5 Mei 2026  
**Last Updated:** [Auto-updated with view changes]  
**Status:** ✅ Production Ready
