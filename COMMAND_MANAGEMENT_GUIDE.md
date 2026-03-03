# Fitur Command Management System

## Overview
Sistem Command Management memungkinkan Department Head untuk memberikan instruksi/command kepada Supervisor dengan rencana aksi yang jelas, dan Supervisor dapat melihat serta memperbarui status dari command tersebut.

## Fitur Utama

### 1. Department Head - Membuat Command
Department Head dapat membuat command baru dengan mengakses menu:
- **URL**: `/commands/create`
- **Route**: `commands.create`

**Field yang diperlukan:**
- **Judul Command** (required) - Judul/nama dari command
- **Command/Instruksi** (required) - Deskripsi instruksi yang ingin diberikan
- **Action Plan** (required) - Rencana aksi atau langkah-langkah untuk menyelesaikan command
- **Supervisor** (required) - Pilih supervisor yang akan menangani command ini
- **Tanggal Jatuh Tempo** (optional) - Deadline untuk menyelesaikan command

**Button Actions:**
- `Buat Command` - Menyimpan command baru
- `Batal` - Kembali ke daftar command

---

### 2. Department Head - Melihat Daftar Command Mereka
Department Head dapat melihat semua command yang telah dibuat:
- **URL**: `/commands/my-list`
- **Route**: `commands.list-department-head`

**Fitur:**
- Filter berdasarkan status (Pending, In Progress, Completed, Cancelled)
- Menampilkan informasi: Judul, Supervisor, Status, Tanggal Jatuh Tempo, Tanggal Dibuat
- Action buttons:
  - **View** - Melihat detail command
  - **Edit** - Edit command (hanya jika status masih Pending)
  - **Delete** - Hapus command (hanya jika status masih Pending)

**Status Badges:**
- 🟡 Pending (Warning - Kuning)
- 🔵 In Progress (Info - Biru)
- 🟢 Completed (Success - Hijau)
- 🔴 Cancelled (Danger - Merah)

---

### 3. Department Head - Edit Command
Department Head dapat mengedit command yang masih berstatus Pending:
- **URL**: `/commands/{id}/edit`
- **Route**: `commands.edit`

**Field yang dapat diedit:**
- Judul Command
- Command/Instruksi
- Action Plan
- Supervisor
- Tanggal Jatuh Tempo

**Catatan:** Hanya command dengan status Pending yang dapat diedit.

---

### 4. Department Head - Hapus Command
Department Head dapat menghapus command yang masih berstatus Pending:
- **Route**: `commands.destroy`
- **Method**: DELETE
- **Authorization**: Hanya department head yang membuat command yang bisa menghapus

---

### 5. Supervisor - Melihat Daftar Command
Supervisor dapat melihat semua command yang ditugaskan kepada mereka:
- **URL**: `/commands`
- **Route**: `commands.index`

**Fitur:**
- Filter berdasarkan status (Pending, In Progress, Completed, Cancelled)
- Menampilkan informasi: Judul, Department Head, Status, Tanggal Jatuh Tempo, Tanggal Dibuat
- Hanya menampilkan command yang ditugaskan kepada supervisor yang login

**Action buttons:**
- **View** - Melihat detail command
- **Update Status** - Mengubah status command

---

### 6. Supervisor - Update Status Command
Supervisor dapat mengubah status command mereka:
- **URL**: `/commands/{id}/edit-status`
- **Route**: `commands.edit-status`

**Field:**
- **Status** (required) - Pilih dari: Pending, In Progress, Completed, Cancelled
- **Catatan/Progress Update** (optional) - Tambahkan catatan atau update kemajuan

**Fitur:**
- Menampilkan detail command (read-only) sebagai konteks
- Supervisor dapat menambahkan catatan/progress update
- Catatan akan tersimpan dan dapat dilihat oleh Department Head

---

### 7. View Detail Command
Untuk melihat detail lengkap command:
- **URL**: `/commands/{id}`
- **Route**: `commands.show`

**Konten yang ditampilkan:**
- Judul Command
- Status
- Department Head
- Supervisor
- Tanggal Dibuat
- Tanggal Jatuh Tempo
- Command/Instruksi (full text)
- Action Plan (full text)
- Catatan dari Supervisor (jika ada)

**Authorization:**
- Department Head yang membuat command
- Supervisor yang ditugaskan untuk command

---

## Database Schema

### Table: commands

| Field | Type | Description |
|-------|------|-------------|
| id | bigint (PK) | Primary Key |
| department_head_id | bigint (FK) | Foreign Key ke Users table |
| title | string(255) | Judul command |
| command_text | longText | Instruksi/command detail |
| action_plan | longText | Rencana aksi |
| status | enum | Pending, In Progress, Completed, Cancelled |
| supervisor_id | bigint (FK) | Foreign Key ke Users table (nullable) |
| supervisor_notes | longText | Catatan dari supervisor |
| created_date | dateTime | Tanggal pembuatan command |
| due_date | dateTime | Tanggal jatuh tempo |
| created_at | timestamp | Timestamp created by Laravel |
| updated_at | timestamp | Timestamp updated by Laravel |

**Indexes:**
- department_head_id
- supervisor_id
- status

---

## Model Relationships

### Command Model

```php
// Relationship dengan User (Department Head)
public function departmentHead(): BelongsTo
{
    return $this->belongsTo(User::class, 'department_head_id');
}

// Relationship dengan User (Supervisor)
public function supervisor(): BelongsTo
{
    return $this->belongsTo(User::class, 'supervisor_id');
}
```

---

## Authorization & Permissions

### Department Head Role
- ✅ Buat command baru
- ✅ Lihat daftar command mereka
- ✅ Edit command (hanya jika status Pending)
- ✅ Hapus command (hanya jika status Pending)
- ✅ Lihat detail command
- ❌ Lihat semua command

### Supervisor Role
- ❌ Buat command
- ✅ Lihat command yang ditugaskan kepada mereka
- ✅ Update status command
- ✅ Lihat detail command
- ✅ Menambah catatan progress

### Admin Role
- ✅ Akses semua fitur supervisor

---

## Routes Overview

```php
// Supervisor & Admin - Lihat Command
GET    /commands                          // Index/List
GET    /commands/{command}               // Show detail
GET    /commands/{command}/edit-status   // Form update status
PUT    /commands/{command}/update-status // Update status

// Department Head - Manage Command
GET    /commands/my-list                 // Daftar command mereka
GET    /commands/create                  // Form buat command
POST   /commands                         // Store command
GET    /commands/{command}/edit          // Form edit command
PUT    /commands/{command}               // Update command
DELETE /commands/{command}               // Delete command
```

---

## Workflow Diagram

```
Department Head
    ↓
    ├─→ Create Command (Buat Command)
    │   ├── Fill Form (Judul, Command, Action Plan, Supervisor, Due Date)
    │   └── Submit → Status = Pending
    │
    ├─→ View My Commands (Lihat daftar command mereka)
    │   ├── Filter by Status
    │   └── View, Edit (jika Pending), Delete (jika Pending)
    │
    └─→ View Command Details (Melihat detail command)
        └── Monitor progress via supervisor notes

Supervisor
    ↓
    ├─→ View Commands (Lihat command yang ditugaskan)
    │   ├── Filter by Status
    │   └── View Detail, Update Status
    │
    └─→ Update Status & Add Notes
        ├── Change Status: Pending → In Progress → Completed/Cancelled
        └── Add Progress Notes/Catatan
```

---

## Fitur Lanjutan yang Dapat Ditambahkan

1. **Email Notifications** - Notifikasi email saat ada command baru
2. **Activity Log** - Log setiap perubahan status
3. **Bulk Actions** - Update multiple commands sekaligus
4. **Command Categories** - Kategori command (Maintenance, Production, etc)
5. **Attachment Support** - Upload file/dokumen
6. **Comments/Discussion** - Diskusi antara DH dan Supervisor
7. **Time Tracking** - Track waktu pengerjaan
8. **Dashboard Widget** - Widget untuk quick stats
9. **Export Reports** - Export command data ke Excel/PDF
10. **Command Templates** - Template command yang sering digunakan

---

## Troubleshooting

### Command tidak muncul untuk Supervisor
- Pastikan supervisor yang login memiliki role `supervisor`
- Pastikan command ditugaskan kepada supervisor yang benar
- Cek di database bahwa `supervisor_id` sudah terisi

### Tidak bisa edit command
- Hanya command dengan status `pending` yang bisa diedit
- Hanya department head yang membuat command yang bisa edit

### Tidak bisa delete command
- Hanya command dengan status `pending` yang bisa didelete
- Hanya department head yang membuat command yang bisa delete

---

## Testing Checklist

- [ ] Department Head dapat membuat command baru
- [ ] Command muncul di list "Command Saya" untuk Department Head
- [ ] Supervisor dapat melihat command yang ditugaskan
- [ ] Supervisor dapat mengubah status command
- [ ] Supervisor dapat menambah catatan
- [ ] Department Head dapat melihat catatan dari supervisor
- [ ] Filter status berfungsi dengan baik
- [ ] Pagination berfungsi di kedua list view
- [ ] Authorization working (tidak bisa akses view orang lain)
- [ ] Edit dan Delete hanya berfungsi saat status Pending

