# Debug Gumpalan di Hosting - Step by Step

## Status
- ✅ Lokal: Input 1.2 → Tersimpan 1.2 (BEKERJA)
- ❌ Hosting: Input 1.2 → Tersimpan 1 (BERMASALAH)

---

## Step 1: Verifikasi Kolom Gumpalan di Hosting

### Cek via phpMyAdmin

1. **Login ke phpMyAdmin hosting**
   - URL: `https://oro3departemen.my.id/phpmyadmin`

2. **Pilih database: `oroproduksi`**

3. **Buka tabel: `produksi_headers`**

4. **Tab: "Structure"**

5. **Cari kolom `gumpalan`**

**Kemungkinan hasil:**

**A) Kolom TIDAK ADA** ← **INILAH MASALAHNYA!**
```
Migration tidak dijalankan di hosting
Solusi: Jalankan migration (Step 2)
```

**B) Kolom ADA tapi type INT/TINYINT** ← **JUGA MASALAH!**
```
Type: INT(11) atau TINYINT(1)
Solusi: Drop dan jalankan migration (Step 2)
```

**C) Kolom ADA dan type DECIMAL(10,2)** ← **GOOD, tapi ada masalah lain**
```
Type: DECIMAL(10,2)
Solusi: Lanjut ke Step 3 (cek MySQL strict mode)
```

---

## Step 2: Jalankan Migration di Hosting

### Opsi A: Via SSH (Terbaik)

```bash
cd /var/www/html/oro3departemen
php spark migrate --group produksi
```

Output seharusnya:
```
Running all new migrations...
  App\Database\Migrations\AddGumpalanDecimalToProduksiHeaders: 2026-07-31-000002_AddGumpalanDecimalToProduksiHeaders migrated
```

### Opsi B: Via Browser (Jika Tidak Ada SSH)

1. **Upload file `run_migration.php` ke root hosting:**
   - Source: (dari lokal) `run_migration.php`
   - Destination: `https://oro3departemen.my.id/run_migration.php`

2. **Akses file via browser:**
   ```
   https://oro3departemen.my.id/run_migration.php
   ```

3. **Lihat output:**
   - Jika hijau "✅ Migration BERHASIL" → Selesai
   - Jika merah "❌ Migration GAGAL" → Lihat error message, lanjut ke Opsi C

4. **HAPUS file `run_migration.php` setelah selesai!**
   ```bash
   rm /var/www/html/oro3departemen/run_migration.php
   ```

### Opsi C: Manual Query di phpMyAdmin (Jika Migration Gagal)

1. **Buka phpMyAdmin**

2. **Pilih database: `oroproduksi`**

3. **Tab: "SQL"**

4. **Copy-paste query ini:**

```sql
-- Step 1: Cek apakah kolom sudah ada
-- (Skip jika tidak ada error)
-- ALTER TABLE produksi_headers DROP COLUMN gumpalan;

-- Step 2: Tambah kolom gumpalan dengan DECIMAL
ALTER TABLE produksi_headers 
ADD COLUMN gumpalan DECIMAL(10,2) DEFAULT 0.00 NULL 
AFTER total_downtime;

-- Step 3: Verifikasi
DESCRIBE produksi_headers;
```

5. **Klik "Go"**

6. **Verifikasi di hasil:**
   - Cari `gumpalan` dengan type `decimal(10,2)`

---

## Step 3: Cek MySQL Strict Mode (Jika Kolom DECIMAL Tapi Masih Pembulatan)

Jika kolom sudah DECIMAL tapi masih ada pembulatan, kemungkinan ada `STRICT_TRANS_TABLES` mode.

### Cek via phpMyAdmin

1. **Tab: "SQL"**

2. **Query ini:**

```sql
SELECT @@GLOBAL.sql_mode;
SELECT @@SESSION.sql_mode;
```

3. **Lihat output:**
   - Jika ada `STRICT_TRANS_TABLES` → Bisa menyebabkan rounding

### Alternatif: Cek via Kolom Casting

```sql
-- Query test
SELECT 
    id,
    nomor_spk,
    gumpalan,
    CAST(gumpalan AS DECIMAL(10,2)) as gumpalan_cast,
    CAST(gumpalan AS FLOAT) as gumpalan_float,
    CAST(gumpalan AS VARCHAR) as gumpalan_varchar
FROM produksi_headers 
ORDER BY id DESC 
LIMIT 1;
```

---

## Step 4: Verifikasi Kolom Type di Controller

Pastikan controller tidak melakukan casting:

**File:** `app/Controllers/Produksi.php`

Cari baris gumpalan:
```php
$gumpalanValue = $this->request->getPost('gumpalan') ?: '0';

$headerData = [
    'gumpalan' => $gumpalanValue,  // ✅ BENAR - simpan as-is
    // Bukan: 'gumpalan' => (int)$gumpalanValue,  ❌ SALAH
    // Bukan: 'gumpalan' => round($gumpalanValue), ❌ SALAH
];
```

---

## Step 5: Test di Hosting

### Setelah migration selesai:

1. **Login ke dashboard hosting**
   - URL: `https://oro3departemen.my.id/produksi`

2. **Buka form Tambah Laporan Produksi**

3. **Input field Gumpalan:**
   - Ketik: `1.2` → Simpan
   - Ketik: `1.6` → Simpan
   - Ketik: `2.5` → Simpan

4. **Buka Detail masing-masing**
   - Cek apakah nilai tetap atau berubah

5. **Verifikasi di database:**

```sql
SELECT id, nomor_spk, gumpalan 
FROM produksi_headers 
ORDER BY id DESC 
LIMIT 3;
```

Expected output:
```
+----+-----------+----------+
| id | nomor_spk | gumpalan |
+----+-----------+----------+
| 1  | XXXX      | 1.20     |
| 2  | XXXX      | 1.60     |
| 3  | XXXX      | 2.50     |
+----+-----------+----------+
```

---

## Checklist Debugging

- [ ] Buka phpMyAdmin hosting
- [ ] Tab "Structure" tabel produksi_headers
- [ ] **HASIL: Kolom gumpalan ada/tidak ada?**
  - Jika tidak ada → Lanjut Opsi A/B/C di Step 2
  - Jika ada INT → Lanjut Opsi C di Step 2
  - Jika ada DECIMAL → Lanjut ke Step 3

- [ ] **Setelah migration:**
  - Cek lagi kolom gumpalan type DECIMAL(10,2)
  
- [ ] **MySQL Strict Mode:**
  - Query: `SELECT @@GLOBAL.sql_mode;`
  - Ada STRICT_TRANS_TABLES?

- [ ] **Test input:**
  - Ketik 1.2 di form
  - Simpan
  - Buka detail
  - Cek database
  - Nilai sesuai? ✓

---

## Troubleshooting Tambahan

### Error: "Migration X already exists"
**Solusi:** Migration sudah pernah dijalankan (tanda di tabel `migrations`)
- Ini OK, tidak perlu khawatir
- Lanjut ke Step 3

### Error: "Column already exists"
**Solusi:** Kolom gumpalan sudah ada dari sebelumnya
```sql
-- Cek type kolom saat ini
DESCRIBE produksi_headers;
-- Jika type INT/TINYINT, perlu drop dulu:
ALTER TABLE produksi_headers DROP COLUMN gumpalan;
-- Baru jalankan migration lagi
```

### Error: "Access denied for user"
**Solusi:** phpMyAdmin tidak punya akses
- Hubungi hosting provider
- Minta mereka jalankan migration via SSH

---

## Contact Hosting Provider (Jika Stuck)

Jika tidak bisa via browser atau SSH, minta hosting provider:

> "Tolong jalankan command ini di server:
> ```bash
> cd /path/to/oro3departemen
> php spark migrate --group produksi
> ```
> 
> Atau execute query ini di database oroproduksi:
> ```sql
> ALTER TABLE produksi_headers 
> ADD COLUMN gumpalan DECIMAL(10,2) DEFAULT 0.00 NULL 
> AFTER total_downtime;
> ```"

---

## Summary

**Masalah:** Migration tidak dijalankan di hosting
**Solusi:** Jalankan migration Step 2 (Opsi A/B/C)
**Result:** Input 1.2 tetap 1.2 ✅
