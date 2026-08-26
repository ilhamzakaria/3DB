# 📊 Penjelasan Database Warehouse2 - VERSI SIMPEL

## 🎯 Jawaban Singkat

### Database Mana?
**Jawab**: `oromaster`

### Table Mana?
**Jawab**: Ada 5 table berbeda untuk setiap tab

---

## 📋 Tabel Per Tab

### Tab 1️⃣ - "Barang Masuk dari Pemasok"
```
Database: oromaster
Table: barang_masuk_pemasok
Fungsi: Mencatat barang masuk dari supplier
```

**Columns penting**:
- `id` - ID unik
- `no_dokumen` - Nomor dokumen
- `tanggal` - Tanggal masuk
- `nama_pemasok` - Nama supplier
- `nama_barang` - Nama barang
- `jumlah` - Jumlah
- `status` - Status (pending/diterima/ditolak)

---

### Tab 2️⃣ - "Barang Masuk ke Produksi"
```
Database: oromaster
Table: barang_masuk_produksi
Fungsi: Mencatat barang keluar ke produksi
```

**Columns penting**:
- `id` - ID unik
- `no_dokumen` - Nomor dokumen
- `tanggal` - Tanggal keluar
- `no_spk` - Nomor SPK produksi
- `nama_barang` - Nama barang
- `jumlah_keluar` - Jumlah yang keluar
- `status` - Status

---

### Tab 3️⃣ - "Barang Keluar ke Pembeli"
```
Database: oromaster
Table: barang_keluar_pembeli
Fungsi: Mencatat barang keluar ke pembeli/customer
```

**Columns penting**:
- `id` - ID unik
- `no_dokumen` - Nomor dokumen
- `tanggal` - Tanggal keluar
- `nama_pembeli` - Nama pembeli
- `nama_barang` - Nama barang
- `jumlah` - Jumlah
- `total_harga` - Total harga
- `status` - Status

---

### Tab 4️⃣ - "Barang Masuk dari Produksi"

#### Bagian A (Table Lama)
```
Database: oromaster
Table: barang_masuk_dari_produksi
Fungsi: Mencatat barang masuk dari produksi
```

#### Bagian B (OROWAREHOUSE - Table Baru)
```
Database: oromaster
Table: orowarehouse
Fungsi: Warehouse management dengan detail produksi
```

**Columns penting**:
- `id` - ID unik
- `tanggal` - Tanggal
- `spk` - Nomor SPK (dari oroproduksi)
- `produk` - Nama produk
- `shift` - Shift (1/2/3)
- `hasil_bagus` - Hasil bagus
- `hasil_reject` - Hasil reject
- `material` - Material yang digunakan
- `colorant` - Colorant yang digunakan
- `packaging` - Info packaging
- `operator` - Nama operator
- `status` - Status

---

## 🔗 Data Auto-Fill dari Mana?

Saat user memilih SPK di Tab 4 Bagian B:

```
User pilih SPK
    ↓
Query ke database OROPRODUKSI
    ↓
Cari table: produksi_headers
    ↓
Ambil data:
  - nomor_spk
  - nama_produk
  - shift, grup, operator
  - grand_total_bagus
  - grand_total_reject
    ↓
Cari table: produksi_materials & produksi_colorants
    ↓
Ambil materials & colorants yang digunakan
    ↓
Tampilkan di form
    ↓
User bisa edit dan simpan ke OROMASTER.orowarehouse
```

---

## 🗂️ File Yang Terlibat

### Model (File yang define table)
```
app/Models/
├── MBarangMasukPemasok.php → table barang_masuk_pemasok
├── MBarangMasukProduksi.php → table barang_masuk_produksi
├── MBarangKeluarPembeli.php → table barang_keluar_pembeli
├── MBarangMasukDariProduksi.php → table barang_masuk_dari_produksi
└── MOrowarehouse.php → table orowarehouse
```

### Controller (File yang handle logic)
```
app/Controllers/Warehouse2.php
  - storeOrowarehouse() → Simpan ke orowarehouse
  - updateOrowarehouse() → Update orowarehouse
  - deleteOrowarehouse() → Delete dari orowarehouse
  - getOrowarehouse() → Ambil data orowarehouse
  - getOrowarehouseBySpk() → Ambil data dari oroproduksi berdasarkan SPK
```

### View (File UI)
```
app/Views/warehouse2/index.php
  - Modal forms untuk setiap tab
  - AJAX calls untuk save/update/delete
```

---

## 💾 Lihat Data di Database

### Pakai phpMyAdmin
1. Buka: http://localhost/phpmyadmin
2. Login (user: root, password: root)
3. Pilih database: `oromaster`
4. Pilih table:
   - `barang_masuk_pemasok` (Tab 1)
   - `barang_masuk_produksi` (Tab 2)
   - `barang_keluar_pembeli` (Tab 3)
   - `barang_masuk_dari_produksi` (Tab 4A)
   - `orowarehouse` (Tab 4B)

### Atau Query di SQL
```sql
-- Lihat semua barang masuk dari pemasok
SELECT * FROM oromaster.barang_masuk_pemasok WHERE deleted_at IS NULL;

-- Lihat semua barang di warehouse
SELECT * FROM oromaster.orowarehouse WHERE deleted_at IS NULL;

-- Lihat barang yang sudah diterima
SELECT * FROM oromaster.barang_masuk_pemasok 
WHERE status = 'diterima' AND deleted_at IS NULL;
```

---

## ✅ Quick Summary

| Tab | Database | Table | Fungsi |
|-----|----------|-------|--------|
| 1 | oromaster | barang_masuk_pemasok | Masuk dari supplier |
| 2 | oromaster | barang_masuk_produksi | Keluar ke produksi |
| 3 | oromaster | barang_keluar_pembeli | Keluar ke pembeli |
| 4A | oromaster | barang_masuk_dari_produksi | Masuk dari produksi |
| 4B | oromaster | orowarehouse | Warehouse management |

**Semua tersimpan di 1 database: `oromaster`** ✅

Data dari SPK diambil dari database lain: `oroproduksi` untuk auto-fill. ✅

---

## 🤔 FAQ

**Q: Semua table di database mana?**
A: Database `oromaster` (sesuai .env)

**Q: Gimana cara lihat data?**
A: Buka phpMyAdmin → oromaster → pilih table → lihat data

**Q: Data dari SPK dari mana?**
A: Dari database `oroproduksi` tabel `produksi_headers`

**Q: Kalau saya delete data, benar-benar dihapus?**
A: Tidak, hanya di-mark sebagai deleted (soft delete). Bisa di-restore.

**Q: Kolom apa saja di setiap table?**
A: Lihat file `DATABASE_EXPLANATION.md` untuk detail lengkap.

