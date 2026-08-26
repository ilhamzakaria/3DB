# Fix Gumpalan Decimal - Panduan Lengkap

## Masalah
Input `1.2` berubah jadi `1`, input `1.6` berubah jadi `2` saat disimpan.

## Penyebab
1. ✅ **Form input sudah benar** - type text dengan inputmode decimal
2. ✅ **Controller sudah benar** - tidak ada casting yang salah
3. ❌ **Database belum ada kolom gumpalan** - PERLU MIGRATION!

---

## Solusi: Jalankan Migration

### Step 1: Pastikan Migration File Ada

File migration sudah dibuat di:
```
app/Database/Migrations/2026-07-31-000002_AddGumpalanDecimalToProduksiHeaders.php
```

Isi migration:
```php
public function up()
{
    $this->forge->addColumn('produksi_headers', [
        'gumpalan' => [
            'type'       => 'DECIMAL',
            'constraint' => '10,2',
            'default'    => 0.00,
            'null'       => true,
            'after'      => 'total_downtime',
        ],
    ]);
}
```

### Step 2: Jalankan Migration

**Di Lokal Development:**
```bash
php spark migrate --group produksi
```

**Di Server Hosting (via SSH):**
```bash
cd /path/to/oro3departemen
php spark migrate --group produksi
```

**Di Server Hosting (via Browser - jika tidak ada SSH):**
1. Upload file `run_migration.php` ke root hosting
2. Akses: `https://oro3departemen.my.id/run_migration.php`
3. **HAPUS file run_migration.php setelah selesai!**

### Step 3: Verifikasi Database

Query untuk cek kolom sudah ada:
```sql
DESCRIBE produksi_headers;
```

Cari kolom `gumpalan` - seharusnya muncul dengan type `decimal(10,2)`

---

## Testing

### Test Input Desimal

1. **Buka form Tambah Laporan Produksi**
   - URL: `http://localhost:8080/produksi`
   - Klik tombol "Tambah Laporan"

2. **Input nilai di field Gumpalan:**
   - Ketik: `1.2` → Harus tetap `1.2`
   - Ketik: `1.6` → Harus tetap `1.6`
   - Ketik: `2.5` → Harus tetap `2.5`
   - Ketik: `10.99` → Harus tetap `10.99`

3. **Simpan data**

4. **Buka Detail Laporan Produksi**
   - Lihat bagian "Status Produksi"
   - Gumpalan seharusnya menampilkan nilai desimal yang sama

### Verifikasi Database

Query untuk cek data:
```sql
SELECT id, nomor_spk, gumpalan 
FROM produksi_headers 
ORDER BY id DESC 
LIMIT 5;
```

Output seharusnya:
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

## Penjelasan Teknis

### 1. Form Input (View)
```html
<input type="text" 
    name="gumpalan" 
    inputmode="decimal"
    pattern="[0-9]*\.?[0-9]*">
```
- **type="text"** - tidak ada auto-rounding dari browser
- **inputmode="decimal"** - keyboard smartphone tunjukkan numeric keypad
- **pattern** - validasi regex untuk desimal

### 2. Controller (PHP)
```php
$gumpalanValue = $this->request->getPost('gumpalan') ?: '0';

$headerData = [
    'gumpalan' => $gumpalanValue,  // Simpan as-is (string "1.2")
];
```
- Tidak ada casting `(int)` atau `round()`
- Nilai dikirim ke database apa adanya

### 3. Database (MySQL)
```sql
ALTER TABLE produksi_headers 
ADD COLUMN gumpalan DECIMAL(10,2) DEFAULT 0.00;
```
- **DECIMAL(10,2)** - simpan 10 digit total, 2 di belakang koma
- MySQL otomatis convert string "1.2" jadi decimal `1.20`
- Tidak ada pembulatan

### 4. Display (View)
```php
<?= number_format($header['gumpalan'], 2) ?>
```
- Format dengan 2 desimal: `1.20`, `1.60`, `2.50`

---

## Troubleshooting

### ❌ Error: Column 'gumpalan' doesn't exist
**Solusi:** Migration belum dijalankan
```bash
php spark migrate --group produksi
```

### ❌ Input 1.2 tapi tersimpan sebagai 1
**Penyebab:** Kolom masih INT atau TINYINT (bukan DECIMAL)
**Solusi:** 
1. Drop kolom lama (jika ada): 
   ```sql
   ALTER TABLE produksi_headers DROP COLUMN gumpalan;
   ```
2. Jalankan migration lagi

### ❌ Input 1.2 tapi tersimpan sebagai 1.00
**Penyebab:** Controller melakukan `(int)` casting
**Solusi:** Sudah diperbaiki - pastikan pakai versi code terbaru

---

## Summary Perubahan

### Files Modified:
1. ✅ `app/Views/produksi/partials/form_fields.php` - Input type text dengan validation
2. ✅ `app/Controllers/Produksi.php` - Controller simpan() dan update() - no casting
3. ✅ `app/Models/MProduksiHeader.php` - gumpalan di allowedFields
4. ✅ `app/Database/Migrations/2026-07-31-000002_AddGumpalanDecimalToProduksiHeaders.php` - Migration DECIMAL

### Migration Command:
```bash
php spark migrate --group produksi
```

### Expected Result:
- Input `1.2` → Saved as `1.20` ✓
- Input `1.6` → Saved as `1.60` ✓
- Input `2.5` → Saved as `2.50` ✓

---

## Checklist

- [ ] Migration file ada di `app/Database/Migrations/`
- [ ] Jalankan: `php spark migrate --group produksi`
- [ ] Verifikasi kolom `gumpalan` ada (type: decimal)
- [ ] Test input 1.2 di form
- [ ] Simpan data
- [ ] Cek database - nilai harus 1.20
- [ ] Cek detail view - nilai harus 1.20

---

**Jika sudah checklist semua, masalah pasti solved!** ✅
