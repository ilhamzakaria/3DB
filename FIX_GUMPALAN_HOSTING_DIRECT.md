# Fix Gumpalan Decimal(25,0) → Decimal(10,2) di Hosting

## Masalah Ditemukan
```
Kolom gumpalan di hosting: decimal(25,0)
                           ↑ 0 desimal = pembulatan otomatis!

Seharusnya: decimal(10,2)
            ↑ 2 desimal = simpan 1.20, 1.60, dst
```

**Contoh:**
- Input: `1.2` 
- Tersimpan (25,0): `1` ← Pembulatan! ❌
- Tersimpan (10,2): `1.20` ← Benar! ✅

---

## Solusi CEPAT: Query Manual di phpMyAdmin

### Step 1: Buka phpMyAdmin
- URL: `https://oro3departemen.my.id/phpmyadmin`
- Login dengan credential hosting

### Step 2: Pilih Database & Jalankan Query

1. **Di sidebar kiri:** Klik database `oroproduksi`
2. **Tab: "SQL"** (di bagian atas)
3. **Copy-paste query ini:**

```sql
-- Ubah kolom gumpalan dari decimal(25,0) menjadi decimal(10,2)
ALTER TABLE produksi_headers 
MODIFY COLUMN gumpalan DECIMAL(10,2) DEFAULT 0.00 NULL;

-- Verifikasi
DESCRIBE produksi_headers;
```

4. **Klik: "Go"** (tombol biru di bawah query)

### Step 3: Verifikasi Berhasil

Lihat output DESCRIBE - kolom gumpalan seharusnya berubah:
```
Sebelum: decimal(25,0)
Sesudah: decimal(10,2)  ✅
```

---

## Solusi ALTERNATIF: Via Migration

Jika ingin lebih clean, jalankan migration:

### Via SSH (Terbaik):
```bash
cd /var/www/html/oro3departemen
php spark migrate --group produksi
```

### Via Browser (Jika tidak ada SSH):

1. **Upload file:** `run_migration.php` ke root hosting
2. **Akses:** `https://oro3departemen.my.id/run_migration.php`
3. **Lihat output:** Jika hijau "✅ BERHASIL"
4. **HAPUS file** setelah selesai

---

## Test Setelah Fix

### 1. Buka Form Produksi
```
https://oro3departemen.my.id/produksi
→ Klik "Tambah Laporan Produksi"
```

### 2. Input Field Gumpalan
```
Ketik: 1.2  → Simpan
Ketik: 1.6  → Simpan
Ketik: 2.5  → Simpan
```

### 3. Buka Detail & Verifikasi
```
Buka masing-masing record
Cek field Gumpalan di "Status Produksi"
Harus tetap: 1.20, 1.60, 2.50 ✅
```

### 4. Verifikasi di Database
```sql
SELECT id, nomor_spk, gumpalan 
FROM produksi_headers 
ORDER BY id DESC 
LIMIT 5;
```

Expected output:
```
+----+-----------+----------+
| id | nomor_spk | gumpalan |
+----+-----------+----------+
| 1  | 2926      | 1.20     |
| 2  | 2927      | 1.60     |
| 3  | 2928      | 2.50     |
+----+-----------+----------+
```

---

## Checklist

- [ ] Buka phpMyAdmin hosting
- [ ] Jalankan query ALTER TABLE (ubah decimal(25,0) → decimal(10,2))
- [ ] Verifikasi kolom berubah di DESCRIBE
- [ ] Test input 1.2 di form
- [ ] Simpan & buka detail
- [ ] Verifikasi di database query
- [ ] Hasil tetap 1.20 (bukan 1) ✅

---

## Penjelasan

**Mengapa `decimal(25,0)` bermasalah?**
- `25` = total digit yang bisa disimpan
- `0` = **jumlah digit di belakang koma**
- Decimal(25,0) = hanya bisa integer (1, 2, 3, bukan 1.2, 1.6)
- Saat user input 1.2 → MySQL otomatis bulatkan ke 1

**Mengapa `decimal(10,2)` benar?**
- `10` = total 10 digit
- `2` = **2 digit di belakang koma**
- Decimal(10,2) = bisa simpan: 12345678.99 (max)
- Saat user input 1.2 → MySQL simpan persis 1.20

---

## Result yang Diharapkan

**Sebelum Fix:**
```
Input: 1.2  →  Database: 1     ❌
Input: 1.6  →  Database: 2     ❌
Input: 2.5  →  Database: 3     ❌
```

**Setelah Fix:**
```
Input: 1.2  →  Database: 1.20  ✅
Input: 1.6  →  Database: 1.60  ✅
Input: 2.5  →  Database: 2.50  ✅
```

---

**Yuk segera jalankan fix! Problem solved! 🎉**
