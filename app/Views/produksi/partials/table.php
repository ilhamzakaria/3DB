<div class="plant-table-wrap">
    <?php if (!empty($filter_mesin)): ?>
        <?php $mesinLabel = $mesin_labels[$filter_mesin] ?? $filter_mesin; ?>
        <div class="px-3 pt-3 pb-2 d-flex align-items-center gap-2" style="border-bottom: 2px solid #4e73df20;">
            <span style="width:8px;height:8px;border-radius:50%;background:#4e73df;display:inline-block;flex-shrink:0;"></span>
            <span style="font-size:.8rem;font-weight:700;color:#4e73df;text-transform:uppercase;letter-spacing:.5px;">
                <?= esc($mesinLabel) ?>
            </span>
            <span class="text-muted" style="font-size:.8rem;">— laporan produksi</span>
        </div>
    <?php endif; ?>
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
                        <p class="mb-0">
                            <?php if (!empty($filter_mesin)): ?>
                                Belum ada laporan untuk mesin <strong><?= esc($mesin_labels[$filter_mesin] ?? $filter_mesin) ?></strong>.
                            <?php else: ?>
                                Tidak ada data produksi yang ditemukan.
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            <?php else: 
                $currentDate = '';
                $currentShift = '';
                // Roles that may edit / delete records
                $canDelete    = in_array(session('role'), ['manager', 'produksi']);
                $_tableRole   = session('role');
                $_plantMesin  = \App\Controllers\Produksi::getPlantMesin($_tableRole);
                // canEdit dihitung per baris (plant check), pre-set true untuk non-plant roles
                $canEditBase  = in_array($_tableRole, ['admin', 'manager', 'leader', 'leader_plant1', 'leader_plant2', 'produksi']);
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

            <tr<?= (!empty($p['selesai_spk'])) ? ' class="spk-selesai-row"' : '' ?>>
                <td data-label="No. SPK">
                    <button type="button" class="badge badge-modern badge-spk btn-spk-autofill"
                        style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important;
                               border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important;
                               padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem; cursor: pointer;"
                        data-spk="<?= esc($p['nomor_spk']) ?>">
                        <?= esc($p['nomor_spk']) ?>
                    </button>
                    <?php if (!empty($p['selesai_spk'])): ?>
                        <span class="badge badge-spk-selesai d-block mt-1" title="SPK ini telah ditandai selesai oleh PPIC">
                            <i class="fas fa-check-circle me-1"></i>Selesai
                        </span>
                    <?php else: ?>
                        <span class="badge badge-spk-ongoing d-block mt-1">
                            <i class="fas fa-spinner me-1"></i>Berjalan
                        </span>
                    <?php endif; ?>
                </td>
                <td data-label="Mesin">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2"
                             style="width: 30px; height: 30px;">
                            <i class="fas fa-cog text-muted small"></i>
                        </div>
                        <div>
                            <div class="font-weight-600"><?= esc($p['nama_mesin']) ?></div>
                            <div class="text-xs text-muted">M#<?= esc($p['nomor_mesin']) ?></div>
                        </div>
                    </div>
                </td>
                <td data-label="Nama Produk"><?= esc($p['nama_produk']) ?></td>
                <td data-label="Grup" class="text-center">
                    <span class="badge badge-pill badge-light text-muted small px-3"><?= esc($p['grup'] ?? '-') ?></span>
                </td>
                <td data-label="Operator">
                    <div class="d-flex align-items-center">
                        <i class="far fa-user me-2 text-muted small"></i>
                        <span class="small font-weight-600"><?= esc($p['operator']) ?></span>
                    </div>
                </td>
                <td data-label="Hasil Bagus" class="text-right font-weight-bold text-success"><?= number_format($p['grand_total_bagus']) ?></td>
                <td data-label="Reject" class="text-right text-danger font-weight-bold"><?= number_format($p['grand_total_reject']) ?></td>
                <td data-label="Aksi" class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn-action btn-detail btn_view_detail"
                                data-id="<?= $p['id'] ?>"
                                data-bs-toggle="modal" data-bs-target="#modalDetail"
                                title="Detail">
                            <i class="fas fa-eye small"></i>
                        </button>
                        <?php
                        // Per-row: leader_plant hanya boleh edit baris mesin plant-nya
                        if ($canEditBase && $_plantMesin !== null) {
                            $rowSlug = array_search($p['nama_mesin'] ?? '', \App\Controllers\Produksi::mesinLabels());
                            $canEdit = $rowSlug !== false && in_array($rowSlug, $_plantMesin, true);
                        } else {
                            $canEdit = $canEditBase;
                        }
                        ?>
                        <?php if ($canEdit): ?>
                            <button class="btn-action btn-edit btn_edit_laporan"
                                    data-id="<?= $p['id'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                    title="Edit">
                                <i class="fas fa-edit small"></i>
                            </button>
                        <?php endif; ?>
                        <?php if ($canDelete): ?>
                            <button class="btn-action btn-delete btn_confirm_delete"
                                    data-id="<?= $p['id'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalDeleteConfirm"
                                    title="Hapus">
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
