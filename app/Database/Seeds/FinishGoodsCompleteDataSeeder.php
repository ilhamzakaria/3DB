<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinishGoodsCompleteDataSeeder extends Seeder
{
    protected $DBGroup = 'ppic';

    public function run()
    {
        // Galon Data - All 10 rows from catatan.md
        $galonData = [
            ['bagian' => 'Galon', 'nomor' => '1', 'nama_produk' => '168', 'grade' => 'D.0.V-2', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '1700'],
            ['bagian' => 'Galon', 'nomor' => '2', 'nama_produk' => '168', 'grade' => 'A.1.V-3', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '3', 'nama_produk' => '212', 'grade' => 'C.2.B-3', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '4', 'nama_produk' => 'AL-FALAH', 'grade' => 'A+', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '5', 'nama_produk' => 'AIA', 'grade' => 'B.2.B-2', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '6', 'nama_produk' => 'AGUARIA', 'grade' => 'A.1.V-3', 'berat' => '750', 'wh_oro' => '4', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '7', 'nama_produk' => 'AQUARAN', 'grade' => 'C.2.B-3', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '8', 'nama_produk' => 'AERZIPS', 'grade' => 'A.1.V-3', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '9', 'nama_produk' => 'AERZIPS', 'grade' => 'B.2.B-2', 'berat' => '750', 'wh_oro' => '0', 'wh_bam' => '0'],
            ['bagian' => 'Galon', 'nomor' => '10', 'nama_produk' => 'ALBIL', 'grade' => 'A.1.V-4', 'berat' => '780', 'wh_oro' => '0', 'wh_bam' => '0'],
        ];

        $this->db->table('finish_good_galon')->insertBatch($galonData);
        echo "✓ Inserted " . count($galonData) . " Galon records\n";

        // Cap Galon Data - All 36 rows from catatan.md
        $capGalonData = [
            ['bagian' => 'Cap Galon', 'nomor' => '1', 'nama_produk' => 'AQ 8', 'warna' => 'RED 01', 'berat' => '9.5', 'jumlah' => '201000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '2', 'nama_produk' => "ALMA'SOEM", 'warna' => 'BLUE 08', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '3', 'nama_produk' => 'AIRCOOL', 'warna' => 'BLUE 10', 'berat' => '9.5', 'jumlah' => '80000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '4', 'nama_produk' => 'BLANK', 'warna' => 'WHITE 01', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '5', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 10', 'berat' => '9.5', 'jumlah' => '30000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '6', 'nama_produk' => 'BLANK', 'warna' => 'RED 01', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '8', 'nama_produk' => 'BLANK (GHAMIR)', 'warna' => 'BLUE 04', 'berat' => '9.5', 'jumlah' => '15000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '9', 'nama_produk' => 'BLANK (BOLESA) SN', 'warna' => 'BLUE 05', 'berat' => '9.5', 'jumlah' => '37500', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '10', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 07', 'berat' => '9.5', 'jumlah' => '6000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '11', 'nama_produk' => 'BLANK', 'warna' => 'CLEAR', 'berat' => '9.5', 'jumlah' => '10000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '12', 'nama_produk' => 'BW', 'warna' => 'BLUE 10-1', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '13', 'nama_produk' => 'BLU9 SN', 'warna' => 'BLUE 07', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '14', 'nama_produk' => 'BLANK (SAJUAK) SN', 'warna' => 'BLUE 07', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '15', 'nama_produk' => 'LEEQUIL', 'warna' => 'BLUE 08', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '16', 'nama_produk' => 'G', 'warna' => 'BLUE 04', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '17', 'nama_produk' => 'JATILUHUR', 'warna' => 'BLUE 07', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '18', 'nama_produk' => 'MASKUA', 'warna' => 'GREEN 03', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '19', 'nama_produk' => 'MOUNTOYA NEW', 'warna' => 'CLEAR', 'berat' => '9.5', 'jumlah' => '150000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '20', 'nama_produk' => 'PREMIER WATER', 'warna' => 'BLUE 10', 'berat' => '9.5', 'jumlah' => '50000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '21', 'nama_produk' => 'PRISTINE NEW', 'warna' => 'GREEN 02', 'berat' => '9.5', 'jumlah' => '50000', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '22', 'nama_produk' => 'PRISTINE SWT', 'warna' => 'GREEN 02', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '23', 'nama_produk' => 'PROF', 'warna' => 'CLEAR', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '24', 'nama_produk' => 'QUELLE', 'warna' => 'BLUE 07', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '25', 'nama_produk' => 'SWS SN', 'warna' => 'WHITE 01', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '26', 'nama_produk' => 'SIERRA', 'warna' => 'BLUE 07', 'berat' => '9.5', 'jumlah' => '100000', 'packing' => '15'],
            ['bagian' => 'Cap Galon', 'nomor' => '27', 'nama_produk' => 'SUMMIT', 'warna' => 'BLUE 04', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '28', 'nama_produk' => 'TDS NOL', 'warna' => 'WHITE 01', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '29', 'nama_produk' => 'T-WATER', 'warna' => 'CLEAR', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '30', 'nama_produk' => 'UMAXX', 'warna' => 'BLUE 04', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '31', 'nama_produk' => 'WINRO SN', 'warna' => 'RED 01', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '32', 'nama_produk' => 'VIZ (SN)', 'warna' => 'BLUE 04', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '33', 'nama_produk' => 'BLANK (QFAST)', 'warna' => 'BLUE 10', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '34', 'nama_produk' => 'PRISTINE LAMA', 'warna' => 'GREEN 02', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '35', 'nama_produk' => 'WATER STORE SN', 'warna' => 'PURPLE 01', 'berat' => '9', 'jumlah' => '0', 'packing' => null],
            ['bagian' => 'Cap Galon', 'nomor' => '36', 'nama_produk' => 'YORA', 'warna' => 'GREEN 03', 'berat' => '9.5', 'jumlah' => '0', 'packing' => null],
        ];

        $this->db->table('finish_good_cap_galon')->insertBatch($capGalonData);
        echo "✓ Inserted " . count($capGalonData) . " Cap Galon records\n";

        // Freeform Data - All 37 rows from catatan.md
        $freeformData = [
            ['bagian' => 'Freeform', 'nomor' => '1', 'nama_produk' => 'NESTLE CBN 10,12,18,19,21,24,27,30', 'warna' => 'BLUE 14', 'berat' => '12.5', 'jumlah' => '325800', 'packing' => '25'],
            ['bagian' => 'Freeform', 'nomor' => '2', 'nama_produk' => 'NESTLE SGN 14,16,18,21,23,25,28,30', 'warna' => 'BLUE 14', 'berat' => '12.5', 'jumlah' => '1042560', 'packing' => '80'],
            ['bagian' => 'Freeform', 'nomor' => '3', 'nama_produk' => 'NESTLE HOLD KARUNG', 'warna' => 'BLUE 14', 'berat' => '12.5', 'jumlah' => '471744', 'packing' => '312'],
            ['bagian' => 'Freeform', 'nomor' => '4', 'nama_produk' => 'SORTIR FM NESTLE', 'warna' => 'BLUE 14', 'berat' => '12.5', 'jumlah' => '55944', 'packing' => '37'],
            ['bagian' => 'Freeform', 'nomor' => '5', 'nama_produk' => 'FM WK LIV IPS 01', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '6', 'nama_produk' => 'FM WK LIV IPS 02', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '160272', 'packing' => '106'],
            ['bagian' => 'Freeform', 'nomor' => '7', 'nama_produk' => 'FM MT IPS 01 & 2 HOLD', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '216216', 'packing' => '143'],
            ['bagian' => 'Freeform', 'nomor' => '8', 'nama_produk' => 'GRAND JADE L IPS 2', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '9', 'nama_produk' => 'TRAIL 15,7 WK LIV IPS 03', 'warna' => 'CLR & B29', 'berat' => '15.7', 'jumlah' => '138240', 'packing' => '120'],
            ['bagian' => 'Freeform', 'nomor' => '10', 'nama_produk' => 'FM YS-W01 L.IV IPS 2', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '27216', 'packing' => '18'],
            ['bagian' => 'Freeform', 'nomor' => '11', 'nama_produk' => 'TRANSISI', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '31752', 'packing' => '21'],
            ['bagian' => 'Freeform', 'nomor' => '12', 'nama_produk' => 'SIERRA', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '13', 'nama_produk' => 'SIERRA 14,23', 'warna' => 'BLUE 20', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '14', 'nama_produk' => 'TIRTA JADE', 'warna' => 'BLUE 20', 'berat' => '12.5', 'jumlah' => '57456', 'packing' => '38'],
            ['bagian' => 'Freeform', 'nomor' => '15', 'nama_produk' => 'FM JADE IPS 01', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '9072', 'packing' => '6'],
            ['bagian' => 'Freeform', 'nomor' => '16', 'nama_produk' => 'BLU9 MT IPS 02', 'warna' => 'BLUE 14', 'berat' => '12.5', 'jumlah' => '786240', 'packing' => '520'],
            ['bagian' => 'Freeform', 'nomor' => '17', 'nama_produk' => 'ASPEN YS IPS 03', 'warna' => 'BLUE 29', 'berat' => '12.5', 'jumlah' => '87552', 'packing' => '76'],
            ['bagian' => 'Freeform', 'nomor' => '18', 'nama_produk' => 'FM JADE L IPS 01', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '1221696', 'packing' => '808'],
            ['bagian' => 'Freeform', 'nomor' => '19', 'nama_produk' => 'FM JADE L IPS 02', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '1295784', 'packing' => '857'],
            ['bagian' => 'Freeform', 'nomor' => '20', 'nama_produk' => 'ASPEN JADE LIV IPS 03 9,10 & 11', 'warna' => 'BLUE 29', 'berat' => '15.7', 'jumlah' => '720000', 'packing' => '625'],
            ['bagian' => 'Freeform', 'nomor' => '21', 'nama_produk' => 'FM YS-W01 IPS 03 10,13,15,17,20,22,24,27,29', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '89208', 'packing' => '59'],
            ['bagian' => 'Freeform', 'nomor' => '22', 'nama_produk' => 'TRANSISI', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '4536', 'packing' => '3'],
            ['bagian' => 'Freeform', 'nomor' => '23', 'nama_produk' => 'FM MT IPS 03', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '24', 'nama_produk' => 'FM JADE IPS 03', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '19656', 'packing' => '13'],
            ['bagian' => 'Freeform', 'nomor' => '25', 'nama_produk' => 'SIERRA RETURN YS IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '492912', 'packing' => '326'],
            ['bagian' => 'Freeform', 'nomor' => '26', 'nama_produk' => 'FM JADE IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '42336', 'packing' => '28'],
            ['bagian' => 'Freeform', 'nomor' => '27', 'nama_produk' => 'FM MT IPS 01 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '28', 'nama_produk' => 'FM JADE LIV IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '3557736', 'packing' => '2353'],
            ['bagian' => 'Freeform', 'nomor' => '29', 'nama_produk' => 'FM WK IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '30', 'nama_produk' => 'FM MT IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '30240', 'packing' => '20'],
            ['bagian' => 'Freeform', 'nomor' => '31', 'nama_produk' => 'FM WK L.IV IPS 02 BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '0', 'packing' => '0'],
            ['bagian' => 'Freeform', 'nomor' => '32', 'nama_produk' => 'TRANSISI BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '1309392', 'packing' => '866'],
            ['bagian' => 'Freeform', 'nomor' => '33', 'nama_produk' => 'FM WK IPS 02 HOLD BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '149688', 'packing' => '99'],
            ['bagian' => 'Freeform', 'nomor' => '34', 'nama_produk' => 'FM WK L.IV IPS 02 HOLD BAM', 'warna' => 'CLEAR', 'berat' => '12.5', 'jumlah' => '80136', 'packing' => '53'],
            ['bagian' => 'Freeform', 'nomor' => '35', 'nama_produk' => 'INDOMARET WK L.IV HOLD BAM', 'warna' => 'BLUE 20-1', 'berat' => '12.5', 'jumlah' => '225288', 'packing' => '149'],
            ['bagian' => 'Freeform', 'nomor' => '36', 'nama_produk' => 'FM WK IPS 03 BAM HJU', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '36288', 'packing' => '24'],
            ['bagian' => 'Freeform', 'nomor' => '37', 'nama_produk' => 'FM JADE LIV IPS 03 BAM', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '455112', 'packing' => '301'],
            ['bagian' => 'Freeform', 'nomor' => '43', 'nama_produk' => 'FM JADE LIV IPS 03 HOLIS', 'warna' => 'CLEAR', 'berat' => '10.9', 'jumlah' => '0', 'packing' => '0'],
        ];

        $this->db->table('finish_good_freeform')->insertBatch($freeformData);
        echo "✓ Inserted " . count($freeformData) . " Freeform records\n";

        // Screw Cap Data - All 71 rows from catatan.md
        $screwCapData = [
            ['bagian' => 'Screw Cap', 'nomor' => '1', 'nama_produk' => "ALMA'SOEM", 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '2', 'nama_produk' => 'AMIDIS', 'warna' => 'ORANGE 01', 'berat' => '1.30', 'jumlah' => '2000000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '3', 'nama_produk' => 'BLANK', 'warna' => 'BLACK 01', 'berat' => '1.30', 'jumlah' => '158000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '4', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 01-2', 'berat' => '1.30', 'jumlah' => '40000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '5', 'nama_produk' => 'BLANK (CA)', 'warna' => 'BLUE 01-2', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '6', 'nama_produk' => 'BLANK (J WATER)', 'warna' => 'GREY 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '7', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 02', 'berat' => '1.30', 'jumlah' => '144000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '8', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 04', 'berat' => '1.30', 'jumlah' => '296000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '9', 'nama_produk' => 'BLANK (Stream)', 'warna' => 'BLUE 04', 'berat' => '1.30', 'jumlah' => '104000', 'keterangan' => '28'],
            ['bagian' => 'Screw Cap', 'nomor' => '10', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 05', 'berat' => '1.30', 'jumlah' => '8000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '11', 'nama_produk' => 'BLANK', 'warna' => 'CLEAR', 'berat' => '1.30', 'jumlah' => '712000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '12', 'nama_produk' => 'BLANK', 'warna' => 'PURPLE 01', 'berat' => '1.30', 'jumlah' => '64000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '13', 'nama_produk' => 'BLANK', 'warna' => 'GOLD 01', 'berat' => '1.30', 'jumlah' => '80000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '14', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 04', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '15', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 06', 'berat' => '1.30', 'jumlah' => '88000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '16', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 02', 'berat' => '1.30', 'jumlah' => '40000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '17', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 03', 'berat' => '1.30', 'jumlah' => '72000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '18', 'nama_produk' => 'BLANK (Lakers)', 'warna' => 'GREEN 07', 'berat' => '1.30', 'jumlah' => '12000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '19', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 08', 'berat' => '1.30', 'jumlah' => '64000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '20', 'nama_produk' => 'BLANK', 'warna' => 'RED 01', 'berat' => '1.30', 'jumlah' => '832000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '21', 'nama_produk' => 'BLANK', 'warna' => 'WHITE 01', 'berat' => '1.30', 'jumlah' => '120000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '22', 'nama_produk' => 'BLANK', 'warna' => 'YELLOW 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '23', 'nama_produk' => 'BLANK', 'warna' => 'ORANGE 01', 'berat' => '1.30', 'jumlah' => '40000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '24', 'nama_produk' => 'BISTO', 'warna' => 'ORANGE 01', 'berat' => '1.30', 'jumlah' => '472000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '25', 'nama_produk' => 'BLU 9 (DW)', 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '26', 'nama_produk' => 'BLANK (VICA)', 'warna' => 'YELLOW 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => '21'],
            ['bagian' => 'Screw Cap', 'nomor' => '27', 'nama_produk' => 'BLANK (Guardian)', 'warna' => 'ORANGE 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => '23'],
            ['bagian' => 'Screw Cap', 'nomor' => '28', 'nama_produk' => 'NESTLE', 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '2720000', 'keterangan' => '21 & 10,24'],
            ['bagian' => 'Screw Cap', 'nomor' => '29', 'nama_produk' => 'BLANK (Watsons)', 'warna' => 'BLUE 13', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => '10'],
            ['bagian' => 'Screw Cap', 'nomor' => '30', 'nama_produk' => 'BLANK (Superindo)', 'warna' => 'RED 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '31', 'nama_produk' => 'ES ER CE', 'warna' => 'RED 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '32', 'nama_produk' => 'FATQUA', 'warna' => 'BLUE 04', 'berat' => '1.30', 'jumlah' => '160000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '33', 'nama_produk' => 'SIERRA', 'warna' => 'BLUE 03', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => '15,21,27'],
            ['bagian' => 'Screw Cap', 'nomor' => '34', 'nama_produk' => 'OMBE (DW)', 'warna' => 'BLUE 17', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '35', 'nama_produk' => 'DELQUA', 'warna' => 'GREEN 11', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '36', 'nama_produk' => 'DELQUA', 'warna' => 'GREEN 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '37', 'nama_produk' => 'TIRTA', 'warna' => 'BLUE 01-2', 'berat' => '1.30', 'jumlah' => '16000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '38', 'nama_produk' => 'SHOFA', 'warna' => 'GREEN 06', 'berat' => '1.30', 'jumlah' => '152000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '39', 'nama_produk' => 'PRIMA PANDAAN', 'warna' => 'BLUE 21', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '40', 'nama_produk' => 'BLANK', 'warna' => 'PINK 02', 'berat' => '1.30', 'jumlah' => '224000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '41', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '1088000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '42', 'nama_produk' => 'BLANK (Sanqua)', 'warna' => 'BLUE 23', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '43', 'nama_produk' => 'AIR ALAM', 'warna' => 'BLUE 22', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '44', 'nama_produk' => 'SISESA', 'warna' => 'PURPLE 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '45', 'nama_produk' => 'PRIMA SENTUL', 'warna' => 'BLUE 21', 'berat' => '1.30', 'jumlah' => '8000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '46', 'nama_produk' => 'TRIPANCA', 'warna' => 'BLUE 04', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '47', 'nama_produk' => 'BLANK (HN)', 'warna' => 'BLUE 24', 'berat' => '1.30', 'jumlah' => '152000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '48', 'nama_produk' => 'BLANK (BI)', 'warna' => 'GREEN 09', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '49', 'nama_produk' => 'INDOMARET', 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '504000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '50', 'nama_produk' => 'LEVIOS', 'warna' => 'BLUE 01', 'berat' => '1.30', 'jumlah' => '8000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '51', 'nama_produk' => 'BLANK (SIERRA)', 'warna' => 'BLACK 01', 'berat' => '1.30', 'jumlah' => '220000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '52', 'nama_produk' => 'BLANK (SIERRA)', 'warna' => 'RED 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => '15,20,25'],
            ['bagian' => 'Screw Cap', 'nomor' => '53', 'nama_produk' => 'LEVIOS', 'warna' => 'BLUE 17', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '54', 'nama_produk' => 'BLANK (ASMI)', 'warna' => 'BROWN 01', 'berat' => '1.30', 'jumlah' => '104000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '55', 'nama_produk' => 'BLANK', 'warna' => 'GREEN 01', 'berat' => '1.30', 'jumlah' => '152000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '56', 'nama_produk' => 'BLANK (CLUB)', 'warna' => 'BLUE 01-1', 'berat' => '1.30', 'jumlah' => '8000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '57', 'nama_produk' => 'BLANK (VIONA)', 'warna' => 'GREEN 02', 'berat' => '1.30', 'jumlah' => '104000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '58', 'nama_produk' => 'ALKAKUA', 'warna' => 'BLACK 01', 'berat' => '1.30', 'jumlah' => '504000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '59', 'nama_produk' => 'BLANK (CLUB)', 'warna' => 'BLUE 25', 'berat' => '1.30', 'jumlah' => '8000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '60', 'nama_produk' => 'HEVITRO', 'warna' => 'GREEN 10', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '61', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 03', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '62', 'nama_produk' => 'BLANK', 'warna' => 'BLUE 13', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '63', 'nama_produk' => 'BLANK (BOX POLOS)', 'warna' => 'GREEN 03', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '64', 'nama_produk' => 'BLANK (BOX POLOS)', 'warna' => 'BLACK 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '65', 'nama_produk' => 'BLANK (BOX POLOS)', 'warna' => 'WHITE 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '66', 'nama_produk' => 'BLANK (VIONA)', 'warna' => 'BLUE 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '67', 'nama_produk' => 'BLANK (AMOZ)', 'warna' => 'SILVER 01', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '68', 'nama_produk' => 'BLANK (LAVIDA)', 'warna' => 'BLUE 08', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '69', 'nama_produk' => 'BLANK (POLOS)', 'warna' => 'BLUE 02', 'berat' => '1.30', 'jumlah' => '352000', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '70', 'nama_produk' => 'BLANK (PELANGI)', 'warna' => 'PINK 02', 'berat' => '1.30', 'jumlah' => '0', 'keterangan' => null],
            ['bagian' => 'Screw Cap', 'nomor' => '71', 'nama_produk' => 'BLANK (ASPEN)', 'warna' => 'BLUE 30', 'berat' => '1.45', 'jumlah' => '12864000', 'keterangan' => null],
        ];

        $this->db->table('finish_good_screw_cap')->insertBatch($screwCapData);
        echo "✓ Inserted " . count($screwCapData) . " Screw Cap records\n";

        echo "\n✓ All Finish Goods data seeded successfully!\n";
        echo "  - Galon: 10 records\n";
        echo "  - Cap Galon: 36 records\n";
        echo "  - Freeform: 38 records\n";
        echo "  - Screw Cap: 71 records\n";
        echo "  Total: " . (10 + 36 + 38 + 71) . " records\n";
    }
}
