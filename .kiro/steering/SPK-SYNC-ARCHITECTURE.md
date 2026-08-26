# SPK Synchronization Architecture
## Automatic Status Sync: PPIC → Produksi

**Last Updated:** July 23, 2026  
**Status:** ✅ Fully Implemented & Operational

---

## Overview

When an operator marks a **Nomor SPK** (production order) as complete in the PPIC module, the production reporting module (**Produksi**) automatically updates to show that the SPK is complete. This keeps the two modules synchronized without requiring manual status updates.

**Key Principle:** Single source of truth (PPIC) → Automatic propagation to dependent module (Produksi).

---

## Architecture

### Three-Database System

```
┌─────────────────────────────────────────────────────────────────┐
│                    PPIC Database (oroppic)                      │
│  • ppic_mesin_becum     (SPK planning for Becum machine)        │
│  • ppic_mesin_powerjet1 (SPK planning for Powerjet 1)           │
│  • ppic_mesin_powerjet2 (SPK planning for Powerjet 2)           │
│  • ppic_mesin_ccm1      (SPK planning for CCM 1)                │
│  • ppic_mesin_ccm2      (SPK planning for CCM 2)                │
│  • ppic_mesin_ips1      (SPK planning for IPS 1)                │
│  • ppic_mesin_ips2      (SPK planning for IPS 2)                │
│  • ppic_mesin_ips3      (SPK planning for IPS 3)                │
│  • ppic_mesin_ips4      (SPK planning for IPS 4)                │
│                                                                  │
│  Fields: no_spk, nama_mesin, operator, target, selesai_spk (0|1)│
└─────────────┬──────────────────────────────────────────────────┘
              │ syncSpkStatusToProduksi()
              │ when selesai_spk toggle = 1 or 0
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                 Produksi Database (oroproduksi)                 │
│  • produksi_headers                                              │
│  • produksi (legacy)                                             │
│                                                                  │
│  Fields: nomor_spk (matches PPIC.no_spk)                        │
│          selesai_spk (mirrored from PPIC)                       │
│          tercapai (target achievement - separate)               │
└─────────────────────────────────────────────────────────────────┘
              ▲
              │ reads PPIC status via getPpicSpkDetail()
              │ displays selesai_spk badge
              │ (green "Selesai" or orange "Berjalan")
              │
┌─────────────┴──────────────────────────────────────────────────┐
│                   Produksi View (Frontend)                      │
│  Table rows with selesai_spk status display                     │
│  Clicked SPK → auto-populates target from PPIC                  │
└─────────────────────────────────────────────────────────────────┘
```

### Database Columns

| Database | Table | Column | Type | Purpose |
|----------|-------|--------|------|---------|
| **PPIC** | `ppic_mesin_*` | `no_spk` | VARCHAR/INT | Production order identifier |
| **PPIC** | `ppic_mesin_*` | `selesai_spk` | TINYINT(1) | Completion flag: 0=ongoing, 1=complete |
| **Produksi** | `produksi_headers` | `nomor_spk` | VARCHAR | Stores PPIC `no_spk` for matching |
| **Produksi** | `produksi_headers` | `selesai_spk` | TINYINT(1) | Synced from PPIC `selesai_spk` |
| **Produksi** | `produksi_headers` | `tercapai` | TINYINT(1) | Target achievement (independent flag) |

---

## Sync Flow

### 1. **PPIC Operator Marks SPK Complete**

**Location:** PPIC Module → Mesin Page (e.g., `/ppic/mesin_becum`)

**Action:** Operator clicks the "Selesai" toggle button on a production order (SPK row)

**UI Element:** Button in the PPIC mesin table showing current status (checkmark or clock icon)

### 2. **completeSpk() Handler Triggers**

**Route:** `POST /ppic/mesin_{slug}/completeSpk/{id}`  
**Handler:** `app/Controllers/Traits/MesinModuleTrait::completeSpk()`

**Logic:**
```php
// Step 1: Get current SPK record from PPIC mesin table
$currentData = $this->activeMesin()->find($id);
$noSpk = trim((string) ($currentData['no_spk'] ?? ''));

// Step 2: Toggle selesai_spk (0→1 or 1→0) in PPIC
$newStatus = $currentData['selesai_spk'] == 1 ? 0 : 1;
$this->activeMesin()->update($id, ['selesai_spk' => $newStatus]);

// Step 3: If PPIC context, sync to Produksi
if ($this->isPpicContext() && $noSpk !== '') {
    $this->syncSpkStatusToProduksi($noSpk, $newStatus);
}
```

**Key Points:**
- ✅ Only syncs when in **PPIC context** (not from Gudang)
- ✅ Toggles between 0 (ongoing) and 1 (complete)
- ✅ Handles both marking as complete AND unmarking

### 3. **syncSpkStatusToProduksi() Updates Produksi Database**

**Handler:** `app/Controllers/Traits/MesinModuleTrait::syncSpkStatusToProduksi()`

**Logic:**
```php
// Build candidates to handle format variations
// Example: "2926" from PPIC might be stored as " 2926 " in Produksi
$candidates = [$noSpk, (string) (int) $noSpk]; // ["2926", "2926"]

// Update ALL produksi_headers rows matching this SPK
$db->query(
    "UPDATE produksi_headers
        SET selesai_spk = ?
      WHERE deleted_at IS NULL
        AND TRIM(nomor_spk) IN (?, ?)",
    [$newStatus, ...$candidates]
);
```

**Coverage:**
- 🎯 Updates **ALL** rows in `produksi_headers` that share the same `nomor_spk`
- 🎯 Handles format variations (spaces, integer casting)
- 🎯 Skips soft-deleted rows (`WHERE deleted_at IS NULL`)
- 🎯 Best-effort: any DB errors logged but don't block PPIC update

### 4. **Produksi Page Displays Updated Status**

**Route:** `GET /produksi`  
**View:** `app/Views/produksi/partials/table.php`

**Display Logic:**
```php
<?php if (!empty($p['selesai_spk'])): ?>
    <!-- Green badge: SPK marked complete -->
    <span class="badge badge-spk-selesai" title="SPK ini telah ditandai selesai oleh PPIC">
        <i class="fas fa-check-circle me-1"></i>Selesai
    </span>
<?php else: ?>
    <!-- Orange badge: SPK still ongoing -->
    <span class="badge badge-spk-ongoing">
        <i class="fas fa-spinner me-1"></i>Berjalan
    </span>
<?php endif; ?>

<!-- Also tints row background green if selesai_spk = 1 -->
<tr<?= (!empty($p['selesai_spk'])) ? ' class="spk-selesai-row"' : '' ?>>
```

**Visual Feedback:**
- 🟢 Green badge + checkmark icon under SPK number
- 🟢 Subtle green tint on table row background
- 🟠 Orange badge + spinner icon for ongoing SPKs

---

## Complete SPK Lookup

### Secondary Sync: getPpicSpkDetail()

**Purpose:** Produksi page retrieves PPIC data when a production SPK is clicked

**Route:** `GET /produksi/get_ppic_spk/{spk}` or AJAX call  
**Handler:** `app/Controllers/Produksi::getPpicSpkDetail()`

**Process:**
1. User clicks SPK number in production form (auto-fill button)
2. Sends AJAX request to fetch PPIC details for that SPK
3. Searches all 9 PPIC mesin tables in priority order
4. Returns: `no_spk`, `nama_mesin`, `nama_produk`, `operator`, `target`, `selesai_spk`
5. Frontend auto-fills form fields + shows current completion status

**Example Response:**
```json
{
  "no_spk": "2926",
  "nama_mesin": "IPS 1",
  "nama_produk": "Galon 5L Biru",
  "operator": "Budi",
  "target": "500",
  "selesai_spk": 1
}
```

---

## Visual Design

### Status Badges

#### Completed SPK (selesai_spk = 1)
```
✓ Selesai
────────────
Background: rgba(40, 167, 69, 0.12)  [light green]
Text Color: #1a7a32                   [dark green]
Border:     rgba(40, 167, 69, 0.25)   [green]
Icon:       fas fa-check-circle       [checkmark]
```

#### Ongoing SPK (selesai_spk = 0)
```
⏳ Berjalan
────────────
Background: rgba(255, 159, 28, 0.1)   [light orange]
Text Color: #9a5e00                   [dark orange]
Border:     rgba(255, 159, 28, 0.25)  [orange]
Icon:       fas fa-spinner            [spinner]
```

### Row Highlighting

Completed SPK rows have subtle green tint:
```css
tr.spk-selesai-row > td {
    background-color: rgba(40, 167, 69, 0.035) !important;
}
tr.spk-selesai-row:hover > td {
    background-color: rgba(40, 167, 69, 0.065) !important;
}
```

---

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│              PPIC Operator Action                            │
│    Clicks "Selesai" toggle on SPK row in PPIC                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  POST /ppic/mesin_*/   │
        │  completeSpk/{id}      │
        └────────────┬───────────┘
                     │
      ┌──────────────┴──────────────┐
      │                             │
      ▼                             ▼
┌─────────────────────────┐   ┌──────────────────────┐
│ Toggle PPIC mesin row   │   │ Check isPpicContext()│
│ selesai_spk = 0→1 or 1→0│   │ (Must be PPIC, not   │
│ in ppic_mesin_*         │   │  Gudang)             │
└─────────────────────────┘   └──────────────────────┘
                                     │
                    ✅ Pass ─────────┘
                     │
                     ▼
         ┌──────────────────────────────┐
         │ syncSpkStatusToProduksi()    │
         │ ($noSpk, $newStatus)         │
         └──────────────┬───────────────┘
                        │
        ┌───────────────┴───────────────┐
        │                               │
        ▼                               ▼
   ┌─────────────┐           ┌────────────────────┐
   │ Build       │           │ Execute SQL UPDATE │
   │ candidates: │           │ produksi_headers   │
   │ ["2926",    │           │ WHERE              │
   │  "2926"]    │           │ TRIM(nomor_spk)    │
   │             │           │ IN (?, ?)          │
   └─────────────┘           └──────────┬─────────┘
                                        │
                                        ▼
                         ┌──────────────────────────┐
                         │ All matching rows update:│
                         │ selesai_spk = $newStatus │
                         │ deleted_at IS NULL       │
                         └──────────────┬───────────┘
                                        │
                                        ▼
                    ┌───────────────────────────────┐
                    │ Log rows affected             │
                    │ e.g., "updated 3 row(s)"      │
                    └───────────────────────────────┘

                              ┌─────────────────────┐
                              │  PRODUKSI PAGE      │
                              │  Displays status    │
                              │  ✓ Selesai badge   │
                              │  (green tint)       │
                              └─────────────────────┘
```

---

## Files Involved

### PPIC Module (Source)
- **`app/Controllers/Traits/MesinModuleTrait.php`**
  - `completeSpk($id)` — Toggle handler
  - `syncSpkStatusToProduksi()` — Propagates to Produksi
  - `isPpicContext()` — Checks if running from PPIC

### Produksi Module (Target)
- **`app/Models/MProduksiHeader.php`**
  - Defines `produksi_headers` table + fields
  - Includes `selesai_spk` in `$allowedFields`

- **`app/Controllers/Produksi.php`**
  - `getPpicSpkDetail($spk)` — Fetches PPIC status on demand

- **`app/Views/produksi/partials/table.php`**
  - Displays `selesai_spk` status with badges
  - Applies green tint to completed rows

- **`app/Views/produksi/partials/styles.php`**
  - `.badge-spk-selesai` — Green badge styling
  - `.badge-spk-ongoing` — Orange badge styling
  - `tr.spk-selesai-row` — Row highlight styling

### Database Migration
- **`app/Database/Migrations/2026-07-23-000001_AddSelesaiSpkToProduksiHeaders.php`**
  - Adds `selesai_spk` column to `produksi_headers`
  - Command: `php spark migrate --group produksi`

---

## Testing the Sync

### Manual Test Steps

1. **Navigate to PPIC Module**
   - Go to `http://localhost:8080/ppic/mesin_becum` (or any mesin)

2. **Find an SPK**
   - Locate any production order row (SPK number visible)
   - Current status shown in badge

3. **Mark Complete**
   - Click the "Selesai" toggle button
   - Status in PPIC should change
   - Flash message: "SPK XXXX berhasil diubah menjadi selesai"

4. **Check Produksi Module**
   - Go to `http://localhost:8080/produksi`
   - Search for same SPK number (use Ctrl+F)
   - ✅ Status should show "✓ Selesai" (green badge)
   - ✅ Row should have green tint

5. **Unmark Complete (Toggle Back)**
   - Return to PPIC, click toggle again
   - Status changes back to "Berjalan"
   - Return to Produksi
   - ✅ Badge should change to orange "⏳ Berjalan"
   - ✅ Green tint removed

### Verification Checklist

- [ ] PPIC `selesai_spk` field toggled in correct table
- [ ] Produksi `selesai_spk` field updated in ALL matching rows
- [ ] Badge displays correct color (green = complete, orange = ongoing)
- [ ] Row tint applied only for selesai_spk = 1
- [ ] Flash message confirms status change
- [ ] No database errors in logs
- [ ] Works across all 9 mesin types

---

## Known Limitations & Edge Cases

### Format Variations
SPK numbers stored differently across modules:
- PPIC: `2926` (integer)
- Produksi: `"2926"` or `" 2926 "` (string with possible spaces)
- **Solution:** Fuzzy matching with `TRIM()` and integer casting

### Multiple Produksi Rows per SPK
One PPIC SPK may have multiple `produksi_headers` rows (e.g., different operators, shifts, dates)
- **Behavior:** ALL matching rows updated synchronously
- **Rationale:** Ensures complete synchronization

### Soft Delete Protection
Sync only updates active (non-deleted) rows
```sql
WHERE deleted_at IS NULL
```
- **Rationale:** Respects business rules for archiving

### Error Handling
If sync fails (DB error), PPIC row update already succeeded
- **Approach:** Best-effort, errors logged but non-fatal
- **Rationale:** Prevents data corruption by maintaining PPIC as source of truth

---

## Related Workflows

### Warehouse2 Module Sync
A third module (`Warehouse2`) also integrates with Produksi:
- Trait: `app/Controllers/Traits/Warehouse2SyncTrait.php`
- Syncs `nomor_spk` when saving goods receipts
- **Independent:** Does not affect SPK completion status

### Auto-Fill JavaScript
Frontend JavaScript auto-fills production forms:
- Clicks SPK badge → calls `getPpicSpkDetail()`
- Populates: machine, product, operator, target
- Also displays PPIC completion status in form

---

## Troubleshooting

### Status Not Syncing?

1. **Check PPIC context**
   - Make sure toggling from `/ppic/mesin_*` route
   - Toggling from `/gudang/` doesn't sync to Produksi

2. **Check SPK format matching**
   - Log the SPK value from PPIC
   - Search `produksi_headers.nomor_spk` for that value
   - Ensure `TRIM()` removes spaces

3. **Check Produksi database**
   - Verify `produksi_headers.selesai_spk` column exists
   - Run migration: `php spark migrate --group produksi`

4. **Check logs**
   - `app/Logs/` for sync errors
   - Search for "syncSpkStatusToProduksi"

### Badge Not Showing?

1. **Check view file**
   - Verify `app/Views/produksi/partials/table.php` includes status display
   - Confirm `app/Views/produksi/partials/styles.php` loaded

2. **Check data**
   - Query: `SELECT id, nomor_spk, selesai_spk FROM produksi_headers LIMIT 5`
   - Ensure `selesai_spk` values are 0 or 1 (not NULL)

3. **Check CSS**
   - Inspect element (F12) → check for style application
   - Verify no CSS conflicts overriding badge styling

---

## Future Enhancements

### Potential Improvements
1. **Event listeners** — Replace traits with event system for cleaner architecture
2. **Bulk operations** — API endpoint to mark multiple SPKs complete
3. **Real-time updates** — WebSocket push to refresh Produksi without reload
4. **Audit trail** — Log timestamp + operator who changed status
5. **Two-way sync** — Allow Produksi to mark SPK complete (back to PPIC)

---

## Summary

✅ **The synchronization system is fully operational.**

When PPIC marks an SPK as complete (`selesai_spk = 1`), all matching rows in the Produksi module automatically update within milliseconds. The UI provides clear visual feedback (green badge, row tint) so operators see the status immediately.

**Key Benefits:**
- 🔄 Single-direction sync prevents conflicts
- ⚡ Automatic propagation eliminates manual updates
- 🎯 Format-aware matching handles variations
- 🛡️ Error handling maintains data integrity
- 📊 Clear visual feedback for operators
