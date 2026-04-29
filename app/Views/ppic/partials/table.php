<div class="table-responsive ppic-table-wrap">
    <table class="table table-hover border-0" id="dataTable" cellspacing="0">
        <thead class="text-center">
            <tr>
                <th>Jam</th>
                <th>No. SPK</th>
                <th>Nama Mesin</th>
                <th>Nama Produk</th>
                <th>Grade</th>
                <th>Warna</th>
                <th>No. Mesin</th>
                <th>Operator</th>
                <th>Target</th>
                <th>Revisi</th>
                <th width="140px">Aksi</th>
            </tr>
        </thead>

        <tbody class="text-center">
            <?php
            $shiftRanges = [
                '1' => '07-14',
                '2' => '14-22',
                '3' => '22-06',
            ];

            $currentDate = '';
            $currentShift = '';

            foreach ($produksi as $p):
            ?>
                <?php if ($currentDate != $p['tanggal']): ?>
                    <tr class="table-secondary">
                        <td colspan="11">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="date-header">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <strong class="text-uppercase small font-weight-bold">
                                        <?php
                                        if (!empty($p['tanggal'])) {
                                            $date = new DateTime($p['tanggal']);
                                            $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                                            echo esc($formatter->format($date));
                                        }
                                        ?>
                                    </strong>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $currentDate = $p['tanggal'];
                    $currentShift = '';
                    ?>
                <?php endif; ?>

                <?php if ($currentShift != $p['shif']): ?>
                    <tr class="table-primary">
                        <td colspan="11">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-clock text-primary"></i>
                                <strong class="small fw-bold text-primary">
                                    SHIFT <?= $p['shif']; ?>
                                    <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                </strong>
                            </div>
                        </td>
                    </tr>
                    <?php $currentShift = $p['shif']; ?>
                <?php endif; ?>

                <tr>
                    <td class="fw-bold text-primary"><?= esc($p['jam']) ?></td>
                    <td><span class="badge badge-modern badge-spk"><?= esc($p['no_spk']) ?></span></td>
                    <td><?= esc($p['nama_mesin']) ?></td>
                    <td><?= esc($p['nama_produk']) ?></td>
                    <td><?= esc($p['grade']) ?></td>
                    <td><?= esc($p['warna']) ?></td>
                    <td><?= esc($p['nomor_mesin']) ?></td>
                    <td><?= esc($p['operator']) ?></td>
                    <td class="fw-bold"><?= esc($p['targett']) ?></td>

                    <?php
                    $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? ''));
                    $isRevisi = $revisiText !== '';
                    ?>
                    <td class="revisi-text text-center">
                        <?php if ($isRevisi): ?>
                            <?php
                            // Split revisi text if it contains multiple items separated by |
                            $revisiItems = explode(' | ', $revisiText);
                            foreach ($revisiItems as $item):
                            ?>
                                <span class="revisi-item"><?= esc($item) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if (session()->get('role') == 'ppic'): ?>
                            <div class="d-flex gap-2 justify-content-center">
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm btnEdit d-flex align-items-center gap-1"
                                    data-id="<?= $p['id'] ?>"
                                    data-jam="<?= $p['jam'] ?>"
                                    data-no_spk="<?= $p['no_spk'] ?>"
                                    data-nama_mesin="<?= $p['nama_mesin'] ?>"
                                    data-nama_produk="<?= $p['nama_produk'] ?>"
                                    data-grade="<?= $p['grade'] ?>"
                                    data-warna="<?= $p['warna'] ?>"
                                    data-nomor_mesin="<?= $p['nomor_mesin'] ?>"
                                    data-shif="<?= $p['shif'] ?>"
                                    data-operator="<?= $p['operator'] ?>"
                                    data-targett="<?= $p['targett'] ?>"
                                    data-revisi="<?= $p['revisi'] ?>"
                                    data-tanggal="<?= $p['tanggal'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    style="border-radius: 8px; font-weight: 600; font-size: 11px; padding: 5px 10px;">
                                    <i class="fas fa-edit" style="font-size: 10px;"></i> Edit
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm d-flex align-items-center gap-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDelete"
                                    data-id="<?= $p['id'] ?>"
                                    data-spk="<?= esc($p['no_spk']) ?>"
                                    style="border-radius: 8px; font-weight: 600; font-size: 11px; padding: 5px 10px;">
                                    <i class="fas fa-trash" style="font-size: 10px;"></i> Hapus
                                </button>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
    <div class="small text-muted">
        Menampilkan <?= count($produksi) ?> data
    </div>
    <div class="pagination-modern">
        <?= $pager->links('ppic', 'bootstrap_pagination') ?>
    </div>
</div>
