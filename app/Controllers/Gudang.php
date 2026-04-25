<?php

namespace App\Controllers;

use App\Models\MGudang;

class Gudang extends BaseController
{
    protected $MGudang;
    protected $MGudangKategori;
    protected $MGudangDetail;

    private const KOMPONEN_FIELDS = [
        'bahan_baku',
        'box',
        'karung',
        'plastik',
        'masterbatch',
        'galon_reject_supplier',
        'galon_reject_produksi',
        'gilingan_reject_supplier',
        'gilingan_reject_produksi',
        'stiker',
        'reject_preform',
        'bekuat_pet',
        'bekuan_capgalon',
        'gilingan_screwcap',
    ];

    private const DASHBOARD_MODULES = [
        ['key' => 'bahan_baku', 'label' => 'Bahan Baku', 'group' => 'material', 'route' => 'gudang/bahan_baku', 'table' => 'bahan_baku', 'icon' => 'fas fa-boxes', 'accent' => 'success', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_bahan', 'produk', 'kode']],
        ['key' => 'packaging', 'label' => 'Packaging', 'group' => 'material', 'route' => 'gudang/packaging', 'table' => 'packaging', 'icon' => 'fas fa-archive', 'accent' => 'primary', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_packaging', 'produk', 'kode']],
        ['key' => 'masterbatch', 'label' => 'Masterbatch', 'group' => 'material', 'route' => 'gudang/masterbatch', 'table' => 'masterbatch', 'icon' => 'fas fa-palette', 'accent' => 'info', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_masterbatch', 'produk', 'kode']],
        ['key' => 'galon_reject', 'label' => 'Galon Reject', 'group' => 'material', 'route' => 'gudang/galon_reject', 'table' => 'galon_reject', 'icon' => 'fas fa-exclamation-triangle', 'accent' => 'danger', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_galon_reject', 'produk', 'kode']],
        ['key' => 'gilingan_galon', 'label' => 'Gilingan Galon', 'group' => 'material', 'route' => 'gudang/gilingan_galon', 'table' => 'gilingan_galon', 'icon' => 'fas fa-recycle', 'accent' => 'secondary', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_gilingan', 'produk', 'kode']],
        ['key' => 'stiker', 'label' => 'Stiker', 'group' => 'material', 'route' => 'gudang/stiker', 'table' => 'stiker_gudang', 'icon' => 'fas fa-sticky-note', 'accent' => 'warning', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_stiker', 'produk', 'kode']],
        ['key' => 'reject_produksi', 'label' => 'Reject Produksi', 'group' => 'material', 'route' => 'gudang/reject_produksi', 'table' => 'reject_produksi', 'icon' => 'fas fa-dumpster', 'accent' => 'dark', 'quantity_field' => 'jumlah', 'item_fields' => ['nama_limbah', 'produk', 'kode']],
        ['key' => 'mesin_becum', 'label' => 'Mesin Becum', 'group' => 'mesin', 'route' => 'gudang/mesin_becum', 'table' => 'mesin_becum', 'icon' => 'fas fa-industry', 'accent' => 'secondary', 'quantity_field' => 'stock', 'item_fields' => ['produk_galon', 'brand', 'grade']],
        ['key' => 'mesin_powerjet1', 'label' => 'Mesin Powerjet 1', 'group' => 'mesin', 'route' => 'gudang/mesin_powerjet1', 'table' => 'mesin_powerjet1', 'icon' => 'fas fa-cogs', 'accent' => 'primary', 'quantity_field' => 'stock', 'item_fields' => ['produk_cap_galon', 'brand', 'warna']],
        ['key' => 'mesin_powerjet2', 'label' => 'Mesin Powerjet 2', 'group' => 'mesin', 'route' => 'gudang/mesin_powerjet2', 'table' => 'mesin_powerjet2', 'icon' => 'fas fa-cogs', 'accent' => 'primary', 'quantity_field' => 'stock', 'item_fields' => ['produk_cap_galon', 'brand', 'warna']],
        ['key' => 'mesin_ccm1', 'label' => 'Mesin CCM 1', 'group' => 'mesin', 'route' => 'gudang/mesin_ccm1', 'table' => 'mesin_ccm1', 'icon' => 'fas fa-cogs', 'accent' => 'info', 'quantity_field' => 'stock', 'item_fields' => ['produk_cap_galon', 'brand', 'warna']],
        ['key' => 'mesin_ccm2', 'label' => 'Mesin CCM 2', 'group' => 'mesin', 'route' => 'gudang/mesin_ccm2', 'table' => 'mesin_ccm2', 'icon' => 'fas fa-cogs', 'accent' => 'info', 'quantity_field' => 'stock', 'item_fields' => ['produk_cap_galon', 'brand', 'warna']],
        ['key' => 'mesin_ips1', 'label' => 'Mesin IPS 1', 'group' => 'mesin', 'route' => 'gudang/mesin_ips1', 'table' => 'mesin_ips1', 'icon' => 'fas fa-cog', 'accent' => 'dark', 'quantity_field' => 'stock', 'item_fields' => ['produk_preform', 'brand', 'warna']],
        ['key' => 'mesin_ips2', 'label' => 'Mesin IPS 2', 'group' => 'mesin', 'route' => 'gudang/mesin_ips2', 'table' => 'mesin_ips2', 'icon' => 'fas fa-cog', 'accent' => 'dark', 'quantity_field' => 'stock', 'item_fields' => ['produk_preform', 'brand', 'warna']],
        ['key' => 'mesin_ips3', 'label' => 'Mesin IPS 3', 'group' => 'mesin', 'route' => 'gudang/mesin_ips3', 'table' => 'mesin_ips3', 'icon' => 'fas fa-cog', 'accent' => 'dark', 'quantity_field' => 'stock', 'item_fields' => ['produk_preform', 'brand', 'warna']],
        ['key' => 'mesin_ips4', 'label' => 'Mesin IPS 4', 'group' => 'mesin', 'route' => 'gudang/mesin_ips4', 'table' => 'mesin_ips4', 'icon' => 'fas fa-cog', 'accent' => 'dark', 'quantity_field' => 'stock', 'item_fields' => ['produk_preform', 'brand', 'warna']],
    ];

    public function __construct()
    {
        $this->MGudang = new MGudang();
        $this->MGudangKategori = model('App\Models\MGudangKategori');
        $this->MGudangDetail = model('App\Models\MGudangDetail');
        helper('form');
    }

    public function index()
    {
        $id_user = session()->get('id_user');

        if (!$id_user) {
            return redirect()->to('/login');
        }

        $dashboardModules = $this->buildGudangDashboardModules();

        return view('gudang', [
            'title' => 'gudang',
            'dashboard_metrics' => $this->buildGudangDashboardMetrics($dashboardModules),
            'material_modules' => array_values(array_filter($dashboardModules, static fn(array $module): bool => $module['group'] === 'material')),
            'machine_modules' => array_values(array_filter($dashboardModules, static fn(array $module): bool => $module['group'] === 'mesin')),
            'recent_activities' => $this->buildGudangRecentActivities(self::DASHBOARD_MODULES, 12),
        ]);
    }

    private function buildGudangDashboardModules(): array
    {
        $modules = [];

        foreach (self::DASHBOARD_MODULES as $config) {
            $modules[] = $this->buildGudangModuleSummary($config);
        }

        return $modules;
    }

    private function buildGudangModuleSummary(array $config): array
    {
        $table = $config['table'];
        $summary = $config + [
            'record_count' => 0,
            'quantity_total' => 0.0,
            'has_quantity' => false,
            'latest_item' => '-',
            'latest_spk' => '-',
            'latest_shift' => '-',
            'latest_timestamp' => 0,
            'available' => false,
        ];

        if (!$this->tableExists($table)) {
            return $summary;
        }

        $summary['available'] = true;
        $columns = $this->getTableColumns($table);
        if (empty($columns)) {
            return $summary;
        }

        $db = db_connect();
        $summary['record_count'] = (int) $db->table($table)->countAllResults();

        $quantityField = $config['quantity_field'] ?? null;
        if ($quantityField !== null && in_array($quantityField, $columns, true)) {
            $sumRow = $db->table($table)
                ->selectSum($quantityField, 'total_quantity')
                ->get()
                ->getRowArray();

            $summary['has_quantity'] = true;
            $summary['quantity_total'] = (float) ($sumRow['total_quantity'] ?? 0);
        }

        $latestRows = $this->getLatestDashboardRows($config, 1);
        if (!empty($latestRows)) {
            $row = $latestRows[0];
            $summary['latest_item'] = $this->extractDashboardItemLabel($row, $config['item_fields'] ?? []);
            $summary['latest_spk'] = trim((string) ($row['no_spk'] ?? '')) ?: '-';
            $summary['latest_shift'] = trim((string) ($row['shif'] ?? '')) ?: '-';
            $summary['latest_timestamp'] = $this->extractDashboardTimestamp($row);
        }

        return $summary;
    }

    private function buildGudangDashboardMetrics(array $modules): array
    {
        $totalRecords = 0;
        $totalQuantity = 0.0;
        $modulesWithData = 0;
        $latestTimestamp = 0;
        $latestModule = '-';

        foreach ($modules as $module) {
            $totalRecords += (int) ($module['record_count'] ?? 0);

            if (!empty($module['has_quantity'])) {
                $totalQuantity += (float) ($module['quantity_total'] ?? 0);
            }

            if (($module['record_count'] ?? 0) > 0) {
                $modulesWithData++;
            }

            $moduleTimestamp = (int) ($module['latest_timestamp'] ?? 0);
            if ($moduleTimestamp > $latestTimestamp) {
                $latestTimestamp = $moduleTimestamp;
                $latestModule = $module['label'] ?? '-';
            }
        }

        return [
            'module_total' => count($modules),
            'modules_with_data' => $modulesWithData,
            'total_records' => $totalRecords,
            'total_quantity' => $totalQuantity,
            'latest_timestamp' => $latestTimestamp,
            'latest_module' => $latestModule,
        ];
    }

    private function buildGudangRecentActivities(array $modules, int $limit = 12): array
    {
        $activities = [];

        foreach ($modules as $config) {
            foreach ($this->getLatestDashboardRows($config, 3) as $row) {
                $quantityField = $config['quantity_field'] ?? null;

                $activities[] = [
                    'module_label' => $config['label'],
                    'route' => $config['route'],
                    'item_label' => $this->extractDashboardItemLabel($row, $config['item_fields'] ?? []),
                    'quantity' => $quantityField !== null && array_key_exists($quantityField, $row) ? $row[$quantityField] : null,
                    'spk' => trim((string) ($row['no_spk'] ?? '')) ?: '-',
                    'shift' => trim((string) ($row['shif'] ?? '')) ?: '-',
                    'timestamp' => $this->extractDashboardTimestamp($row),
                ];
            }
        }

        usort($activities, static function (array $left, array $right): int {
            return ($right['timestamp'] ?? 0) <=> ($left['timestamp'] ?? 0);
        });

        return array_slice($activities, 0, $limit);
    }

    private function getLatestDashboardRows(array $config, int $limit = 1): array
    {
        $table = $config['table'];
        if (!$this->tableExists($table)) {
            return [];
        }

        $columns = $this->getTableColumns($table);
        if (empty($columns)) {
            return [];
        }

        $selectColumns = array_values(array_unique(array_filter(array_merge(
            ['id', 'no_spk', 'shif', 'tanggal', 'created_at', 'updated_at'],
            $config['item_fields'] ?? [],
            isset($config['quantity_field']) ? [$config['quantity_field']] : []
        ))));

        $selectColumns = array_values(array_intersect($selectColumns, $columns));
        if (empty($selectColumns)) {
            return [];
        }

        $builder = db_connect()->table($table)->select(implode(', ', $selectColumns));

        foreach (['updated_at', 'created_at', 'tanggal', 'id'] as $orderField) {
            if (in_array($orderField, $columns, true)) {
                $builder->orderBy($orderField, 'DESC');
            }
        }

        return $builder->limit($limit)->get()->getResultArray();
    }

    private function extractDashboardItemLabel(array $row, array $candidateFields): string
    {
        foreach ($candidateFields as $field) {
            $value = trim((string) ($row[$field] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return trim((string) ($row['no_spk'] ?? '')) ?: '-';
    }

    private function extractDashboardTimestamp(array $row): int
    {
        foreach (['updated_at', 'created_at', 'tanggal'] as $field) {
            $value = trim((string) ($row[$field] ?? ''));
            if ($value === '') {
                continue;
            }

            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return 0;
    }

    private function getTableColumns(string $table): array
    {
        if (!$this->tableExists($table)) {
            return [];
        }

        try {
            return db_connect()->getFieldNames($table);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/gudang');
        }

        $id_user = session()->get('id_user');
        if (!$id_user) {
            return redirect()->to('/login');
        }

        $data = [
            'id_user'      => $id_user,
            'no_spk'       => $this->request->getPost('no_spk'),
            'shif'         => $this->request->getPost('shif'),
            'tanggal'      => $this->request->getPost('tanggal'),
        ];
        $data = array_merge($data, $this->collectKomponenPayload());

        $this->MGudang->insert($data);
        $gudangId = (int) $this->MGudang->getInsertID();
        $this->syncGudangDetails($gudangId, $data);

        return redirect()->to('/gudang')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        if ($this->tableExists('gudang_item_details')) {
            $this->MGudangDetail->where('gudang_id', $id)->delete();
        }
        $this->MGudang->delete($id);
        return redirect()->to('/gudang')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MGudang->find($id);
        return view('edit', [
            'title' => 'Ubah Data Gudang',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        $id_user = session()->get('id_user');
        $data = [
            'id_user'      => $id_user,
            'no_spk'       => $this->request->getPost('no_spk'),
            'shif'         => $this->request->getPost('shif'),
            'tanggal'      => $this->request->getPost('tanggal'),
        ];
        $data = array_merge($data, $this->collectKomponenPayload());

        $this->MGudang->update($id, $data);
        $this->syncGudangDetails((int) $id, $data);
        return redirect()->to('/gudang')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MGudang->findAll();

        // Bersihkan output buffer (penting!)
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=data_produksi.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr>
                <th>No SPK</th>
                <th>Tanggal</th>
                <th>Nama Mesin</th>
                <th>Nama Produk</th>
                <th>Batch Number</th>
                <th>Shift</th>
                <th>Nomor Mesin</th>
                <th>Packing Isi</th>
                <th>Cycle Time</th>
                <th>Target</th>
                <th>Operator</th>
            </tr>";

        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $d['no_spk'] . "</td>
                    <td>" . $d['tanggal'] . "</td>
                    <td>" . $d['nama_mesin'] . "</td>
                    <td>" . $d['nama_produk'] . "</td>
                    <td>" . $d['batch_number'] . "</td>
                    <td>" . $d['shift'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['packing_isi'] . "</td>
                    <td>" . $d['cycle_time'] . "</td>
                    <td>" . $d['target'] . "</td>
                    <td>" . $d['operator'] . "</td>
                </tr>";
        }

        echo "</table>";
        exit;
    }

    private function getSpkMasterData(): array
    {
        $master = [];

        try {
            $ppicRows = db_connect()
                ->table('ppic')
                ->select('no_spk, tanggal, id')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            $ppicRows = [];
        }

        $gudangRows = $this->MGudang
            ->select('no_spk, shif, tanggal, id, bahan_baku, box, karung, plastik, masterbatch, galon_reject_supplier, galon_reject_produksi, gilingan_reject_supplier, gilingan_reject_produksi, stiker, reject_preform, bekuat_pet, bekuan_capgalon, gilingan_screwcap')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
        $gudangRows = $this->hydrateGudangDetails($gudangRows);

        $latestGudangBySpk = [];
        foreach ($gudangRows as $row) {
            $noSpk = trim((string) ($row['no_spk'] ?? ''));
            if ($noSpk === '' || isset($latestGudangBySpk[$noSpk])) {
                continue;
            }

            $latestGudangBySpk[$noSpk] = [
                'shif' => trim((string) ($row['shif'] ?? '')),
                'bahan_baku' => trim((string) ($row['bahan_baku'] ?? '')),
                'box' => trim((string) ($row['box'] ?? '')),
                'karung' => trim((string) ($row['karung'] ?? '')),
                'plastik' => trim((string) ($row['plastik'] ?? '')),
                'masterbatch' => trim((string) ($row['masterbatch'] ?? '')),
                'galon_reject_supplier' => trim((string) ($row['galon_reject_supplier'] ?? '')),
                'galon_reject_produksi' => trim((string) ($row['galon_reject_produksi'] ?? '')),
                'gilingan_reject_supplier' => trim((string) ($row['gilingan_reject_supplier'] ?? '')),
                'gilingan_reject_produksi' => trim((string) ($row['gilingan_reject_produksi'] ?? '')),
                'stiker' => trim((string) ($row['stiker'] ?? '')),
                'reject_preform' => trim((string) ($row['reject_preform'] ?? '')),
                'bekuat_pet' => trim((string) ($row['bekuat_pet'] ?? '')),
                'bekuan_capgalon' => trim((string) ($row['bekuan_capgalon'] ?? '')),
                'gilingan_screwcap' => trim((string) ($row['gilingan_screwcap'] ?? '')),
            ];
        }

        foreach ($ppicRows as $row) {
            $noSpk = trim((string) ($row['no_spk'] ?? ''));
            if ($noSpk === '' || isset($master[$noSpk])) {
                continue;
            }

            $master[$noSpk] = array_merge([
                'no_spk' => $noSpk,
                'shif' => '',
                'bahan_baku' => '',
                'box' => '',
                'karung' => '',
                'plastik' => '',
                'masterbatch' => '',
                'galon_reject_supplier' => '',
                'galon_reject_produksi' => '',
                'gilingan_reject_supplier' => '',
                'gilingan_reject_produksi' => '',
                'stiker' => '',
                'reject_preform' => '',
                'bekuat_pet' => '',
                'bekuan_capgalon' => '',
                'gilingan_screwcap' => '',
            ], $latestGudangBySpk[$noSpk] ?? []);
        }

        foreach ($latestGudangBySpk as $noSpk => $values) {
            if (isset($master[$noSpk])) {
                continue;
            }

            $master[$noSpk] = array_merge([
                'no_spk' => $noSpk,
                'shif' => '',
                'bahan_baku' => '',
                'box' => '',
                'karung' => '',
                'plastik' => '',
                'masterbatch' => '',
                'galon_reject_supplier' => '',
                'galon_reject_produksi' => '',
                'gilingan_reject_supplier' => '',
                'gilingan_reject_produksi' => '',
                'stiker' => '',
                'reject_preform' => '',
                'bekuat_pet' => '',
                'bekuan_capgalon' => '',
                'gilingan_screwcap' => '',
            ], $values);
        }

        ksort($master, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($master);
    }

    private function collectKomponenPayload(): array
    {
        $payload = [];

        foreach (self::KOMPONEN_FIELDS as $field) {
            $payload[$field] = $this->request->getPost($field);
        }

        // Sinkronisasi dengan field lama agar data historis tetap terbaca.
        $payload['bahan_baku'] ?? null;
        $payload['reject_preform'] ?? null;
        $payload['bekuat_pet'] ?? null;
        $payload['gilingan_screwcap'] ?? null;

        return $payload;
    }

    private function getGudangKategori(): array
    {
        if (!$this->tableExists('gudang_item_categories')) {
            return [];
        }

        return $this->MGudangKategori
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    private function hydrateGudangDetails(array $rows): array
    {
        if (empty($rows) || !$this->tableExists('gudang_item_details') || !$this->tableExists('gudang_item_categories')) {
            return $rows;
        }

        $ids = array_values(array_filter(array_map(static function ($row) {
            return (int) ($row['id'] ?? 0);
        }, $rows)));

        if (empty($ids)) {
            return $rows;
        }

        $detailRows = $this->MGudangDetail
            ->select('gudang_item_details.gudang_id, gudang_item_categories.kode, gudang_item_details.nilai')
            ->join('gudang_item_categories', 'gudang_item_categories.id = gudang_item_details.kategori_id', 'inner')
            ->whereIn('gudang_item_details.gudang_id', $ids)
            ->findAll();

        $map = [];
        foreach ($detailRows as $detail) {
            $gudangId = (int) ($detail['gudang_id'] ?? 0);
            $kode = trim((string) ($detail['kode'] ?? ''));
            if ($gudangId <= 0 || $kode === '') {
                continue;
            }
            $map[$gudangId][$kode] = $detail['nilai'] ?? '';
        }

        foreach ($rows as &$row) {
            $id = (int) ($row['id'] ?? 0);
            foreach (self::KOMPONEN_FIELDS as $field) {
                if (isset($map[$id][$field])) {
                    $row[$field] = $map[$id][$field];
                }
            }
        }
        unset($row);

        return $rows;
    }

    private function syncGudangDetails(int $gudangId, array $data): void
    {
        if ($gudangId <= 0 || !$this->tableExists('gudang_item_details') || !$this->tableExists('gudang_item_categories')) {
            return;
        }

        $kategori = $this->MGudangKategori
            ->select('id, kode')
            ->findAll();

        if (empty($kategori)) {
            return;
        }

        $kategoriMap = [];
        foreach ($kategori as $item) {
            $kode = trim((string) ($item['kode'] ?? ''));
            if ($kode === '') {
                continue;
            }
            $kategoriMap[$kode] = (int) $item['id'];
        }

        $this->MGudangDetail->where('gudang_id', $gudangId)->delete();

        $insertRows = [];
        foreach (self::KOMPONEN_FIELDS as $field) {
            if (!isset($kategoriMap[$field])) {
                continue;
            }

            $nilai = trim((string) ($data[$field] ?? ''));
            if ($nilai === '') {
                continue;
            }

            $insertRows[] = [
                'gudang_id' => $gudangId,
                'kategori_id' => $kategoriMap[$field],
                'nilai' => $nilai,
            ];
        }

        if (!empty($insertRows)) {
            $this->MGudangDetail->insertBatch($insertRows);
        }
    }

    private function tableExists(string $table): bool
    {
        try {
            return db_connect()->tableExists($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
