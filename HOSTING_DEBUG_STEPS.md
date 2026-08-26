# 🔧 HOSTING DEBUG - Step by Step

## Problem
Error 500 di hosting saat insert data ke `orowarehouse`, tapi lokal normal.

## Files Modified
✅ `app/Controllers/Warehouse2.php` - Enhanced error logging + debug endpoint  
✅ `app/Config/Routes.php` - Added debug route  
✅ `app/Database/Migrations/2026-07-31-000001_AddNomorLotToOrowarehouse.php` - Migration untuk column `nomor_lot`

---

## 🎯 STEP 1: Upload Files ke Hosting

Upload files yang sudah dimodifikasi:
```
app/Controllers/Warehouse2.php
app/Config/Routes.php
app/Database/Migrations/2026-07-31-000001_AddNomorLotToOrowarehouse.php
app/Models/MOrowarehouse.php
```

---

## 🎯 STEP 2: Jalankan Migration (Jika Belum)

### Via SSH:
```bash
cd /path/to/project
php spark migrate --group warehouse
php spark migrate:status --group warehouse
```

### Via phpMyAdmin:
```sql
-- Check if column exists
SHOW COLUMNS FROM orowarehouse LIKE 'nomor_lot';

-- If not exists, add it:
ALTER TABLE `orowarehouse` 
ADD COLUMN `nomor_lot` VARCHAR(100) NULL 
AFTER `operator`;
```

---

## 🎯 STEP 3: Set CI_ENVIRONMENT = development (Temporarily)

Edit file `.env` di hosting, set:
```ini
CI_ENVIRONMENT = development
```

**⚠️ PENTING:** Ini temporary untuk debugging! Nanti kembalikan ke `production`.

---

## 🎯 STEP 4: Test Debug Endpoint

Buka di browser:
```
https://oro3departemen.my.id/warehouse2/debug-insert-test
```

### Expected Responses:

#### ✅ Success:
```json
{
  "success": true,
  "message": "Test insert successful",
  "id": 123,
  "test_data": {...}
}
```
**Artinya:** Database OK! Problem ada di frontend data yang dikirim.

#### ❌ Error - DatabaseException:
```json
{
  "success": false,
  "error_type": "DatabaseException",
  "message": "Unknown column 'nomor_lot' in 'field list'",
  "code": 1054
}
```
**Artinya:** Column `nomor_lot` belum ditambahkan. Jalankan migration lagi.

#### ❌ Error - Connection Issue:
```json
{
  "success": false,
  "message": "Unable to connect to database: orowarehouse"
}
```
**Artinya:** Config `.env` untuk `database.warehouse.*` salah atau database belum dibuat.

---

## 🎯 STEP 5: Test Insert via Frontend

1. Buka https://oro3departemen.my.id/warehouse2
2. Tab "Barang Masuk dari Produksi"
3. Klik "Tambah Laporan"
4. Isi form:
   - Tanggal: (hari ini)
   - SPK: TEST-001
   - Produk: Test Product
   - Shift: 1
   - Hasil Bagus: 100
   - Hasil Reject: 5
5. Klik **Simpan**

### Check Console (F12):

#### Jika masih error 500:
Lihat **response body** di Network tab → klik request `store-orowarehouse` → tab "Response".

Sekarang error message akan lebih detail:
```json
{
  "success": false,
  "message": "Database Error: ...",
  "debug": {
    "code": 1054,
    "file": "/path/to/Models/MOrowarehouse.php",
    "line": 123
  }
}
```

---

## 🎯 STEP 6: Check Server Logs

### Via SSH:
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).php
```

### Via FTP/cPanel:
1. Download file: `writable/logs/log-2026-07-31.php`
2. Buka dengan text editor
3. Search: `[storeOrowarehouse]`
4. Lihat error message lengkap

**Example log entries:**
```php
ERROR - 2026-07-31 10:15:23 --> [storeOrowarehouse] Database Exception: Unknown column 'nomor_lot' in 'field list'
DEBUG - 2026-07-31 10:15:23 --> [storeOrowarehouse] Attempting insert with data: {"tanggal":"2026-07-31","spk":"TEST-001",...}
```

---

## 🎯 STEP 7: Common Errors & Solutions

### Error 1: "Unknown column 'nomor_lot'"
**Cause:** Migration belum dijalankan  
**Fix:** Jalankan migration atau manual ALTER TABLE

### Error 2: "Data too long for column 'shift'"
**Cause:** Shift value tidak sesuai ENUM ('1', '2', '3')  
**Fix:** Pastikan shift value berupa string '1', '2', atau '3'

### Error 3: "Incorrect decimal value"
**Cause:** hasil_bagus/hasil_reject bukan numeric  
**Fix:** Convert ke number di frontend:
```javascript
hasil_bagus: parseFloat(document.getElementById('hasilBagus').value) || 0
```

### Error 4: "Cannot add or update a child row"
**Cause:** Foreign key constraint (jika ada)  
**Fix:** Check referential integrity

### Error 5: "Access denied for user"
**Cause:** Database credentials salah di `.env`  
**Fix:** Verify username/password di cPanel

---

## 🎯 STEP 8: Kembalikan CI_ENVIRONMENT = production

Setelah error teridentifikasi dan fix, edit `.env`:
```ini
CI_ENVIRONMENT = production
```

Dan **HAPUS** debug endpoint dari Routes.php:
```php
// Remove this line:
$routes->get('/warehouse2/debug-insert-test', 'Warehouse2::debugInsertTest', ['filter' => 'auth']);
```

---

## 📊 Diagnostic Checklist

| Item | Status | Check Command |
|------|--------|---------------|
| Database exists | ❓ | `SHOW DATABASES LIKE '%warehouse%';` |
| Table exists | ❓ | `SHOW TABLES FROM orowarehouse;` |
| Column `nomor_lot` exists | ❓ | `SHOW COLUMNS FROM orowarehouse LIKE 'nomor_lot';` |
| Table structure complete | ❓ | `SHOW CREATE TABLE orowarehouse;` |
| Database connection OK | ❓ | Test via debug endpoint |
| Model validation rules | ❓ | Check `MOrowarehouse::$validationRules` |
| .env config correct | ❓ | Verify `database.warehouse.*` settings |

---

## 🚀 Quick Test Commands

```bash
# Check PHP version
php -v

# Check database connection
php spark db:table orowarehouse --group warehouse

# Run migration
php spark migrate --group warehouse

# Check migration status
php spark migrate:status --group warehouse

# View last 50 log lines
tail -50 writable/logs/log-$(date +%Y-%m-%d).php
```

---

## 📞 Next Steps

1. Jalankan STEP 1-4 di atas
2. Screenshot hasil debug endpoint
3. Screenshot console error (jika masih ada)
4. Screenshot/copy log entries dari `writable/logs/`
5. Share hasilnya untuk analisa lebih lanjut

**Expected Timeline:** 5-10 minutes untuk identify root cause

---

**Created:** 2026-07-31  
**Last Updated:** 2026-07-31  
**Status:** Ready for testing
