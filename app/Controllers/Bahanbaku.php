<?php

namespace App\Controllers;

use App\Models\MBahanBaku;

class BahanBaku extends BaseController
{
    private const INDEX_ROUTE = '/gudang/bahan_baku';
    private const VIEW_PATH = 'gudang/bahanbaku';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const BAHAN_BAKU_PRODUCTS = [
        'Galon',
        'BAHAN DALAM PROSES',
        'BAHAN PENOLONG',
        'BARANG PACKAGING / KEMASAN',
    ];
    private const BAHAN_BAKU_TYPES = [
        'Virgin',
        'Regrind (gilingan) dari Supplier',
        'Regrind (gilingan) dari PT.Oro Plastindo',
        'Barium',
        'Regrind Cap Gallon',
        'Stock Turunan Bin',
        'Master Batch',
        'Plastik Bungkus',
    ];
    private const BAHAN_BAKU_OPTIONS = [
        'Virgin' => [
            'Poly Carbonate (PC) merk "Makrolon" WB 1239',
            'PC Makrolon 1239 WB Blue Tint',
            'Virgin PC Lexan PK 2870 "Lexan"',
            'Virgin PC Novarex M 7027 BF',
            'PC LUPOY 1621-02-B0786T BLUE',
            'PC Samyang Trirex 3026-L',
            'LEXAN PK2870-21317',
            'PC PELLET (Bpk Hendri)',
            'PC Clarnate WB-2032',
            'PC WB2032-9109 (Wanhua)',
        ],
        'Regrind (gilingan) dari Supplier' => [
            'R-R',
            'R-S',
            'R-T',
            'R-S AQUA',
            'RS IKRAMA',
            'R-S Hold',
            'R-R Supplier',
            'R-T Supplier',
            'R-S Bolesa',
            'R-S Pristine',
            'R-S Vica (Retur)',
            'R-S Vica AWI',
            'R-S CLUB',
            'R-S CLUB (Karantina)',
            'R-S AQUA (Karantina)',
            'R-R AM Plastik',
            'R-T (Karantina)',
        ],
        'Regrind (gilingan) dari PT.Oro Plastindo' => [
            'OR+',
            'OR',
            'OS',
            'OS Vivian',
            'OT+',
            'OT',
            'OT Vivian',
            'OT-V',
            'OU',
            'OUV',
            'RO-R',
            'RO-R (Karantina)',
            'RO-S',
            'RO-S (Karantina)',
            'RO-S.2',
            'RO-S.2 NEW',
            'RO-S.2-1',
            'RO-S.2 (Karantina)',
            'RO-S.3',
            'RO-S.3 VICA Retur',
            'RO-S.3 VICA AWI',
            'RO-S.3 Club',
            'RO-S.3 Prima',
            'RO-T',
            'RO-T+',
            'RO-T++',
            'RO-T+(Karantina)',
            'RO-U',
            'Flash Kuras Barel',
        ],
        'Barium' => [
            'Innoplus LD2426K (cap)',
            'Cosmothene LDPE F410 - 1',
            'LDPE PETLIN C-150Y',
            'Filler Masterbatch 6025',
        ],
        'Regrind Cap Gallon' => [
            'RC-B',
            'RC-Blue 04',
            'RC-Blue 11',
            'RC-Blue 07',
            'RC-Blue 08',
            'RC-BM.2',
            'RC-Blue 10',
            'RC-Blue 10-1',
            'RC-Campuran Blue & White',
            'RC-Campuran White & Red',
            'RC-Green 01',
            'RC-Green 02',
            'RC-Green 03',
            'RC-Blue 05',
            'RC-Red 01',
            'RC-White 01',
            'RC-Clear',
            'RC-Orange',
            'RC-Gold',
            'RC-Purple 01',
        ],
        'Stock Turunan Bin' => [
            'TB R',
            'TB R (Indomaret)',
            'TB R (EAZY)',
            'TB R Lower',
            'TB R upper',
            'TB R+',
            'TB R+. Karantina',
            'TB R+ (T Water)',
            'TB S',
            'TB S NEW',
            'TB S+',
            'TB S-',
            'TB S Karantina',
            'TB T',
            'TB T Violet',
            'TB T +',
            'TB T + (pristine all)',
            'TB T Vivian /Andalas',
            'TB T -V Upper',
            'TB T Lower',
            'TB T Upper',
            'TB U',
            'TB U New (Violet)',
            'TB U Karantina',
            'TB Kuras Barel',
            'TB Club Reguler (Virgin)',
            'TB Club D+(Non Virgin)',
            'TB Club (all plant)',
            'TB CLUB Karantina',
            'TB PrimA Ns',
            'TB PrimA Oro',
            'TB Vica',
            'TB T+Trial',
            'TB Trial',
            'TB PRISTINE LOWER 0,15 RR',
            'TB PRISTINE LOWER 0,18 VICA',
            'TB PRISTINE UPPER 0,18',
            'TB RC-Blue 05',
            'TB RC-Blue 04',
            'TB RC-Blue 11',
            'TB RC-Blue 10',
            'TB RC-Blue 10-1',
            'TB RC-Blue 07',
            'TB RC-Blue 08',
            'TB RC-Green 01',
            'TB RC-Green 02',
            'TB RC-Green 03',
            'TB RC-Red 01',
            'TB RC-White 01',
            'TB RC-Clear',
            'TB RC-Orange',
            'TB RC-Purple',
        ],
        'Master Batch' => [
            'Renol Blue PC-C7216R',
            'Renol Blue PC-D8997',
            'Renol Violet PC-D87226 MG',
            'Plast Trans Blue MFPC 15023',
            'Plast Trans Violet MFPC 15999 (A)',
            'Plast Green MFPE 20564-22',
            'Lucent Blue 0878-BL LM5300729',
            'Lucent Blue 1761 (A)-BL LM5201494',
            'Lucent Red 0157-BL LM3300115',
            'Lucent Green 1749-BL LM6301490',
            'Lucent Green 5771-BL',
            'Lucent Blue 6019 BL LM5305779',
            'Lucent Blue 6172 (A) LM5106030',
            'Lucent Blue 6420 (A) SWS',
            'Lucent Green 2221-BL LM6301973',
            'Lucent Violet 6407 BL',
            'Finolene Green GSE-1407',
        ],
        'Plastik Bungkus' => [
            'HD 45x75x0.015 (GALLON)',
            'PE 45x75x0.015 (GALLON)',
            'PE 45x75x0.03 (GALLON)',
            'HD 73.5x171x0.03 (GALLON)',
            'PE 73.5x171x0.03 (GALLON)',
            'PE 50x003x85',
            'PE 60x115x008',
            'PE 135 X 110 X 0.03',
        ],
    ];

    protected $MBahanBaku;

    public function __construct()
    {
        $this->MBahanBaku = new MBahanBaku();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MBahanBaku;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('tipe', $q)
                ->orLike('nama_bahan', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('DATE(created_at) >=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where('DATE(created_at) <=', $endDate);
        }

        $bahanBaku = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Bahan Baku',
            'bahan_baku' => $bahanBaku,
            'bahan_baku_products' => self::BAHAN_BAKU_PRODUCTS,
            'bahan_baku_types' => self::BAHAN_BAKU_TYPES,
            'bahan_baku_options' => self::BAHAN_BAKU_OPTIONS,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    public function bahan_baku()
    {
        return $this->index();
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'tipe' => $this->request->getPost('tipe'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MBahanBaku->insert($data)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $bahanBaku = $this->MBahanBaku->find($id);

        if (!$bahanBaku) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($bahanBaku);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBahanBaku->find($id)) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'tipe' => $this->request->getPost('tipe'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MBahanBaku->update($id, $data)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBahanBaku->find($id)) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MBahanBaku->delete($id)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    private function normalizeStatus(?string $status): string
    {
        $status = trim((string) $status);

        if (!in_array($status, self::STATUS_OPTIONS, true)) {
            return self::STATUS_OPTIONS[0];
        }

        return $status;
    }

}
