<div class="plant-table-wrap">
    <table class="table table-main">
        <thead>
            <tr>
                <th>No. SPK</th>
                <th>Mesin</th>
                <th>Nama Produk</th>
                <th class="text-center">Grup</th>
                <th>Operator</th>
                <th class="text-right">Hasil Bagus</th>
                <th class="text-right">Reject</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produksi)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                        <p class="mb-0">Tidak ada data produksi yang ditemukan.</p>
                    </td>
                </tr>
            <?php else: 
                $currentDate = '';
                $currentShift = '';
                foreach ($produksi as $p): 
                    if ($currentDate != $p['tanggal']): 
                        $currentDate = $p['tanggal'];
                        $currentShift = ''; 
                        $formattedDate = date('d M Y', strtotime($currentDate));
            ?>
                <tr class="date-divider text-center">
                    <td colspan="8"><i class="far fa-calendar-alt me-2"></i> <?= $formattedDate ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($currentShift != $p['shift']): 
                $currentShift = $p['shift'];
            ?>
                <tr class="shift-divider">
                    <td colspan="8" class="ps-4">
                        <span class="badge badge-modern badge-shift-<?= $p['shift'] ?>">
                            <i class="fas fa-clock me-1"></i> SHIFT <?= $p['shift'] ?>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td>
                    <span class="badge badge-modern badge-spk" style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important; padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem;">
                        <?= esc($p['nomor_spk']) ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                            <i class="fas fa-cog text-muted small"></i>
                        </div>
                        <div>
                            <div class="font-weight-600"><?= esc($p['nama_mesin']) ?></div>
                            <div class="text-xs text-muted">M#<?= esc($p['nomor_mesin']) ?></div>
                        </div>
                    </div>
                </td>
                <td><?= esc($p['nama_produksi']) ?></td>
                <td class="text-center">
                    <span class="badge badge-pill badge-light text-muted small px-3"><?= esc($p['grup'] ?? '-') ?></span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="far fa-user me-2 text-muted small"></i>
                        <span class="small font-weight-600"><?= esc($p['operator']) ?></span>
                    </div>
                </td>
                <td class="text-right font-weight-bold text-success"><?= number_format($p['grand_total_bagus']) ?></td>
                <td class="text-right text-danger font-weight-bold"><?= number_format($p['grand_total_reject']) ?></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn-action btn-detail btn_view_detail" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalDetail" title="Detail">
                            <i class="fas fa-eye small"></i>
                        </button>
                        <?php if (session('role') == 'produksi') : ?>
                            <button class="btn-action btn-edit btn_edit_laporan" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit">
                                <i class="fas fa-edit small"></i>
                            </button>
                            <button class="btn-action btn-delete btn_confirm_delete" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalDeleteConfirm" title="Hapus">
                                <i class="fas fa-trash-alt small"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Menampilkan <?= count($produksi) ?> data
        </div>
        <?= $pager->links('produksi', 'bootstrap_pagination') ?>
    </div>
</div>
