# Fitur Import Excel Karyawan

## Deskripsi
Fitur ini memungkinkan administrator untuk import data karyawan secara massal dari file Excel (.xlsx, .xls) atau CSV.

## File yang Dibuat/Dimodifikasi

### 1. Files Baru Dibuat:
- **app/Imports/EmployeesImport.php** - Class untuk import data dari Excel
- **app/Http/Requests/ImportEmployeeRequest.php** - Request validation class
- **resources/views/employee/import.blade.php** - View untuk form import

### 2. Files Dimodifikasi:
- **app/Http/Controllers/EmployeeController.php** - Tambah method: importForm(), import(), template()
- **routes/web.php** - Tambah 3 routes untuk import
- **resources/views/employee/index.blade.php** - Tambah tombol import

## Cara Menggunakan

### 1. Download Template
- Klik tombol "Download Template Excel" di halaman import
- Atau buka: `/employees/template`

### 2. Isi Data di Excel
Format yang dibutuhkan:
| name | email | password |
|------|-------|----------|
| Budi Santoso | budi@example.com | pass12345 |
| Ani Wijaya | ani@example.com | pass54321 |

**Ketentuan:**
- `name`: Wajib, max 255 karakter
- `email`: Wajib, format email valid, harus unik
- `password`: Opsional (jika kosong, default: password123)

### 3. Upload File
- Klik tombol "Import Excel" di halaman daftar karyawan
- Pilih file Excel yang sudah diisi
- Klik "Import Data"

### 4. Hasil Import
Sistem akan:
- ✓ Menambahkan karyawan baru yang valid
- ✓ Melewati email yang sudah ada (duplikat)
- ✓ Menampilkan summary hasil import
- ✓ Secara otomatis memberikan role 'operator' ke setiap karyawan baru

## Format File Excel

### Header (Baris 1):
```
A1: name
B1: email
C1: password
```

### Data (Baris 2+):
```
A2: Nama Karyawan
B2: email@example.com
C2: password123
```

## Validasi

### Server-side validation:
- Email harus format email yang valid
- Email harus unik (tidak boleh duplikat dengan yang sudah ada)
- Name wajib diisi
- File harus berformat .xlsx, .xls, atau .csv
- File maksimal 10MB

### Error Handling:
- Baris kosong akan dilewati (skip)
- Email duplikat akan ditampilkan di error messages
- Format email tidak valid akan ditolak
- Sistem tetap melanjutkan import meski ada error di beberapa baris

## Routes yang Ditambahkan

1. `GET /employees/import-form` → Tampilkan form import
2. `POST /employees/import` → Proses import file
3. `GET /employees/template` → Download template Excel

## Permissions

Fitur ini dilindungi dengan middleware:
```php
Route::middleware(['can:manage_employees'])->group(...)
```

Hanya user dengan permission `manage_employees` yang dapat mengakses.

## Catatan Penting

1. **Password Default**: Jika password tidak diisi di Excel, sistem menggunakan default `password123`
2. **Role Otomatis**: Semua karyawan yang diimport akan mendapatkan role 'operator' secara otomatis
3. **Email Unik**: Email harus berbeda untuk setiap karyawan
4. **No Data Loss**: Data yang sudah ada tidak akan dihapus
5. **Limit File**: Maksimal 10MB untuk menghindari timeout

## Teknologi yang Digunakan

- **maatwebsite/excel** - Library untuk handle Excel files
- **PhpOffice/PhpSpreadsheet** - Library untuk generate template Excel
- **Laravel Validation** - Validasi data
- **Laravel Request Classes** - Form request validation
