# Template Import Guide - Sistem Laporan Maintenance

Panduan lengkap untuk menggunakan fitur template download dan import data.

## Daftar Template Tersedia

### 1. Template Mesin (Machines)
**File:** `template_mesin_YYYY-MM-DD.xlsx`

**Kolom yang diperlukan:**
- **Nama Mesin** (Required) - Nama mesin yang akan ditambahkan
- **Line** (Required) - Nama line/departemen tempat mesin berada
- **Deskripsi** (Optional) - Keterangan detail tentang mesin
- **Status** (Required) - Status mesin: `active` atau `inactive`

**Contoh format:**
```
Nama Mesin | Line | Deskripsi | Status
Mesin Pembanding Dimensi | Inspection | Mesin untuk inspeksi dimensi | active
Motor Penggerak | Assembly | Motor penggerak produksi | active
```

### 2. Template Line (Production Lines)
**File:** `template_line_YYYY-MM-DD.xlsx`

**Kolom yang diperlukan:**
- **Nama Line** (Required) - Nama departemen atau line produksi
- **Deskripsi** (Optional) - Keterangan detail tentang line
- **Status** (Required) - Status line: `active` atau `inactive`

**Contoh format:**
```
Nama Line | Deskripsi | Status
Inspection | Departemen inspeksi kualitas | active
Assembly | Departemen perakitan produk | active
Packing | Departemen pengemasan produk | active
```

### 3. Template Spare Part
**File:** `template_spare_part_YYYY-MM-DD.xlsx`

**Kolom yang diperlukan:**
- **Nama Spare Part** (Required) - Nama komponen spare part
- **Kode** (Required) - Kode unik spare part
- **Deskripsi** (Optional) - Keterangan detail tentang spare part
- **Stok** (Required) - Jumlah stok awal (angka)
- **Status** (Required) - Status: `active` atau `inactive`

**Contoh format:**
```
Nama Spare Part | Kode | Deskripsi | Stok | Status
V-Belt Taper Lock | VBT-001 | Sabuk transmisi V-Belt | 10 | active
Bearing SKF | BRG-002 | Bearing standar industri | 15 | active
Motor Elektrik | MTR-003 | Motor 3 phase 2.2kW | 5 | active
```

## Langkah-langkah Import

### Untuk Data Mesin:
1. Buka menu **Admin → Produksi → Data Mesin**
2. Klik tombol **Template** untuk download template XLSX
3. Buka file XLSX dengan Excel
4. Isi data sesuai format yang tersedia di bawah header
5. Simpan file (tetap format XLSX)
6. Kembali ke halaman Data Mesin
7. Klik tombol **Import**
8. Upload file XLSX yang sudah diisi
9. Sistem akan validasi dan memasukkan data

### Untuk Data Line:
1. Buka menu **Admin → Produksi → Data Line**
2. Klik tombol **Template** untuk download template XLSX
3. Isi data sesuai format
4. Simpan file sebagai XLSX
5. Klik tombol **Import** atau buat secara manual melalui **Tambah Line**
6. Upload file XLSX

### Untuk Data Spare Part:
1. Buka menu **Admin → Inventory → Spare Part**
2. Klik tombol **Template** untuk download template XLSX
3. Isi data sesuai format
4. Simpan file sebagai XLSX
5. Klik tombol **Import**
6. Upload file XLSX

## Format File XLSX yang Benar

### Encoding & Format
- Format: **Microsoft Excel 2007+ (.xlsx)**
- Encoding: **UTF-8**
- Sheet name: Sesuai dengan nama default yang ada

### Aturan Pengisian Data

#### Status
- Gunakan lowercase: `active` atau `inactive`
- Jangan gunakan: `Active`, `ACTIVE`, `Aktif`, dll.

#### Nama
- Tidak boleh kosong
- Maksimal 255 karakter
- Hindari karakter khusus yang tidak perlu

#### Angka (untuk Stok)
- Gunakan format angka (0-9)
- Tidak boleh menggunakan tanda khusus
- Contoh: `10`, `25`, `100`

#### Deskripsi
- Opsional (boleh kosong)
- Bisa menggunakan multiple line dalam cell
- Support untuk karakter Indonesia

## Tips Menggunakan Excel

### Jika Membuka Template di Excel:
1. Download template XLSX
2. Buka langsung dengan Excel
3. Isi data di bawah header yang sudah disediakan
4. Pastikan tidak menambah baris header baru
5. Simpan dengan **Ctrl+S** (tetap format XLSX)

### Menghindari Error:
- Jangan mengubah nama header kolom
- Jangan menambah kolom baru
- Pastikan tidak ada spasi di awal/akhir data
- Hapus baris kosong sebelum upload
- Pastikan jumlah baris sesuai dengan data yang ingin diimport

## Daftar Error Umum & Solusi

| Error | Penyebab | Solusi |
|-------|---------|--------|
| "Invalid file format" | File bukan XLSX atau format salah | Pastikan download template dan isi langsung, lalu simpan sebagai XLSX |
| "Required field missing" | Ada kolom yang kosong (yang required) | Pastikan semua kolom yang diperlukan terisi |
| "Invalid status value" | Status bukan 'active' atau 'inactive' | Gunakan hanya: active atau inactive (lowercase) |
| "Duplicate entry" | Data sudah ada di database | Cek database atau gunakan nama berbeda |
| "Line not found" | Nama line di machines tidak sesuai | Pastikan line sudah ada terlebih dahulu |

## Urutan Import yang Direkomendasikan

Jika melakukan import data baru:
1. **Pertama**: Import Data Line
2. **Kedua**: Import Data Mesin (karena referensi ke Line)
3. **Ketiga**: Import Data Spare Part (tidak bergantung pada data lain)

## Download Template

Akses link berikut untuk langsung download template (perlu login):
- **Mesin**: `/templates/download-machine` (template_mesin_YYYY-MM-DD.xlsx)
- **Line**: `/templates/download-line` (template_line_YYYY-MM-DD.xlsx)
- **Spare Part**: `/templates/download-spare-part` (template_spare_part_YYYY-MM-DD.xlsx)

---

*Dokumentasi ini berlaku untuk Sistem Laporan Maintenance v1.0*
*Format: Excel 2007+ (.xlsx) dengan PhpSpreadsheet*
