# MTBF Display on Department Head Dashboard - Visual Guide

## Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────┐
│                  Department Head Dashboard                      │
└─────────────────────────────────────────────────────────────────┘

[Filter Section - Bulan | Tahun | Mesin | Line | Filter Button]

[Alert Box - Department Head Information]

┌──────────────────────┬──────────────────────┬──────────────────────┐
│  Availability %      │  Downtime %          │  Rata-rata MTTR      │
│  Machine KPI Cards                          │  Rata-rata MTBF      │
└──────────────────────┴──────────────────────┴──────────────────────┘

┌──────────────────────┬──────────────────────┬──────────────────────┬──────────────────────┐
│  Planned Time        │  Down Time           │  Operation Time      │  Breakdown           │
│  Performance Cards (Machine Performance)                                                 │
└──────────────────────┴──────────────────────┴──────────────────────┴──────────────────────┘

┌──────────────────────┬──────────────────────┬──────────────────────┬──────────────────────┐
│  Corrective Maint    │  Preventive Maint    │  Predictive          │  Change Over Product │
│  More Performance Cards                                                                  │
└──────────────────────┴──────────────────────┴──────────────────────┴──────────────────────┘

┌──────────────────────┬──────────────────────┬──────────────────────┐
│  Total Laporan       │  Total Downtime      │  Jam Downtime        │
│  Summary Cards                                                      │
└──────────────────────┴──────────────────────┴──────────────────────┘

╔══════════════════════════════════════════════════════════════════╗
║  NEW MTBF SECTION STARTS HERE                                   ║
╚══════════════════════════════════════════════════════════════════╝

📊 MTBF (Mean Time Between Failures) Analysis

┌──────────────────────┬──────────────────────┬──────────────────────┐
│  📈 Average MTBF     │  ✅ Machines         │  🔗 View Full        │
│  XXX.XX jam          │  5 mesin             │  [MTBF Dashboard]    │
│  Performance Card    │  Performance Card    │  Performance Card    │
└──────────────────────┴──────────────────────┴──────────────────────┘

┌────────────────────────────────────────────────────────────────┐
│  🏆 Top 5 Most Reliable Machines                              │
├────────────────────────────────────────────────────────────────┤
│  Mesin Name          │ MTBF (hrs) │ Failures │ Status         │
├────────────────────────────────────────────────────────────────┤
│  Machine A (Line 1)  │ 240.50     │ 5        │ ✅ Excellent   │
│  Machine B (Line 2)  │ 180.25     │ 8        │ ✅ Excellent   │
│  Machine C (Line 1)  │ 96.75      │ 12       │ 🔵 Good        │
│  Machine D (Line 3)  │ 72.50      │ 15       │ 🔵 Good        │
│  Machine E (Line 2)  │ 48.00      │ 20       │ 🟡 Fair        │
└────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────┐
│  ⚠️ Bottom 5 Worst Performing Machines                         │
├────────────────────────────────────────────────────────────────┤
│  Mesin Name          │ MTBF (hrs) │ Failures │ Status         │
├────────────────────────────────────────────────────────────────┤
│  Machine X (Line 4)  │ 8.50       │ 48       │ 🔴 Poor        │
│  Machine Y (Line 3)  │ 12.25      │ 42       │ 🔴 Poor        │
│  Machine Z (Line 2)  │ 16.00      │ 35       │ 🔴 Poor        │
│  Machine W (Line 1)  │ 20.50      │ 30       │ 🟡 Fair        │
│  Machine V (Line 4)  │ 24.00      │ 25       │ 🟡 Fair        │
└────────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════

[Existing Content Below...]
- Top 10 Mesin dengan Downtime Tertinggi
- Top 7 Breakdown Per Line
- etc.
```

## Color Coding System

### For Most Reliable Machines
| Badge Color | Status     | MTBF Value | Meaning |
|-------------|------------|-----------|---------|
| 🟢 Green    | Excellent  | ≥ 168 hrs | Highly reliable |
| 🔵 Blue     | Good       | ≥ 72 hrs  | Reliable |
| 🟡 Yellow   | Fair       | < 72 hrs  | Needs attention |

### For Worst Performing Machines
| Badge Color | Status     | MTBF Value | Meaning |
|-------------|------------|-----------|---------|
| 🔴 Red      | Poor       | < 24 hrs  | Frequent failures |
| 🟡 Yellow   | Fair       | 24-72 hrs | Needs maintenance |
| 🔵 Blue     | Good       | ≥ 72 hrs  | Acceptable |

## Interactive Elements

### Statistics Cards
- **Average MTBF Card**: Shows average reliability across fleet
- **Machines with Data**: Tells how many machines have failure records
- **View Full Button**: Direct link to detailed MTBF analysis page at `/mtbf`

### Tables
- **Most Reliable**: Shows best performing machines
- **Worst Performing**: Shows problematic machines needing attention
- Both tables show: Machine name, line, MTBF hours, failure count, status badge
- Click on "MTBF Dashboard" link to see all machines ranked by reliability

## Data Interpretation

### What the Numbers Mean
- **Average MTBF**: Average time between failures across all machines
  - Higher = Better (machines are more reliable)
  - Example: MTBF of 168 hours = machine fails on average every 7 days
  
- **Machines with Data**: Count of machines that have corrective maintenance records
  - Shows how much failure data is available
  - Machines with no failures not included
  
- **Top 5 Most Reliable**: Fleet's best performers
  - Focus on preventive maintenance for these
  - Use as benchmark for other machines
  
- **Bottom 5 Worst**: Machines needing immediate attention
  - Review maintenance procedures
  - May need overhaul or replacement
  - Increase monitoring frequency

## How to Use This Information

### For Department Head Monitoring
1. **Daily Check**: Review top and bottom machines
2. **Identify Trends**: Compare with previous periods
3. **Take Action**: 
   - Schedule maintenance for poor machines
   - Investigate what makes top machines reliable
   - Adjust preventive maintenance schedules
4. **Plan Budget**: Focus resources on worst performers

### For Decision Making
- **Maintenance Priority**: Focus on bottom 5 machines
- **Staffing**: Allocate technicians to problem areas
- **Equipment**: Plan replacement for persistently poor machines
- **Training**: Learn from top machines and replicate best practices

## Quick Navigation
- **See Detailed Analysis**: Click "MTBF Dashboard" button
  - Go to `/mtbf` for complete fleet analysis
  - View individual machine details
  - See corrective maintenance history
  
- **View Machine Details**: Go to Machines page
  - Click graph icon on any machine
  - See detailed MTBF for that machine
  - Review full maintenance history

## Example Scenarios

### Scenario 1: High Average MTBF (> 100 hours)
**Indication**: Fleet is performing well
**Action**: Continue current maintenance approach, monitor for changes

### Scenario 2: Low Average MTBF (< 50 hours)
**Indication**: Fleet has reliability issues
**Action**: Review maintenance procedures, increase preventive maintenance, consider equipment replacement

### Scenario 3: Bottom 5 Machines < 24 hours MTBF
**Indication**: Machines are failing too frequently
**Action**: Urgent - Schedule immediate maintenance review and investigation

### Scenario 4: Top 5 Machines > 168 hours MTBF
**Indication**: These machines are excellent
**Action**: Document their maintenance procedures as best practices
