<!-- Modal: Tambah Produk Baru -->
<div class="modal fade" id="addProdukModal" tabindex="-1" role="dialog" aria-labelledby="addProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProdukModalLabel">
                    <i class="fas fa-plus-circle mr-2 text-success"></i>Tambah Produk Baru - <?= $type_label ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddProduk" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor</label>
                                <input type="text" class="form-control" name="nomor" id="produk_nomor" placeholder="Nomor...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_produk" id="produk_nama" placeholder="Nama produk..." required>
                            </div>
                        </div>
                    </div>

                    <?php if ($type === 'galon'): ?>
                        <!-- Galon Specific Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Grade</label>
                                    <input type="text" class="form-control" name="grade" id="produk_grade" placeholder="Grade...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat (gr)</label>
                                    <input type="text" class="form-control" name="berat" id="produk_berat" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>WH ORO</label>
                                    <input type="text" class="form-control" name="wh_oro" id="produk_wh_oro" placeholder="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>WH BAM</label>
                                    <input type="text" class="form-control" name="wh_bam" id="produk_wh_bam" placeholder="0" value="0">
                                </div>
                            </div>
                        </div>

                    <?php elseif ($type === 'screw_cap'): ?>
                        <!-- Screw Cap Specific Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Warna</label>
                                    <input type="text" class="form-control" name="warna" id="produk_warna" placeholder="Warna...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat (gr)</label>
                                    <input type="text" class="form-control" name="berat" id="produk_berat" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah (pcs)</label>
                                    <input type="text" class="form-control" name="jumlah" id="produk_jumlah" placeholder="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" id="produk_keterangan" placeholder="Keterangan...">
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Cap Galon & Freeform Specific Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Warna</label>
                                    <input type="text" class="form-control" name="warna" id="produk_warna" placeholder="Warna...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat (gr)</label>
                                    <input type="text" class="form-control" name="berat" id="produk_berat" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah (pcs)</label>
                                    <input type="text" class="form-control" name="jumlah" id="produk_jumlah" placeholder="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Packing</label>
                                    <input type="text" class="form-control" name="packing" id="produk_packing" placeholder="Packing...">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Produk baru akan ditambahkan dan langsung muncul di dropdown pilihan produk.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle mr-2"></i>Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    const type = '<?= $type ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_token';

    // Handle form submit untuk tambah produk
    document.addEventListener('DOMContentLoaded', function() {
        const formAddProduk = document.getElementById('formAddProduk');
        const submitBtn = formAddProduk?.querySelector('button[type="submit"]');
        
        if (!formAddProduk) {
            console.error('[addProdukModal] Form tidak ditemukan');
            return;
        }

        formAddProduk.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Validasi
            if (!data.nama_produk || data.nama_produk.trim() === '') {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Nama produk tidak boleh kosong',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading
            const originalBtnText = submitBtn?.innerHTML;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            }

            console.log('[addProdukModal] Submitting:', { type, data });

            try {
                // Prepare headers
                const headers = new Headers();
                headers.append('Content-Type', 'application/json');
                headers.append('X-Requested-With', 'XMLHttpRequest');
                
                // Add CSRF token to headers if available
                if (csrfToken && csrfTokenName) {
                    headers.append(csrfTokenName, csrfToken);
                }

                console.log('[addProdukModal] Headers ready, sending request...');

                const response = await fetch(`/ppic/finish_good/${type}/add-produk`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });

                console.log('[addProdukModal] Response status:', response.status);

                const result = await response.json();
                console.log('[addProdukModal] Response body:', result);

                if (!response.ok || !result.success) {
                    // Error alert
                    Swal.fire({
                        title: 'Gagal!',
                        text: result.error || 'Gagal menambahkan produk',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Coba Lagi'
                    });
                    console.error('[addProdukModal] Error:', result);
                    
                    // Restore button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                    return;
                }

                // Success alert
                console.log('[addProdukModal] Success!');
                
                Swal.fire({
                    title: 'Berhasil! ✓',
                    text: 'Produk "' + result.nama_produk + '" telah ditambahkan',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    didClose: function() {
                        // Close modal
                        if (window.bootstrap && window.bootstrap.Modal) {
                            const modal = window.bootstrap.Modal.getInstance(document.getElementById('addProdukModal'));
                            if (modal) modal.hide();
                        } else if (window.$) {
                            window.$('#addProdukModal').modal('hide');
                        }
                        
                        // Reset form
                        formAddProduk.reset();
                        
                        // Reload page setelah beberapa ms
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    }
                });

            } catch (err) {
                console.error('[addProdukModal] Fetch error:', err);
                
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + err.message,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                
                // Restore button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    });
})();
</script>
