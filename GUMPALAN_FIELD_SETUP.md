# Field Gumpalan - Setup Desimal (1.1)

## Ringkasan

Untuk mendukung input desimal seperti `1.1`, `2.5`, dll, berikut adalah konfigurasi yang benar:

---

## 1. HTML Form Input

**File:** `app/Views/produksi/partials/form_fields.php`

```html
<input type="number" 
    name="gumpalan" 
    class="form-control form-control-lg gumpalan" 
    placeholder="0.0"
    step="0.1"
    value="<?= $isEdit ? $header['gumpalan'] : '' ?>">
```

**Penjelasan:**
- `type="number"` - input numerik (browser akan validate format angka)
- `step="0.1"` - user bisa increment/decrement dengan step 0.1 (1.1, 1.2, 1.3, dst)
- `placeholder="0.0"` - hint format desimal

**Alternatif lain:**
- `step="0.01"` - jika mau precision 2 desimal (1.11, 1.25, dst)
- `step="0.001"` - jika mau precision 3 desimal (1.111, 1.253, dst)

---

## 2. Database MySQL - Column Type

**File:** `app/Database/Migrations/2026-07-31-000002_AddGumpalanDecimalToProduksiHeaders.php`

```php
'gumpalan' => [
    'type'       => 'DECIMAL',
    'constraint' => '10,2',      // 10 digit total, 2 di belakang koma
    'default'    => 0.00,
    'null'       => true,
    'after'      => 'total_downtime',
],
```

**Penjelasan:**
- `DECIMAL(10,2)` = bisa simpan nilai dari `-99999999.99` hingga `99999999.99`
- `10` = total 10 digit (termasuk yang di belakang koma)
- `2` = 2 digit di belakang koma
- Cocok untuk: 0, 0.1, 1.5, 10.25, 123.99, dst

**Alternatif MySQL Types:**
```sql
-- Jika mau lebih presisi:
DECIMAL(10,3)   -- sampai 3 desimal (1.123)
DECIMAL(10,4)   -- sampai 4 desimal (1.1234)

-- Jika mau lebih besar:
DECIMAL(12,2)   -- sampai 99999999999.99
DECIMAL(15,2)   -- untuk nilai sangat besar
```

---

## 3. Model - AllowedFields

**File:** `app/Models/MProduksiHeader.php`

Pastikan `gumpalan` sudah di `$allowedFields`:

```php
protected $allowedFields    = [
    'nomor_spk', 'nama_mesin', 'nama_produk', 'batch_number', 'shift', 'grup', 
    'nomor_mesin', 'packing', 'cycle_time', 'target', 'isi', 'tanggal', 
    'operator', 'catatan', 'grand_total_bagus', 'grand_total_reject', 
    'total_downtime', 'sisa_po', 'hold', 'gumpalan',  // ← gumpalan ada di sini
    'ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv', 'auto_user_ttd'
];
```

---

## 4. Display di Detail Modal

**File:** `app/Views/produksi/modals/detail.php`

Tampilkan dengan 2 desimal:

```php
<div class="fw-bold text-secondary" style="font-size: 0.95rem;">
    <?= number_format($header['gumpalan'] ?: 0, 2) ?>
</div>
```

---

## 5. Implementasi Langkah-Langkah

### Lokal Development:

1. **Update migration** ke database lokal:
   ```bash
   php spark migrate --group produksi
   ```

2. **Test input** form Gumpalan - harusnya bisa input 1.1, 2.5, dll

3. **Verifikasi database**:
   ```sql
   SELECT id, gumpalan FROM produksi_headers LIMIT 5;
   -- Output: 0.00, 1.10, 2.50, etc
   ```

### Production Server:

1. **Upload migration file:**
   - `app/Database/Migrations/2026-07-31-000002_AddGumpalanDecimalToProduksiHeaders.php`

2. **Jalankan migration** (via SSH):
   ```bash
   cd /path/to/hosting
   php spark migrate --group produksi
   ```

   **Atau via Browser** (jika tidak ada SSH):
   - Upload file `run_migration.php` dari task sebelumnya
   - Akses: `https://oro3departemen.my.id/run_migration.php`
   - **HAPUS file setelah selesai!**

3. **Verifikasi kolom ada**:
   ```sql
   DESC produksi_headers;
   -- Cari kolom "gumpalan" dengan type "decimal(10,2)"
   ```

---

## Ringkasan Tipe Data

| Komponen | Tipe | Nilai Contoh |
|----------|------|-------------|
| HTML Input | `type="number"` `step="0.1"` | 1.1, 2.5, 10.9 |
| MySQL Database | `DECIMAL(10,2)` | 1.10, 2.50, 10.90 |
| Model | allowedFields | ✓ included |
| Display | `number_format(x, 2)` | 1.10, 2.50 |

---

## Testing Checklist

- [ ] Migration file dibuat di `app/Database/Migrations/`
- [ ] Jalankan `php spark migrate --group produksi`
- [ ] Kolom `gumpalan` sudah ada di `produksi_headers`
- [ ] Tipe kolom: `DECIMAL(10,2)`
- [ ] Form input Gumpalan terima 1.1, 2.5, dst
- [ ] Simpan data, ambil detail - nilai desimal tetap tersimpan
- [ ] Display di modal detail menampilkan 2 desimal (1.10, 2.50)

---

## Troubleshooting

### Error: Column 'gumpalan' doesn't exist
**Solusi:** Migration belum dijalankan. Jalankan: `php spark migrate --group produksi`

### Input 1.1 tapi ternyata tersimpan sebagai 1 (INT)
**Solusi:** Kolom masih INT, bukan DECIMAL. Jalankan migration.

### Nilai tampil 1 bukannya 1.10
**Solusi:** Format display belum dikonfigurasi. Pakai `number_format($val, 2)` di view.

---

## Referensi MySQL DECIMAL

```sql
-- Current setup:
ALTER TABLE produksi_headers 
ADD COLUMN gumpalan DECIMAL(10,2) DEFAULT 0.00 NULL;

-- Insert sample values:
INSERT INTO produksi_headers (nomor_spk, gumpalan) 
VALUES ('2926', 1.10), ('2927', 2.50), ('2928', 10.99);

-- Query:
SELECT gumpalan FROM produksi_headers;
-- Result: 1.10, 2.50, 10.99
```
