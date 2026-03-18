<?php

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $pecah = explode('-', date('Y-m-d', strtotime($tanggal)));

        return (int)$pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
    }
}
