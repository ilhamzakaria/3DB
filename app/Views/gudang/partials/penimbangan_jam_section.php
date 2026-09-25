<?php
/**
 * Penimbangan Jam Section - Reusable UI Component
 * 
 * Displays 8-hour timed weighing data input (galon & flash) in a clean, responsive layout.
 * Used in both Add and Edit modals for MesinBecum.
 * 
 * Parameters passed via view:
 * - $mode: 'add' or 'edit'
 * - $penimbanganData: Array of penimbangan form data (flat format)
 *   Format: ['jam_1_galon' => 10, 'jam_1_flash' => 8.5, 'jam_2_galon' => 12, ...]
 * - $errors: Array of validation errors keyed by field name
 * 
 * @var string $mode
 * @var array $penimbanganData
 * @var array $errors
 */

$mode = $mode ?? 'add';
$penimbanganData = $penimbanganData ?? [];
$errors = $errors ?? [];

// Initialize default values if not provided
for ($h = 1; $h <= 8; $h++) {
    if (!isset($penimbanganData["jam_{$h}_galon"])) {
        $penimbanganData["jam_{$h}_galon"] = 0;
    }
    if (!isset($penimbanganData["jam_{$h}_flash"])) {
        $penimbanganData["jam_{$h}_flash"] = 0;
    }
}
?>

<div class="penimbangan-jam-section">
    <!-- Section Header -->
    <div class="section-header mb-3">
        <h6 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-hourglass-half mr-2"></i> Durasi Penimbangan (8 Jam)
        </h6>
        <small class="text-muted d-block mt-1">Input data penimbangan galon dan flash untuk setiap jam</small>
    </div>

    <!-- Hour Grid Layout -->
    <div class="penimbangan-hours-grid">
        <?php for ($hour = 1; $hour <= 8; $hour++) : ?>
            <?php
            $galonKey = "jam_{$hour}_galon";
            $flashKey = "jam_{$hour}_flash";
            $galonError = $errors[$galonKey] ?? null;
            $flashError = $errors[$flashKey] ?? null;
            $galonValue = $penimbanganData[$galonKey] ?? 0;
            $flashValue = $penimbanganData[$flashKey] ?? 0;
            ?>

            <!-- Hour Card -->
            <div class="penimbangan-hour-card">
                <!-- Hour Label -->
                <div class="hour-label">
                    <span class="badge badge-primary">Jam <?= $hour ?></span>
                </div>

                <!-- Galon Input -->
                <div class="form-group mb-2">
                    <label class="form-label small mb-1">
                        <i class="fas fa-droplet text-info"></i> Galon
                        <span class="unit">(kg)</span>
                    </label>
                    <input 
                        type="number" 
                        name="penimbangan_jam[jam_<?= $hour ?>_galon]"
                        class="form-control form-control-sm penimbangan-galon-input <?= $galonError ? 'is-invalid' : '' ?>"
                        value="<?= esc($galonValue) ?>"
                        placeholder="0"
                        min="0"
                        max="999.99"
                        step="0.01"
                        data-hour="<?= $hour ?>"
                        data-type="galon"
                    />
                    <?php if ($galonError) : ?>
                        <small class="text-danger d-block mt-1">
                            <i class="fas fa-exclamation-circle"></i> <?= $galonError ?>
                        </small>
                    <?php endif; ?>
                </div>

                <!-- Flash Input -->
                <div class="form-group mb-0">
                    <label class="form-label small mb-1">
                        <i class="fas fa-bolt text-warning"></i> Flash
                        <span class="unit">(kg)</span>
                    </label>
                    <input 
                        type="number" 
                        name="penimbangan_jam[jam_<?= $hour ?>_flash]"
                        class="form-control form-control-sm penimbangan-flash-input <?= $flashError ? 'is-invalid' : '' ?>"
                        value="<?= esc($flashValue) ?>"
                        placeholder="0"
                        min="0"
                        max="999.99"
                        step="0.01"
                        data-hour="<?= $hour ?>"
                        data-type="flash"
                    />
                    <?php if ($flashError) : ?>
                        <small class="text-danger d-block mt-1">
                            <i class="fas fa-exclamation-circle"></i> <?= $flashError ?>
                        </small>
                    <?php endif; ?>
                </div>

                <!-- Hour Subtotal -->
                <div class="hour-subtotal mt-2 pt-2 border-top">
                    <small class="text-muted">
                        <strong>Total:</strong> 
                        <span class="hour-total-value" data-hour="<?= $hour ?>">
                            <?= number_format((float)$galonValue + (float)$flashValue, 2) ?>
                        </span> kg
                    </small>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Summary Row -->
    <div class="penimbangan-summary mt-4 p-3 bg-light rounded border">
        <div class="row text-center g-2">
            <div class="col-md-4">
                <div>
                    <small class="text-muted d-block">Total Galon</small>
                    <strong class="h5 mb-0 text-info" id="penimbangan-total-galon">0.00</strong>
                    <small class="text-muted">kg</small>
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <small class="text-muted d-block">Total Flash</small>
                    <strong class="h5 mb-0 text-warning" id="penimbangan-total-flash">0.00</strong>
                    <small class="text-muted">kg</small>
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <small class="text-muted d-block">Grand Total</small>
                    <strong class="h5 mb-0 text-success" id="penimbangan-grand-total">0.00</strong>
                    <small class="text-muted">kg</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="penimbangan-legend mt-3 pt-3 border-top">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Semua input bersifat opsional. Nilai default adalah 0 jika tidak diisi. 
            Gunakan format desimal (contoh: 10.5) untuk nilai yang lebih presisi.
        </small>
    </div>
</div>

<!-- Styling -->
<style>
.penimbangan-jam-section {
    margin-top: 1rem;
    margin-bottom: 1rem;
}

.section-header {
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e3e6f0;
}

.section-header h6 {
    color: #2a5a8c;
    font-size: 0.95rem;
}

/* Grid Layout - 8 hours in responsive columns */
.penimbangan-hours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

/* Individual Hour Card */
.penimbangan-hour-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.penimbangan-hour-card:hover {
    border-color: #4361ee;
    box-shadow: 0 4px 8px rgba(67, 97, 238, 0.1);
    transform: translateY(-2px);
}

/* Hour Label Badge */
.penimbangan-hour-card .hour-label {
    margin-bottom: 0.75rem;
}

.penimbangan-hour-card .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
    width: 100%;
    text-align: center;
}

/* Form Controls */
.penimbangan-hour-card .form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #2b3674;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.penimbangan-hour-card .unit {
    font-size: 0.65rem;
    font-weight: 400;
    color: #a3aed1;
    text-transform: none;
    letter-spacing: normal;
    margin-left: 0.25rem;
}

.penimbangan-hour-card .form-control-sm {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
    height: 32px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}

.penimbangan-hour-card .form-control-sm:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.penimbangan-hour-card .form-control-sm.is-invalid {
    border-color: #dc3545;
}

.penimbangan-hour-card .form-control-sm.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

/* Hour Subtotal */
.hour-subtotal {
    font-size: 0.75rem;
    background: #f8f9fc;
    padding: 0.5rem;
    border-radius: 4px;
}

.hour-total-value {
    color: #2a5a8c;
    font-weight: 700;
}

/* Summary Section */
.penimbangan-summary {
    background-color: #f8f9fc;
    border: 1px solid #e3e6f0;
    margin-top: 1.5rem;
}

.penimbangan-summary .col-md-4 {
    padding: 1rem 0.5rem;
    border-right: 1px solid #dee2e6;
}

.penimbangan-summary .col-md-4:last-child {
    border-right: none;
}

.penimbangan-summary .h5 {
    margin-top: 0.5rem;
}

/* Legend */
.penimbangan-legend {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.5;
}

/* Responsive: Tablet */
@media (max-width: 768px) {
    .penimbangan-hours-grid {
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 0.75rem;
    }

    .penimbangan-hour-card {
        padding: 0.75rem;
    }

    .penimbangan-summary .col-md-4 {
        padding: 0.75rem 0.25rem;
        font-size: 0.85rem;
    }
}

/* Responsive: Mobile */
@media (max-width: 576px) {
    .penimbangan-hours-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .penimbangan-hour-card {
        padding: 0.6rem;
    }

    .penimbangan-hour-card .form-label {
        font-size: 0.7rem;
    }

    .penimbangan-hour-card .form-control-sm {
        font-size: 0.75rem;
        height: 28px;
    }

    .penimbangan-summary {
        margin-top: 1rem;
    }

    .penimbangan-summary .col-md-4 {
        padding: 0.5rem 0.25rem;
        font-size: 0.75rem;
    }

    .penimbangan-summary .h5 {
        font-size: 1rem;
    }
}

/* Accessibility */
.penimbangan-hour-card .form-control-sm:focus,
.penimbangan-hour-card .form-control-sm:active {
    outline: 2px solid #4361ee;
    outline-offset: 2px;
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .penimbangan-hour-card {
        background: #1a1f35;
        border-color: #2d3748;
    }

    .penimbangan-hour-card .form-control-sm {
        background: #2d3748;
        color: #e2e8f0;
        border-color: #4a5568;
    }
}
</style>

<!-- JavaScript: Real-time calculations and validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const galonInputs = document.querySelectorAll('.penimbangan-galon-input');
    const flashInputs = document.querySelectorAll('.penimbangan-flash-input');

    // Bind change events to all inputs
    [...galonInputs, ...flashInputs].forEach(input => {
        input.addEventListener('change', updatePenimbanganCalculations);
        input.addEventListener('input', updatePenimbanganCalculations);
        input.addEventListener('blur', validatePenimbanganInput);
    });

    // Initial calculation on page load
    updatePenimbanganCalculations();
});

/**
 * Update penimbangan totals in real-time
 */
function updatePenimbanganCalculations() {
    let totalGalon = 0;
    let totalFlash = 0;

    // Calculate totals
    document.querySelectorAll('.penimbangan-galon-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        totalGalon += value;

        // Update hour subtotal
        const hour = input.getAttribute('data-hour');
        const flashValue = parseFloat(
            document.querySelector(`.penimbangan-flash-input[data-hour="${hour}"]`)?.value || 0
        );
        const hourTotal = value + flashValue;
        const subtotalSpan = document.querySelector(`.hour-total-value[data-hour="${hour}"]`);
        if (subtotalSpan) {
            subtotalSpan.textContent = hourTotal.toFixed(2);
        }
    });

    document.querySelectorAll('.penimbangan-flash-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        totalFlash += value;
    });

    // Update summary display
    const totalGalonDisplay = document.getElementById('penimbangan-total-galon');
    const totalFlashDisplay = document.getElementById('penimbangan-total-flash');
    const grandTotalDisplay = document.getElementById('penimbangan-grand-total');

    if (totalGalonDisplay) totalGalonDisplay.textContent = totalGalon.toFixed(2);
    if (totalFlashDisplay) totalFlashDisplay.textContent = totalFlash.toFixed(2);
    if (grandTotalDisplay) grandTotalDisplay.textContent = (totalGalon + totalFlash).toFixed(2);
}

/**
 * Validate individual input on blur
 */
function validatePenimbanganInput(event) {
    const input = event.target;
    const value = parseFloat(input.value) || 0;

    // Check range
    if (value < 0 || value > 999.99) {
        input.classList.add('is-invalid');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('text-danger')) {
            const errorMsg = document.createElement('small');
            errorMsg.className = 'text-danger d-block mt-1';
            errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Nilai harus antara 0 dan 999.99';
            input.parentElement.appendChild(errorMsg);
        }
    } else {
        input.classList.remove('is-invalid');
        const errorMsg = input.parentElement.querySelector('.text-danger');
        if (errorMsg) errorMsg.remove();
    }
}
</script>
