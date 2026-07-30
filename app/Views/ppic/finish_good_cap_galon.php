<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$formatDate = static fn($v): string => ($t = strtotime((string)$v)) ? date('d-m-Y', $t) : '-';
$baseUrl    = 'ppic/finish_good/cap_galon';
$typeLabel  = 'Cap Galon';
?>

<style>
.filter-sidebar{position:fixed;top:0;right:-360px;width:360px;height:100vh;background:#fff;z-index:1100;box-shadow:-5px 0 15px rgba(0,0,0,.1);transition:all .3s ease;display:flex;flex-direction:column}
.filter-sidebar.active{right:0}
.filter-overlay{position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.5);z-index:1090;display:none;backdrop-filter:blur(2px)}
.filter-overlay.active{display:block}
.filter-header{padding:1.25rem;border-bottom:1px solid #e3e6f0;display:flex;justify-content:space-between;align-items:center;background:#f8f9fc}
.filter-body{padding:1.5rem;flex-grow:1;overflow-y:auto}
.filter-footer{padding:1.25rem;border-top:1px solid #e3e6f0;background:#f8f9fc;display:flex;gap:10px}
.filter-chip{display:inline-flex;align-items:center;background:#eaecf4;padding:.25rem .75rem;border-radius:20px;font-size:.85rem;margin-right:.5rem;margin-bottom:.5rem}
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('ppic/partials/machine_links', ['current_machine' => $current_machine]) ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <a href="<?= base_url('ppic') ?>" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-primary btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleFilter(true)">
                    <i class="fas fa-filter"></i> Filter
                    <?php if (!empty($start_date) || !empty($end_date)): ?>
                        <span class="badge badge-primary ml-1">!</span>
                    <?php endif; ?>
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#trashModalFg">
                    <i class="fas fa-trash-restore"></i> Sampah
                </button>
            </div>
            <form method="get" action="<?= base_url($baseUrl) ?>" class="d-flex align-items-center mb-0 mt-2 mt-md-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" style="min-width:200px" placeholder="Cari data..." value="<?= esc($q ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <?php if (!empty($start_date) || !empty($end_date)): ?>
            <div class="mt-2">
                <?php if (!empty($start_date)): ?><span class="filter-chip">Mulai: <?= esc($start_date) ?></span><?php endif; ?>
                <?php if (!empty($end_date)): ?><span class="filter-chip">Selesai: <?= esc($end_date) ?></span><?php endif; ?>
                <a href="<?= base_url($baseUrl) ?>" class="text-danger small ml-2"><i class="fas fa-times"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>

        <!-- Total Summary Cards -->
        <div class="d-flex gap-2 mb-3">
            <div class="bg-white border rounded px-3 py-2 d-flex align-items-center flex-fill" style="border-left:4px solid #f6c23e!important;gap:12px">
                <i class="fas fa-boxes text-warning" style="font-size:1rem;opacity:.7"></i>
                <div>
                    <div style="font-size:.65rem;font-weight:700;color:#f6c23e;text-transform:uppercase;letter-spacing:.05em">Total Jumlah (Filter)</div>
                    <div style="font-size:.9rem;font-weight:700;line-height:1"><?= number_format($totals['jumlah'] ?? 0) ?> pcs</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Warna</th>
                        <th>Berat (gr)</th>
                        <th>Jumlah (pcs)</th>
                        <th>Packing</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_list)): ?>
                        <?php foreach ($data_list as $item): ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'])) ?></td>
                                <td><?= esc($item['nomor'] ?? '-') ?></td>
                                <td><?= esc($item['nama_produk'] ?? '-') ?></td>
                                <td><?= esc($item['warna'] ?? '-') ?></td>
                                <td><?= esc($item['berat'] ?? '-') ?></td>
                                <td><?= esc($item['jumlah'] ?? '-') ?></td>
                                <td><?= esc($item['packing'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="setEditModal(this)" data-bs-toggle="modal" data-bs-target="#editDataModal"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-tanggal="<?= esc($item['tanggal'] ?? '') ?>"
                                            data-nomor="<?= esc($item['nomor'] ?? '') ?>"
                                            data-nama_produk="<?= esc($item['nama_produk'] ?? '') ?>"
                                            data-warna="<?= esc($item['warna'] ?? '') ?>"
                                            data-berat="<?= esc($item['berat'] ?? '') ?>"
                                            data-jumlah="<?= esc($item['jumlah'] ?? '') ?>"
                                            data-packing="<?= esc($item['packing'] ?? '') ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-nama="<?= esc($item['nama_produk'] ?? '') ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="thead-light font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">TOTAL</td>
                        <td class="text-warning"><?= number_format($totals['jumlah'] ?? 0) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <div class="text-muted small">
                <?php
                $pg    = $pager->getCurrentPage('fg_cap_galon');
                $perPg = 15;
                $total = count($data_list ?? []);
                $start = (($pg - 1) * $perPg) + 1;
                $end   = $start + $total - 1;
                ?>
                Menampilkan <strong><?= $start ?>–<?= $end ?></strong> entri
            </div>
            <?= $pager->links('fg_cap_galon', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Finish Good <?= $typeLabel ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('ppic/finish_good/cap_galon/tambah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal <span class="text-danger">*</span></label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" placeholder="Nomor..."></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Produk</label><input type="text" class="form-control" name="nama_produk" placeholder="Nama produk..."></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" placeholder="Warna..."></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" placeholder="Berat..."></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah (pcs)</label><input type="text" class="form-control" name="jumlah" placeholder="Jumlah..."></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" placeholder="Packing..."></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2 text-warning"></i>Edit Finish Good <?= $typeLabel ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" id="edit_nomor"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Produk</label><input type="text" class="form-control" name="nama_produk" id="edit_nama_produk"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="edit_warna"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" id="edit_berat"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah (pcs)</label><input type="text" class="form-control" name="jumlah" id="edit_jumlah"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" id="edit_packing"></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formDelete" method="post" action="<?= base_url('ppic/finish_good/cap_galon/delete/0') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                    <p id="delete_message" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Sidebar -->
<div class="filter-overlay" onclick="toggleFilter(false)"></div>
<div class="filter-sidebar">
    <div class="filter-header">
        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter</h5>
        <button type="button" class="close" onclick="toggleFilter(false)">&times;</button>
    </div>
    <form method="get" action="<?= base_url($baseUrl) ?>">
        <div class="filter-body">
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <div class="form-group"><label class="small font-weight-bold">Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="<?= esc($start_date ?? '') ?>"></div>
            <div class="form-group"><label class="small font-weight-bold">Tanggal Selesai</label><input type="date" name="end_date" class="form-control" value="<?= esc($end_date ?? '') ?>"></div>
        </div>
        <div class="filter-footer">
            <a href="<?= base_url($baseUrl) ?>" class="btn btn-secondary flex-fill">Reset</a>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </form>
</div>

<script>
function toggleFilter(show) {
    document.querySelector('.filter-sidebar').classList.toggle('active', show);
    document.querySelector('.filter-overlay').classList.toggle('active', show);
}

function setEditModal(btn) {
    const d = btn.dataset;
    document.getElementById('edit_id').value          = d.id;
    document.getElementById('edit_tanggal').value     = d.tanggal      || '';
    document.getElementById('edit_nomor').value       = d.nomor        || '';
    document.getElementById('edit_nama_produk').value = d.nama_produk  || '';
    document.getElementById('edit_warna').value       = d.warna        || '';
    document.getElementById('edit_berat').value       = d.berat        || '';
    document.getElementById('edit_jumlah').value      = d.jumlah       || '';
    document.getElementById('edit_packing').value     = d.packing      || '';
    document.getElementById('formEdit').action = '<?= base_url('ppic/finish_good/cap_galon/update') ?>/' + d.id;
}

function setDeleteModal(btn) {
    const id   = btn.dataset.id;
    const nama = btn.dataset.nama || 'data ini';
    document.getElementById('delete_id').value                    = id;
    document.getElementById('delete_message').textContent         = 'Hapus "' + nama + '"?';
    document.getElementById('formDelete').action                  = '<?= base_url('ppic/finish_good/cap_galon/delete') ?>/' + id;
}

// Load Trash Data
function loadTrashCapGalon() {
    const tbody = document.querySelector('#trashTableCapGalon tbody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
    
    const url = '<?= base_url('ppic/finish_good/cap_galon/get-trash') ?>';
    console.log('Fetching trash from:', url);
    
    fetch(url)
        .then(r => {
            console.log('Response status:', r.status);
            if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
            return r.json();
        })
        .then(data => {
            console.log('Trash data:', data);
            tbody.innerHTML = '';
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Tempat sampah kosong.</td></tr>';
                return;
            }
            data.forEach(item => {
                const html = `<tr style="font-size: 13px;">
                    <td class="px-3 py-2">
                        <div class="font-weight-bold">${item.nama_produk || '-'}</div>
                        <div class="text-muted" style="font-size: 11px;">Warna: ${item.warna || '-'}</div>
                    </td>
                    <td class="py-2">${item.nomor || '-'}</td>
                    <td class="py-2 text-muted text-center">${item.deleted_at}</td>
                    <td class="text-end py-2 px-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="<?= base_url('ppic/finish_good/cap_galon/restore') ?>/${item.id}" class="btn btn-success btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Pulihkan">
                                <i class="fas fa-undo"></i>
                            </a>
                            <a href="<?= base_url('ppic/finish_good/cap_galon/delete-permanent') ?>/${item.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Hapus Permanen" onclick="return confirm('Hapus permanen? Tidak dapat dikembalikan!')">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', html);
            });
        })
        .catch(err => {
            console.error('Error loading trash:', err);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">Gagal memuat data sampah: ' + err.message + '</td></tr>';
        });
}

// Initialize modals
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded: Initializing delete modal handler');
    
    // Delete modal handler
    const deleteModal = document.getElementById('modalDelete');
    console.log('Delete modal element:', deleteModal);
    
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            console.log('Delete modal showing', event);
            const btn = event.relatedTarget;
            console.log('Related target button:', btn);
            
            if (!btn) {
                console.error('No related target button found');
                return;
            }
            
            const id = btn.getAttribute('data-id');
            const nama = btn.getAttribute('data-nama') || 'data ini';
            console.log('Delete ID:', id, 'Nama:', nama);
            
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_message').textContent = 'Hapus "' + nama + '"?';
            document.getElementById('formDelete').action = '<?= base_url('ppic/finish_good/cap_galon/delete') ?>/' + id;
            console.log('Form action set to:', document.getElementById('formDelete').action);
        });
        console.log('Delete modal event listener attached');
    }
    
    // Trash modal handler
    const trashModal = document.getElementById('trashModalFg');
    if (trashModal) {
        trashModal.addEventListener('show.bs.modal', loadTrashCapGalon);
        console.log('Trash modal event listener registered');
    }
});
</script>

<!-- Trash Modal -->
<div class="modal fade" id="trashModalFg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah - Finish Good <?= $typeLabel ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="trashTableCapGalon" style="min-width: auto;">
                        <thead class="bg-white text-xs text-muted text-uppercase">
                            <tr>
                                <th class="px-3 py-3" style="width: 35%;">Nama Produk</th>
                                <th class="py-3" style="width: 20%;">Nomor</th>
                                <th class="py-3 text-center" style="width: 25%;">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center py-3 text-muted">Klik tombol untuk memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
