# Reject Types Management Feature

## Overview

Users can now add custom reject types (Jenis Reject) to the production report module. Instead of being limited to a hardcoded list, operators and managers can manage the available reject types dynamically through a dedicated management interface.

## Features

### 1. **Dynamic Reject Type Dropdown**
- The reject dropdown in production forms now loads from the database
- Defaults include 20 common reject types (Start Up, Karantina, Trial, Camera, etc.)
- New types can be added immediately and appear in all forms

### 2. **Management Page**
- **Location**: `/reject-type/manage`
- **Access**: Users with `admin`, `manager`, or `produksi` roles
- **Features**:
  - View all reject types
  - Search by name
  - Filter by status (active/inactive)
  - Add new reject types
  - Edit existing reject types
  - Delete reject types
  - Deactivate/activate reject types

### 3. **Inline Creation**
- When adding a reject row in the production form, users see a link to "Tambah jenis baru" (Add new type)
- This opens the management page in a new tab for quick access

### 4. **API Endpoints**
- `GET /reject-type/getAll` — Fetch all active reject types (JSON)
- `POST /reject-type/create` — Create new reject type
- `POST /reject-type/update/{id}` — Update reject type details
- `POST /reject-type/toggle/{id}` — Toggle active/inactive status
- `POST /reject-type/delete/{id}` — Delete (soft delete) reject type

## Database Changes

### New Table: `reject_types`
```sql
CREATE TABLE reject_types (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nama_reject VARCHAR(100) UNIQUE NOT NULL,
  deskripsi TEXT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  deleted_at DATETIME NULL,
  KEY (is_active)
);
```

**Initial Data** (20 default reject types):
- Start Up
- Karantina
- Trial
- Camera
- Bottom Putih
- Oval
- Flashing
- Short Shoot
- Kotor
- Beda Warna
- Sampling QC
- Kontaminasi
- Black Spot
- Gosong
- Struktur Tidak Standard
- Inject Poin Tidak Standard
- Bolong
- Bubble
- Berair
- Neck Panjang

## Files Changed/Created

### New Files
1. **app/Database/Migrations/2026-08-20-000001_CreateRejectTypesTable.php**
   - Creates `reject_types` table
   - Inserts 20 default reject types

2. **app/Models/MRejectType.php**
   - Model for reject types management
   - Methods: `getActive()`, `getById()`, `getByName()`, `exists()`, `createNew()`, `updateType()`, `toggleActive()`, `deleteType()`

3. **app/Controllers/RejectType.php**
   - Controller for reject type operations
   - Endpoints for CRUD operations and management page

4. **app/Views/reject_type/manage.php**
   - Management page view
   - Includes modals for adding/editing reject types
   - Search and filter functionality

### Modified Files
1. **app/Controllers/Produksi.php**
   - Added `MRejectType` model injection
   - Updated `get_edit_form()` to include `reject_types` in view data

2. **app/Config/Routes.php**
   - Added routes for reject-type endpoints

3. **app/Views/produksi/partials/form_fields.php**
   - Changed hardcoded reject options to dynamic list from view data

4. **app/Views/produksi/scripts.php**
   - Updated `addRejectInlineRow()` to fetch reject types from API
   - Added `getRejectTypes()` async function
   - Added "Add new type" link in reject rows

## How to Use

### For Operators
1. When filling out a production report, click "Tambah Reject" to add a reject row
2. Select a reject type from the dropdown
3. If the reject type you need isn't in the list, click "Tambah jenis baru" to add it

### For Managers/Admins
1. Navigate to `/reject-type/manage`
2. **To add a new type**:
   - Click "Tambah Jenis Reject" button
   - Enter the name (max 100 characters)
   - Optionally add a description
   - Click "Simpan"

3. **To edit a type**:
   - Click the edit icon on any row
   - Modify the name or description
   - Click "Simpan"

4. **To delete a type**:
   - Click the delete icon
   - Confirm deletion

5. **To search/filter**:
   - Use the search box to find types by name
   - Use the status filter to show active or inactive types

## Permissions

- **View manage page**: `admin`, `manager`, `produksi` roles
- **Create/Update**: `admin`, `manager`, `produksi` roles
- **Toggle/Delete**: `admin`, `manager` roles only

## Technical Details

### API Response Examples

**GET /reject-type/getAll**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama_reject": "Start Up",
      "deskripsi": null,
      "is_active": 1,
      "created_at": "2026-08-20 12:00:00",
      "updated_at": "2026-08-20 12:00:00",
      "deleted_at": null
    },
    ...
  ]
}
```

**POST /reject-type/create**
```json
{
  "success": true,
  "message": "Jenis reject berhasil ditambahkan",
  "data": {
    "id": 21,
    "nama_reject": "Goresan",
    "deskripsi": "Goresan pada permukaan galon",
    "is_active": 1
  }
}
```

### Frontend Caching
The JavaScript frontend caches reject types in `cachedRejectTypes` to minimize API calls. The cache is populated on first modal open or when adding a new reject row.

### Soft Deletes
Reject types use soft deletes (`deleted_at` field). Deleted types remain in the database but are excluded from the active list.

## Validation

- **Nama Reject**: Required, max 100 characters, must be unique
- **Deskripsi**: Optional, max 500 characters
- **Duplicate Prevention**: System prevents adding reject types with the same name

## Error Handling

The system includes comprehensive error handling:
- Validation errors (empty names, max length exceeded)
- Duplicate name detection
- Database errors logged but don't block operations
- User-friendly error messages in both Indonesian and English

## Future Enhancements

Potential improvements for future versions:
1. **Batch Operations**: Import/export reject types as CSV/Excel
2. **Categorization**: Group reject types by category (quality, operational, etc.)
3. **Audit Trail**: Log who created/modified each reject type
4. **Auto-suggestion**: AI-powered suggestions for new reject type names
5. **Usage Statistics**: Show which reject types are most commonly used

## Migration Instructions

To enable this feature on an existing installation:

```bash
php spark migrate --group produksi
```

This will:
1. Create the `reject_types` table
2. Insert 20 default reject types
3. Make the system ready for custom reject type management

## Troubleshooting

### Reject types not showing in dropdown
- Check that the migration ran successfully: `php spark migrate status --group produksi`
- Verify the `reject_types` table exists in the database
- Clear browser cache (the frontend caches the list)

### "Add new type" link not working
- Ensure you have permission to access `/reject-type/manage`
- Check browser console for JavaScript errors
- Verify the route is configured correctly in `Routes.php`

### Performance concerns with large number of types
- The system fetches reject types once per page load (cached in memory)
- For sites with >1000 reject types, consider adding pagination to the management page
- Consider archiving old unused types

## Support

For issues or questions about this feature, contact the development team or refer to the system documentation.
