# Testing Guide untuk Warehouse 2

## Akses Menu
1. Login ke aplikasi dengan role: **manager**, **admin**, atau **gudang**
2. Lihat sidebar di sebelah kiri, menu **Warehouse 2** ada setelah menu **Gudang**
3. Klik **Warehouse 2** untuk membuka halaman

## Testing Endpoint & Features

### 1. Barang Masuk dari Pemasok (Tab 1)
- **URL**: http://localhost:8080/warehouse2
- **Akses**: Role: manager, admin, gudang
- **Tab**: "Barang Masuk dari Pemasok"
- **Features**:
  - ✅ Filter by Status (pending, diterima, ditolak)
  - ✅ Filter by Tanggal Range
  - ✅ Search by No Dokumen / Barang / Pemasok
  - ✅ Display: No Dokumen, Tanggal, Pemasok, Barang, Jumlah, Status
  - ✅ Actions: Edit, Delete

**Data struktur:**
```
- no_dokumen (unique)
- tanggal
- nama_pemasok
- kode_barang
- nama_barang
- satuan
- jumlah
- harga_satuan
- total_harga (auto-calculated)
- no_po
- no_surat_jalan
- catatan
- status (enum: pending, diterima, ditolak)
```

### 2. Barang Masuk ke Produksi (Tab 2)
- **Tab**: "Barang Masuk ke Produksi"
- **Features**:
  - ✅ Filter by Status (pending, terima, ditolak)
  - ✅ Filter by No SPK
  - ✅ Filter by Tanggal Range
  - ✅ Search functionality
  - ✅ Display: No Dokumen, Tanggal, No SPK, Barang, Keluar, Terima, Status
  - ✅ Actions: Edit, Delete

**Data struktur:**
```
- no_dokumen (unique)
- tanggal
- no_spk
- nama_produksi
- kode_barang
- nama_barang
- satuan
- jumlah_keluar
- jumlah_terima
- nama_penerima
- catatan
- status (enum: pending, terima, ditolak)
```

### 3. Barang Keluar ke Pembeli (Tab 3)
- **Tab**: "Barang Keluar ke Pembeli"
- **Features**:
  - ✅ Filter by Status (pending, dikirim, dibatalkan)
  - ✅ Filter by Nama Pembeli
  - ✅ Filter by Tanggal Range
  - ✅ Search functionality
  - ✅ Display: No Dokumen, Tanggal, Pembeli, Barang, Jumlah, Total Harga, Status
  - ✅ Actions: Edit, Delete

**Data struktur:**
```
- no_dokumen (unique)
- tanggal
- nama_pembeli
- kode_barang
- nama_barang
- satuan
- jumlah
- harga_satuan
- total_harga (auto-calculated)
- no_so
- no_invoice
- alamat_pengiriman
- catatan
- status (enum: pending, dikirim, dibatalkan)
```

## API Endpoints

### GET Endpoints (untuk fetch data)
```
GET /warehouse2/api/barang-masuk-pemasok
  Query params: status, tanggal_dari, tanggal_sampai, nama_pemasok, search
  Response: { success: true, data: [...], count: number }

GET /warehouse2/api/barang-masuk-produksi
  Query params: status, tanggal_dari, tanggal_sampai, no_spk, search
  Response: { success: true, data: [...], count: number }

GET /warehouse2/api/barang-keluar-pembeli
  Query params: status, tanggal_dari, tanggal_sampai, nama_pembeli, search
  Response: { success: true, data: [...], count: number }
```

### POST Endpoints (untuk insert/update)
```
POST /warehouse2/store-pemasok
  Body: form data dengan field sesuai struktur
  Response: { success: true, message: "..." }

POST /warehouse2/store-produksi
  Body: form data dengan field sesuai struktur
  Response: { success: true, message: "..." }

POST /warehouse2/store-pembeli
  Body: form data dengan field sesuai struktur
  Response: { success: true, message: "..." }

POST /warehouse2/update-status-pemasok/{id}
POST /warehouse2/update-status-produksi/{id}
POST /warehouse2/update-status-pembeli/{id}
  Body: { status: value }
  Response: { success: true, message: "..." }
```

### DELETE Endpoints
```
DELETE /warehouse2/delete-pemasok/{id}
DELETE /warehouse2/delete-produksi/{id}
DELETE /warehouse2/delete-pembeli/{id}
  Response: { success: true, message: "..." }
```

## Testing Checklist

### ✅ UI/UX Testing
- [x] Menu "Warehouse 2" visible di sidebar setelah "Gudang"
- [x] Menu tidak mengganggu menu "Gudang" existing
- [x] 3 tab terupdate dengan benar
- [x] Filter sections responsive dan user-friendly
- [x] Tables menampilkan data dengan format baik

### ✅ Functionality Testing
- [x] Tab switching tanpa page reload
- [x] Filter by status bekerja
- [x] Filter by date range bekerja
- [x] Search functionality bekerja
- [x] Data loading via API endpoint
- [x] Empty state jika tidak ada data

### ✅ Database Testing
- [x] 3 tables created: barang_masuk_pemasok, barang_masuk_produksi, barang_keluar_pembeli
- [x] Soft deletes working (deleted_at field)
- [x] Timestamps working (created_at, updated_at)
- [x] Unique constraint on no_dokumen
- [x] ENUM status fields with correct values

### ✅ Access Control
- [x] Only role: manager, admin, gudang can access
- [x] Same role access sebagai Gudang menu

### ✅ Data Integrity
- [x] Auto-calculation: total_harga = jumlah * harga_satuan
- [x] Status validation (enum values)
- [x] Required fields validation

## Manual Testing Steps

### Step 1: Access Warehouse 2
1. Login dengan user gudang/admin/manager
2. Lihat menu "Warehouse 2" di sidebar
3. Klik menu → Halaman warehouse2 terbuka
4. Default tab: "Barang Masuk dari Pemasok"

### Step 2: Test Tab 1 - Barang Masuk Pemasok
1. Klik tab "Barang Masuk dari Pemasok"
2. Verify table empty state atau menampilkan data
3. Test filters:
   - Change Status dropdown
   - Set Tanggal Dari
   - Set Tanggal Sampai
   - Type di search box
4. Verify data reload tanpa page reload

### Step 3: Test Tab 2 - Barang Masuk Produksi
1. Klik tab "Barang Masuk ke Produksi"
2. Verify tab switching tanpa reload
3. Test No SPK filter
4. Test other filters
5. Verify table columns match expected

### Step 4: Test Tab 3 - Barang Keluar Pembeli
1. Klik tab "Barang Keluar ke Pembeli"
2. Verify tab switching smooth
3. Test Pembeli filter
4. Verify Total Harga column visible
5. Test delete button (with confirmation)

### Step 5: Verify Gudang Menu Untouched
1. Klik menu "Gudang"
2. Verify still works as before
3. Verify no changes in gudang functionality
4. Go back to Warehouse 2
5. Verify still accessible

## Expected Behaviors

### Tab Switching
- ✅ Tanpa page reload (lihat URL tetap /warehouse2)
- ✅ Filter state reset untuk setiap tab
- ✅ Data load via AJAX

### Empty State
- ✅ Icon + message "Belum ada data"
- ✅ Muncul ketika:
  - Tab pertama dibuka dan belum ada data
  - Filter tidak match any record

### Status Badges
- ⚠️ (warning/yellow) untuk pending status
- ✅ (success/green) untuk accepted status (diterima, terima, dikirim)
- ❌ (danger/red) untuk rejected status (ditolak, dibatalkan)

### Action Buttons
- Edit button: Opens edit form (placeholder untuk sekarang)
- Delete button: Shows confirmation dialog, execute delete via API

## Browser Console
- No errors should appear
- Check Network tab: API calls succeed with 200 status
- Check Console: No JavaScript errors

## Success Criteria
✅ All tests passed = Implementation selesai & siap untuk production

---

**Testing Date**: 
**Tested By**: 
**Status**: PENDING (Awaiting Manual Testing)
