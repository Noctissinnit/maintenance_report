# MTBF Feature - Quick Testing Guide

## Quick Start
1. Login with any user account
2. Look for "MTBF Analysis" in the sidebar under **Analytics** section
3. Click to view all machines ranked by reliability

## Test Scenarios

### Scenario 1: View MTBF Dashboard
**Path:** `/mtbf`
**Expected:**
- Page title: "MTBF Analysis (Mean Time Between Failures)"
- 4 statistics cards at top (Total Machines, Total Failures, Total Downtime, Average MTBF)
- Table with all active machines sorted by MTBF (highest first)
- Each row shows: Machine Name, Line, Failures count, Total Downtime, MTBF value, Reliability status
- View button for each machine

### Scenario 2: View Machine MTBF Details
**Path:** `/machines/{id}/mtbf` (e.g., `/machines/1/mtbf`)
**Expected:**
- Machine name and details in header
- 4 statistics cards: Total Failures, Total Downtime, MTBF (Hours), MTBF (Days)
- Maintenance summary showing counts by type (Corrective, Preventive, Modifikasi, Utility)
- Table with corrective maintenance history (paginated)
- Each history row: Date, Time Start, Time End, Downtime, Notes
- MTBF calculation explanation at bottom

### Scenario 3: Access from Machine Index
**Path:** `/machines` (Machine list page)
**Expected:**
- New "View MTBF" button (graph icon) appears in Actions column
- Click button to navigate to `/machines/{id}/mtbf`

### Scenario 4: Reliability Status Colors
**Expected on MTBF Dashboard:**
- 🟢 Green badge "Excellent" = MTBF ≥ 168 hours (≥1 week)
- 🔵 Blue badge "Good" = MTBF ≥ 72 hours (≥3 days)
- 🟡 Yellow badge "Fair" = MTBF ≥ 24 hours (≥1 day)
- 🔴 Red badge "Poor" = MTBF < 24 hours

### Scenario 5: MTBF Calculation Example
**Given:**
- Machine has 5 corrective maintenance records
- Total downtime = 300 minutes (5 hours)
- Expected MTBF = 5 hours ÷ 5 failures = 1.00 hour

**Verify on Details Page:**
- Failures count: 5
- Total Downtime: 5.00 hrs
- MTBF (Hours): 1.00
- MTBF (Days): 0.04
- Status: Poor (red badge)

## Permission Testing

### Who Can Access MTBF?
- ✅ Admin (all permissions)
- ✅ Department Head (view_own_laporan)
- ✅ Supervisor (view_own_laporan)
- ✅ Operator (view_own_laporan)

### Who Cannot Access?
- ❌ Users without `view_own_laporan` permission
- Expected: 403 Forbidden error

## Edge Cases to Test

### Case 1: Machine with No Failures
**Expected:**
- Failures count: 0
- MTBF: N/A (shown in details)
- Status: Excellent (no failures = no downtime)
- Maintenance summary: Shows 0 for corrective

### Case 2: Machine with Single Failure
**Expected:**
- Failures count: 1
- MTBF = Total downtime in hours
- Corrective history: Shows only 1 record

### Case 3: Very Long Downtime
**Expected:**
- Very low MTBF value (small number of hours)
- Status: Poor (red)
- Warning level: Frequent failures indicated

### Case 4: Machine with Preventive Only
**Expected:**
- Corrective failures: 0
- MTBF: N/A (no corrective failures to calculate)
- Maintenance summary shows preventive count

## Data Validation

### On Index Page
- [ ] Machines are sorted by MTBF (highest first)
- [ ] Statistics cards show correct totals
- [ ] Failure counts match actual corrective records
- [ ] Downtime values are realistic

### On Detail Page
- [ ] Machine details match Machine record
- [ ] Corrective history only shows corrective type records
- [ ] Downtime values match what's stored
- [ ] Pagination works (if > 10 records)
- [ ] Calculation explanation shows correct formula

## Sidebar & Navigation Testing

### Check Sidebar
- [ ] "MTBF Analysis" appears under "Analytics" section
- [ ] Icon is speedometer icon (bi-speedometer2)
- [ ] Link points to `/mtbf`
- [ ] Active state highlights when on MTBF page

### Check Breadcrumb/Back Links
- [ ] "Back to MTBF List" on detail page
- [ ] "Back to Machines" on index page

## URL Routes Testing

```
GET /mtbf → MTBFController@index → mtbf.index
GET /machines/{id}/mtbf → MTBFController@show → mtbf.show
```

**Verify with:**
```bash
php artisan route:list | grep mtbf
```

Expected output:
```
GET|HEAD  mtbf ............................ mtbf.index ║ MTBFController@index
GET|HEAD  machines/{machine}/mtbf ........ mtbf.show ║ MTBFController@show
```

## Performance Testing

### Load Index Page with Many Machines
- [ ] Page loads in < 2 seconds
- [ ] No database errors
- [ ] All calculations complete
- [ ] Sorting works correctly

### Load Detail Page with Long History
- [ ] Page loads quickly
- [ ] Pagination works
- [ ] Can navigate between pages
- [ ] All records display correctly

## Browser Testing

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (if available)
- [ ] Edge (latest)
- [ ] Mobile browser (iPhone/Android)

**Check:**
- [ ] Layout is responsive
- [ ] Tables display correctly
- [ ] Buttons are clickable
- [ ] Modals (for long notes) work
- [ ] Charts/badges display correctly

## Sample Data Query

To verify calculations with database, run:

```sql
-- Get machine failures
SELECT 
    m.id,
    m.nama_mesin,
    COUNT(l.id) as failure_count,
    SUM(l.downtime_min) as total_downtime_min,
    SUM(l.downtime_min) / 60 as total_downtime_hours
FROM machines m
LEFT JOIN laporan_harian l ON m.id = l.machine_id AND l.jenis_pekerjaan = 'corrective'
GROUP BY m.id, m.nama_mesin
ORDER BY total_downtime_hours DESC;
```

## Known Limitations

1. MTBF is based ONLY on corrective maintenance
2. Other maintenance types don't count toward MTBF
3. Zero failures = N/A MTBF (not calculated)
4. No historical trending (shows current state only)
5. No CSV/PDF export (can add later)

## Troubleshooting

### MTBF Analysis not showing in sidebar
- [ ] Verify user has `view_own_laporan` permission
- [ ] Check roles in database: `model_has_permissions` table
- [ ] Clear cache: `php artisan cache:clear`

### Route not found error
- [ ] Run: `php artisan route:cache`
- [ ] Verify MTBFController exists at `app/Http/Controllers/`
- [ ] Check web.php imports include MTBFController

### Permission denied (403)
- [ ] Verify logged-in user
- [ ] Check user roles and permissions
- [ ] Verify permission was assigned correctly

### No machines display
- [ ] Check machines exist: `SELECT * FROM machines WHERE status = 'active';`
- [ ] Verify at least one machine is in database
- [ ] Check line relationships are set

## Success Criteria

✅ Feature is complete when:
1. MTBF Analysis visible in sidebar
2. Can navigate to MTBF dashboard
3. Dashboard shows all machines with MTBF calculated
4. Can click through to machine details
5. Detail page shows maintenance history
6. Calculation matches expected formula
7. Reliability status badges display correctly
8. No permission errors for authorized users
9. No permission access for unauthorized users
10. All links work and navigate correctly

---

**Last Updated:** 2024
**Status:** Ready for Testing
