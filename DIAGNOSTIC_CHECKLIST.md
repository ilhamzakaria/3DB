# Total SPK Autofill - Server Diagnostic Checklist

## ✅ Quick Diagnosis Steps

### Step 1: Test Database & Data Layer
**URL:** `http://hosting-url/produksi/test_total_spk_autofill`

Expected output structure:
```
✓ step1_database.status = "OK"
✓ step1_database.has_total_spk_column = "YES"
✓ step2_getspklist.with_total_spk_value > 0
✓ step3_ajax_endpoint.has_total_spk = "YES"
```

**Copy entire JSON output here:**
```json
[PASTE OUTPUT HERE]
```

---

### Step 2: Debug Info Endpoint
**URL:** `http://hosting-url/produksi/debug_spk_list`

Look for:
```json
"total_spk_analysis": {
  "with_value": [NUMBER],
  "empty": [NUMBER],
  "percentage_filled": "[X]%"
}
```

**Expected:** percentage_filled should be > 0%

---

### Step 3: Browser Console Test

**Go to:** `http://hosting-url/produksi` → Tambah Data

**Open DevTools (F12) → Console tab**

**Paste and run:**
```javascript
// Quick diagnostic
console.log('=== DIAGNOSTIC CHECK ===');
console.log('1. Scripts loaded:', typeof fillProduksiFromPpic === 'function' ? '✓' : '✗');
console.log('2. SPK select exists:', !!document.querySelector('.nomor_spk') ? '✓' : '✗');
console.log('3. total_spk field exists:', !!document.querySelector('.total_spk') ? '✓' : '✗');
console.log('4. total_spk_hidden exists:', !!document.querySelector('.total_spk_hidden') ? '✓' : '✗');

// Now select an SPK and wait 3 seconds
console.log('\n5. Select an SPK from dropdown, then run this after 3 seconds:');
const vis = document.querySelector('.total_spk');
const hid = document.querySelector('.total_spk_hidden');
console.log('   Visible:', vis?.value || 'EMPTY');
console.log('   Hidden:', hid?.value || 'EMPTY');

// Get diagnostic logs
console.log('\n6. Diagnostic logs:', window.getDiagnostics());
```

**Screenshot console output:**
- Show items 1-4 results
- Show items 5-6 after selecting SPK

---

### Step 4: Network Tab Test

**In same browser tab, go to DevTools → Network tab**

**Filter:** `getPpicSpkDetail`

**Action:** Select SPK from dropdown

**Check:**
- ✅ Is request sent? (green request line)
- ✅ Status 200?
- ✅ Response contains total_spk?

**Screenshot:**
- Show network request
- Show Response tab content

---

## 📋 Summary Template

Please fill this out and send back:

```
## Environment
- Server: [hosting provider/localhost]
- Database: PPIC = oroppic, Produksi = oroproduksi
- PHP version: [if you know]

## Diagnostic Results

### DB Test (/test_total_spk_autofill)
- step1_database.status: [OK/ERROR]
- step1_database.has_total_spk_column: [YES/NO]
- step2_getspklist.percentage: [X%]
- step3_ajax_endpoint.has_total_spk: [YES/NO]

### Console Test
- Scripts loaded: [✓/✗]
- SPK select exists: [✓/✗]
- total_spk field exists: [✓/✗]
- After autofill:
  - Visible value: [value or EMPTY]
  - Hidden value: [value or EMPTY]

### Network Test
- getPpicSpkDetail request sent: [YES/NO]
- Response status: [200/ERROR]
- Response has total_spk: [YES/NO]

### Errors seen
[List any errors from console]

## Key Finding
[Based on above, what seems to be the issue?]
- [ ] Database has no total_spk values
- [ ] getSpkList() not returning total_spk
- [ ] AJAX endpoint failing
- [ ] JavaScript not executing
- [ ] Fields not syncing
- [ ] Other: ___________
```

---

## 🔍 If You Get Stuck

**Post in console and share output:**

```javascript
// Copy entire diagnostic object
JSON.stringify(window.getDiagnostics(), null, 2)
```

This will show all console logs captured during execution.
