<!-- Edit Data Modal Fragment -->
<div class="modal-header">
    <input type="hidden" name="id" value="<?= $header['id'] ?>">
    <h5 class="modal-title font-weight-bold text-primary">
        <i class="fas fa-edit mr-2"></i> Edit Laporan Produksi
    </h5>
</div>
<div class="modal-body p-4">
    <style>
        .bg-primary-light { background-color: #cfe2ff; }
        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }
        .card {
            border-radius: 12px !important;
            transition: box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }
        .fw-600 {
            font-weight: 600 !important;
        }
    </style>
    <form id="formEditLaporan" action="<?= base_url('produksi/update/' . $header['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?= view('produksi/partials/form_fields', ['header' => $header, 'jams' => $jams, 'rejects' => $rejects, 'materials' => $materials, 'colorants' => $colorants, 'packaging' => $packaging, 'downtimes' => $downtimes, 'spk_list' => $spk_list]) ?>
    </form>

    <script>
        // Update Shift dropdown with tercapai status when SPK changes
        function updateShiftOptions() {
            const spkSelect = document.querySelector('.nomor_spk');
            const shiftSelect = document.querySelector('#shift_select');
            
            if (!spkSelect || !shiftSelect) return;
            
            const selectedSpkOption = spkSelect.selectedOptions[0];
            if (!selectedSpkOption || !selectedSpkOption.value) {
                // Reset all shift options when no SPK selected
                Array.from(shiftSelect.options).forEach(opt => {
                    if (opt.value) opt.textContent = opt.value === '1' ? 'Shift 1' : opt.value === '2' ? 'Shift 2' : 'Shift 3';
                });
                return;
            }
            
            // Get is_completed from data attribute
            const isCompleted = selectedSpkOption.dataset.isCompleted === '1';
            
            // Update shift option labels
            Array.from(shiftSelect.options).forEach(opt => {
                if (opt.value) {
                    const baseLabel = opt.value === '1' ? 'Shift 1' : opt.value === '2' ? 'Shift 2' : 'Shift 3';
                    opt.textContent = isCompleted ? baseLabel + ' ✓ TERCAPAI' : baseLabel;
                    opt.dataset.isCompleted = isCompleted ? '1' : '0';
                }
            });
        }
        
        // Update tercapai hidden field when shift is selected
        function updateTercapaiField() {
            const shiftSelect = document.querySelector('#shift_select');
            const tercapaiHidden = document.querySelector('.tercapai_hidden');
            
            if (!shiftSelect || !tercapaiHidden) return;
            
            const selectedOption = shiftSelect.selectedOptions[0];
            if (selectedOption && selectedOption.dataset.isCompleted) {
                tercapaiHidden.value = selectedOption.dataset.isCompleted;
            }
        }
        
        // Initialize on modal open
        document.addEventListener('DOMContentLoaded', function() {
            const spkSelect = document.querySelector('.nomor_spk');
            const shiftSelect = document.querySelector('#shift_select');
            
            if (spkSelect) spkSelect.addEventListener('change', function() {
                updateShiftOptions();
                updateTercapaiField();
            });
            
            if (shiftSelect) {
                shiftSelect.addEventListener('change', updateTercapaiField);
                
                // For edit mode: if shift already has a value on page load, trigger change to load TTD
                // This will call the shift-change handler in scripts.php that loads TTD
                setTimeout(function() {
                    if (shiftSelect.value) {
                        shiftSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }, 100);
            }
            
            // Initial check
            updateShiftOptions();
            updateTercapaiField();
        });
    </script>
</div>
<div class="modal-footer border-top-0 pt-0 pb-4 px-4">
    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
    <button type="submit" form="formEditLaporan" class="btn btn-primary px-5 shadow-sm">Update Laporan</button>
</div>
