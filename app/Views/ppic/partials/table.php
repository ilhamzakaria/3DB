<div class="ppic-table-wrap">
    <table class="table" id="dataTable">
        <thead>
            <tr>
                <th>No. SPK</th>
                <th>Tanggal</th>
                <th>Mesin</th>
                <th>Nama Produk</th>
                <th>Grade / Warna</th>
                <th>Operator</th>
                <th class="text-center">Shift / Jam</th>
                <th class="text-right">Target</th>
                <th class="text-right">Hasil</th>
                <th>Revisi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produksi)): ?>
                <tr>
                    <td colspan="11" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                        Tidak ada data PPIC yang ditemukan.
                    </td>
                </tr>
            <?php else:
                $currentDate = '';
                $canWrite = in_array(session('role'), ['admin', 'manager', 'ppic']);
                foreach ($produksi as $p):
                    if ($currentDate !== $p['tanggal']):
                        $currentDate = $p['tanggal'];
            ?>
                <tr class="table-secondary">
                    <td colspan="11" class="py-2 px-3">
                        <span class="date-header">
                            <i class="far fa-calendar-alt text-muted"></i>
                            <strong><?= date('d F Y', strtotime($currentDate)) ?></strong>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td>
                    <span class="badge badge-modern badge-spk"><?= esc($p['no_spk'] ?? '-') ?></span>
                </td>
                <td class="text-muted small"><?= esc($p['tanggal'] ?? '-') ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                             style="width:28px;height:28px;flex-shrink:0;">
                            <i class="fas fa-cog text-muted" style="font-size:.7rem;"></i>
                        </div>
                        <div>
                            <div class="small font-weight-bold"><?= esc($p['nama_mesin'] ?? '-') ?></div>
                            <?php if (!empty($p['nomor_mesin'])): ?>
                                <div class="text-muted" style="font-size:.72rem;">M#<?= esc($p['nomor_mesin']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><?= esc($p['nama_produk'] ?? '-') ?></td>
                <td>
                    <div class="small"><?= esc($p['grade'] ?? '-') ?></div>
                    <?php if (!empty($p['warna'])): ?>
                        <div class="text-muted" style="font-size:.75rem;"><?= esc($p['warna']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-1">
                        <i class="far fa-user text-muted small"></i>
                        <span class="small"><?= esc($p['operator'] ?? '-') ?></span>
                    </div>
                </td>
                <td class="text-center">
                    <?php $shif = $p['shif'] ?? '-'; ?>
                    <span class="badge badge-modern"
                          style="background:rgba(67,97,238,0.08);color:#4361ee;">
                        Shift <?= esc($shif) ?>
                    </span>
                    <?php if (!empty($p['jam'])): ?>
                        <div class="text-muted mt-1" style="font-size:.72rem;"><?= esc($p['jam']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-right font-weight-bold"><?= esc($p['targett'] ?? '-') ?></td>
                <td class="text-right">
                    <?php $hasil = (int) ($p['hasil_produksi'] ?? 0); ?>
                    <span class="<?= $hasil > 0 ? 'text-success font-weight-bold' : 'text-muted' ?>">
                        <?= $hasil > 0 ? number_format($hasil) : '-' ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($p['revisi_display'])): ?>
                        <div class="revisi-text">
                            <?php foreach (explode(' | ', $p['revisi_display']) as $r): ?>
                                <span class="revisi-item"><?= esc($r) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif (!empty($p['revisi'])): ?>
                        <span class="revisi-item"><?= esc($p['revisi']) ?></span>
                    <?php else: ?>
                        <span class="text-muted small">-</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <?php if ($canWrite): ?>
                            <button class="btn btn-sm btn-outline-primary btn-action"
                                    onclick="openEditModal(<?= (int)($p['id'] ?? 0) ?>)"
                                    title="Edit">
                                <i class="fas fa-edit small"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action"
                                    onclick="confirmDelete(<?= (int)($p['id'] ?? 0) ?>, '<?= esc($p['no_spk'] ?? '', 'js') ?>')"
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
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            Menampilkan <?= count($produksi ?? []) ?> data
        </div>
        <?php if (!empty($pager)): ?>
            <?= $pager->links('ppic', 'bootstrap_pagination') ?>
        <?php endif; ?>
    </div>
</div>
