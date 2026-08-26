# 🔧 FIX ERROR 500 DI HOSTING - OROWAREHOUSE

## 🎯 Root Cause
**Model `MOrowarehouse` memiliki field `nomor_lot` tapi database di hosting tidak punya column ini!**

Di lokal mungkin sudah ditambahkan manual, tapi hosting belum.

---

## ✅ SOLUSI - Jalankan Migration Baru

### Step 1: Upload File Migration Baru
Upload file ini ke hosting:
```
app/Database/Migrations/2026-07-31-000001_AddNomorLotToOrowarehouse.php
```

### Step 2: Run Migration di Hosting
Via **SSH**:
```bash
cd /path/to/project
php spark migrate --group warehouse
```

**ATAU** via **phpMyAdmin** (jika tidak ada SSH):
```sql
ALTER TABLE `orowarehouse` 
ADD COLUMN `nomor_lot` VARCHAR(100) NULL AFTER `operator`;
```

### Step 3: Test
1. Refresh halaman warehouse2
2. Tab "Barang Masuk dari Produksi"
3. Klik "Tambah Laporan"
4. Isi form dan simpan
5. ✅ Seharusnya berhasil tanpa error 500

---

## 🔍 Verifikasi Column Exists

Via phpMyAdmin, jalankan query:
```sql
SHOW COLUMNS FROM orowarehouse;
```

Pastikan ada column:
- tanggal
- spk
- produk
- shift
- grup
- hasil_bagus
- hasil_reject
- material
- colorant
- packaging
- operator
- **nomor_lot** ← HARUS ADA
- status
- catatan
- created_at
- updated_at
- deleted_at

---

## 📊 Penjelasan Teknis

### Kenapa Error 500 Hanya di Hosting?

**Lokal:**
- Database punya column `nomor_lot` (ditambahkan manual?)
- Model kirim data dengan field `nomor_lot`
- Insert berhasil ✅

**Hosting:**
- Database **TIDAK punya** column `nomor_lot`
- Model kirim data dengan field `nomor_lot`
- MySQL error: "Unknown column 'nomor_lot' in 'field list'"
- CodeIgniter catch exception → return 500 ❌

### Log Error Message (di writable/logs/)
```
storeOrowarehouse Error: Unknown column 'nomor_lot' in 'field list'
```

---

## 🚨 Jika Migration Gagal

Jika command `php spark migrate` error, jalankan SQL manual:

```sql
-- Cek database aktif
USE oro3dep_orowarehouse; -- sesuaikan nama database

-- Tambahkan column
ALTER TABLE `orowarehouse` 
ADD COLUMN `nomor_lot` VARCHAR(100) NULL 
COMMENT 'Nomor Lot Produksi'
AFTER `operator`;

-- Verify
SHOW COLUMNS FROM orowarehouse LIKE 'nomor_lot';
```

---

## ✨ Alternative: Hapus Field (Jika Tidak Digunakan)

Jika field `nomor_lot` tidak diperlukan sama sekali:

Edit `app/Models/MOrowarehouse.php`:
```php
protected $allowedFields = [
    'tanggal',
    'spk',
    'produk',
    'shift',
    'grup',
    'hasil_bagus',
    'hasil_reject',
    'material',
    'colorant',
    'packaging',
    'operator',
    // 'nomor_lot', // ← REMOVED
    'status',
    'catatan',
];
```

Upload model yang sudah diedit ke hosting, dan error 500 akan hilang.

**⚠️ WARNING:** Jika ada data produksi yang sudah pakai `nomor_lot`, jangan hapus field ini!

---

## 📝 Summary

| Item | Status Lokal | Status Hosting | Fix |
|------|--------------|----------------|-----|
| Database connection | ✅ | ✅ | - |
| Table `orowarehouse` | ✅ | ✅ | - |
| Column `nomor_lot` | ✅ (manual) | ❌ Missing | Run migration |
| Model `allowedFields` | ✅ Include `nomor_lot` | ✅ Include `nomor_lot` | - |

**Action:** Tambahkan column `nomor_lot` di database hosting!

---

## 🎯 Quick Commands

```bash
# Via SSH - Run migration
cd /home/oro3dep/public_html
php spark migrate --group warehouse

# Via SSH - Check migration status
php spark migrate:status --group warehouse

# Via SSH - Rollback last migration (if needed)
php spark migrate:rollback --group warehouse
```

---

## ✅ Success Indicators

Setelah fix, test dengan:
1. POST ke `/warehouse2/store-orowarehouse`
2. HTTP Status: **201 Created** (bukan 500)
3. Response JSON: `{"success": true, "message": "Data berhasil disimpan", "id": 123}`
4. Data muncul di tabel warehouse2

---

**Last Updated:** 2026-07-31  
**Fixed By:** Kiro AI Assistant
