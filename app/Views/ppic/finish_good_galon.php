<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$formatDate = static fn($v): string => ($t = strtotime((string)$v)) ? date('d-m-Y', $t) : '-';
$baseUrl    = 'ppic/finish_good/galon';

// Array lengkap produk galon — edit di sini untuk mengubah data referensi
// fields: nomor, nama_produk, grade, berat, wh_oro, wh_bam
$produkGalon = [
    ['nomor' => 1,   'nama_produk' => '168',                     'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 150,  'wh_bam' => 0],
    ['nomor' => 2,   'nama_produk' => '168',                     'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 3,   'nama_produk' => '212',                     'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 4,   'nama_produk' => 'AL-FALAH',                'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 5,   'nama_produk' => 'AIA',                     'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 6,   'nama_produk' => 'AGUARIA',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 7,   'nama_produk' => 'AEKTA',                   'grade' => 'C.3.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 8,   'nama_produk' => 'AERZIPS',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 9,   'nama_produk' => 'AERZIPS',                 'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 10,  'nama_produk' => 'ALBIL',                   'grade' => 'A.1.V-4',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 11,  'nama_produk' => "AL-MA'SOEM",              'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 12,  'nama_produk' => 'ARTHESS',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 1000, 'wh_bam' => 0],
    ['nomor' => 13,  'nama_produk' => "AL-MA'SOEM",              'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 14,  'nama_produk' => 'ARMIO',                   'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 15,  'nama_produk' => 'AMIDIS PANDAAN',          'grade' => 'B.1.B-3',  'berat' => 750, 'wh_oro' => 120,  'wh_bam' => 0],
    ['nomor' => 16,  'nama_produk' => 'AMIDIS DROP TEST',        'grade' => 'B.1.B-3',  'berat' => 750, 'wh_oro' => 8,    'wh_bam' => 0],
    ['nomor' => 17,  'nama_produk' => 'AMIDIS BDG',              'grade' => 'B.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 18,  'nama_produk' => 'AMIDIS BAWEN',            'grade' => 'B.1.B-3',  'berat' => 750, 'wh_oro' => 1040, 'wh_bam' => 0],
    ['nomor' => 19,  'nama_produk' => 'AQ8',                     'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 3000, 'wh_bam' => 0],
    ['nomor' => 20,  'nama_produk' => 'AQUALITY',                'grade' => 'D.0.B-2',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 21,  'nama_produk' => 'ARLO',                    'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 22,  'nama_produk' => 'AYONIA',                  'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 23,  'nama_produk' => 'AMBIERO',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 24,  'nama_produk' => 'ARUMA',                   'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 25,  'nama_produk' => 'AIR COOL',                'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 26,  'nama_produk' => 'ANDALAS',                 'grade' => 'C.3.B-2',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 27,  'nama_produk' => 'AURA',                    'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 28,  'nama_produk' => 'AYYA',                    'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 29,  'nama_produk' => 'BAHARI',                  'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 30,  'nama_produk' => 'BERTON',                  'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 31,  'nama_produk' => 'BISTO',                   'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 32,  'nama_produk' => 'BLANK (UBARAKAH)',        'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 33,  'nama_produk' => 'BLANK (JARWOTO)',         'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 34,  'nama_produk' => 'BLANK (KITAFIT)',         'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 35,  'nama_produk' => 'BLU 9',                   'grade' => 'C.1.V-2',  'berat' => 750, 'wh_oro' => 8,    'wh_bam' => 0],
    ['nomor' => 36,  'nama_produk' => 'BLANK BIRU (double)',     'grade' => 'D.0.B-2',  'berat' => 650, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 37,  'nama_produk' => 'BLANK (MMP)',             'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 38,  'nama_produk' => 'BW',                      'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 45,   'wh_bam' => 0],
    ['nomor' => 39,  'nama_produk' => 'BOLESA',                  'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 3000, 'wh_bam' => 0],
    ['nomor' => 40,  'nama_produk' => 'BLANK',                   'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 41,  'nama_produk' => 'BEN ORA SALIT',           'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 42,  'nama_produk' => 'CLARAS',                  'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 43,  'nama_produk' => 'CITRABASS',               'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 44,  'nama_produk' => 'CELESTY',                 'grade' => 'B.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 45,  'nama_produk' => 'CLUB CIRANJANG',          'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 46,  'nama_produk' => 'CLUB PALEMBANG',          'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 47,  'nama_produk' => 'CLUB BTA',                'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 48,  'nama_produk' => 'COOLPLUS',                'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 49,  'nama_produk' => 'DELQUA',                  'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 50,  'nama_produk' => 'DD WATER',                'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 51,  'nama_produk' => 'DT FRESH',                'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 52,  'nama_produk' => 'ELIF',                    'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 53,  'nama_produk' => 'ERVINA',                  'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 54,  'nama_produk' => 'ESTOLIA',                 'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 55,  'nama_produk' => 'EDURA',                   'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 56,  'nama_produk' => 'FOSS',                    'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 57,  'nama_produk' => 'FOUNTAIN',                'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 58,  'nama_produk' => 'GMES',                    'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 59,  'nama_produk' => 'GHAMIR',                  'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 60,  'nama_produk' => 'GOOGQUA',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 61,  'nama_produk' => 'GUNUNG SL',               'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 62,  'nama_produk' => 'GREAT',                   'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 63,  'nama_produk' => 'HANAANG',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 64,  'nama_produk' => 'HILLS',                   'grade' => 'C.1.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 65,  'nama_produk' => 'HIDO',                    'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 66,  'nama_produk' => 'HEVITRO',                 'grade' => 'C.1.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 67,  'nama_produk' => 'HUDRO',                   'grade' => 'A.1.V-4',  'berat' => 780, 'wh_oro' => 99,   'wh_bam' => 0],
    ['nomor' => 68,  'nama_produk' => 'INDOMARET',               'grade' => 'A.1.V-4',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 69,  'nama_produk' => 'JAM',                     'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 70,  'nama_produk' => 'JATILUHUR',               'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 71,  'nama_produk' => 'JAVA',                    'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 72,  'nama_produk' => 'JEERA',                   'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 73,  'nama_produk' => 'KENIRO (double)',         'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 74,  'nama_produk' => 'KALIA',                   'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 75,  'nama_produk' => 'KITAFIT',                 'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 200,  'wh_bam' => 0],
    ['nomor' => 76,  'nama_produk' => 'LW 8',                    'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 77,  'nama_produk' => "MASO'EM ARIAS",           'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 78,  'nama_produk' => 'LEEQUIL',                 'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 79,  'nama_produk' => 'MASKUA (embos di balik)', 'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 80,  'nama_produk' => 'MERIBA',                  'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 81,  'nama_produk' => 'LAMIRA',                  'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 82,  'nama_produk' => 'MOUNTOYA',                'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 83,  'nama_produk' => 'MOUNTOYA PRINT',          'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 84,  'nama_produk' => 'MQS',                     'grade' => 'A+',       'berat' => 750, 'wh_oro' => 4,    'wh_bam' => 0],
    ['nomor' => 85,  'nama_produk' => 'MURAQUA PRINT',           'grade' => 'A+',       'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 86,  'nama_produk' => 'NARITA',                  'grade' => 'A+',       'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 87,  'nama_produk' => 'NEW NORMAL',              'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 88,  'nama_produk' => "O'SENANGYA",              'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 89,  'nama_produk' => 'OCEAN',                   'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 90,  'nama_produk' => 'OK OCE',                  'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 91,  'nama_produk' => 'OKSITRA',                 'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 92,  'nama_produk' => 'OKSITRA',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 93,  'nama_produk' => 'OXXYDAV',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 94,  'nama_produk' => 'OXYINDO',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 95,  'nama_produk' => 'PATRAQUA',                'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 96,  'nama_produk' => 'PATISA',                  'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 97,  'nama_produk' => 'PELANGI',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 98,  'nama_produk' => 'PRISTINE DROP TEST',      'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 99,  'nama_produk' => 'PRISTINE DROP TEST NEW',  'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 20,   'wh_bam' => 0],
    ['nomor' => 100, 'nama_produk' => 'PRISTINE ALL NEW',        'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 2000, 'wh_bam' => 0],
    ['nomor' => 101, 'nama_produk' => 'PRISTINE NON STIKER',     'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 102, 'nama_produk' => 'PREMIER WATER (BR)',      'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 103, 'nama_produk' => 'PRISMA',                  'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 104, 'nama_produk' => 'PULO AIR',                'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 105, 'nama_produk' => 'Q WATER',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 106, 'nama_produk' => 'QUAVIT',                  'grade' => 'D NEW',    'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 107, 'nama_produk' => 'QUELLE (BR)',             'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 108, 'nama_produk' => 'QUAZAM',                  'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 109, 'nama_produk' => 'QIANA',                   'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 110, 'nama_produk' => 'RAMUS',                   'grade' => 'A.1.V-4',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 111, 'nama_produk' => 'RON 88 ASTRO',            'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 112, 'nama_produk' => 'RON 88 PANFILA',          'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 113, 'nama_produk' => 'ROYAL',                   'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 114, 'nama_produk' => 'SALIMDO',                 'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 115, 'nama_produk' => 'SANKEY',                  'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 116, 'nama_produk' => 'SCINTILA',                'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 117, 'nama_produk' => 'SMS',                     'grade' => 'A+',       'berat' => 800, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 118, 'nama_produk' => 'SIERRA',                  'grade' => 'B.2.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 119, 'nama_produk' => 'SAJUAK',                  'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 120, 'nama_produk' => 'SIPP',                    'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 121, 'nama_produk' => 'TDS NOL',                 'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 122, 'nama_produk' => 'SUMMIT',                  'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 123, 'nama_produk' => 'T WATER',                 'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 124, 'nama_produk' => 'TRIPANCA',                'grade' => 'A+',       'berat' => 750, 'wh_oro' => 760,  'wh_bam' => 0],
    ['nomor' => 125, 'nama_produk' => 'THE LIVING WATER',        'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 126, 'nama_produk' => 'UMAXX',                   'grade' => 'C.1.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 127, 'nama_produk' => 'UFIA',                    'grade' => 'B.3.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 128, 'nama_produk' => 'UIN CYBER',               'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 129, 'nama_produk' => 'VIONA',                   'grade' => 'C.1.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 130, 'nama_produk' => 'VIVIAN',                  'grade' => 'C.3.B-2',  'berat' => 780, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 131, 'nama_produk' => 'VIZ',                     'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 132, 'nama_produk' => 'VIVA SELEBRITY',          'grade' => 'D.0.V-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 133, 'nama_produk' => 'VIVA SELEBRITY',          'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 134, 'nama_produk' => 'WATERSTORE',              'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 135, 'nama_produk' => 'WIGO',                    'grade' => 'D.0.B-2',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 136, 'nama_produk' => 'WINRO',                   'grade' => 'C.2.B-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 137, 'nama_produk' => 'YOJI',                    'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 138, 'nama_produk' => 'YORA',                    'grade' => 'A+',       'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
    ['nomor' => 139, 'nama_produk' => 'ZEN',                     'grade' => 'A.1.V-3',  'berat' => 750, 'wh_oro' => 0,    'wh_bam' => 0],
];
// JSON untuk JS autofill
$produkGalonJson = json_encode(array_values($produkGalon));
?>

<style>
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -360px;
        width: 360px;
        height: 100vh;
        background: #fff;
        z-index: 1100;
        box-shadow: -5px 0 15px rgba(0, 0, 0, .1);
        transition: all .3s ease;
        display: flex;
        flex-direction: column
    }

    .filter-sidebar.active {
        right: 0
    }

    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, .5);
        z-index: 1090;
        display: none;
        backdrop-filter: blur(2px)
    }

    .filter-overlay.active {
        display: block
    }

    .filter-header {
        padding: 1.25rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fc
    }

    .filter-body {
        padding: 1.5rem;
        flex-grow: 1;
        overflow-y: auto
    }

    .filter-footer {
        padding: 1.25rem;
        border-top: 1px solid #e3e6f0;
        background: #f8f9fc;
        display: flex;
        gap: 10px
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        background: #eaecf4;
        padding: .25rem .75rem;
        border-radius: 20px;
        font-size: .85rem;
        margin-right: .5rem;
        margin-bottom: .5rem
    }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('ppic/partials/machine_links', ['current_machine' => $current_machine]) ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <a href="<?= base_url('ppic') ?>" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-primary btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
                <button type="button" class="btn btn-success btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#pasteImportModal">
                    <i class="fas fa-paste"></i> Paste dari Excel
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleFilter(true)">
                    <i class="fas fa-filter"></i> Filter
                    <?php if (!empty($start_date) || !empty($end_date)): ?>
                        <span class="badge badge-primary ml-1">!</span>
                    <?php endif; ?>
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#trashModalFg">
                    <i class="fas fa-trash-restore"></i> Sampah
                </button>
            </div>
            <form method="get" action="<?= base_url($baseUrl) ?>" class="d-flex align-items-center mb-0 mt-2 mt-md-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" style="min-width:200px" placeholder="Cari data..." value="<?= esc($q ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <?php if (!empty($start_date) || !empty($end_date)): ?>
            <div class="mt-2">
                <?php if (!empty($start_date)): ?><span class="filter-chip">Mulai: <?= esc($start_date) ?></span><?php endif; ?>
                <?php if (!empty($end_date)): ?><span class="filter-chip">Selesai: <?= esc($end_date) ?></span><?php endif; ?>
                <a href="<?= base_url($baseUrl) ?>" class="text-danger small ml-2"><i class="fas fa-times"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>

        <!-- Total Summary Cards -->
        <div class="d-flex gap-2 mb-3">
            <div class="bg-white border rounded px-3 py-2 d-flex align-items-center flex-fill" style="border-left:4px solid #f6c23e!important;gap:12px">
                <i class="fas fa-warehouse text-warning" style="font-size:1rem;opacity:.7"></i>
                <div>
                    <div style="font-size:.65rem;font-weight:700;color:#f6c23e;text-transform:uppercase;letter-spacing:.05em">WH ORO</div>
                    <div style="font-size:.9rem;font-weight:700;line-height:1"><?= number_format($totals['wh_oro'] ?? 0) ?></div>
                </div>
            </div>
            <div class="bg-white border rounded px-3 py-2 d-flex align-items-center flex-fill" style="border-left:4px solid #36b9cc!important;gap:12px">
                <i class="fas fa-warehouse text-info" style="font-size:1rem;opacity:.7"></i>
                <div>
                    <div style="font-size:.65rem;font-weight:700;color:#36b9cc;text-transform:uppercase;letter-spacing:.05em">WH BAM</div>
                    <div style="font-size:.9rem;font-weight:700;line-height:1"><?= number_format($totals['wh_bam'] ?? 0) ?></div>
                </div>
            </div>
            <div class="bg-white border rounded px-3 py-2 d-flex align-items-center flex-fill" style="border-left:4px solid #1cc88a!important;gap:12px">
                <i class="fas fa-calculator text-success" style="font-size:1rem;opacity:.7"></i>
                <div>
                    <div style="font-size:.65rem;font-weight:700;color:#1cc88a;text-transform:uppercase;letter-spacing:.05em">Grand Total (ORO + BAM)</div>
                    <div style="font-size:.9rem;font-weight:700;line-height:1"><?= number_format(($grand_totals['wh_oro'] ?? 0) + ($grand_totals['wh_bam'] ?? 0)) ?></div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Grade</th>
                        <th>Berat</th>
                        <th>WH ORO</th>
                        <th>WH BAM</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_list)): ?>
                        <?php foreach ($data_list as $item): ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'])) ?></td>
                                <td><?= esc($item['nomor'] ?? '-') ?></td>
                                <td><?= esc($item['nama_produk'] ?? '-') ?></td>
                                <td><?= esc($item['grade'] ?? '-') ?></td>
                                <td><?= esc($item['berat'] ?? '-') ?></td>
                                <td><?= esc($item['wh_oro'] ?? '-') ?></td>
                                <td><?= esc($item['wh_bam'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="setEditModal(this)" data-bs-toggle="modal" data-bs-target="#editDataModal"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-bagian="<?= esc($item['bagian'] ?? '') ?>"
                                            data-nomor="<?= esc($item['nomor'] ?? '') ?>"
                                            data-nama_produk="<?= esc($item['nama_produk'] ?? '') ?>"
                                            data-grade="<?= esc($item['grade'] ?? '') ?>"
                                            data-berat="<?= esc($item['berat'] ?? '') ?>"
                                            data-wh_oro="<?= esc($item['wh_oro'] ?? '') ?>"
                                            data-wh_bam="<?= esc($item['wh_bam'] ?? '') ?>"
                                            data-tanggal="<?= esc($item['tanggal'] ?? '') ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-nama="<?= esc($item['nama_produk'] ?? '') ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="thead-light font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">TOTAL</td>
                        <td class="text-warning"><?= number_format($totals['wh_oro'] ?? 0) ?></td>
                        <td class="text-info"><?= number_format($totals['wh_bam'] ?? 0) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <div class="text-muted small">
                Total: <strong><?= count($data_list ?? []) ?></strong> entri
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Finish Good Galon</h5><button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('ppic/finish_good/galon/tambah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Tanggal <span class="text-danger">*</span></label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" placeholder="Nomor..."></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Nama Produk</label>
                                <div class="input-group">
                                    <select class="form-control select2-produk" name="nama_produk" id="add_nama_produk">
                                        <option value="">-- Pilih Produk --</option>
                                        <?php foreach ($produkGalon as $p): ?>
                                            <option value="<?= esc($p['nama_produk']) ?>"
                                                data-nomor="<?= esc($p['nomor']) ?>"
                                                data-grade="<?= esc($p['grade']) ?>"
                                                data-berat="<?= esc($p['berat']) ?>"
                                                data-wh_oro="<?= esc($p['wh_oro']) ?>"
                                                data-wh_bam="<?= esc($p['wh_bam']) ?>">
                                                <?= esc($p['nomor'] . '. ' . $p['nama_produk'] . ' — ' . $p['grade']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addProdukModal" title="Tambah produk baru">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Grade</label><input type="text" class="form-control" name="grade" id="add_grade" placeholder="Grade..."></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Berat</label><input type="text" class="form-control" name="berat" id="add_berat" placeholder="Berat..."></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>WH ORO</label><input type="number" class="form-control" name="wh_oro" id="add_wh_oro" placeholder="0"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>WH BAM</label><input type="number" class="form-control" name="wh_bam" id="add_wh_bam" placeholder="0"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2 text-warning"></i>Edit Finish Good Galon</h5><button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" id="edit_nomor"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Nama Produk</label>
                                <select class="form-control select2-produk" name="nama_produk" id="edit_nama_produk">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach ($produkGalon as $p): ?>
                                        <option value="<?= esc($p['nama_produk']) ?>"
                                            data-nomor="<?= esc($p['nomor']) ?>"
                                            data-grade="<?= esc($p['grade']) ?>"
                                            data-berat="<?= esc($p['berat']) ?>"
                                            data-wh_oro="<?= esc($p['wh_oro']) ?>"
                                            data-wh_bam="<?= esc($p['wh_bam']) ?>">
                                            <?= esc($p['nomor'] . '. ' . $p['nama_produk'] . ' — ' . $p['grade']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Grade</label><input type="text" class="form-control" name="grade" id="edit_grade"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Berat</label><input type="text" class="form-control" name="berat" id="edit_berat"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>WH ORO</label><input type="number" class="form-control" name="wh_oro" id="edit_wh_oro"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>WH BAM</label><input type="number" class="form-control" name="wh_bam" id="edit_wh_bam"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formDelete" method="post" action="<?= base_url('ppic/finish_good/galon/delete/0') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                    <p id="delete_message" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Sidebar -->
<div class="filter-overlay" onclick="toggleFilter(false)"></div>
<div class="filter-sidebar">
    <div class="filter-header">
        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter</h5>
        <button type="button" class="close" onclick="toggleFilter(false)">&times;</button>
    </div>
    <form method="get" action="<?= base_url($baseUrl) ?>">
        <div class="filter-body">
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <div class="form-group"><label class="small font-weight-bold">Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="<?= esc($start_date ?? '') ?>"></div>
            <div class="form-group"><label class="small font-weight-bold">Tanggal Selesai</label><input type="date" name="end_date" class="form-control" value="<?= esc($end_date ?? '') ?>"></div>
        </div>
        <div class="filter-footer">
            <a href="<?= base_url($baseUrl) ?>" class="btn btn-secondary flex-fill">Reset</a>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </form>
</div>

<script>
    function toggleFilter(show) {
        document.querySelector('.filter-sidebar').classList.toggle('active', show);
        document.querySelector('.filter-overlay').classList.toggle('active', show);
    }

    function setEditModal(btn) {
        const d = btn.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('edit_tanggal').value = d.tanggal || '';
        document.getElementById('edit_bagian').value = d.bagian || '';
        document.getElementById('edit_nomor').value = d.nomor || '';
        const selProduk = document.getElementById('edit_nama_produk');
        selProduk.value = d.nama_produk || '';
        if (selProduk.value === '' && d.nama_produk) {
            const opt = new Option(d.nama_produk, d.nama_produk, true, true);
            selProduk.add(opt);
        }
        if (typeof $(selProduk).select2 === 'function') $(selProduk).trigger('change');
        document.getElementById('edit_grade').value = d.grade || '';
        document.getElementById('edit_berat').value = d.berat || '';
        document.getElementById('edit_wh_oro').value = d.wh_oro || '';
        document.getElementById('edit_wh_bam').value = d.wh_bam || '';
        document.getElementById('formEdit').action = '<?= base_url('ppic/finish_good/galon/update') ?>/' + d.id;
    }

    // Load Trash Data
    function loadTrashGalon() {
        const tbody = document.querySelector('#trashTableGalon tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';

        fetch('<?= base_url('ppic/finish_good/galon/get-trash') ?>')
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                tbody.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Tempat sampah kosong.</td></tr>';
                    return;
                }
                data.forEach(item => {
                    tbody.insertAdjacentHTML('beforeend', `<tr style="font-size:13px">
                    <td class="px-3 py-2">
                        <div class="font-weight-bold">${item.nama_produk || '-'}</div>
                        <div class="text-muted" style="font-size:11px">Grade: ${item.grade || '-'}</div>
                    </td>
                    <td class="py-2">${item.nomor || '-'}</td>
                    <td class="py-2 text-muted text-center">${item.deleted_at}</td>
                    <td class="text-end py-2 px-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="<?= base_url('ppic/finish_good/galon/restore') ?>/${item.id}" class="btn btn-success btn-xs px-2" style="font-size:10px;padding:2px 8px" title="Pulihkan"><i class="fas fa-undo"></i></a>
                            <a href="<?= base_url('ppic/finish_good/galon/delete-permanent') ?>/${item.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size:10px;padding:2px 8px" title="Hapus Permanen" onclick="return confirm('Hapus permanen? Tidak dapat dikembalikan!')"><i class="fas fa-times"></i></a>
                        </div>
                    </td>
                </tr>`);
                });
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-danger">Gagal memuat: ' + err.message + '</td></tr>';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init Select2 for produk dropdowns
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-produk').select2({
                width: '100%',
                placeholder: '-- Pilih Produk --',
                allowClear: true,
                dropdownParent: $('body')
            });
        }

        // Autofill form tambah saat produk dipilih
        const addProduk = document.getElementById('add_nama_produk');
        if (addProduk) {
            $(addProduk).on('change', function() {
                const sel = this.options[this.selectedIndex];
                if (!sel || !sel.value) return;
                const nomor = sel.getAttribute('data-nomor') || '';
                const grade = sel.getAttribute('data-grade') || '';
                const berat = sel.getAttribute('data-berat') || '';
                const whOro = sel.getAttribute('data-wh_oro') || '';
                const whBam = sel.getAttribute('data-wh_bam') || '';
                const nomorEl = document.querySelector('#addDataModal [name="nomor"]');
                if (nomorEl && !nomorEl.value) nomorEl.value = nomor;
                document.getElementById('add_grade').value = grade;
                document.getElementById('add_berat').value = berat;
                document.getElementById('add_wh_oro').value = whOro;
                document.getElementById('add_wh_bam').value = whBam;
            });
        }

        // Delete modal
        const deleteModal = document.getElementById('modalDelete');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const nama = btn.getAttribute('data-nama') || 'data ini';
                document.getElementById('delete_id').value = id;
                document.getElementById('delete_message').textContent = 'Hapus "' + nama + '"?';
                document.getElementById('formDelete').action = '<?= base_url('ppic/finish_good/galon/delete') ?>/' + id;
            });
        }

        // Trash modal
        const trashModal = document.getElementById('trashModalFg');
        if (trashModal) {
            trashModal.addEventListener('show.bs.modal', loadTrashGalon);
        }
    });
</script>

<!-- Trash Modal -->
<div class="modal fade" id="trashModalFg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah - Finish Good Galon
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="trashTableGalon" style="min-width:auto">
                        <thead class="bg-white text-xs text-muted text-uppercase">
                            <tr>
                                <th class="px-3 py-3" style="width:35%">Nama Produk</th>
                                <th class="py-3" style="width:20%">Nomor</th>
                                <th class="py-3 text-center" style="width:25%">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width:20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Klik tombol untuk memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= view('ppic/partials/paste_import_modal', ['type' => 'galon', 'type_label' => 'Galon']) ?>

<?= view('ppic/partials/add_produk_modal', ['type' => 'galon', 'type_label' => 'Galon']) ?>