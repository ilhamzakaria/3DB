<!-- Add Data Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Laporan Produksi
                </h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
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
                <form id="formAddLaporan" action="<?= base_url('produksi/simpan') ?>" method="POST" enctype="multipart/form-data" onkeydown="return event.key !== 'Enter' || event.target.tagName === 'TEXTAREA';">
                    <?= csrf_field() ?>
                    <input type="hidden" name="submit_type" id="submit_type" value="save">
                    <?= view('produksi/partials/form_fields', [
                        'spk_list'           => $spk_list,
                        'context_mesin_slug' => $context_mesin_slug ?? '',
                        'reject_types'       => $reject_types ?? [],
                    ]) ?>
                </form>

                <script>
                    // Regenerate CSRF token sebelum submit untuk menghindari token expired
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('formAddLaporan');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                // Fetch fresh CSRF token
                                const csrfInput = form.querySelector('input[name="<?= csrf_token() ?>"]');
                                if (csrfInput) {
                                    console.log('CSRF token before submit:', csrfInput.value.substring(0, 20) + '...');
                                }
                            });
                        }
                    });
                    
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
                    
                    // Autofill semua field dari SPK yang dipilih
                    function triggerSpkAutofill(spkSelect) {
                        if (!spkSelect || !spkSelect.value) return;
                        
                        const modal = document.getElementById('addDataModal');
                        const container = modal ? (modal.querySelector('#formAddLaporan') || modal.querySelector('.modal-body')) : document;
                        
                        // Gunakan fillFromSpkOption dari scripts.php jika tersedia
                        if (typeof fillFromSpkOption === 'function') {
                            fillFromSpkOption(spkSelect, container);
                        } else if (typeof fillFromSpkOptionAlt === 'function') {
                            fillFromSpkOptionAlt(spkSelect.value, container);
                        }
                    }

                    // Initialize on modal open
                    document.addEventListener('DOMContentLoaded', function() {
                        const spkSelect = document.querySelector('.nomor_spk');
                        const shiftSelect = document.querySelector('#shift_select');
                        
                        // Native change event (untuk non-Select2)
                        if (spkSelect) spkSelect.addEventListener('change', function() {
                            updateShiftOptions();
                            updateTercapaiField();
                            triggerSpkAutofill(this);
                        });

                        // Select2 event (untuk hosting yang pakai Select2)
                        if (spkSelect && typeof jQuery !== 'undefined') {
                            jQuery(spkSelect).on('select2:select', function(e) {
                                updateShiftOptions();
                                updateTercapaiField();
                                triggerSpkAutofill(spkSelect);
                            });
                        }
                        
                        if (shiftSelect) {
                            shiftSelect.addEventListener('change', updateTercapaiField);
                            
                            // If shift already has a value (edit mode), trigger change event to load TTD
                            const modal = document.getElementById('addDataModal');
                            if (modal) {
                                modal.addEventListener('shown.bs.modal', function() {
                                    if (shiftSelect.value) {
                                        // Trigger change event to load TTD via scripts.php handler
                                        shiftSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                });
                            }
                        }
                        
                        // Initial check
                        updateShiftOptions();
                        updateTercapaiField();
                    });
                </script>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddLaporan" class="btn btn-primary px-5 shadow-sm" onclick="document.getElementById('submit_type').value='save'">Simpan Laporan</button>
            </div>
        </div>
    </div>
</div>
