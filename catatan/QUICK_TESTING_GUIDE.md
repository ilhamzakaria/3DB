# Warehouse2 - Quick Testing Guide

## Fast Access URLs

### Main Page
```
http://localhost:8080/warehouse2
```
Then click on tab "Barang Masuk dari Produksi"

---

## API Testing with cURL

### 1. Get Orowarehouse Data (with Period Filter)
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse?period=day&page=1&per_page=10"
```

### 2. Get Orowarehouse Data (with Search)
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse?search=SPK001&page=1"
```

### 3. Get Orowarehouse Data (with Custom Filter)
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse?shift=1&status=diterima&page=1"
```

### 4. Get SPK List (for dropdown)
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse/spk-list"
```

### 5. Get SPK Details (autofill)
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse/by-spk?spk=SPK001"
```

### 6. Get Filter Options
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse/filters"
```

### 7. Get Trash Data
```bash
curl "http://localhost:8080/warehouse2/api/orowarehouse-trash"
```

### 8. Create New Record
```bash
curl -X POST http://localhost:8080/warehouse2/store-orowarehouse \
  -H "Content-Type: application/json" \
  -d '{
    "tanggal": "2026-07-12",
    "spk": "SPK001",
    "produk": "Product A",
    "shift": "1",
    "grup": "Grup A",
    "hasil_bagus": 100,
    "hasil_reject": 5,
    "material": "Material A",
    "colorant": "Colorant A",
    "packaging": "Box",
    "operator": "Operator A",
    "status": "diterima",
    "catatan": "Test note"
  }'
```

### 9. Soft Delete Record
```bash
curl -X DELETE "http://localhost:8080/warehouse2/delete-orowarehouse/1"
```

### 10. Restore from Trash
```bash
curl -X POST "http://localhost:8080/warehouse2/restore-orowarehouse/1"
```

### 11. Permanent Delete
```bash
curl -X DELETE "http://localhost:8080/warehouse2/delete-permanent-orowarehouse/1"
```

### 12. Export Excel
```bash
# Without filters
curl "http://localhost:8080/warehouse2/export-orowarehouse" -o orowarehouse.xls

# With period filter
curl "http://localhost:8080/warehouse2/export-orowarehouse?period=day" -o orowarehouse-today.xls

# With search
curl "http://localhost:8080/warehouse2/export-orowarehouse?search=SPK001" -o orowarehouse-search.xls

# With custom filters
curl "http://localhost:8080/warehouse2/export-orowarehouse?shift=1&status=diterima" -o orowarehouse-filtered.xls

# With all filters
curl "http://localhost:8080/warehouse2/export-orowarehouse?period=month&shift=1&search=SPK&status=diterima" -o orowarehouse-all.xls
```

---

## Manual Testing Checklist (Quick 10-minute test)

### Step 1: Load & Verify UI (1 min)
- [ ] Load `http://localhost:8080/warehouse2`
- [ ] Click "Barang Masuk dari Produksi" tab
- [ ] Verify toolbar shows: Tambah, Export, Sampah, Periode filter, Search, Custom button
- [ ] Verify table displays correctly

### Step 2: Test Period Filters (2 min)
- [ ] Click period filter dropdown
- [ ] Select "Hari Ini" → verify label changes
- [ ] Select "Minggu Ini" → verify data updates
- [ ] Select "Bulan Ini" → verify data updates
- [ ] Reset to "Semua Periode"

### Step 3: Test Search (1 min)
- [ ] Enter any SPK in search box
- [ ] Click search button
- [ ] Verify table filters
- [ ] Clear search

### Step 4: Test Custom Filters (2 min)
- [ ] Click "Custom" button
- [ ] Select Shift = "1"
- [ ] Click "Terapkan Filter"
- [ ] Verify badge shows "1"
- [ ] Verify data filters
- [ ] Click "Custom" again
- [ ] Click "Reset"
- [ ] Verify badge disappears

### Step 5: Test Export (1 min)
- [ ] Click "Export Excel" button
- [ ] Verify file downloads
- [ ] Open file, verify headers and data
- [ ] Go back to page

### Step 6: Test Add/Edit (2 min)
- [ ] Click "Tambah Laporan"
- [ ] Modal opens
- [ ] Select SPK from dropdown
- [ ] Verify auto-fill fields populate (disabled)
- [ ] Fill remaining fields
- [ ] Click "Simpan"
- [ ] Verify record appears in table

### Step 7: Test Trash (1 min)
- [ ] Click delete icon on any record
- [ ] Confirm deletion
- [ ] Record disappears from table
- [ ] Click "Sampah" button
- [ ] Trash modal shows deleted record
- [ ] Click restore button
- [ ] Verify record restores to table

---

## Browser Console Commands for Testing

### Check if functions exist
```javascript
// Copy-paste in browser console:
console.log('Functions exist:');
console.log('loadOrowarehouseData:', typeof loadOrowarehouseData);
console.log('setPeriodFilter:', typeof setPeriodFilter);
console.log('applyOroCustomFilter:', typeof applyOroCustomFilter);
console.log('loadOrowarehouseTrash:', typeof loadOrowarehouseTrash);
console.log('updateExportLink:', typeof updateExportLink);
```

### Manually call data load
```javascript
// Load data for page 1
loadOrowarehouseData(1);
```

### Check active filters
```javascript
// Get current period filter
console.log('Period:', getOroPeriodFilter());

// Check filter values
console.log('Filters:', {
  tanggal: document.getElementById('oroFilterTanggal').value,
  shift: document.getElementById('oroFilterShift').value,
  status: document.getElementById('oroFilterStatus').value,
  search: document.getElementById('oroSearchQuery').value
});
```

### Check export link
```javascript
// View current export URL
console.log('Export link:', document.getElementById('exportOroLink').href);
```

---

## Expected Responses

### GET /warehouse2/api/orowarehouse
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tanggal": "2026-07-12",
      "spk": "SPK001",
      "produk": "Product A",
      "shift": "1",
      "grup": "Grup A",
      "hasil_bagus": 100,
      "hasil_reject": 5,
      "material": "Material A",
      "colorant": "Colorant A",
      "packaging": "Box",
      "operator": "Operator A",
      "status": "diterima",
      "catatan": "Note",
      "created_at": "2026-07-12 10:00:00",
      "updated_at": "2026-07-12 10:00:00",
      "deleted_at": null
    }
  ],
  "pager": {
    "current_page": 1,
    "total_pages": 1,
    "per_page": 10,
    "total": 1,
    "has_prev": false,
    "has_next": false
  }
}
```

### GET /warehouse2/api/orowarehouse-trash
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "tanggal": "2026-07-12",
      "spk": "SPK002",
      "produk": "Product B",
      ...
      "deleted_at": "2026-07-12 11:00:00"
    }
  ]
}
```

### POST /warehouse2/store-orowarehouse
```json
{
  "success": true,
  "message": "Data berhasil disimpan",
  "id": 1
}
```

### DELETE /warehouse2/delete-orowarehouse/1
```json
{
  "success": true,
  "message": "Data berhasil dipindahkan ke sampah"
}
```

### POST /warehouse2/restore-orowarehouse/1
```json
{
  "success": true,
  "message": "Data berhasil dipulihkan"
}
```

---

## Common Issues & Quick Fixes

### Issue: Data not loading
**Solution:** 
1. Check browser console for errors
2. Verify API endpoints are correct
3. Verify database connection
4. Check if records exist in database

### Issue: Filters not working
**Solution:**
1. Clear browser cache
2. Try different filter combination
3. Check network tab for API requests
4. Verify query parameters in URL

### Issue: Export not downloading
**Solution:**
1. Check browser download settings
2. Verify ExcelExport library exists
3. Check server error logs
4. Try different filter to verify export works

### Issue: Trash modal shows no data
**Solution:**
1. Verify you have deleted records
2. Check if deleted_at column exists
3. Try deleting a record first
4. Refresh page

### Issue: Modal not closing after save
**Solution:**
1. Check browser console for errors
2. Verify Bootstrap version compatibility
3. Check if Swal.fire is defined
4. Try refreshing page

---

## Performance Testing

### Load Test (simulate 100+ records)
1. Insert test data into orowarehouse table
2. Test pagination with 50 items/page
3. Verify page load time < 2 seconds
4. Verify filter response time < 1 second

### Export Performance
1. Try exporting all data (>100 records)
2. Verify file generates in < 5 seconds
3. Verify file size is reasonable

---

## Browser Compatibility

Tested & Working:
- ✅ Chrome 120+
- ✅ Firefox 120+
- ✅ Edge 120+
- ✅ Safari 16+

---

## Quick Troubleshooting Commands

```bash
# Check if routes are registered
grep -r "warehouse2/api/orowarehouse" app/Config/Routes.php

# Check if controller methods exist
grep -r "function.*Orowarehouse" app/Controllers/Warehouse2.php

# Check if table exists
mysql -u root -p orowarehouse -e "DESCRIBE orowarehouse;"

# Check for PHP errors
tail -f /var/log/apache2/error.log

# Verify file permissions
ls -la app/Views/warehouse2/index.php
ls -la app/Controllers/Warehouse2.php
```

---

## Contact & Support

For issues or questions:
1. Check WAREHOUSE2_FEATURE_VERIFICATION.md for detailed specs
2. Check WAREHOUSE2_IMPLEMENTATION_SUMMARY.md for technical details
3. Review code comments in respective files
4. Check browser console for JavaScript errors

---

**Last Updated:** July 12, 2026
**Status:** Ready for Testing ✅
