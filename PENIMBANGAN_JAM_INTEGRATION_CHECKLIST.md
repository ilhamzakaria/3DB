# Penimbangan Jam (8-Hour Timed Weighing) - Integration Checklist

**Feature:** Durasi Penimbangan for Mesin Becum Add/Edit forms  
**Last Updated:** August 22, 2026  
**Status:** ✅ Ready for Deployment

---

## Pre-Deployment Checklist

### 1. Database Setup

- [ ] **Run Migration**
  ```bash
  php spark migrate --group produksi
  ```
  - Creates table: `penimbangan_jam`
  - Columns: `id`, `becum_id` (FK), `hour` (1-8), `galon` (DECIMAL 8,2), `flash` (DECIMAL 8,2), `created_at`, `updated_at`
  - Constraints: UNIQUE(becum_id, hour), CASCADE DELETE on becum_id

- [ ] **Verify Migration**
  ```bash
  php spark migrate:status --group produksi
  ```
  - Ensure `2026-09-12-000001_CreatePenimbanganJamTable.php` shows as migrated

- [ ] **Verify Table Structure**
  ```sql
  DESCRIBE penimbangan_jam;
  ```
  - Expected columns:
    - `id` INT PRIMARY KEY AUTO_INCREMENT
    - `becum_id` INT NOT NULL (Foreign Key → mesin_becum.id)
    - `hour` TINYINT NOT NULL (1-8 range)
    - `galon` DECIMAL(8,2)
    - `flash` DECIMAL(8,2)
    - `created_at` TIMESTAMP
    - `updated_at` TIMESTAMP
    - `deleted_at` TIMESTAMP NULL

### 2. Code Verification

- [ ] **Check Model Files**
  - [ ] `app/Models/MPenimbanganJam.php` exists
    - Contains validation rules: galon/flash 0-999.99 numeric
    - Methods: `savePenimbanganData()`, `getPenimbanganByBecumId()`, `getPenimbanganStats()`
  
  - [ ] `app/Repositories/PenimbanganJamRepository.php` exists
    - Methods: `savePenimbanganFromForm()`, `getPenimbanganForForm()`, `getPenimbanganForApi()`, `validatePenimbanganForm()`

- [ ] **Check Controller Methods**
  - [ ] `app/Controllers/MesinBecum.php`
    - `tambahData()` - calls `validatePenimbanganFromRequest()` and `savePenimbanganFromForm()`
    - `update()` - calls `validatePenimbanganFromRequest()` and `savePenimbanganFromForm()`
    - `getPenimbanganSection()` - AJAX endpoint for edit prefill
    - `validatePenimbanganFromRequest()` - server-side validation

  - [ ] `app/Controllers/Traits/MesinModuleTrait.php`
    - `delete()` - deletes penimbangan records before soft delete
    - `deletePermanent()` - deletes penimbangan records before permanent delete

- [ ] **Check View Files**
  - [ ] `app/Views/gudang/mesinbecum.php`
    - Includes penimbangan_jam_section in Add modal
    - Includes penimbangan_jam_section in Edit modal (populated via AJAX)
  
  - [ ] `app/Views/gudang/partials/penimbangan_jam_section.php`
    - Renders 8 hour cards with galon/flash inputs
    - Includes real-time calculations
    - Includes validation error display
    - CSS Grid responsive layout

### 3. Test Execution

- [ ] **Unit Tests**
  ```bash
  php spark test --filter=MPenimbanganJamTest
  ```
  - Expected: 21 tests pass
  - Coverage: Model validation rules, helper functions, data types

  ```bash
  php spark test --filter=PenimbanganJamRepositoryTest
  ```
  - Expected: 12 tests pass
  - Coverage: Form parsing, data transformation, validation

- [ ] **Integration Tests**
  ```bash
  php spark test --filter=PenimbanganJamIntegrationTest
  ```
  - Expected: 21 tests pass
  - Coverage: Database operations, API payloads, delete cascade, statistics

- [ ] **E2E Tests**
  ```bash
  php spark test --filter=PenimbanganJamE2ETest
  ```
  - Expected: 24 tests pass
  - Coverage: Form rendering, prefill, submission, validation display, accessibility

- [ ] **Run All Tests**
  ```bash
  php spark test --filter=PenimbanganJam
  ```
  - Expected: 78 total tests pass (21 + 12 + 21 + 24)

### 4. Feature Testing

#### Add Form Testing

- [ ] **Navigate to Mesin Becum Add**
  - URL: `/gudang/mesin_becum` (click "Tambah Data" button)
  - Modal opens with "Durasi Penimbangan" section

- [ ] **Verify Add Form Structure**
  - [ ] Section title: "Durasi Penimbangan"
  - [ ] 8 hour cards visible (Jam 1 through Jam 8)
  - [ ] Each card has 2 inputs: Galon (kg), Flash (kg)
  - [ ] All inputs default to 0
  - [ ] Summary row shows: Total Galon, Total Flash, Grand Total (all 0)

- [ ] **Test Add Form Submission**
  - [ ] Fill in galon values: 10, 12, 11, 13, 14, 15, 16, 17
  - [ ] Fill in flash values: 8, 9, 8, 10, 11, 12, 13, 14
  - [ ] Click "Simpan" button
  - [ ] Form submits successfully
  - [ ] Page redirects to list view
  - [ ] Flash message: "Data Mesin Becum berhasil disimpan"

- [ ] **Verify Add Form Data Saved**
  - [ ] Query database: `SELECT * FROM penimbangan_jam WHERE becum_id = [last_id] ORDER BY hour`
  - [ ] All 8 records created
  - [ ] Data matches submitted values

#### Edit Form Testing

- [ ] **Navigate to Edit Form**
  - [ ] Click "Edit" button on any mesin_becum row
  - [ ] Modal opens with existing data
  - [ ] Wait for "Durasi Penimbangan" section to load via AJAX

- [ ] **Verify Edit Form Prefill**
  - [ ] All galon inputs prefilled with existing values
  - [ ] All flash inputs prefilled with existing values
  - [ ] Summary totals recalculated and displayed correctly

- [ ] **Test Edit Form Submission**
  - [ ] Change some galon values: +5 to first 4 hours
  - [ ] Change some flash values: +3 to last 4 hours
  - [ ] Click "Simpan" button
  - [ ] Form submits successfully
  - [ ] Page redirects to list view
  - [ ] Flash message: "Data Mesin Becum berhasil diperbarui"

- [ ] **Verify Edit Form Data Updated**
  - [ ] Query database: `SELECT * FROM penimbangan_jam WHERE becum_id = [edited_id] ORDER BY hour`
  - [ ] All 8 records updated with new values
  - [ ] Old values replaced (not appended)

#### Validation Testing

- [ ] **Client-Side Validation**
  - [ ] Enter value "1000" (exceeds max 999.99)
  - [ ] Click submit
  - [ ] JavaScript alert shows: "Galon jam [X] harus antara 0 dan 999.99"
  - [ ] Form not submitted

  - [ ] Enter value "-5" (negative)
  - [ ] Click submit
  - [ ] JavaScript alert shows: "Galon jam [X] harus antara 0 dan 999.99"
  - [ ] Form not submitted

  - [ ] Enter text "abc" (non-numeric)
  - [ ] Click submit
  - [ ] JavaScript alert shows validation error
  - [ ] Form not submitted

- [ ] **Server-Side Validation**
  - [ ] Bypass client validation (disable JavaScript)
  - [ ] Submit form with invalid value "1000"
  - [ ] Page redirects back with flash message: "Ada kesalahan dalam data penimbangan"
  - [ ] Form preserved with error highlighting (is-invalid class)
  - [ ] Error message shown per field

#### Real-Time Calculations

- [ ] **Test Totals Calculation**
  - [ ] Fill all galon inputs with 10.5
  - [ ] Verify "Total Galon" updates to 84 (8 × 10.5)
  - [ ] Fill all flash inputs with 8.2
  - [ ] Verify "Total Flash" updates to 65.6 (8 × 8.2)
  - [ ] Verify "Grand Total" updates to 149.6

- [ ] **Test Incremental Updates**
  - [ ] Change first hour galon from 10.5 to 15
  - [ ] Verify Total Galon updates immediately (+4.5 = 88.5)
  - [ ] Verify Grand Total updates

#### Delete Cascade Testing

- [ ] **Test Soft Delete Cascade**
  - [ ] Find a mesin_becum record with penimbangan data
  - [ ] Verify `penimbangan_jam` records exist for that becum_id
  - [ ] Click Delete button on that record
  - [ ] Record moves to trash
  - [ ] Query database: `SELECT * FROM penimbangan_jam WHERE becum_id = [deleted_id]`
  - [ ] Penimbangan records are deleted

- [ ] **Test Permanent Delete Cascade**
  - [ ] Find a deleted (trashed) mesin_becum record
  - [ ] Click "Permanently Delete" option
  - [ ] Record removed from database completely
  - [ ] Query database: `SELECT * FROM penimbangan_jam WHERE becum_id = [id]`
  - [ ] No records found (penimbangan also permanently deleted)

- [ ] **Test Restore Behavior**
  - [ ] Find a deleted (trashed) mesin_becum record
  - [ ] Click "Restore" option
  - [ ] Record restored to active list
  - [ ] Query database: `SELECT * FROM penimbangan_jam WHERE becum_id = [restored_id]`
  - [ ] Penimbangan records NOT restored (expected: permanently deleted earlier)

### 5. API Response Verification

#### Add/Edit Response

**Request:**
```http
POST /gudang/mesin_becum/tambahData
Content-Type: application/x-www-form-urlencoded

tanggal=2026-08-22
&produk_galon=Gallon
&grade=A+
&brand=AQUA
&berat=750
&nomor_mesin=B.01
&status=ganti bottom rata
&penimbangan_jam[jam_1_galon]=10.5
&penimbangan_jam[jam_1_flash]=8.2
&penimbangan_jam[jam_2_galon]=12.0
&penimbangan_jam[jam_2_flash]=9.5
&penimbangan_jam[jam_3_galon]=11.5
&penimbangan_jam[jam_3_flash]=8.8
&penimbangan_jam[jam_4_galon]=13.0
&penimbangan_jam[jam_4_flash]=10.0
&penimbangan_jam[jam_5_galon]=14.5
&penimbangan_jam[jam_5_flash]=11.2
&penimbangan_jam[jam_6_galon]=15.0
&penimbangan_jam[jam_6_flash]=12.0
&penimbangan_jam[jam_7_galon]=16.5
&penimbangan_jam[jam_7_flash]=13.5
&penimbangan_jam[jam_8_galon]=17.0
&penimbangan_jam[jam_8_flash]=14.0
```

**Response:**
```
Redirect to: /gudang/mesin_becum (302 Found)
Flash Message: "Data Mesin Becum berhasil disimpan"
Database: mesin_becum record created + 8 penimbangan_jam records
```

#### Edit Response

**Request:**
```http
POST /gudang/mesin_becum/update/1
Content-Type: application/x-www-form-urlencoded

id=1
&tanggal=2026-08-22
&produk_galon=Gallon
&grade=A+
&brand=AQUA
&penimbangan_jam[jam_1_galon]=20.5
&penimbangan_jam[jam_1_flash]=18.2
&penimbangan_jam[jam_2_galon]=22.0
&penimbangan_jam[jam_2_flash]=19.5
[... rest of hours ...]
```

**Response:**
```
Redirect to: /gudang/mesin_becum (302 Found)
Flash Message: "Data Mesin Becum berhasil diperbarui"
Database: mesin_becum record updated + 8 penimbangan_jam records replaced
```

#### AJAX Prefill Response

**Request:**
```http
POST /gudang/mesin_becum/getPenimbanganSection
X-Requested-With: XMLHttpRequest
Content-Type: application/x-www-form-urlencoded

id=1
```

**Response:**
```json
{
  "success": true,
  "html": "<div class='penimbangan-section'>...</div>",
  "data": {
    "jam_1_galon": 10.5,
    "jam_1_flash": 8.2,
    "jam_2_galon": 12.0,
    "jam_2_flash": 9.5,
    ...
  }
}
```

#### Validation Error Response

**Request:**
```http
POST /gudang/mesin_becum/tambahData
Content-Type: application/x-www-form-urlencoded

[... with penimbangan_jam[jam_1_galon]=1000 (exceeds max) ...]
```

**Response:**
```
Redirect back to: /gudang/mesin_becum (with form data in session)
Flash Message: "Ada kesalahan dalam data penimbangan. Periksa kembali input Anda."
Form State: Preserved in session with error highlighting
```

### 6. Flash Messages Verification

- [ ] **Add Success Message**
  - Message: "Data Mesin Becum berhasil disimpan"
  - Type: success (green badge)
  - Appears at top of page after redirect

- [ ] **Edit Success Message**
  - Message: "Data Mesin Becum berhasil diperbarui"
  - Type: success (green badge)
  - Appears at top of page after redirect

- [ ] **Penimbangan Save Error Message**
  - Message: "Ada kesalahan dalam data penimbangan. Periksa kembali input Anda."
  - Type: error (red badge)
  - Appears when validation fails
  - Form redirects back with input preserved

- [ ] **Delete Success Message**
  - Message: "Data Mesin Becum berhasil dihapus"
  - Type: success (green badge)

### 7. Database Integrity Verification

- [ ] **Foreign Key Constraints**
  ```sql
  SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE TABLE_NAME = 'penimbangan_jam' AND COLUMN_NAME = 'becum_id';
  ```
  - Verify: Foreign key exists, points to mesin_becum(id), has CASCADE DELETE

- [ ] **Unique Constraint**
  ```sql
  SELECT * FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_NAME = 'penimbangan_jam' AND INDEX_NAME != 'PRIMARY';
  ```
  - Verify: UNIQUE index on (becum_id, hour)

- [ ] **Data Consistency**
  ```sql
  SELECT COUNT(*) as total_records, 
         COUNT(DISTINCT becum_id) as unique_mesin,
         MIN(hour) as min_hour,
         MAX(hour) as max_hour
  FROM penimbangan_jam;
  ```
  - Verify: Expected record counts, hour range 1-8

- [ ] **Decimal Precision**
  ```sql
  SELECT MIN(galon), MAX(galon), MIN(flash), MAX(flash)
  FROM penimbangan_jam;
  ```
  - Verify: Values within 0-999.99 range, precision preserved to 2 decimals

### 8. Logs Verification

- [ ] **Check Application Logs**
  - Location: `app/Logs/`
  - Search for: "penimbangan", "savePenimbanganFromForm", "validatePenimbanganFromRequest"
  - Expected: No error logs for normal operations

- [ ] **Check System Logs**
  - Search for delete cascade operations
  - Expected: Logs showing penimbangan deletion before mesin deletion

### 9. Performance Testing

- [ ] **Load Test - Add Form**
  - Fill all 8 hours, submit 10 times rapidly
  - Expected: All records saved, no duplicates, no missing data

- [ ] **Load Test - Edit Form**
  - Load edit form for record with 8 penimbangan records
  - Expected: AJAX prefill completes within 1 second

- [ ] **Query Performance**
  ```bash
  php spark db:table penimbangan_jam --verbose
  ```
  - Expected: Indices used properly, query execution < 100ms

---

## Deployment Steps

### 1. Pre-Deployment

1. **Backup Database**
   ```bash
   mysqldump -u [user] -p [database] > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Verify Test Suite Passes**
   ```bash
   php spark test --filter=PenimbanganJam
   ```

3. **Review Modified Files**
   - [ ] `app/Models/MPenimbanganJam.php`
   - [ ] `app/Repositories/PenimbanganJamRepository.php`
   - [ ] `app/Controllers/MesinBecum.php`
   - [ ] `app/Controllers/Traits/MesinModuleTrait.php`
   - [ ] `app/Views/gudang/mesinbecum.php`
   - [ ] `app/Views/gudang/partials/penimbangan_jam_section.php`
   - [ ] `app/Database/Migrations/2026-09-12-000001_CreatePenimbanganJamTable.php`

### 2. Deployment

1. **Push Code to Production**
   ```bash
   git add app/
   git commit -m "Deploy: Penimbangan Jam feature (8-hour timed weighing)"
   git push origin main
   ```

2. **Run Migration on Production**
   ```bash
   cd /var/www/oro
   php spark migrate --group produksi
   ```

3. **Verify Migration**
   ```bash
   php spark migrate:status --group produksi
   ```

4. **Clear Application Cache** (if applicable)
   ```bash
   php spark cache:clear
   ```

### 3. Post-Deployment Validation

1. **Verify UI in Production**
   - [ ] Add form renders penimbangan section
   - [ ] Edit form loads and prefills data
   - [ ] Real-time calculations work
   - [ ] Validation errors display correctly

2. **Verify Database in Production**
   - [ ] Table exists and has correct structure
   - [ ] Sample records created
   - [ ] Cascade delete works

3. **Monitor Logs**
   - [ ] No error logs after deployment
   - [ ] Monitor for 24 hours for any issues

4. **User Acceptance Testing**
   - [ ] Have operations team test add form
   - [ ] Have operations team test edit form
   - [ ] Have operations team test validation
   - [ ] Collect feedback

---

## Rollback Plan

If deployment fails or issues are discovered:

1. **Revert Code**
   ```bash
   git revert [commit_hash]
   git push origin main
   ```

2. **Rollback Database** (if needed)
   ```bash
   # Option 1: Drop table (deletes all penimbangan data)
   DROP TABLE penimbangan_jam;
   
   # Option 2: Restore from backup
   mysql -u [user] -p [database] < backup_YYYYMMDD_HHMMSS.sql
   ```

3. **Clear Cache and Restart**
   ```bash
   php spark cache:clear
   ```

---

## Known Issues & Limitations

### Limits

- **Maximum hours:** 8 (fixed by design)
- **Maximum value:** 999.99 kg per hour
- **Decimal precision:** 2 decimal places (8,2 column type)

### Edge Cases Handled

- [x] Zero values allowed (0.00 kg per hour)
- [x] Format variations in SPK numbers
- [x] Missing penimbangan records on old mesin_becum rows
- [x] Concurrent edits (last write wins via UPDATE)
- [x] Soft delete cascade behavior

### Not Included (Future Enhancement)

- Real-time WebSocket updates to other users
- Bulk import of penimbangan data
- Historical data archiving
- Role-based edit restrictions

---

## Support & Troubleshooting

### Common Issues

#### Issue: "Durasi Penimbangan section not showing in Add modal"

**Solution:**
1. Verify `app/Views/gudang/mesinbecum.php` includes penimbangan_jam_section
2. Check browser console for JavaScript errors
3. Clear browser cache

#### Issue: "Edit form not prefilling penimbangan data"

**Solution:**
1. Verify AJAX endpoint: POST `/gudang/mesin_becum/getPenimbanganSection`
2. Check network tab in browser dev tools for request/response
3. Verify database contains penimbangan records for that mesin_becum id

#### Issue: "Form submits but penimbangan data not saved"

**Solution:**
1. Check server-side validation errors in application logs
2. Verify all 8 hours are present in form data
3. Check database foreign key constraints

#### Issue: "Delete doesn't remove penimbangan records"

**Solution:**
1. Verify migration ran: `php spark migrate:status`
2. Check database CASCADE DELETE constraint
3. Check MesinModuleTrait delete() method is called

### Support Contact

- **Developer:** [Your Name]
- **Documentation:** `PENIMBANGAN_JAM_INTEGRATION_CHECKLIST.md`
- **Test Suite:** `tests/Feature/PenimbanganJamE2ETest.php`

---

## Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Developer | | | |
| Code Reviewer | | | |
| QA Lead | | | |
| Product Manager | | | |
| Operations | | | |

---

**End of Integration Checklist**
