# Warehouse2 Database Migration Verification Guide

## Migration Summary
- **Source Database**: `oromaster` (original)
- **Target Database**: `orowarehouse` (new)
- **Migration Date**: 2026-07-11
- **Tables Migrated**: 5 tables

## Tables Affected

| No | Table Name | Model File | Status |
|---|---|---|---|
| 1 | barang_masuk_pemasok | MBarangMasukPemasok.php | ✓ Updated |
| 2 | barang_masuk_produksi | MBarangMasukProduksi.php | ✓ Updated |
| 3 | barang_keluar_pembeli | MBarangKeluarPembeli.php | ✓ Updated |
| 4 | barang_masuk_dari_produksi | MBarangMasukDariProduksi.php | ✓ Updated |
| 5 | orowarehouse | MOrowarehouse.php | ✓ Updated |

## Configuration Changes

### 1. .env File Update ✓
```
database.warehouse.hostname = localhost
database.warehouse.database = orowarehouse
database.warehouse.username = root
database.warehouse.password = root
```

### 2. Model Files Updated ✓
Added `protected $db = 'warehouse';` to all 5 model classes

### 3. SQL Migration Script Created ✓
Location: `catatan/migrate_oromaster_to_orowarehouse.sql`

## Verification Steps

### Step 1: Execute SQL Migration Script
**Location**: phpMyAdmin or MySQL CLI

```
File: catatan/migrate_oromaster_to_orowarehouse.sql
```

**To execute in phpMyAdmin**:
1. Open phpMyAdmin
2. Select database: `orowarehouse`
3. Click on "SQL" tab
4. Copy-paste contents of `migrate_oromaster_to_orowarehouse.sql`
5. Click "Go" to execute

**Expected Result**:
- 5 tables created in `orowarehouse` database
- All tables have proper schema with timestamps and soft delete

### Step 2: Optional - Copy Existing Data
**If you want to migrate existing data from oromaster**:

Uncomment these lines in the SQL script:
```sql
INSERT INTO orowarehouse.barang_masuk_pemasok SELECT * FROM oromaster.barang_masuk_pemasok WHERE deleted_at IS NULL;
INSERT INTO orowarehouse.barang_masuk_produksi SELECT * FROM oromaster.barang_masuk_produksi WHERE deleted_at IS NULL;
INSERT INTO orowarehouse.barang_keluar_pembeli SELECT * FROM oromaster.barang_keluar_pembeli WHERE deleted_at IS NULL;
INSERT INTO orowarehouse.barang_masuk_dari_produksi SELECT * FROM oromaster.barang_masuk_dari_produksi WHERE deleted_at IS NULL;
INSERT INTO orowarehouse.orowarehouse SELECT * FROM oromaster.orowarehouse WHERE deleted_at IS NULL;
```

### Step 3: Test Warehouse2 Functionality

**URL**: `http://localhost:8080/warehouse2`

**Test Cases**:

#### Tab 1: Barang Masuk dari Pemasok
- [ ] Load data (GET request)
- [ ] Add new record (POST request to storePemasok)
- [ ] Edit existing record
- [ ] Delete record (soft delete)
- [ ] Verify data persists in `orowarehouse.barang_masuk_pemasok`

#### Tab 2: Barang Masuk Produksi
- [ ] Load data (GET request)
- [ ] Add new record (POST request to storeProduksi)
- [ ] Edit existing record
- [ ] Delete record (soft delete)
- [ ] Verify data persists in `orowarehouse.barang_masuk_produksi`

#### Tab 3: Barang Keluar Pembeli
- [ ] Load data (GET request)
- [ ] Add new record (POST request to storePembeli)
- [ ] Edit existing record
- [ ] Delete record (soft delete)
- [ ] Verify data persists in `orowarehouse.barang_keluar_pembeli`

#### Tab 4: Barang Masuk dari Produksi
- [ ] Load data (GET request)
- [ ] Add new record (POST request to storeMasukProduksi)
- [ ] Edit existing record
- [ ] Delete record (soft delete)
- [ ] Verify data persists in `orowarehouse.barang_masuk_dari_produksi`

#### Tab 5: Warehouse 2
- [ ] Load data with filters
- [ ] Add new record
- [ ] Edit existing record
- [ ] Delete record (soft delete)
- [ ] Verify data persists in `orowarehouse.orowarehouse`

### Step 4: Database Verification

**Via phpMyAdmin**:
1. Select database: `orowarehouse`
2. Verify all 5 tables exist and contain data (if migrated)
3. Check table structure matches expected schema
4. Check row count in each table

**Via MySQL CLI**:
```sql
USE orowarehouse;
SHOW TABLES;
SELECT COUNT(*) as total_records FROM barang_masuk_pemasok WHERE deleted_at IS NULL;
SELECT COUNT(*) as total_records FROM barang_masuk_produksi WHERE deleted_at IS NULL;
SELECT COUNT(*) as total_records FROM barang_keluar_pembeli WHERE deleted_at IS NULL;
SELECT COUNT(*) as total_records FROM barang_masuk_dari_produksi WHERE deleted_at IS NULL;
SELECT COUNT(*) as total_records FROM orowarehouse WHERE deleted_at IS NULL;
```

### Step 5: Browser Console Verification

Open Developer Tools (F12) → Console tab

**Expected**: No database-related errors
- ✓ Data loads without errors
- ✓ CRUD operations complete successfully
- ✓ No "Connection refused" errors
- ✓ No "Table not found" errors

## Rollback Plan (If Needed)

If issues occur:

1. **Revert to oromaster** (temporary):
   - Edit all Model files: Remove `protected $db = 'warehouse';`
   - Change to: `protected $db = 'default';` (or remove line to use default)
   - Restart application

2. **Preserve orowarehouse data**:
   - If orowarehouse data is good, keep it
   - Can drop and recreate empty tables if needed
   - Use SQL script to re-run table creation

3. **Full rollback**:
   - Drop orowarehouse database
   - Remove warehouse connection from .env
   - Revert Model files to use default connection
   - Test with oromaster

## Troubleshooting

### Error: "Table not found"
- **Cause**: SQL script not executed or tables not created
- **Solution**: Execute SQL migration script in phpMyAdmin

### Error: "Connection refused"
- **Cause**: Database connection config incorrect
- **Solution**: Verify .env database.warehouse settings

### Error: "Unknown database"
- **Cause**: orowarehouse database doesn't exist in MySQL
- **Solution**: Create database in phpMyAdmin first

### Data not persisting
- **Cause**: Model not using 'warehouse' database connection
- **Solution**: Verify `protected $db = 'warehouse';` exists in all 5 model files

## Files Modified

1. `.env` - Added database.warehouse connection
2. `app/Models/MBarangMasukPemasok.php` - Added database binding
3. `app/Models/MBarangMasukProduksi.php` - Added database binding
4. `app/Models/MBarangKeluarPembeli.php` - Added database binding
5. `app/Models/MBarangMasukDariProduksi.php` - Added database binding
6. `app/Models/MOrowarehouse.php` - Added database binding

## Migration Status Checklist

- [x] Task #1: Add database connection to .env
- [x] Task #2: Create SQL migration script
- [x] Task #3: Update Warehouse2Controller (no changes needed)
- [x] Task #4: Update all Model files with database binding
- [ ] Task #5: Execute SQL script and verify functionality
- [ ] Task #6: Document migration completion

## Next Steps

1. Execute the SQL migration script in phpMyAdmin
2. Run all test cases from Step 3
3. Verify data in phpMyAdmin
4. Document any issues found
5. Create final migration report
