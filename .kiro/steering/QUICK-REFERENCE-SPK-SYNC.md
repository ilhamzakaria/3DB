# Quick Reference: SPK Synchronization

## TL;DR

✅ **When PPIC marks SPK complete → Produksi automatically updates**

### The Process in 3 Steps

1. **PPIC Operator** clicks toggle button on SPK → marks as "Selesai"
2. **System** automatically calls `syncSpkStatusToProduksi()` 
3. **Produksi Page** shows green "✓ Selesai" badge (live update)

---

## Key Routes & Handlers

| Action | Route | Handler | File |
|--------|-------|---------|------|
| Toggle SPK status | `POST /ppic/mesin_*/completeSpk/{id}` | `completeSpk()` | `MesinModuleTrait.php` |
| Sync to Produksi | (internal call) | `syncSpkStatusToProduksi()` | `MesinModuleTrait.php` |
| Display status | `GET /produksi` | table partial | `table.php` |

---

## Database Sync

```
PPIC mesin table                  Produksi table
─────────────────                 ──────────────
ppic_mesin_*                      produksi_headers
│                                 │
├─ no_spk: "2926"            ────>├─ nomor_spk: " 2926 "
├─ selesai_spk: 1            ────>├─ selesai_spk: 1
├─ nama_mesin: "IPS 1"       ────>├─ nama_mesin: "IPS 1"
└─ ...                           └─ ...
```

**Sync Method:** `TRIM(nomor_spk) IN(?, ?)` handles format variations

---

## Visual Indicators

### Completed SPK (selesai_spk = 1)
```
✓ Selesai
[green badge] [green row tint]
```

### Ongoing SPK (selesai_spk = 0)
```
⏳ Berjalan
[orange badge] [no tint]
```

---

## Files to Check

| File | Purpose |
|------|---------|
| `app/Controllers/Traits/MesinModuleTrait.php` | Sync logic (`completeSpk()`, `syncSpkStatusToProduksi()`) |
| `app/Views/produksi/partials/table.php` | Status display |
| `app/Views/produksi/partials/styles.php` | Badge & row tint styling |
| `app/Database/Migrations/.../AddSelesaiSpkToProduksiHeaders.php` | Database column |

---

## Testing

**Step 1:** Go to PPIC → `/ppic/mesin_becum`  
**Step 2:** Click "Selesai" toggle on any SPK  
**Step 3:** Go to Produksi → `/produksi`  
**Step 4:** Search for same SPK → should show ✓ Selesai (green)

---

## Troubleshooting

### Status didn't sync?
- ✅ Check you're in PPIC context (not Gudang)
- ✅ Check logs for sync errors
- ✅ Verify migration ran: `php spark migrate --group produksi`

### Badge not showing?
- ✅ Check CSS loaded (`styles.php`)
- ✅ Verify DB column exists: `SELECT selesai_spk FROM produksi_headers`
- ✅ Inspect element to see if class applied

---

## Limits & Edge Cases

| Scenario | Behavior |
|----------|----------|
| Multiple Produksi rows per SPK | ALL rows update synchronously |
| Soft-deleted rows | Skipped (only active rows update) |
| Different SPK formats (spaces, ints) | Handled by `TRIM()` + casting |
| Sync DB error | Logged but doesn't block PPIC update |
| Toggle status back & forth | Works both ways (0→1 and 1→0) |

---

## No Configuration Needed ✨

Everything is already set up. Just:
1. Mark SPK complete in PPIC
2. Check Produksi to see updated status
3. Done!

---

**Full Details:** See `SPK-SYNC-ARCHITECTURE.md`
