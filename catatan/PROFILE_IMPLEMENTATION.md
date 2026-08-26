# Profile Feature - Dokumentasi Implementasi

## Overview
Fitur Profile memungkinkan setiap user untuk upload dan manage:
- **Foto Profile** - Foto profil pengguna
- **Tanda Tangan (TTD)** - Tanda tangan digital untuk laporan

## Files Created/Modified

### 1. Migration
**File:** `app/Database/Migrations/2026-07-11-000001_AddPhotoAndSignatureToUsers.php`
- Tambahkan kolom `foto_profile` ke tabel `users`
- Tambahkan kolom `tanda_tangan` ke tabel `users`
- Kedua kolom bertipe VARCHAR(255) untuk menyimpan path file

### 2. Model
**File:** `app/Models/MUser.php`
- Update `$allowedFields` untuk include `foto_profile` dan `tanda_tangan`

### 3. Controller
**File:** `app/Controllers/Profile.php`
- **Methods:**
  - `index()` - Tampilkan halaman profile
  - `uploadFoto()` - Handle upload foto profile (POST)
  - `uploadTtd()` - Handle upload tanda tangan (POST)
  - `autoUploadTtd()` - Auto-load TTD dari `public/uploads/ttd1.png` (POST)
  - `previewFoto($userId)` - Preview foto profile (GET)
  - `previewTtd($userId)` - Preview tanda tangan (GET)

### 4. View
**File:** `app/Views/profile/index.php`
- Form upload foto dan tanda tangan
- Preview images
- Drag-and-drop upload interface
- AJAX-based upload dengan status messages
- Auto-upload button untuk TTD default
- Responsive design

### 5. Routes
**File:** `app/Config/Routes.php`
- `GET /profile` → Profile::index
- `POST /profile/upload-foto` → Profile::uploadFoto
- `POST /profile/upload-ttd` → Profile::uploadTtd
- `POST /profile/auto-upload-ttd` → Profile::autoUploadTtd
- `GET /profile/preview-foto/:id` → Profile::previewFoto
- `GET /profile/preview-ttd/:id` → Profile::previewTtd

### 6. Navbar
**File:** `app/Views/partial/topbar.php`
- Update Profile link di dropdown menu user
- Link ke `/profile` route

## Directory Structure

```
public/uploads/
├── profile/          # Folder untuk foto profile users
│   └── profile_1_*.jpg, profile_2_*.png, dll
├── ttd/              # Folder untuk tanda tangan users
│   └── ttd_1_*.png, ttd_2_*.png, dll
└── ttd1.png          # Default TTD file (harus diletakkan manual)
```

## Database Schema

```sql
ALTER TABLE users ADD COLUMN (
  foto_profile VARCHAR(255) NULL COMMENT 'Path to profile photo',
  tanda_tangan VARCHAR(255) NULL COMMENT 'Path to signature image'
);
```

## Installation Steps

### 1. Run Migration
```bash
php spark migrate
```

Atau via browser:
```
http://localhost:8080/?tool=migrate
```

### 2. Create Upload Directories
Controller akan otomatis membuat folder jika belum ada:
- `public/uploads/profile/`
- `public/uploads/ttd/`

### 3. Place Default TTD (Optional)
Jika ingin fitur auto-upload TTD berfungsi, letakkan file tanda tangan default:
```
public/uploads/ttd1.png
```

## Usage

### Untuk User
1. Akses menu Profile di dropdown user (top-right)
2. Pilih foto dari komputer atau gunakan drag-and-drop
3. Klik tombol "Upload Foto"
4. Tunggu sampai upload selesai
5. Lakukan hal yang sama untuk TTD
6. Atau klik "Gunakan Default" untuk auto-load TTD dari `ttd1.png`

### Untuk Developer
```php
// Get user dengan foto dan TTD
$user = $userModel->find($userId);
echo $user['foto_profile']; // Path: uploads/profile/profile_1_123456.jpg
echo $user['tanda_tangan']; // Path: uploads/ttd/ttd_1_123456.png

// Display foto
echo '<img src="' . base_url($user['foto_profile']) . '" alt="Foto">';

// Display TTD
echo '<img src="' . base_url($user['tanda_tangan']) . '" alt="TTD">';
```

## Validation Rules

### Foto Profile
- Format: JPG, PNG, GIF, WebP
- Maksimal: 5MB
- Auto-resize/optimize dalam controller

### Tanda Tangan
- Format: JPG, PNG, GIF, WebP
- Maksimal: 5MB
- Recommended: PNG dengan background transparan

## File Naming Convention

```
Profile Photo: profile_{user_id}_{timestamp}.{ext}
Example: profile_1_1689072342.jpg

Signature: ttd_{user_id}_{timestamp}.{ext}
Example: ttd_1_1689072342.png
```

## Auto-Upload Default TTD

Fitur ini memungkinkan user untuk secara otomatis menggunakan tanda tangan default tanpa perlu upload manual.

**Flow:**
1. Admin/Manager menempatkan file `ttd1.png` di `public/uploads/`
2. User membuka halaman profile
3. User klik tombol "Gunakan Default" di section Tanda Tangan
4. Sistem copy file dari `ttd1.png` ke folder user dengan nama unique
5. Database diupdate dengan path file baru

**Keuntungan:**
- User tidak perlu upload file manual
- Konsistensi format tanda tangan
- Otomatis integrated ke laporan produksi

## Integration dengan Produksi

Untuk menampilkan TTD di laporan produksi:

```php
// Di view produksi
$user = $userModel->find(session('id_user'));

// Display TTD di laporan
if ($user['tanda_tangan']) {
    echo '<img src="' . base_url($user['tanda_tangan']) . '" 
          alt="Tanda Tangan" 
          style="width: 150px; height: 80px; object-fit: contain;">';
}
```

## Troubleshooting

### Upload Gagal
- Pastikan folder `public/uploads/profile/` dan `public/uploads/ttd/` writable
- Check browser console untuk error message
- Verify file size < 5MB

### Default TTD Tidak Ditemukan
- Pastikan file `public/uploads/ttd1.png` ada
- Check path dan permissions
- Coba upload TTD manual terlebih dahulu

### Preview Tidak Muncul
- Clear browser cache (Ctrl+Shift+Delete)
- Check developer tools Network tab
- Verify file ada di `public/uploads/`

## Security Considerations

1. **File Upload Validation**
   - Validasi MIME type (bukan hanya extension)
   - Validasi ukuran file (max 5MB)
   - Sanitasi filename

2. **Directory Permissions**
   - Folder upload harus writable (755)
   - Disarankan outside webroot untuk production

3. **Path Traversal Prevention**
   - Filenames di-generate system (random hash + timestamp)
   - User tidak bisa specify path sendiri

4. **CSRF Protection**
   - Routes dilindungi dengan session auth
   - AJAX upload include CSRF token implicitly

## Performance Tips

1. **Image Optimization**
   - Compress image sebelum upload
   - Recommend format: WebP untuk profile, PNG untuk TTD

2. **Caching**
   - Browser cache untuk user profile
   - Server-side cache untuk frequently accessed images

3. **CDN Integration** (Optional)
   - Upload ke CDN untuk faster delivery
   - Update database dengan CDN URL

## Future Enhancements

- [ ] Image cropping tool
- [ ] Signature canvas drawing tool
- [ ] Batch upload
- [ ] Image optimization/compression
- [ ] CDN integration
- [ ] Watermark untuk profile photo
- [ ] Multi-signature support
- [ ] Signature verification API

## Testing Checklist

- [ ] Migration runs successfully
- [ ] Profile page loads without error
- [ ] Can upload foto profile
- [ ] Can upload tanda tangan
- [ ] Can auto-upload default TTD
- [ ] Files stored correctly in public/uploads/
- [ ] Database updated with correct paths
- [ ] Preview images display correctly
- [ ] Old files deleted when new uploaded
- [ ] Works on mobile devices
- [ ] File validation works (reject invalid files)
- [ ] Error handling works properly

## Support

Untuk pertanyaan atau issue, silakan hubungi tim development.
