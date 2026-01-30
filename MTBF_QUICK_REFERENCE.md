# MTBF Dashboard Integration - Quick Reference Card

## 🎯 What Was Done
Added MTBF (Mean Time Between Failures) metrics display to Department Head Dashboard

## 📍 Where to Find It
**Location**: Department Head Dashboard, after "Jam Downtime" summary card

## 🎨 What You'll See

### 1. Three Statistics Cards
```
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│ 📈 Average      │  │ ✅ Machines     │  │ 🔗 View Full    │
│ MTBF            │  │ with Data       │  │ Button          │
│ XXX.XX jam      │  │ N mesin         │  │ [MTBF Dash]     │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

### 2. Top 5 Reliable Machines (Green Header)
| Machine | MTBF (hrs) | Failures | Status |
|---------|-----------|----------|--------|
| Best #1 | 250.00 | 5 | ✅ Excellent |
| Best #2 | 180.50 | 8 | ✅ Good |
| ... | ... | ... | ... |

### 3. Bottom 5 Problem Machines (Red Header)
| Machine | MTBF (hrs) | Failures | Status |
|---------|-----------|----------|--------|
| Worst #1 | 8.50 | 48 | ⚠️ Poor |
| Worst #2 | 12.00 | 42 | ⚠️ Poor |
| ... | ... | ... | ... |

## 🔄 Data Source
- **Model**: Machine model
- **Method**: calculateMTBF()
- **Filter**: Corrective maintenance records only
- **Formula**: MTBF = Total Downtime (hours) ÷ Number of Failures

## ⚡ Quick Facts

| Info | Details |
|------|---------|
| **Files Modified** | 2 (Controller + View) |
| **Lines Added** | ~160 |
| **New Components** | 3 cards + 2 tables |
| **Responsive** | Yes (desktop, tablet, mobile) |
| **Performance Impact** | Minimal (~1 additional query) |
| **Data Freshness** | Real-time (calculated each load) |

## 🎯 Color Coding

### Reliability Status Badges
| Color | Status | MTBF Range | Action |
|-------|--------|-----------|--------|
| 🟢 Green | Excellent | ≥ 168 hrs | Monitor |
| 🔵 Blue | Good | ≥ 72 hrs | Maintain |
| 🟡 Yellow | Fair | 24-72 hrs | Review |
| 🔴 Red | Poor | < 24 hrs | Action |

## 💡 How to Use

### Quick Decision Making
1. **See Average MTBF** → Fleet health at a glance
2. **Review Top 5** → Best practices to replicate
3. **Check Bottom 5** → Urgent maintenance needed
4. **Decide** → Schedule maintenance/investigation
5. **Optional** → Click "MTBF Dashboard" for deep dive

### Monitoring Strategy
- **High Avg MTBF (>100 hrs)** → Fleet is healthy, continue current maintenance
- **Low Avg MTBF (<50 hrs)** → Fleet has issues, review maintenance approach
- **Bottom 5 < 24 hrs** → Urgent priority, schedule immediate attention

## 📊 Data Interpretation

### Average MTBF Values
- **> 168 hours (1 week)** → Excellent fleet health
- **72-168 hours (3-7 days)** → Good fleet health
- **24-72 hours (1-3 days)** → Fair, needs attention
- **< 24 hours** → Poor, urgent action needed

### Example Reading
- **Machine A: 240 hrs MTBF with 5 failures**
  - Interpretation: Machine fails on average every 10 days
  - Status: Excellent, maintain current maintenance
  
- **Machine B: 8 hrs MTBF with 48 failures**
  - Interpretation: Machine fails multiple times per day
  - Status: Poor, requires immediate investigation

## 🔗 Related Features

| Link | Destination | Purpose |
|------|-------------|---------|
| MTBF Dashboard | `/mtbf` | Detailed analysis of all machines |
| Machine Graph | `/machines/{id}/mtbf` | Individual machine analysis |
| Sidebar Link | Analytics → MTBF Analysis | Quick navigation |
| Machine Index | Machines page | Machine list with MTBF link |

## 📱 Device Support

| Device | Display | Behavior |
|--------|---------|----------|
| Desktop | 3-col cards, 2-col tables | Full layout |
| Tablet | 3-col cards, stacked tables | Responsive |
| Mobile | Full-width cards, scrollable tables | Mobile-friendly |

## ✅ Verification Checklist

- [ ] Logged in as department_head user
- [ ] On Dashboard (home page)
- [ ] Can see MTBF section below summary cards
- [ ] 3 statistics cards visible
- [ ] Top 5 machines table shows data
- [ ] Bottom 5 machines table shows data
- [ ] Status badges have correct colors
- [ ] "MTBF Dashboard" link works
- [ ] Data looks reasonable (no errors)

## 🔧 Technical Details

### Controller Method
```
DashboardController::departmentHeadDashboard()
```

### View File
```
resources/views/dashboard/department-head.blade.php
```

### Data Variables
- `$mtbfData` - All machines with MTBF
- `$avgMTBFHours` - Average MTBF
- `$topReliableMachines` - Top 5
- `$worstMachines` - Bottom 5

### Query Performance
- Single query to get machines
- Filtered calculation (only active machines)
- Array sorting (in-memory)
- **Total Impact**: Minimal

## 📞 Support Resources

| Resource | Location | Content |
|----------|----------|---------|
| **Dashboard Integration** | `MTBF_DASHBOARD_INTEGRATION.md` | Technical details |
| **Visual Guide** | `MTBF_DASHBOARD_VISUAL_GUIDE.md` | How it looks |
| **Quick Start** | `MTBF_DASHBOARD_QUICK_START.md` | Getting started |
| **Code Changes** | `CODE_CHANGES_MTBF_DASHBOARD.md` | Code examples |
| **Before & After** | `MTBF_BEFORE_AFTER_COMPARISON.md` | Comparison |
| **Complete Summary** | `MTBF_DASHBOARD_COMPLETE.md` | Full overview |

## 🚀 Quick Start (for Testing)

```bash
# 1. Start development server
php artisan serve

# 2. Open browser
http://127.0.0.1:8000

# 3. Login as department_head
# (username/password from database)

# 4. Navigate to Dashboard

# 5. Scroll down to find MTBF section
# Section title: "MTBF (Mean Time Between Failures) Analysis"

# 6. Verify:
# - 3 metrics cards at top
# - Top 5 machines table (green header)
# - Bottom 5 machines table (red header)
```

## ⚙️ Configuration

### To Modify MTBF Reliability Ranges
Edit: `resources/views/dashboard/department-head.blade.php`

Look for status badge logic (in @foreach loops):
```blade
if ($machine['mtbf_hours'] >= 168) {
    $badgeClass = 'bg-success';  // Change thresholds here
    $status = 'Excellent';
}
```

### To Add More Machines to Top/Bottom
Edit: `app/Http/Controllers/DashboardController.php`

Change the slice count:
```php
// Current: Top 5
$topReliableMachines = array_slice($mtbfData, 0, 5);

// To show top 10:
$topReliableMachines = array_slice($mtbfData, 0, 10);
```

## 📊 FAQ

**Q: Why doesn't Machine X show in Top/Bottom?**
A: Only machines with corrective failures are included. Check if machine has failure records.

**Q: What if MTBF is 0?**
A: Machines with no failures show "Tidak ada data" and aren't ranked.

**Q: How often is data updated?**
A: On every page load (real-time from database).

**Q: Can I export this data?**
A: Yes, go to MTBF Dashboard page for export options.

**Q: Are other roles affected?**
A: No, only department_head sees this. Can add to other roles if needed.

---

**Status**: ✅ Ready to Use  
**Last Updated**: January 30, 2026  
**Version**: 1.0
