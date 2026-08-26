# 📊 Penjelasan Database & Table untuk Warehouse2

## 🗄️ Database Configuration

Aplikasi menggunakan **Multiple Database** (Multi-DB Architecture):

```
.env:
├── database.default → oromaster (default)
├── database.master → oromaster (master data)
├── database.gudang → orogudang (warehouse data)
├── database.ppic → oroppic (planning data)
└── database.produksi → oroproduksi (production data)
```

### Credentials
```
Host: localhost
Username: root
Password: root
```

---

## 📋 Warehouse2 Tables

### Tab 1: "Barang Masuk dari Pemasok"

| Aspek | Detail |
|-------|--------|
| **Database** | `oromaster` |
| **Table** | `barang_masuk_pemasok` |
| **Model** | `MBarangMasukPemasok` |
| **Primary Key** | `id` |
| **Soft Delete** | ✅ Enabled (`deleted_at`) |
| **Timestamps** | ✅ Yes (`created_at`, `updated_at`) |

**Columns**:
```sql
- id (int, primary key)
- no_dokumen (varchar) - Document number
- tanggal (date) - Date
- nama_pemasok (varchar) - Supplier name
- kode_barang (varchar) - Item code
- nama_barang (varchar) - Item name
- satuan (varchar) - Unit (kg, liter, pcs, etc)
- jumlah (decimal) - Quantity
- harga_satuan (decimal) - Unit price
- total_harga (decimal) - Total price
- no_po (varchar) - PO number
- no_surat_jalan (varchar) - Delivery letter number
- catatan (text) - Notes
- status (varchar) - Status (pending, diterima, ditolak)
- created_at, updated_at, deleted_at
```

---

### Tab 2: "Barang Masuk ke Produksi"

| Aspek | Detail |
|-------|--------|
| **Database** | `oromaster` |
| **Table** | `barang_masuk_produksi` |
| **Model** | `MBarangMasukProduksi` |
| **Primary Key** | `id` |
| **Soft Delete** | ✅ Enabled |
| **Timestamps** | ✅ Yes |

**Columns**:
```sql
- id (int, primary key)
- no_dokumen (varchar) - Document number
- tanggal (date) - Date
- no_spk (varchar) - SPK/Job Order number
- nama_produksi (varchar) - Production name
- kode_barang (varchar) - Item code
- nama_barang (varchar) - Item name
- satuan (varchar) - Unit
- jumlah_keluar (decimal) - Quantity out
- jumlah_terima (decimal) - Quantity received
- nama_penerima (varchar) - Receiver name
- catatan (text) - Notes
- status (varchar) - Status
- created_at, updated_at, deleted_at
```

---

### Tab 3: "Barang Keluar ke Pembeli"

| Aspek | Detail |
|-------|--------|
| **Database** | `oromaster` |
| **Table** | `barang_keluar_pembeli` |
| **Model** | `MBarangKeluarPembeli` |
| **Primary Key** | `id` |
| **Soft Delete** | ✅ Enabled |
| **Timestamps** | ✅ Yes |

**Columns**:
```sql
- id (int, primary key)
- no_dokumen (varchar) - Document number
- tanggal (date) - Date
- nama_pembeli (varchar) - Buyer name
- kode_barang (varchar) - Item code
- nama_barang (varchar) - Item name
- satuan (varchar) - Unit
- jumlah (decimal) - Quantity
- harga_satuan (decimal) - Unit price
- total_harga (decimal) - Total price
- no_so (varchar) - SO number
- no_invoice (varchar) - Invoice number
- alamat_pengiriman (text) - Delivery address
- catatan (text) - Notes
- status (varchar) - Status
- created_at, updated_at, deleted_at
```

---

### Tab 4: "Barang Masuk dari Produksi"

#### Sub-Tab A: Table Data (Legacy)

| Aspek | Detail |
|-------|--------|
| **Database** | `oromaster` |
| **Table** | `barang_masuk_dari_produksi` |
| **Model** | `MBarangMasukDariProduksi` |
| **Primary Key** | `id` |
| **Soft Delete** | ✅ Enabled |
| **Timestamps** | ✅ Yes |

**Columns**:
```sql
- id (int, primary key)
- no_dokumen (varchar) - Document number
- tanggal (date) - Date
- no_spk (varchar) - SPK number
- nama_produksi (varchar) - Production name
- kode_barang (varchar) - Item code
- nama_barang (varchar) - Item name
- satuan (varchar) - Unit
- jumlah_produksi (decimal) - Production quantity
- jumlah_reject (decimal) - Reject quantity
- jumlah_diterima (decimal) - Received quantity
- nama_penerima (varchar) - Receiver name
- catatan (text) - Notes
- status (varchar) - Status
- created_at, updated_at, deleted_at
```

#### Sub-Tab B: OROWAREHOUSE Data (New)

| Aspek | Detail |
|-------|--------|
| **Database** | `oromaster` |
| **Table** | `orowarehouse` |
| **Model** | `MOrowarehouse` |
| **Primary Key** | `id` |
| **Soft Delete** | ✅ Enabled |
| **Timestamps** | ✅ Yes |

**Columns**:
```sql
- id (int, primary key)
- tanggal (date) - Date
- spk (varchar) - SPK number (from oroproduksi)
- produk (varchar) - Product name
- shift (int) - Shift (1, 2, 3)
- grup (varchar) - Group/Team
- hasil_bagus (decimal) - Good result/quantity
- hasil_reject (decimal) - Reject quantity
- material (varchar) - Material used
- colorant (varchar) - Colorant used
- packaging (varchar) - Packaging info
- operator (varchar) - Operator name
- status (varchar) - Status (pending, diterima, ditolak)
- catatan (text) - Notes
- created_at, updated_at, deleted_at
```

---

## 🔗 Database Relationships

### Data Flow Diagram

```
OROPRODUKSI Database
    │
    ├─→ produksi_headers (SPK master)
    │   └─→ nomor_spk
    │   └─→ nama_produk
    │   └─→ shift, grup, operator
    │   └─→ grand_total_bagus
    │   └─→ grand_total_reject
    │
    ├─→ produksi_materials (Materials/Bahan Baku)
    │   └─→ merek_kode
    │   └─→ pemakaian
    │
    ├─→ produksi_colorants (Colorants)
    │   └─→ code
    │   └─→ pemakaian
    │
    └─→ produksi_packagings (Packaging)
        └─→ box, plastik, karung
        └─→ sizes info


OROMASTER Database
    │
    ├─→ barang_masuk_pemasok (Supplier goods in)
    ├─→ barang_masuk_produksi (Production goods out)
    ├─→ barang_keluar_pembeli (Buyer goods out)
    ├─→ barang_masuk_dari_produksi (Production goods in)
    └─→ orowarehouse (Warehouse management - FINAL TABLE)
```

---

## 🔄 How OROWAREHOUSE Gets Data

When user selects SPK in dropdown on "Tambah Barang Masuk dari Produksi":

```javascript
1. User pilih SPK dari dropdown
   ↓
2. autofillFromSpkSelect() dipanggil
   ↓
3. Query ke OROPRODUKSI database:
   - FROM produksi_headers WHERE nomor_spk = 'user_selection'
   - Ambil: nomor_spk, nama_produk, shift, grup, operator, 
            grand_total_bagus, grand_total_reject
   ↓
4. Query ke produksi_materials & produksi_colorants
   - Ambil semua materials dan colorants
   ↓
5. Data ditampilkan di modal form
   - Fields auto-populated
   - User bisa edit jika diperlukan
   ↓
6. User klik "Simpan"
   ↓
7. Data disimpan ke OROMASTER.orowarehouse table
```

---

## 📌 Important Notes

### Soft Delete
Semua table menggunakan soft delete:
```sql
-- Data dihapus tidak benar-benar dihapus dari database
-- Hanya di-mark dengan deleted_at timestamp
DELETE FROM barang_masuk_pemasok WHERE id = 1;
-- Menghasilkan: deleted_at = NOW()
-- Bukan: TRUE DELETE dari database
```

### Timestamps
Setiap record otomatis tercatat:
- `created_at` - Waktu record dibuat
- `updated_at` - Waktu record terakhir diupdate
- `deleted_at` - Waktu record dihapus (soft delete)

### Status Values
Setiap table menggunakan status:
- `pending` - Menunggu konfirmasi
- `diterima` - Data diterima/approved
- `ditolak` - Data ditolak
- Custom status sesuai kebutuhan

---

## 🧪 Query Examples

### Lihat semua data barang masuk dari pemasok (tidak termasuk deleted)
```sql
SELECT * FROM oromaster.barang_masuk_pemasok 
WHERE deleted_at IS NULL
ORDER BY tanggal DESC;
```

### Lihat data berdasarkan status
```sql
SELECT * FROM oromaster.barang_masuk_pemasok 
WHERE status = 'diterima' AND deleted_at IS NULL
ORDER BY tanggal DESC;
```

### Lihat warehouse data dengan join ke production
```sql
SELECT 
    ow.id,
    ow.tanggal,
    ow.spk,
    ow.produk,
    ow.shift,
    ow.hasil_bagus,
    ow.hasil_reject,
    ow.status
FROM oromaster.orowarehouse ow
WHERE ow.deleted_at IS NULL
ORDER BY ow.tanggal DESC;
```

### Restore deleted data
```sql
UPDATE oromaster.barang_masuk_pemasok 
SET deleted_at = NULL 
WHERE id = 5;
```

---

## 📂 File Locations

**Models**:
```
app/Models/
├── MBarangMasukPemasok.php
├── MBarangMasukProduksi.php
├── MBarangKeluarPembeli.php
├── MBarangMasukDariProduksi.php
└── MOrowarehouse.php
```

**Controller**:
```
app/Controllers/
└── Warehouse2.php
```

**Views**:
```
app/Views/
└── warehouse2/
    └── index.php (All 4 tabs UI)
```

**Routes**:
```
app/Config/Routes.php
(Lines ~210-230 for warehouse2 routes)
```

---

## ✅ Summary

| Komponen | Database | Table | Model | Fungsi |
|----------|----------|-------|-------|--------|
| **Tab 1** | oromaster | barang_masuk_pemasok | MBarangMasukPemasok | Goods from suppliers |
| **Tab 2** | oromaster | barang_masuk_produksi | MBarangMasukProduksi | Goods to production |
| **Tab 3** | oromaster | barang_keluar_pembeli | MBarangKeluarPembeli | Goods to buyers |
| **Tab 4A** | oromaster | barang_masuk_dari_produksi | MBarangMasukDariProduksi | Goods from production (legacy) |
| **Tab 4B** | oromaster | orowarehouse | MOrowarehouse | Warehouse management (new) |

**Semua data tersimpan di database `oromaster`** dengan table masing-masing untuk tracking dan management yang lebih baik.

Data dari OROPRODUKSI diambil untuk auto-fill fields saat user memilih SPK.
