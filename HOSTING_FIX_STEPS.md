# Langkah Perbaikan Error "Gagal menyimpan header produksi" di Hosting

## Penyebab Error
Field `auto_user_ttd` digunakan di controller tapi **tidak ada di database hosting**.

## Solusi

### 1. Upload File Migration Baru
Upload file ini ke hosting:
```
app/Database/Migrations/2026-07-31-000001_AddAutoUserTtdToProduksiHeaders.php
```

### 2. Jalankan Migration di Hosting

**Via SSH/Terminal:**
```bash
cd /path/to/your/hosting/directory
php spark migrate --group produksi
```

**Via Browser (jika tidak ada SSH):**

Buat file temporary `migrate.php` di root hosting:
```php
<?php
require 'vendor/autoload.php';

$pathsConfig = APPPATH . 'Config/Paths.php';
require $pathsConfig;
$paths = new Config\Paths();

$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

$migrate = \Config\Services::migrations();
$migrate->setGroup('produksi');

try {
    $result = $migrate->latest();
    if ($result === false) {
        echo "Migration GAGAL: " . implode(', ', $migrate->getCliMessages());
    } else {
        echo "Migration BERHASIL!<br>";
        echo "Sekarang coba tambah data produksi lagi.";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
```

**Akses:** `https://oro3departemen.my.id/migrate.php`

**PENTING:** Hapus file `migrate.php` setelah berhasil!

### 3. Verifikasi

Setelah migration sukses, coba tambah data produksi lagi.

Error seharusnya hilang.

---

## Alternatif: Manual Query (Jika Migration Gagal)

Jalankan query ini di phpMyAdmin hosting (database `oroproduksi`):

```sql
ALTER TABLE `produksi_headers` 
ADD COLUMN `auto_user_ttd` VARCHAR(255) NULL 
AFTER `ttd_spv`;
```

---

## Catatan Teknis

**Penjelasan:**
- Controller `Produksi::simpan()` mengirim field `auto_user_ttd`
- Model `MProduksiHeader` memiliki `auto_user_ttd` di `$allowedFields`
- Tapi database hosting tidak punya kolom ini
- Saat insert, MySQL menolak karena field tidak dikenal

**Field ini untuk:**
Menyimpan TTD operator yang auto-filled berdasarkan shift (dari tanda tangan yang sudah tersimpan di profil user).

**Testing:**
Setelah fix, error log yang lebih detail sudah ditambahkan di `Produksi::simpan()`. Jika masih error, pesan akan menampilkan detail lengkap.
