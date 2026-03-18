<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Mengaktifkan scroll horizontal seperti Excel pada pembungkus utama */
    #wrapper #content-wrapper {
        overflow-x: auto !important;
    }

    /* Pembungkus tabel untuk memastikan scroll berjalan lancar di mobile/desktop */
    .home-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        margin-bottom: 1.5rem;
    }

    .home-table-wrap table {
        min-width: 1800px;
        /* Lebar minimum agar scroll horizontal muncul */
        margin-bottom: 0;
        border-collapse: collapse;
        background-color: white;
    }

    .home-table-wrap th,
    .home-table-wrap td {
        white-space: nowrap;
        /* Mencegah teks turun ke bawah (wrap) */
        padding: 10px 12px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    /* Styling Header Seksi mirip halaman Admin */
    .home-table-wrap .section-header1 {
        background: #4e73df !important;
        /* Warna biru primary */
        color: white !important;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .section-header2 {
        background: rgb(88, 255, 97) !important;
    }

    .section-header3 {
        background: rgb(238, 255, 80) !important;
    }

    .home-table-wrap thead .table-light th {
        background-color: #f8f9fc;
        font-weight: 700;
        color: #4e73df;
    }

    /* Warna baris selang-seling */
    .home-table-wrap tr:nth-child(even) {
        background-color: #f8f9fc;
    }

    .home-table-wrap tr:hover {
        background-color: #f1f4ff;
    }

    .mesin-link {
        text-decoration: none;
        font-weight: 500;
        color: #4e73df;
        padding: 4px 10px;
        border-radius: 20px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .mesin-link:hover {
        text-decoration: none;
        color: #224abe;
        background-color: #eef3ff;
    }

    .mesin-link.active {
        color: #270000;
        background-color: #4e73df;
        font-weight: 700;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <?php $activeMesin = $active_mesin ?? null; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?= base_url('home') ?>" class="mesin-link <?= empty($activeMesin) ? 'active' : '' ?>">Semua</a>
            <a href="<?= base_url('mesin1') ?>" class="mesin-link <?= $activeMesin === 'mesin1' ? 'active' : '' ?>">Mesin1</a>
            <a href="<?= base_url('mesin2') ?>" class="mesin-link <?= $activeMesin === 'mesin2' ? 'active' : '' ?>">Mesin2</a>
        </div>
        <div class="card-body">
            <div class="home-table-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="9" class="section-header1 text-center">Departemen PPIC</th>
                            <th colspan="8" class="section-header2 text-center">Departemen Produksi</th>
                            <th colspan="6" class="section-header3 text-center">Departemen Gudang</th>
                        </tr>
                        <tr class="table-light">
                            <th>No SPK</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th>Target</th>
                            <th>Jam</th>
                            <th>Revisi</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>No SPK</th>
                            <th>Hasil Produksi</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th>Tanggal</th>
                            <th>No SPK</th>
                            <th>Polycarbonate</th>
                            <th>Sisa PO</th>
                            <th>Hold</th>
                            <th>Gumpalan</th>
                            <th>tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ppic) || !empty($produksi) || !empty($gudang)): ?>
                            <?php
                            // Mengambil jumlah baris terbanyak untuk iterasi gabungan
                            $rowCount = max(count($ppic ?? []), count($produksi ?? []), count($gudang ?? []));
                            for ($i = 0; $i < $rowCount; $i++):
                                $p = $ppic[$i] ?? [];
                                $prod = $produksi[$i] ?? [];
                                $g = $gudang[$i] ?? [];
                            ?>
                                <tr>
                                    <td><?= esc($p['no_spk'] ?? '-') ?></td>
                                    <td><?= esc($p['nama_mesin'] ?? '-') ?></td>
                                    <td><?= esc($p['nama_produk'] ?? '-') ?></td>
                                    <td><?= esc($p['shif'] ?? $p['shift'] ?? '-') ?></td>
                                    <td><?= esc($p['operator'] ?? '-') ?></td>
                                    <td><?= esc($p['targett'] ?? '-') ?></td>
                                    <td><?= esc($p['jam'] ?? '-') ?></td>
                                    <?php $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? '')); ?>
                                    <td class="<?= $revisiText !== '' ? 'text-danger' : '' ?>"><?= esc($revisiText !== '' ? $revisiText : '-') ?></td>
                                    <td><?= !empty($p['tanggal']) ? date('d M Y', strtotime($p['tanggal'])) : '-' ?></td>

                                    <td><?= esc($prod['jam'] ?? '-') ?></td>
                                    <td><?= esc($prod['no_spk'] ?? '-') ?></td>
                                    <td><?= esc($prod['hasil_produksi'] ?? '-') ?></td>
                                    <td><?= esc($prod['nama_mesin'] ?? '-') ?></td>
                                    <td><?= esc($prod['nama_produk'] ?? '-') ?></td>
                                    <td><?= esc($prod['shif'] ?? $prod['shift'] ?? '-') ?></td>
                                    <td><?= esc($prod['operator'] ?? '-') ?></td>
                                    <td><?= !empty($prod['tanggal']) ? date('d M Y', strtotime($prod['tanggal'])) : '-' ?></td>

                                    <td><?= esc($g['no_spk'] ?? '-') ?></td>
                                    <td><?= esc($g['polycarbonate'] ?? '-') ?></td>
                                    <td><?= esc($g['sisa_po'] ?? '-') ?></td>
                                    <td><?= esc($g['hold'] ?? '-') ?></td>
                                    <td><?= esc($g['gumpalan'] ?? '-') ?></td>
                                    <td><?= !empty($g['tanggal']) ? date('d M Y', strtotime($g['tanggal'])) : '-' ?></td>
                                </tr>
                            <?php endfor; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="22" class="text-center text-muted py-4">
                                    <?php
                                    $message = 'Belum ada data dari departemen terkait.';
                                    if (!empty($activeMesin)) {
                                        $mesinLabel = ucfirst(str_replace('mesin', 'Mesin ', $activeMesin));
                                        $message = 'Belum ada data untuk ' . $mesinLabel . '.';
                                    }
                                    ?>
                                    <?= esc($message) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->endSection() ?>
