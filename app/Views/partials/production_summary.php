<?php
/**
 * Production Summary Section
 * Displays summary of production output (Hasil Bagus & Hasil Reject)
 * Both overall and per-machine breakdown
 */

// Calculate totals from produksi data
$totalBagus = 0;
$totalReject = 0;
$machineData = [];

if (!empty($prodData)) {
    foreach ($prodData as $row) {
        $bagus = (int)($row['grand_total_bagus'] ?? 0);
        $reject = (int)($row['grand_total_reject'] ?? 0);
        
        $totalBagus += $bagus;
        $totalReject += $reject;
        
        // Group by machine
        $mesin = trim((string)($row['nama_mesin'] ?? 'Unknown'));
        if (!isset($machineData[$mesin])) {
            $machineData[$mesin] = ['bagus' => 0, 'reject' => 0, 'count' => 0];
        }
        $machineData[$mesin]['bagus'] += $bagus;
        $machineData[$mesin]['reject'] += $reject;
        $machineData[$mesin]['count'] += 1;
    }
}

$totalProduction = $totalBagus + $totalReject;
$rejectPercentage = $totalProduction > 0 ? round(($totalReject / $totalProduction) * 100, 1) : 0;
$qualityPercentage = $totalProduction > 0 ? round(($totalBagus / $totalProduction) * 100, 1) : 0;

// Sort machines by production volume (descending)
uasort($machineData, fn($a, $b) => ($b['bagus'] + $b['reject']) <=> ($a['bagus'] + $a['reject']));
?>

<div class="row g-3 mb-4"></div>


<!-- Per-Machine Breakdown -->
<?php if (!empty($machineData)): ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 8px;">
    <div class="card-header bg-white border-bottom" style="border-radius: 8px 8px 0 0; padding: 12px 12px; display: flex; justify-content: space-between; align-items: center;">
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">
            <i class="fas fa-microchip text-primary me-2"></i> Hasil Bagus & Reject Per Mesin
        </h6>
        <select id="machineFilterDropdown" class="form-select form-select-sm" style="width: 200px; font-size: 0.85rem;">
            <option value="">-- Tampilkan Semua Mesin --</option>
            <?php foreach (array_keys($machineData) as $mesin): ?>
            <option value="<?= esc($mesin) ?>"><?= esc($mesin) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="card-body p-2">
        <div class="row g-2">
            <?php foreach ($machineData as $mesin => $data):
                $machineTotal = $data['bagus'] + $data['reject'];
            ?>
            <div class="col-12 machine-card" data-machine="<?= esc($mesin) ?>">
                <div class="p-3 border rounded" style="background: #f8f9fa; border-radius: 6px; font-size: 0.85rem;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold"><?= esc($mesin) ?></span>
                        <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= $data['count'] ?> laporan</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Bagus</small>
                            <strong class="text-success" style="font-size: 1.1rem;"><?= number_format($data['bagus']) ?></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Reject</small>
                            <strong class="text-danger" style="font-size: 1.1rem;"><?= number_format($data['reject']) ?></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Total</small>
                            <strong style="font-size: 1.1rem;"><?= number_format($machineTotal) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterDropdown = document.getElementById('machineFilterDropdown');
    const machineCards = document.querySelectorAll('.machine-card');

    if (filterDropdown) {
        filterDropdown.addEventListener('change', function() {
            const selectedMachine = this.value;
            
            machineCards.forEach(card => {
                const machineValue = card.getAttribute('data-machine');
                
                if (selectedMachine === '' || machineValue === selectedMachine) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
<?php endif; ?>

<style>
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.progress-bar {
    transition: width 0.6s ease;
}

.badge-light {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}

table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
