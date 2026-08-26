# Fix: Warehouse2 Database Connection Errors

## Problem Identified

When trying to add data to "Barang Masuk dari Produksi" tab, users were getting HTTP 500 error:
```
Gagal Menyimpan Data - HTTP error! status: 500
```

## Root Cause

The validation rules in Warehouse2Controller used CodeIgniter's `unique[table.column]` syntax:
```php
$validation->setRules([
    'no_dokumen' => 'required|unique[barang_masuk_dari_produksi.no_dokumen]',
    ...
]);
```

**Problem**: CodeIgniter validation always checks against the **default database connection**, even though:
- The Model was configured to use the `warehouse` database with `protected $db = 'warehouse'`
- The actual table is in the `orowarehouse` database, not `oromaster`

This caused the validation to:
1. Look for table `barang_masuk_dari_produksi` in the default `oromaster` database
2. Fail because the table doesn't exist there (it's only in `orowarehouse`)
3. Throw an exception which resulted in HTTP 500 error

## Solution Implemented

Replaced all CodeIgniter validation rules with **manual validation** in all Store methods:

### Methods Fixed:
1. `storePemasok()` - Manual validation for barang_masuk_pemasok
2. `storeProduksi()` - Manual validation for barang_masuk_produksi  
3. `storePembeli()` - Manual validation for barang_keluar_pembeli
4. `storeMasukProduksi()` - Manual validation for barang_masuk_dari_produksi

### Validation Approach:

Instead of using CodeIgniter rules, we now:

```php
// Manual validation - validates against the correct database
$errors = [];

// Check required fields
if (!$noDokumen) {
    $errors['no_dokumen'] = 'No Dokumen harus diisi';
}

// Check uniqueness by querying the model directly
if ($noDokumen && !isset($errors['no_dokumen'])) {
    $existing = $this->masukProduksiModel->where('no_dokumen', $noDokumen)->first();
    if ($existing) {
        $errors['no_dokumen'] = 'No Dokumen sudah digunakan';
    }
}

if (!empty($errors)) {
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors,
    ])->setStatusCode(422);
}
```

**Why this works**: 
- The model instance (`$this->masukProduksiModel`) is configured with `protected $db = 'warehouse'`
- When we query the model, it automatically uses the `warehouse` database connection
- This works regardless of which database is "default"

## Files Modified

`app/Controllers/Warehouse2.php`:
- storePemasok() - lines ~168
- storeProduksi() - lines ~230
- storePembeli() - lines ~290
- storeMasukProduksi() - lines ~353

## Testing

To verify the fix works:

1. **Add data via Warehouse2 interface**:
   - Tab 1: Barang Masuk dari Pemasok
   - Tab 2: Barang Masuk Produksi
   - Tab 3: Barang Keluar Pembeli
   - Tab 4: **Barang Masuk dari Produksi** (previously failing)

2. **Expected behavior**:
   - Form submits successfully
   - Alert shows "Data berhasil ditambahkan"
   - Modal closes automatically
   - New data appears in the table

3. **Verify data in database**:
   - Open phpMyAdmin
   - Select `orowarehouse` database
   - Check relevant table for new records

## Database Configuration Reference

**Configuration**: `.env`
```ini
database.warehouse.hostname = localhost
database.warehouse.database = orowarehouse
database.warehouse.username = root
database.warehouse.password = root
```

**Model Configuration**: All warehouse models have
```php
protected $db = 'warehouse';
```

## Additional Notes

- All error messages returned are in the same format as before
- HTTP status codes are preserved (422 for validation, 500 for exceptions)
- Client-side code doesn't need any changes - the fix is transparent
- This approach is more reliable as it validates against the actual database being used

## Future Recommendations

1. Consider creating a base validation service/trait to avoid duplicating validation logic
2. Document the pattern of using manual validation for non-default database connections
3. Consider adding logging for validation errors for debugging purposes
