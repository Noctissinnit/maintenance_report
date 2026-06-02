# Laporan Harian Import Guide

## Penyesuaian Struktur Import Excel

Import Excel untuk **Laporan Harian** telah disesuaikan dengan struktur database terbaru. File ini menjelaskan format dan panduan penggunaan.

---

## Format Header Excel

Kolom yang diperlukan (dengan heading row):

| Kolom | Tipe | Status | Format/Deskripsi |
|-------|------|--------|-----------------|
| **tanggal_laporan** | string | ✅ Required | Format: D/M/Y, Y-M-D, atau D-M-Y<br/>Contoh: `01/06/2026` atau `2026-06-01` |
| machine_name | string | Optional | Nama mesin (harus ada di database)<br/>Contoh: `Mesin Grinder` |
| line_name | string | Optional | Nama line/departemen (harus ada di database)<br/>Contoh: `Line A` |
| **line_status** ✨ | string | Optional | Nilai: `on` atau `off` (default: `on`)<br/>Format alternatif: yes/no, true/false, 1/0, ya/tidak, hidup/mati |
| spare_part_name | string | Optional | Nama spare part (harus ada di database)<br/>Contoh: `Bearing SKF 6205` |
| qty_spare_part | numeric | Optional | Jumlah spare part yang digunakan<br/>Contoh: `2` atau `3.5` |
| spare_part_notes | string | Optional | Komentar/catatan untuk spare part |
| jenis_pekerjaan | string | Optional | Nilai: `corrective`, `preventive`, atau `change over product` |
| scope | string | Optional | Nilai: `Electrical`, `Mechanical`, `Utility`, atau `Building` |
| start_time | string | Optional | Format: HH:MM atau HH:MM:SS<br/>Contoh: `14:30` atau `14:30:00` |
| end_time | string | Optional | Format: HH:MM atau HH:MM:SS<br/>Contoh: `16:45` atau `16:45:00` |
| downtime_min | numeric | Optional | Durasi downtime dalam menit<br/>Contoh: `120` (2 jam) |
| notes | string | Optional | Catatan/deskripsi detail pekerjaan |
| status | string | Optional | Nilai: `completed` atau `pending` (default: `completed`) |
| report_type | string | Optional | Nilai: `daily`, `weekly`, atau `monthly` (default: `daily`) |

✨ = Field baru yang ditambahkan

---

## Fitur Utama

### 1. **Automatic Downtime Calculation**
- Untuk jenis pekerjaan `corrective`, `preventive`, `change over product`:
  - Downtime otomatis dihitung dari `start_time` dan `end_time`
  - Jika tidak ada `start_time`/`end_time`, gunakan nilai `downtime_min` dari Excel
  - Nilai downtime selalu positif (menggunakan absolute value)

### 2. **Line Status Tracking** ✨
- Field `line_status` menentukan apakah downtime line dihitung atau tidak
- **on**: Line dihitung dalam statistik downtime
- **off**: Line tidak dihitung dalam statistik downtime
- Mendukung berbagai format input:
  - English: `on`, `off`, `yes`, `no`, `true`, `false`, `1`, `0`
  - Indonesian: `ya`, `tidak`, `hidup`, `mati`

### 3. **Flexible Date & Time Parsing**
- Tanggal mendukung format:
  - `D/M/Y` (01/06/2026)
  - `Y-M-D` (2026-06-01)
  - `D-M-Y` (01-06-2026)
  - `D.M.Y` (01.06.2026)
  - `Y/M/D` (2026/06/01)
  - Excel serial date format

- Waktu mendukung format:
  - `HH:MM` (14:30)
  - `HH:MM:SS` (14:30:00)
  - European format dengan koma (14,30)
  - European format dengan titik (14.30)
  - Excel serial time format

### 4. **Data Validation**
- Machine, Line, Spare Part harus ada di database
- Jenis pekerjaan harus sesuai enum yang valid
- Tanggal dan waktu di-validate sebelum import
- Nilai numeric di-validate (tidak boleh negatif)

---

## Contoh Data Excel

```
tanggal_laporan | machine_name        | line_name | line_status | jenis_pekerjaan | scope      | start_time | end_time | notes
01/06/2026      | Mesin Grinder       | Line A    | on          | corrective      | Mechanical | 14:30      | 16:45    | Bearing replacement
02/06/2026      | Mesin Bubut         | Line B    | off         | preventive      | Electrical | 08:00      | 08:30    | Maintenance rutin
03/06/2026      | Mesin Drilling      | Line C    | on          | change over product | Utility | 12:00      | 13:15    | Setup for new product
```

---

## Error Handling

Sistem import akan menampilkan error jika:

1. **tanggal_laporan tidak valid**: Format tanggal tidak sesuai
2. **Machine tidak ditemukan**: Nama mesin tidak ada di database
3. **Line tidak ditemukan**: Nama line tidak ada di database
4. **Spare Part tidak ditemukan**: Nama spare part tidak ada di database
5. **Format waktu tidak valid**: Format HH:MM/HH:MM:SS tidak sesuai
6. **Nilai numeric invalid**: Qty atau downtime tidak berupa angka

---

## Best Practices

1. **Validasi data sebelum import**:
   - Pastikan semua machine, line, dan spare part sudah ada di database
   - Gunakan nama yang konsisten

2. **Format konsisten**:
   - Pilih satu format tanggal dan gunakan konsisten di seluruh file
   - Gunakan format waktu HH:MM atau HH:MM:SS (tidak campur)

3. **Field yang wajib**:
   - `tanggal_laporan` adalah satu-satunya field yang truly required
   - Field lainnya opsional, tapi recommended untuk data lengkap

4. **Line Status untuk filtering**:
   - Gunakan `off` untuk memisahkan line yang tidak perlu dihitung downtime
   - Berguna untuk production lines vs utility lines

---

## Field Mapping Terbaru

Pemetaan antara Excel columns dan database fields:

| Excel Column | Database Field | Type | Notes |
|--------------|----------------|------|-------|
| tanggal_laporan | tanggal_laporan | DATE | Required |
| machine_name | mesin_name, machine_id | STRING, FK | Machine lookup |
| line_name | line, line_id | STRING, FK | Line lookup |
| line_status | line_status | ENUM(on,off) | **NEW** |
| spare_part_name | sparepart, spare_part_id | STRING, FK | SparePart lookup |
| qty_spare_part | qty_sparepart | INT | Sum for reports |
| spare_part_notes | komentar_sparepart | TEXT | Additional notes |
| jenis_pekerjaan | jenis_pekerjaan | STRING | Enum validation |
| scope | scope | STRING | Enum validation |
| start_time | start_time | DATETIME | Combined with date |
| end_time | end_time | DATETIME | Combined with date |
| downtime_min | downtime_min | INT | Auto-calculated or manual |
| notes | catatan | TEXT | Description |
| status | status | STRING | completed/pending |
| report_type | tipe_laporan | STRING | daily/weekly/monthly |

---

## Version History

- **v2.0** (June 2, 2026): Added `line_status` field support
- **v1.0**: Initial import with basic fields

---

## Support

Untuk bantuan lebih lanjut, lihat:
- `app/Imports/LaporanHarianImport.php` - Kode implementasi
- `app/Models/LaporanHarian.php` - Model definisi
- Database migrations untuk struktur terbaru
