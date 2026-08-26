# SPK Status Synchronization: PPIC ↔ Produksi

## Overview

Your system already has a **fully implemented automatic SPK status synchronization** feature. When a [Nomor SPK] is marked as **"selesai" (complete)** in the PPIC production log modules, the corresponding status in the Produksi (production reporting) module **automatically updates** to reflect the completion.

---

## How It Works

### 1. **PPIC Module → Mark SPK as Complete**
   - User navigates to any PPIC mesin module (e.g., `/ppic/mesin_becum`)
   - User clicks the "Selesai" button/link next to the SPK they want to complete
   - This triggers the `completeSpk($id)` method in the mesin controller (via POST to `/ppic/mesin_*/completeSpk/<id>`)

### 2. **Trait Method Handles the Toggle**
   - Location: `app/Controllers/Traits/MesinModuleTrait.php`
   - Method: `completeSpk($id)` (lines 172-201)
   
   ```php
   public function completeSpk($id = null)
   {
       // Toggle selesai_spk: 1 ↔ 0
       $newStatus = $currentData['selesai_spk'] == 1 ? 0 : 1;
       
       // Update PPIC mesin table
       $this->activeMesin()->update($id, ['selesai_spk' => $newStatus]);
       
       // If in PPIC context, sync to Produksi
       if ($this->isPpicContext() && $noSpk !== '') {
           $this->syncSpkStatusToProduksi($noSpk, $newStatus);
       }
   }
   ```

### 3. **Automatic Sync to Produksi**
   - Location: `app/Controllers/Traits/MesinModuleTrait.php`
   - Method: `syncSpkStatusToProduksi(string $noSpk, int $selesai)` (lines 203-245)
   
   This method:
   - Connects to the Produksi database (`oroproduksi`)
   - Finds ALL rows in `produksi_headers` table where `nomor_spk` matches the completed SPK
   - Updates the `selesai_spk` column on those rows to match the new status
   - Handles both string and integer SPK formats via candidate list matching

### 4. **Produksi Module Displays the Status**
   - Location: `app/Views/produksi/partials/table.php` (lines 21-31)
   - The Produksi table shows a badge next to each SPK:
   
   ```php
   <?php if (!empty($p['selesai_spk'])): ?>
       <span class="badge badge-spk-selesai">
           <i class="fas fa-check-circle me-1"></i>Selesai
       </span>
   <?php else: ?>
       <span class="badge badge-spk-ongoing">
           <i class="fas fa-spinner me-1"></i>Berjalan
       </span>
   <?php endif; ?>
   ```

   - When `selesai_spk = 1`, row is styled differently (class: `spk-selesai-row`)
   - Visual indicator shows **"Selesai"** instead of **"Berjalan"**

---

## Database Tables Involved

### PPIC Mesin Tables (Database: `oroppic`)
- Tables: `mesin_becum`, `mesin_powerjet1`, `mesin_powerjet2`, `mesin_ccm1`, `mesin_ccm2`, `mesin_ips1`, `mesin_ips2`, `mesin_ips3`, `mesin_ips4`
- Column: `selesai_spk` (TINYINT, 0 = ongoing, 1 = complete)
- Primary key: `id`

### Produksi Headers Table (Database: `oroproduksi`)
- Table: `produksi_headers`
- Column: `selesai_spk` (TINYINT, 0 = ongoing, 1 = complete)
- Column: `nomor_spk` (VARCHAR, the SPK reference)
- Primary key: `id`

### Migration
- File: `app/Database/Migrations/2026-07-23-000001_AddSelesaiSpkToProduksiHeaders.php`
- Adds `selesai_spk` column to `produksi_headers` table
- Already applied to your database

---

## API Routes / Endpoints

### Toggle SPK Completion
```
POST /ppic/mesin_<module>/completeSpk/<id>
POST /gudang/mesin_<module>/completeSpk/<id>
```

**Parameters:**
- `<module>`: One of `becum`, `powerjet1`, `powerjet2`, `ccm1`, `ccm2`, `ips1`, `ips2`, `ips3`, `ips4`
- `<id>`: Database ID of the mesin record (numeric)

**Behavior:**
- **PPIC context** (`/ppic/...`): Toggles `selesai_spk` on PPIC mesin table AND syncs to `produksi_headers`
- **Gudang context** (`/gudang/...`): Toggles `selesai_spk` on Gudang mesin table only (no sync to Produksi)

**Response:**
- Flash message: Success or error notification
- Redirect: Back to the mesin index page

---

## Data Flow Diagram

```
PPIC Module (oroppic)
    ↓
User clicks "Selesai" button on SPK
    ↓
POST /ppic/mesin_becum/completeSpk/<id>
    ↓
MesinBecum::completeSpk() [via trait]
    ↓
completeSpk() updates PPIC mesin row (selesai_spk = 1)
    ↓
isPpicContext() check: YES
    ↓
syncSpkStatusToProduksi(nomor_spk, 1)
    ↓
Query: UPDATE produksi_headers SET selesai_spk = 1 
       WHERE nomor_spk = '<nomor_spk>'
    ↓
Produksi Module (oroproduksi)
    ↓
Produksi page displays "Selesai" badge on that SPK's row
```

---

## Testing the Feature

### Step 1: Navigate to PPIC
1. Go to `/ppic/mesin_becum` (or any mesin module)
2. Find a production record with a Nomor SPK

### Step 2: Mark as Complete in PPIC
1. Click the "Selesai" button/link next to the SPK
2. Verify the action succeeded (flash message appears)
3. Confirm the badge changes from "Berjalan" to "Selesai"

### Step 3: Check Produksi Module
1. Go to `/produksi`
2. Find the same Nomor SPK in the production table
3. Verify the badge now shows **"Selesai"** (was "Berjalan" before)

### Step 4: Check the Database
```sql
-- PPIC database (oroppic)
SELECT id, no_spk, selesai_spk FROM mesin_becum WHERE no_spk = '2926';

-- Produksi database (oroproduksi)
SELECT id, nomor_spk, selesai_spk FROM produksi_headers WHERE nomor_spk = '2926';
```

Both should now have `selesai_spk = 1`.

---

## How It Handles Different SPK Formats

The `syncSpkStatusToProduksi()` method builds a candidate list to handle variations in how SPK is stored:

```php
$candidates = [
    $noSpk,           // e.g., "2926"
    (string)(int)$noSpk  // e.g., "2926" (integer conversion)
];

// Then uses: WHERE TRIM(nomor_spk) IN (...)
```

This ensures the sync works even if:
- PPIC stores SPK as integer: `2926`
- Produksi stores SPK as string: `"2926"`
- SPK has leading/trailing spaces: `" 2926 "`

---

## Error Handling

The sync is **best-effort**:

- If the sync fails (DB error, SPK not found in Produksi), it's logged but **does NOT roll back** the PPIC mesin row update
- The PPIC row has already been successfully updated; only the Produksi sync failed
- Error logs go to `spark logs` (CodeIgniter logs directory)

**Example log entry:**
```
[ERROR] syncSpkStatusToProduksi: failed for SPK=2926: [Database error details]
```

---

## Configuration Files

**No special configuration needed.** The feature uses:
- Database connections defined in `app/Config/Database.php` (ppic, gudang, produksi)
- Routes defined in `app/Config/Routes.php` (lines 273-275 and 325-328)
- Models defined in `app/Models/MPpicMesin*.php` and `app/Models/MProduksiHeader.php`

---

## Summary

✅ **Feature is FULLY IMPLEMENTED and WORKING:**
1. PPIC mesin tables have `selesai_spk` column
2. Produksi headers table has `selesai_spk` column
3. Sync trait method automatically propagates status changes
4. Routes are configured for all 9 mesin modules
5. UI displays status badges in both modules
6. Database migration already applied

**Your system ensures that whenever a user marks an SPK as complete in any PPIC mesin module, all corresponding production records in the Produksi module are instantly updated.**
