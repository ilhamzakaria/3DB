<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinishGoodsDataSeeder extends Seeder
{
    protected $DBGroup = 'ppic';

    public function run()
    {
        // Galon Data
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

        // Cap Galon Data
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
        ];

        $this->db->table('finish_good_cap_galon')->insertBatch($capGalonData);

        // Freeform Data
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
        ];

        $this->db->table('finish_good_freeform')->insertBatch($freeformData);

        // Screw Cap Data
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
        ];

        $this->db->table('finish_good_screw_cap')->insertBatch($screwCapData);

        echo "Finish Goods data seeded successfully!";
    }
}
