# Finish Goods Import Script

## Overview

The `import_finish_goods.php` script imports finish goods data from the `catatan.md` markdown file into the 3DB database. It parses 4 product category tables and inserts data into the corresponding database tables.

## What Gets Imported

The script imports data from 4 tables in the `catatan.md` file:

1. **Gallon** → `finish_good_galon` table
   - Fields: bagian, nomor, nama_produk, grade, berat, wh_oro, wh_bam, tanggal
   - Total records: 10

2. **Cap Gallon** → `finish_good_cap_galon` table
   - Fields: bagian, nomor, nama_produk, warna, berat, jumlah, packing, tanggal
   - Total records: 35

3. **Preform (Freeform)** → `finish_good_freeform` table
   - Fields: bagian, nomor, nama_produk, warna, berat, jumlah, packing, tanggal
   - Total records: 38

4. **Screw Cap** → `finish_good_screw_cap` table
   - Fields: bagian, nomor, nama_produk, warna, berat, jumlah, keterangan, tanggal
   - Total records: 71

**Total Records Imported: 154**

## Data Processing

### Field Mapping

- **Nomor (Number)**: Extracted from the first column of each table row
- **Nama Produk (Product Name)**: Extracted from the second column
- **Grade/Warna (Grade/Color)**: Extracted from the appropriate column based on product type
- **Berat (Weight)**: Numeric values are extracted, with support for decimal numbers (commas converted to periods)
- **Jumlah (Quantity)**: Numeric values with thousands separators properly handled
- **Packing**: Numeric or text values preserved as-is
- **WH ORO/WH BAM**: Warehouse quantity values for Gallon products
- **Keterangan (Notes)**: Text values from the Screw Cap table
- **Tanggal (Date)**: Set to current date/time when records are imported

### Data Sanitization

The script handles the following during data extraction:

1. **HTML Tag Removal**: Strips all HTML tags from extracted cell values
2. **Entity Decoding**: Decodes HTML entities (e.g., `&nbsp;`, `&amp;`)
3. **Whitespace Trimming**: Removes leading and trailing spaces
4. **Empty Value Handling**: Treats empty values as NULL in the database
5. **Number Formatting**: 
   - Converts thousands separators (dots and commas) appropriately
   - Converts decimal numbers (with commas) to proper float format
   - Handles non-numeric values gracefully

## Usage

### Option 1: Command Line (Recommended)

Run the script from the 3DB root directory:

```bash
php import_finish_goods.php
```

### Option 2: Web Browser

Access the script through your web server:

```
http://localhost/3DB/import_finish_goods.php?run_import=1
```

Without the `?run_import=1` parameter, the script displays usage instructions for security.

### Output

The script provides real-time import progress:

```
=== Finish Goods Import Started ===
--- Importing Gallon Data ---
Imported 10 Gallon records
--- Importing Cap Gallon Data ---
Imported 35 Cap Gallon records
--- Importing Freeform Data ---
Imported 38 Freeform records
--- Importing Screw Cap Data ---
Imported 71 Screw Cap records
=== Import Summary ===
Gallon: 10 records imported
Cap Gallon: 35 records imported
Freeform: 38 records imported
Screw Cap: 71 records imported
TOTAL: 154 records imported
=== Import Completed ===
```

## Verification

After running the import, verify the data was successfully inserted by running:

```bash
php verify_import.php
```

This script will:
- Count records in each table
- Display sample records from each category
- Show field values for verification

Sample output:
```
=== Import Verification ===
Gallon: 10 records
Cap Gallon: 35 records
Freeform: 38 records
Screw Cap: 71 records
Total: 154 records

=== Sample Records ===
Gallon Sample:
  - Nomor: 1
  - Nama Produk: 168
  - Grade: D.0.V-2
  - Berat: 750
  - WH ORO: 0
  - WH BAM: 1700
...
```

## Models Used

The script utilizes CodeIgniter 4 models with soft delete support:

- `App\Models\MFinishGoodGalon`
- `App\Models\MFinishGoodCapGalon`
- `App\Models\MFinishGoodFreeform`
- `App\Models\MFinishGoodScrewCap`

All models:
- Use CodeIgniter's timestamp support (`created_at`, `updated_at`)
- Support soft deletes (`deleted_at`)
- Automatically manage timestamps on insert

## Error Handling

The script includes comprehensive error handling:

- Validates that the `catatan.md` file exists before processing
- Catches exceptions during individual row insertions
- Continues processing even if a single row fails
- Logs errors without stopping the entire import process
- Displays a summary of any errors encountered

If any errors occur during import, they are logged and reported in the summary at the end.

## Source Data Format

The data is extracted from an HTML table structure within the `catatan.md` markdown file. The parser:

1. Locates table sections by header title
2. Extracts all `<tr>` rows from the table body
3. Extracts individual `<td>` cells from each row
4. Processes and sanitizes the cell content
5. Inserts valid data into the appropriate database table

## Database Connection

The script uses CodeIgniter 4's built-in database connection through the PPIC database connection (as defined in the models' parent class).

All write operations are handled by the respective model's `insert()` method, which respects:
- Table definitions
- Allowed field configurations
- Timestamp management
- Soft delete settings

## Notes

- **Timestamp**: All imported records receive the current date/time in the `tanggal` field
- **Bagian (Section)**: Automatically set based on the product type being imported (Gallon, Cap Gallon, Freeform, Screw Cap)
- **Idempotency**: The script can be run multiple times; it will add duplicate records if run again
- **Data Backup**: Consider backing up your database before running the import for the first time
- **Updates vs Inserts**: This script inserts new records; it does not update existing data

## Troubleshooting

### Issue: "catatan.md file not found"
- Ensure the file exists at `c:\xampp\htdocs\3DB\catatan\catatan.md`
- Verify the path is correct and file permissions allow reading

### Issue: Records not appearing in database
- Run `verify_import.php` to check if records were inserted
- Check database connection settings in `app/Config/Database.php`
- Ensure the PPIC database connection is properly configured

### Issue: "Class not found" errors
- Verify all model files exist in `app/Models/`
- Ensure CodeIgniter autoloader is properly configured
- Check that vendor/autoload.php is present and valid

### Issue: Numbers not imported correctly
- Verify the source markdown file has consistent number formatting
- Check that HTML entities are properly included in the source file
- Refer to the "Number Formatting" section in Data Sanitization above

## Files

- `import_finish_goods.php` - Main import script
- `verify_import.php` - Verification script to check import results
- `catatan/catatan.md` - Source data file
- `IMPORT_FINISH_GOODS_README.md` - This documentation file

## Future Enhancements

Potential improvements for future versions:

1. Add support for selective imports (by category)
2. Add duplicate detection/prevention
3. Add batch processing with progress indicators
4. Add Excel export of import results
5. Add import history logging
6. Add data validation rules before insertion
7. Add support for updating existing records
