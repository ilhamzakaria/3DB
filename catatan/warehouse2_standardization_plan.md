# Rencana Standarisasi UI/UX Warehouse2

## Status Saat Ini (Analisis)

### Tab yang sudah baik (Barang Masuk dari Produksi / Orowarehouse)
✅ **Fitur yang ada:**
- Pagination dengan prev/next navigation
- Filter dengan toolbar (Period filter, Search, Custom filter)
- Export Excel
- Trash/Sampah dengan restore
- Modal form dengan autofill dari SPK
- Status badge dengan warna
- Table responsif dengan aksi edit/delete

---

## Tab yang perlu diperbaiki

### 1. **Barang Masuk dari Pemasok** ❌
**Masalah saat ini:**
- Simple table tanpa pagination
- Filter dasar (status, tanggal, search)
- Tidak ada export Excel
- Tidak ada trash/sampah
- Modal form basic
- Status badge ada tapi kurang styling

### 2. **Barang Masuk ke Produksi** ❌
**Masalah saat ini:**
- Simple table tanpa pagination
- Filter dasar (status, no SPK, tanggal, search)
- Tidak ada export Excel
- Tidak ada trash/sampah
- Modal form basic
- Status badge ada tapi kurang styling

### 3. **Barang Keluar ke Pembeli** ❌
**Masalah saat ini:**
- Simple table tanpa pagination
- Filter dasar (status, pembeli, tanggal, search)
- Tidak ada export Excel
- Tidak ada trash/sampah
- Modal form basic
- Status badge ada tapi kurang styling

---

## Alur Perbaikan & Penjelasan (Bahasa Indonesia)

### **FASE 1: Persiapan Database Model**
Pastikan setiap model memiliki method yang sama:
- `getWithFilter($filters)` - untuk filter sederhana
- Soft delete support (kolom `deleted_at`)

### **FASE 2: Update Controller**
Untuk setiap tab, buat endpoint API yang sama struktur:
- Pagination support
- Multiple filter support
- Export support
- Trash/restore support

**Endpoint standar:**
- `GET /warehouse2/api/{table}` → List dengan pagination
- `GET /warehouse2/api/{table}/trash` → List sampah
- `POST /warehouse2/restore/{table}/{id}` → Restore dari sampah
- `DELETE /warehouse2/delete-permanent/{table}/{id}` → Delete permanent
- `GET /warehouse2/export/{table}` → Export Excel

### **FASE 3: Update View (index.php)**
Untuk Tab 1, 2, 3: Ubah dari struktur simple menjadi struktur Orowarehouse

**Komponen yang ditambahkan ke setiap tab:**
1. **Toolbar Section**
   - "Tambah Laporan" button
   - "Export Excel" link
   - "Sampah" button (trash modal)

2. **Filter Section (Offcanvas)**
   - Date range / Custom date
   - Status filter
   - Specific filters per tab
   - Apply & Reset buttons

3. **Table Section**
   - Pagination controls (prev, current page, next)
   - Per-page selector (10, 25, 50)
   - Table dengan status badge
   - Edit & Delete buttons

4. **Modals**
   - Form modal untuk tambah/edit
   - Trash modal untuk restore & delete permanent

### **FASE 4: Update JavaScript**
Pindahkan setiap tab dari `loadTabData()` simple menjadi sistem pagination lengkap

---

## Penjelasan Fitur Setiap Komponen

### 1. **Pagination (Paginasi)**
- Navigasi antar halaman dengan prev/next buttons
- Menampilkan halaman saat ini dan total halaman
- Selector untuk jumlah data per halaman
- **Alur:** Klik next/prev → Load data halaman baru → Render table

### 2. **Filter System (Sistem Filter)**
- **Period Filter:** Hari ini, Minggu ini, Bulan ini, Semua periode
- **Custom Filter:** Offcanvas dengan berbagai filter sesuai tab
  - Barang Masuk Pemasok: Status, Tanggal, Pembeli
  - Barang Masuk Produksi: Status, Tanggal, No SPK, Produk
  - Barang Keluar Pembeli: Status, Tanggal, Pembeli, Produk
- **Search:** Field global untuk search di multiple columns
- **Alur:** Pilih filter → Klik Apply → API load dengan filter parameter → Render table

### 3. **Export Excel (Ekspor ke Excel)**
- Export data sesuai filter yang dipilih
- Filename: `{table}-{timestamp}.xlsx`
- Header columns sesuai table struktur
- **Alur:** Klik Export → Build URL dengan filter params → Server generate Excel → Download

### 4. **Trash/Sampah**
- Soft delete: data masuk ke sampah, bukan langsung dihapus
- Modal untuk lihat dan manage sampah
- Opsi: Restore (kembali ke table utama) atau Delete Permanent (hapus total)
- **Alur:**
  - Click Delete → Soft delete (set deleted_at)
  - Click Sampah → Modal load dengan deleted data
  - Click Restore → Update deleted_at = null
  - Click Delete Permanent → Hard delete

### 5. **Status Badge**
- Visual indicator dengan warna:
  - Pending: Warning (kuning)
  - Diterima/Terima/Dikirim: Success (hijau)
  - Ditolak/Dibatalkan: Danger (merah)
- Styling konsisten di semua tab

### 6. **Modal Form**
- Field validation (required fields)
- Error handling dengan SweetAlert2
- Close button yang reliable (data-bs-dismiss)
- Loading state saat submit
- **Alur:** Klik Tambah → Modal buka → Isi form → Klik Simpan → Validate → Submit → Success → Reload table

---

## Implementasi Per Tab

### **TAB 1: Barang Masuk dari Pemasok**

#### API Endpoints (update Warehouse2.php controller):
```php
// Existing methods, tambahkan soft delete support
- getBarangMasukPemasok() → ADD: pagination, deleted_at filter
- storePemasok() → Keep existing
- deletePemasok($id) → Change to soft delete (set deleted_at)
- restorePemasok($id) → New method
- deletePermanentPemasok($id) → New method (hard delete)
- exportPemasok() → New method
- getBarangMasukPemasokTrash() → New method
```

#### UI Changes:
- Replace "Filter Section" dengan offcanvas + toolbar
- Replace simple table dengan pagination table
- Add Trash modal

#### JavaScript Functions:
- `loadPemasokData(page)` → pagination + filter
- `initPemasokTab()` → setup listeners
- `editPemasok(item)` → populate form
- `deletePemasokRecord(id)` → soft delete
- `restorePemasokRecord(id)` → restore
- `exportPemasok()` → trigger download

---

### **TAB 2: Barang Masuk ke Produksi**

#### API Endpoints:
```php
- getBarangMasukProduksi() → ADD: pagination, deleted_at filter
- storeProduksi() → Keep existing
- deleteProduksi($id) → Change to soft delete
- restoreProduksi($id) → New method
- deletePermanentProduksi($id) → New method
- exportProduksi() → New method
- getBarangMasukProduksiTrash() → New method
```

#### UI Changes:
- Same as Tab 1 (Pemasok)

#### Additional Features:
- Autofill No SPK dari Produksi (like Orowarehouse)

#### JavaScript Functions:
- `loadProduksiData(page)` → pagination + filter
- `initProduksiTab()` → setup listeners
- `editProduksi(item)` → populate form
- `deleteProduksiRecord(id)` → soft delete
- `restoreProduksiRecord(id)` → restore
- `autofillFromSpkProduksi()` → fetch data dari produksi

---

### **TAB 3: Barang Keluar ke Pembeli**

#### API Endpoints:
```php
- getBarangKeluarPembeli() → ADD: pagination, deleted_at filter
- storePembeli() → Keep existing
- deletePembeli($id) → Change to soft delete
- restorePembeli($id) → New method
- deletePermanentPembeli($id) → New method
- exportPembeli() → New method
- getBarangKeluarPembeliTrash() → New method
```

#### UI Changes:
- Same as Tab 1 & 2

#### JavaScript Functions:
- `loadPembeliData(page)` → pagination + filter
- `initPembeliTab()` → setup listeners
- `editPembeli(item)` → populate form
- `deletePembeliRecord(id)` → soft delete
- `restorePembeliRecord(id)` → restore
- `exportPembeli()` → trigger download

---

## Database Schema Changes (Required)

### Untuk setiap tabel:
```sql
-- Jika belum ada soft delete column
ALTER TABLE barang_masuk_pemasok ADD COLUMN deleted_at DATETIME NULL;
ALTER TABLE barang_masuk_produksi ADD COLUMN deleted_at DATETIME NULL;
ALTER TABLE barang_keluar_pembeli ADD COLUMN deleted_at DATETIME NULL;
```

### Pastikan model implement SoftDeletes:
```php
protected $useSoftDeletes = true;
protected $deletedField = 'deleted_at';
```

---

## File yang akan Dimodifikasi

1. **app/Controllers/Warehouse2.php**
   - Add soft delete methods untuk setiap tab
   - Add export methods
   - Add trash methods
   - Update filter methods untuk pagination

2. **app/Views/warehouse2/index.php**
   - Update TAB 1, 2, 3 dari simple table ke Orowarehouse structure
   - Copy toolbar, filter offcanvas, pagination dari TAB 4
   - Add trash modals

3. **app/Models/MBarangMasukPemasok.php** (& similar models)
   - Add soft delete support
   - Update getWithFilter() untuk include pagination

---

## Timeline Implementasi

- **Fase 1:** Database schema + Model updates (30 min)
- **Fase 2:** Controller API endpoints (1 hour)
- **Fase 3:** View updates (1 hour)
- **Fase 4:** JavaScript functions (45 min)
- **Testing:** (30 min)

**Total: ~3-4 jam**

---

## Testing Checklist

### Setiap tab harus bisa:
- ✅ Menampilkan data dengan pagination
- ✅ Filter dengan berbagai kombinasi
- ✅ Search data
- ✅ Export ke Excel
- ✅ Tambah data baru
- ✅ Edit data
- ✅ Delete (soft) ke sampah
- ✅ Lihat sampah
- ✅ Restore dari sampah
- ✅ Delete permanent

---

## Catatan Penting

1. **Consistency:** Semua 3 tab harus memiliki struktur yang sama
2. **Performance:** Pagination untuk handle data besar
3. **UX:** Filter yang intuitif dengan offcanvas
4. **Backup:** Trash/sampah untuk safety delete
5. **Auditability:** Timestamp untuk semua operasi
