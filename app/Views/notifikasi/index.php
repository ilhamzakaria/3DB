<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">🔔 Notifikasi</h4>
    <small class="text-muted">Role: <?= esc(ucfirst((string) session('role'))) ?></small>
</div>

<?php if (empty($notifikasi)): ?>
    <div class="alert alert-secondary mb-0">
        Belum ada notifikasi.
    </div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($notifikasi as $n): ?>
            <?php
            $isUnread = (($n['status'] ?? 'unread') === 'unread');
            $href = base_url('notifikasi/read/' . $n['id']);
            ?>
            <a href="<?= esc($href) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="font-weight-bold">
                        <?= $isUnread ? '<span class="badge badge-danger mr-2">baru</span>' : '<span class="badge badge-secondary mr-2">dibaca</span>' ?>
                        <?= esc((string) ($n['pesan'] ?? '')) ?>
                    </div>
                    <?php if (!empty($n['created_at'])): ?>
                        <?php
                        $date = new DateTime($n['created_at']);
                        $formatter = new IntlDateFormatter(
                            'id_ID',
                            IntlDateFormatter::LONG, // Format Tanggal: 15 Maret 2026
                            IntlDateFormatter::MEDIUM // Format Waktu: 18:07:04
                        );
                        $tanggal_full = $formatter->format($date);
                        ?>
                        <small class="text-muted"><?= esc($tanggal_full) ?></small>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>