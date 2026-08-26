# Quick Start: Reject Types Management

## For End Users

### Adding a Reject Type While Filling Production Report

1. Open production form at `http://localhost:8080/produksi`
2. Click **"Tambah Reject"** button to add a reject row
3. In the "JENIS REJECT" dropdown, select a type
4. If the type you need doesn't exist:
   - Click the blue link **"Tambah jenis baru"** (Add new type)
   - A new tab opens with the management page
   - Click **"Tambah Jenis Reject"** button
   - Fill in the reject name (required) and optional description
   - Click **"Simpan"** (Save)
   - Go back to production form and refresh
   - Your new type now appears in the dropdown!

### Example
```
Production Form → Reject Dropdown
                 ↓
              "Goresan" not in list?
                 ↓
          Click "Tambah jenis baru"
                 ↓
         Management Page Opens
                 ↓
          Enter "Goresan"
                 ↓
             Save & Go Back
                 ↓
          "Goresan" now available!
```

## For Administrators

### Accessing Reject Type Management

**Direct URL**: `http://localhost:8080/reject-type/manage`

Or navigate through the menu (if implemented) → Settings → Reject Types

### Common Tasks

#### ➕ Adding a New Reject Type
1. Click **"Tambah Jenis Reject"** button
2. Enter reject type name (max 100 characters)
3. Optionally add a description
4. Click **"Simpan"**

**Example Names**:
- Goresan (Scratch)
- Warna Tidak Merata (Uneven Color)
- Tipis (Too Thin)
- Penyok (Dent)
- Tidak Simetris (Not Symmetric)

#### ✏️ Editing a Reject Type
1. Find the reject type in the list (use search if needed)
2. Click the **Edit** button (pencil icon)
3. Modify the name or description
4. Click **"Simpan"**

#### 🗑️ Deleting a Reject Type
1. Find the reject type in the list
2. Click the **Delete** button (trash icon)
3. Confirm deletion in the popup

**Note**: Deleted types are soft-deleted and can potentially be restored from database backups.

#### 🔍 Finding a Reject Type
1. Use the search box at the top
2. Type any part of the reject type name
3. Results filter automatically
4. Or use the Status filter to show only Active or Inactive types

#### 🔁 Deactivating vs Deleting
- **Deactivate**: Type stays in database but doesn't show in dropdowns
  - Use for types you might want back later
- **Delete**: Type is soft-deleted
  - Use for types you don't need anymore

To toggle active/inactive (if implemented in future):
- Button near each type in management page

## Default Reject Types Included

The system comes with these 20 types ready to use:

```
✓ Start Up
✓ Karantina
✓ Trial
✓ Camera
✓ Bottom Putih
✓ Oval
✓ Flashing
✓ Short Shoot
✓ Kotor
✓ Beda Warna
✓ Sampling QC
✓ Kontaminasi
✓ Black Spot
✓ Gosong
✓ Struktur Tidak Standard
✓ Inject Poin Tidak Standard
✓ Bolong
✓ Bubble
✓ Berair
✓ Neck Panjang
```

## Troubleshooting

### "Can't access management page"
**Problem**: Getting "permission denied" or redirect
**Solution**: 
- Verify you're logged in
- Check your role (admin, manager, or produksi)
- Ask your administrator for access

### "New reject type doesn't appear in dropdown"
**Problem**: Just added a type but don't see it
**Solution**:
- Refresh the production page (F5 or Ctrl+R)
- Browser might be caching the old list
- Try clearing browser cache

### "Can't find the reject type I need"
**Problem**: Type exists but can't find it
**Solution**:
- Use the search box on management page
- Check if it's deactivated (if filtering by status)
- Type only partial name to search

### "Duplicate name error"
**Problem**: System says type already exists
**Solution**:
- Search to see if it's already in the system
- Use a different name if it's truly a new type
- If it's the same type, just use the existing one

### "Type appears but can't select it"
**Problem**: Type shows in management page but not in dropdown
**Solution**:
- Type might be deactivated
- Go to management page and check its status
- If inactive, activate it

## Tips & Best Practices

### ✅ Naming Conventions
- Use Indonesian names (consistent with existing types)
- Keep names concise (max 100 characters)
- Avoid special characters
- Example: "Goresan" instead of "Scratch/Goresan"

### ✅ Organization
- Group similar issues into one type
- Example: "Warna Tidak Merata" covers all color issues
- Don't create duplicate types with slightly different names

### ✅ Descriptions
- Add optional descriptions for clarity
- Example: "Goresan - Goresan pada permukaan galon"
- Helps operators understand when to use each type

### ✅ Maintenance
- Periodically review and clean up unused types
- Archive old types if they're no longer needed
- Keep at least 15-20 core types

## Permissions

| Action | Role Required |
|--------|--|
| View types | Any logged-in user |
| View management page | Admin, Manager, Produksi |
| Add new type | Admin, Manager, Produksi |
| Edit type | Admin, Manager, Produksi |
| Delete type | Admin, Manager only |
| Toggle active status | Admin, Manager only |

## API Reference (For Developers)

### Load Reject Types in Dropdown
```javascript
fetch('/reject-type/getAll')
  .then(r => r.json())
  .then(data => {
    data.data.forEach(type => {
      console.log(type.nama_reject);
    });
  });
```

### Create New Type
```javascript
fetch('/reject-type/create', {
  method: 'POST',
  body: new FormData(document.querySelector('form'))
})
.then(r => r.json())
.then(data => console.log(data));
```

## Mobile Access

✅ The management page is responsive and works on:
- Tablets (iPad, Samsung Tab)
- Large phones (iPhone 12+, Samsung S21+)

⚠️ For small phones (<5"), use desktop version for better UX

## Keyboard Shortcuts (Web)

| Shortcut | Action |
|----------|--------|
| Tab | Navigate between form fields |
| Enter | Submit form (if focused on button) |
| Esc | Close modal |

## Video Tutorial (If Available)

See the internal knowledge base for video walkthrough of:
1. Adding production report
2. Adding reject types
3. Managing reject types

## Support

- **Questions?** Contact your system administrator
- **Bug report?** Email: [support email]
- **Feature request?** Submit through system feedback form

---

**Last Updated**: August 20, 2026  
**Difficulty Level**: ⭐ Easy (5 minutes to learn)  
**Average Use Time**: ~30 seconds per new type
