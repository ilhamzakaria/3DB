# Downtime Toggle + Realtime Status Sync — Implementation Checklist

**Status:** ✅ All 5 core tasks completed, final verification in progress

---

## Task Summary

| # | Task | Status | Files Modified |
|---|------|--------|-----------------|
| 1 | Backend Route + toggleDowntime() | ✅ | Routes.php, Produksi.php |
| 2 | API deteksi status problem | ✅ | RealtimeProduksiApi.php |
| 3 | Frontend Realtime CSS+JS problem state | ✅ | realtime/index.php |
| 4 | Tombol toggle di tabel Produksi | ✅ | table.php, styles.php, scripts.php |
| 5 | Pass has_active_downtime ke view | ✅ | Produksi.php |
| 6 | Wire up & testing | 🔄 | (This checklist) |

---

## Implementation Details

### 1️⃣ Backend: Route + toggleDowntime() Method

**Route:** `POST /produksi/downtime/toggle/{id}` (app/Config/Routes.php)

**Handler:** `Produksi::toggleDowntime($headerId)` (app/Controllers/Produksi.php, line ~2340)

**Logic Flow:**
```
Guard: role === 'produksi' (return 403 if not)
  ↓
Check if active downtime exists (waktu_selesai IS NULL)
  ├─ YES (Toggle OFF):
  │   └─ UPDATE produksi_downtimes SET waktu_selesai=NOW(), durasi_menit=(diff)
  │   └─ Return { success: true, active: false, message: "Downtime berakhir" }
  │
  └─ NO (Toggle ON):
      └─ INSERT INTO produksi_downtimes { header_id, waktu_mulai=NOW(), waktu_selesai=NULL }
      └─ Return { success: true, active: true, message: "Downtime dimulai" }

Error Cases:
- Header not found → 404
- Role not produksi → 403
- DB error on insert/update → 500 with message
```

**Response Format:**
```json
{
  "success": true/false,
  "active": true/false,
  "message": "Human-readable message"
}
```

---

### 2️⃣ API: Realtime Status Detection

**Endpoint:** `GET /api/realtime/produksi-live` (app/Controllers/Api/RealtimeProduksiApi.php, line ~35)

**New Logic:**
```
FOR EACH mesin:
  1. Query latest produksi_headers for today
  2. Check: COUNT(*) FROM produksi_downtimes 
           WHERE header_id = ? 
           AND waktu_selesai IS NULL
  3. IF count > 0:
       status = 'problem'
       downtime_active = true
       Fetch alasan_downtime, problem, action_plant, waktu_mulai
     ELSE:
       status = 'running'
       downtime_active = false
```

**Response Fields Added:**
```json
{
  "status": "idle|running|problem",
  "downtime_active": boolean,
  "alasan_downtime": "...",
  "problem": "...",
  "action_plant": "...",
  "waktu_mulai": "ISO datetime"
}
```

---

### 3️⃣ Frontend Realtime: Problem State Styling

**File:** `app/Views/realtime/index.php`

**CSS Changes:**
- `.mesin-card.problem { border-color: #fca5a5; }` — Red border
- `.mesin-card.problem .card-head { gradient from #fef2f2 to #fee2e2; }` — Red gradient header
- `.mesin-card.problem .gear-icon { color: #dc2626; animation: pulse-warning; }` — Red pulsing gear
- `.status-badge.problem { background: #fee2e2; color: #b91c1c; }` — Red badge
- `.pulse-dot.problem { animation: pulse-ring-warning; }` — Red pulse animation

**JavaScript Changes:**
- `updateCard(d)` function updated to handle `d.status === 'problem'`
- Display badge: `⚠ Problem` with red pulse dot
- Data remains visible (unlike idle state which clears data)
- Display downtime alasan if available in card body

---

### 4️⃣ Frontend Produksi: Downtime Toggle Button

**File:** `app/Views/produksi/partials/table.php`

**Button Rendering (role guard):**
```php
<?php if (session('role') === 'produksi'): ?>
    <button class="btn-toggle-downtime"
        data-id="<?= $p['id'] ?>"
        data-active="<?= !empty($p['has_active_downtime']) ? '1' : '0' ?>">
        <i class="fas <?= ... fa-play-circle|fa-pause-circle ?>"></i>
    </button>
<?php endif; ?>
```

**Styling (app/Views/produksi/partials/styles.php):**
- `.btn-downtime` (OFF state) → Orange background, play-circle icon
- `.btn-downtime[data-active="1"]` (ON state) → Red background, pause-circle icon

**JavaScript Handler (app/Views/produksi/scripts.php):**
- Attach click listener to `.btn-toggle-downtime`
- POST to `/produksi/downtime/toggle/{headerId}`
- Update button state: icon, data-active attribute
- Show toast notification (success/error)
- Trigger realtime refetch if available

---

### 5️⃣ Controller: has_active_downtime Lookup

**Files:** `app/Controllers/Produksi.php::index()` and `::mesinIndex()`

**Process:**
1. Get displayed header IDs from pagination result
2. Query produksi_downtimes (waktu_selesai IS NULL, deleted_at IS NULL)
3. Build lookup array: `$activeDowntimes[header_id] = true`
4. Map to produksi array: `$p['has_active_downtime'] = isset($activeDowntimes[$p['id']])`

**Ensures:**
- Button renders with correct initial state (red if active, orange if not)
- No page reload needed — just button color sync

---

## Testing Checklist

### ✅ Unit Tests

- [ ] **toggleDowntime() role guard**
  - Call as non-produksi role → expect 403
  - Call as produksi role → expect 200

- [ ] **toggleDowntime() toggle ON**
  - No active downtime exists
  - POST → INSERT executed
  - Return active=true

- [ ] **toggleDowntime() toggle OFF**
  - Active downtime exists (waktu_selesai IS NULL)
  - POST → UPDATE with waktu_selesai, durasi_menit calculated
  - Return active=false

- [ ] **toggleDowntime() durasi_menit calculation**
  - waktu_mulai = 10:00:00
  - waktu_selesai = 10:15:30 (15.5 minutes)
  - durasi_menit should be 15

- [ ] **API downtime detection**
  - No downtime → status='running'
  - Active downtime exists → status='problem'
  - Inactive downtime (waktu_selesai filled) → status='running'

### ✅ Integration Tests

- [ ] **Full Flow: Produksi → Realtime**
  - **Setup:** Open Produksi in Tab A, Realtime in Tab B (polling 30s)
  - **Step 1:** Produksi page load → button shows orange (no downtime)
  - **Step 2:** Click toggle button → button turns red, toast shows "Downtime dimulai"
  - **Step 3:** Wait max 30s → Realtime card for mesin turns red with "⚠ Problem" badge
  - **Step 4:** Go back to Produksi → button still red
  - **Step 5:** Click toggle again → button turns orange, toast shows "Downtime berakhir"
  - **Step 6:** Wait max 30s → Realtime card turns green back to normal
  - **Expected:** Status sync complete within ~30s

- [ ] **Multiple Downtimes on Different Mesin**
  - Toggle downtime ON for Mesin A
  - Toggle downtime ON for Mesin B
  - Realtime should show BOTH cards red
  - Toggle downtime OFF for Mesin A
  - Realtime should show Mesin A green, Mesin B red

- [ ] **Downtime Info Display in Realtime**
  - Set downtime with alasan_downtime value
  - Toggle ON
  - Realtime card should display alasan_downtime row

### ✅ Edge Cases

- [ ] **No header found**
  - POST to non-existent header_id → 404 response

- [ ] **Multiple active downtimes for same header**
  - Manually insert 2 downtimes for same header_id (waktu_selesai IS NULL)
  - Toggle → should update the LATEST one (ORDER BY id DESC LIMIT 1)
  - (Rare edge case, but handled by design)

- [ ] **Soft-deleted downtime record**
  - Downtime has deleted_at NOT NULL
  - Toggle ON → INSERT new record
  - Status should not count soft-deleted record

- [ ] **Zero durasi_menit edge case**
  - waktu_mulai and waktu_selesai same (< 1 second apart)
  - durasi_menit = 0 (valid)
  - Should still work without error

- [ ] **Network error during toggle**
  - Toggle click → network error
  - Button returns to previous state (not grayed out)
  - Toast shows error message
  - User can retry

- [ ] **Permission denied for non-produksi role**
  - Try to access toggle endpoint as ppic, gudang, etc.
  - Expect 403 Forbidden
  - No downtime record created

### ✅ UI/UX Tests

- [ ] **Button visual feedback**
  - OFF state: orange play-circle
  - ON state: red pause-circle
  - Hover: color intensifies
  - Click: brief spinner during request

- [ ] **Realtime card visual feedback**
  - Running: green header, pulse dot green
  - Problem: red header, pulse dot red, gear icon red
  - Idle: gray header, no pulse

- [ ] **Badge transitions**
  - Running → Problem: transition smooth
  - Problem → Running: transition smooth
  - Data remains visible during transition

- [ ] **Toast notifications**
  - Success: appears 4s
  - Error: appears 5s
  - Can dismiss manually
  - Non-intrusive positioning (bottom-right)

### ✅ Database Integrity Tests

- [ ] **No duplicate active downtimes**
  - Toggle ON → 1 record with waktu_selesai IS NULL
  - Toggle ON again (shouldn't happen) → still 1 record

- [ ] **Durasi_menit accuracy**
  - Sample 5 toggle cycles
  - Calculate expected durasi
  - Verify actual durasi ± 1 minute (network/system variance)

- [ ] **Soft delete respected**
  - Toggle OFF → downtime record has updated_at
  - Manually set deleted_at
  - Toggle ON again → new record inserted, soft-deleted one ignored

---

## Manual Testing Workflow

### Test Environment Setup
1. Create test user with role='produksi'
2. Create produksi_headers record for today
3. Ensure browser has 2 tabs ready (Produksi + Realtime)

### Test Scenario: Happy Path

**Time: 10:00 AM**
```
Tab A (Produksi): /produksi
├─ Load page
├─ See produksi table
└─ Downtime button (orange play icon)

Tab B (Realtime): /realtime
├─ Load page
├─ Card shows green "Running"
└─ Polling every 30s
```

**Time: 10:01 AM — Toggle ON**
```
Tab A: Click downtime button
├─ Button turns red (pause icon)
├─ Toast: "Downtime dimulai"
└─ Database: INSERT produksi_downtimes { waktu_mulai=10:01, waktu_selesai=NULL }

Tab B: (Wait for next poll ~30s)
├─ Card turns red
├─ Badge: "⚠ Problem"
└─ Gear icon: Red, spinning
```

**Time: 10:16 AM — Toggle OFF**
```
Tab A: Click downtime button
├─ Button turns orange (play icon)
├─ Toast: "Downtime berakhir"
└─ Database: UPDATE produksi_downtimes { waktu_selesai=10:16, durasi_menit=15 }

Tab B: (Wait for next poll ~30s)
├─ Card turns green
├─ Badge: "Running"
└─ Data refreshed
```

---

## Deployment Notes

### Pre-Deployment Checklist

- [ ] All migrations applied: `php spark migrate --group produksi`
- [ ] Database has `produksi_downtimes` table with all fields
- [ ] `problem` and `action_plant` columns added (migration 2026-08-24-000002)
- [ ] Role 'produksi' exists in users table
- [ ] `RealtimeProduksiApi` endpoint accessible at `/api/realtime/produksi-live`

### Post-Deployment Verification

- [ ] Visit `/produksi` as produksi role → button visible
- [ ] Visit `/api/realtime/produksi-live` → JSON response includes new fields
- [ ] Click toggle button → AJAX request succeeds
- [ ] Visit `/realtime` → realtime polling works
- [ ] Check logs for any errors during toggle

### Performance Considerations

- **Query Performance:**
  - Downtime lookup in Produksi::index() uses `whereIn(header_ids)` — efficient batch query
  - Realtime API subquery is indexed (header_id, waktu_selesai)
  - No N+1 queries

- **Polling:**
  - Realtime polls every 30s (configurable INTERVAL_SEC in JS)
  - Max delay from toggle to card update: ~30s
  - Acceptable for production monitoring

---

## Known Limitations & Future Work

### Current Limitations

1. **Downtime reason can only be set during form edit** — Toggle button doesn't open a modal to set reason. Reason must be filled in during `/produksi/edit` form.

2. **One active downtime per header** — Design assumption. If multiple downtimes somehow exist, only the latest is toggled.

3. **Polling-based sync** — ~30s delay. Consider WebSocket for real-time updates in future.

4. **No audit trail** — Doesn't log WHO toggled downtime. Future: add user_id, action_timestamp to logs.

### Future Enhancements

- [ ] Open modal on toggle to allow setting downtime reason inline
- [ ] Add "duration so far" timer while downtime is active
- [ ] Email/SMS alert when downtime duration exceeds threshold
- [ ] Downtime auto-close after X hours of inactivity
- [ ] WebSocket push (replace polling)
- [ ] Audit log: user_id, action, timestamp
- [ ] Bulk toggle (multiple headers at once)

---

## Reference Implementation

### Minimal Test: Toggle Down then Up

```bash
# 1. Get a produksi header ID (e.g., id=42)
SELECT id FROM produksi_headers WHERE tanggal=CURDATE() LIMIT 1;

# 2. Toggle ON
curl -X POST http://localhost:8080/produksi/downtime/toggle/42 \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=..."

# Expected: { "success": true, "active": true, "message": "Downtime dimulai" }

# 3. Check database
SELECT * FROM produksi_downtimes 
WHERE produksi_header_id=42 AND waktu_selesai IS NULL;

# Expected: 1 row with waktu_mulai = NOW()

# 4. Check Realtime API
curl http://localhost:8080/api/realtime/produksi-live

# Expected: status='problem' for that mesin

# 5. Toggle OFF
curl -X POST http://localhost:8080/produksi/downtime/toggle/42

# Expected: { "success": true, "active": false, "message": "Downtime berakhir" }

# 6. Check database again
SELECT * FROM produksi_downtimes 
WHERE produksi_header_id=42 AND waktu_selesai IS NOT NULL;

# Expected: 1 row with waktu_selesai = NOW(), durasi_menit filled
```

---

## Summary

✅ **All implementation complete and ready for testing.**

**Key Features:**
- ✅ Toggle downtime ON/OFF via Produksi table button
- ✅ Automatic durasi calculation
- ✅ Real-time sync to realtime API
- ✅ Visual feedback: red card when downtime active
- ✅ Role-based access control (produksi only)
- ✅ Toast notifications
- ✅ Error handling & logging

**Next Steps:**
1. Run manual testing scenarios
2. Verify database integrity
3. Test edge cases
4. Deploy to staging
5. Final UAT before production

---

**Generated:** August 22, 2026  
**Status:** Implementation Complete — Awaiting Testing & Deployment
