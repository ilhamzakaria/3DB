<?php
// update_views.php  — replaces old tbody + modals in 8 mesin views

function tdRows(string $produkField, string $produkLabel): string {
    return <<<PHP
                            <tr>
                                <td><?= esc(\$formatDate(\$item['tanggal'])) ?></td>
                                <td><?= esc(\$item['{$produkField}'] ?? '-') ?></td>
                                <td><button type="button" class="btn btn-sm btn-link btn-spk-produksi-autofill p-0" data-spk="<?= esc(\$item['no_spk'] ?? '') ?>" style="text-decoration:none;color:#4361ee;"><?= esc(\$item['no_spk'] ?? '-') ?></button></td>
                                <td><?= esc(\$item['warna'] ?? '-') ?></td>
                                <td><?= esc(\$item['nomor_mesin'] ?? '-') ?></td>
                                <td><?= esc(\$item['jam'] ?? '-') ?></td>
                                <td><?= esc(\$item['waktu_akhir_target'] ?? '-') ?></td>
                                <td><?= esc(\$item['target_shift1'] ?? '-') ?></td>
                                <td><?= esc(\$item['target_shift2'] ?? '-') ?></td>
                                <td><?= esc(\$item['target_shift3'] ?? '-') ?></td>
                                <td><?= esc(\$item['waktu_awal_realisasi'] ?? '-') ?></td>
                                <td><?= esc(\$item['waktu_akhir_realisasi'] ?? '-') ?></td>
                                <td><?= esc(\$item['realisasi_shift1'] ?? '-') ?></td>
                                <td><?= esc(\$item['realisasi_shift2'] ?? '-') ?></td>
                                <td><?= esc(\$item['realisasi_shift3'] ?? '-') ?></td>
                                <td><?= esc(\$item['berat'] ?? '-') ?></td>
                                <td style="max-width:120px;white-space:normal;"><?= esc(\$item['revisi'] ?? '-') ?></td>
                                <td><?= esc(\$item['total_spk'] ?? '-') ?></td>
                                <td><?= esc(\$item['keterangan'] ?? '-') ?></td>
PHP;
}
