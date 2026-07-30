<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMesinBecumBrandsTable extends Migration
{
    protected $DBGroup = 'gudang';

    // All hardcoded brands from MesinBecum::BECUM_BRANDS, keyed by grade
    private const SEED_DATA = [
        'A+'        => ['8','AQ-8','Ajwa','Ambiero','AGS','AIRO','Al- Falah','Arthess','Armio','Amidis','Astar','Atarin','Aruma','Authentic Alkali Water','Benzano','Blank','Bright','Cirill','Cairoh','Cool Plus','Dufia','El- Haadi','Good Qua','Grand','Hills','Jam','Java','Keniro','Ki Water','MQS','Maleo','Meriba','Oksitra','Patraqua','Premier Water','PrimA','Pristine','Quelle','RNI','SMS','SPA','NARITA','OXYINDO','TRIPANCA','T Water','Watermed','Yora'],
        'A'         => ['2Tang','Adikarya Okane','Aguaria','Airros','AIA','Al-AIN','Airqu','AJS','Aekta108','AlMasoem','Amidis Bandung','Amidis Semarang','Aqnu','Aquanab','Ardelia','Arona','Arlo','Arra','Aruma','Batik','Benzano','Bismi','Blue Oase','Bolesa','C181','Cairoh','Citra','Club 750','Cool Plus','Desqua','Dufia','Elana','Fajar','Fresh','Good Qua','Grand','Gunung','HOKI899','Hanaang','Hanun','Jam','Jatiluhur','Jeera','Jesslyn','JQ','Karya Umat','Khoyiir','Kiitafit','Lamira','MQS','Mountoya','New Tiara','NISA OXY','Ocean','Origen','Oso','Oso Nouveo','Oxxydav','Ozoniri','Patisa','Patraqua','Petriqua','PrimA Oro','PrimA NS','Premier Water','Pristine','Quat','Quelle','Q Water','Ron88','Sankey','Suli 5','UIN','Vania','Watermed','WaterPlus','Water Store','Yoji'],
        'A.1.V-3'   => ['168','Aguaria','AQ-8','Aerzhips','AMC','Arthess','Ayya','Ayonia','Ben Ora Salit','Blank','Blank (Frozen)','Blank (Kitafit)','Blank (Qfast)','DT Fresh','Ervina','Frozen','Hills','Kalia','Keniro','Meriba','OXYINDO','Pelangi','Pulo Air','VIVA Celebrity','Royal','Sajuak','Sehat Berkah','Sipp','TDS NOL','TGM99','The Living Water','Blank TDS','Tirta Intan','Ubarakah','U-Maxx','VANN','Wigo','Zen'],
        'A.1.V-4'   => ['Albil','Eazy','Hudro','Indomaret','Ramus'],
        'Club'      => ['Club Cianjur','Club BTA'],
        'Club.'     => ['Club Banjarmasin','Club Pandaan','Club Bogor','Club Pontianak','Club Palembang','Club BOJA'],
        'A.1.v-3'   => ['Oksitra'],
        'B'         => ['2Tang','AEKTA 108','Arthess','Arvit','Atharo','Aguaria','Al- Masoem','AMC \'','ASLI','Value Plus','Bright','Bahari','Benzano','Bolesa','Cirill','Dufia','Elqua','Fajar','Frozen','Grand','Great','HASS','Hanun','Hanaang','Hatsa','Hoki 899','JQ','Ma soem Arias"','Min-Ral','Oimantika','Pajajaran','Pelangi','Petriqua','Pretty','Quazam','Ramus','SPA','Hudro','Scintilla','Suli 5','Jati Luhur','Tripanca','UFIA','Vica','Vann','VR 9','Zodiak'],
        'B.2.B-2'   => ['Aerzhips','Air Cool','Arqu','Ahyaqua','AIA','Blank','Ben Ora Salit','BW','Celesty','Citrabass','Claras','EAZY','Gunung','GMes','Kiitafit','Ki Water','Premier Water','Quelle','Ron88','Sierra','Kalia','Ayya'],
        'B.1.B-2'   => ['AL- AIN','Blank'],
        'B.1.B-3 New' => ['Amidis Droptest','Amidis Bandung','Amidis Bawen','Amidis Pandaan'],
        'B.1.B-3'   => ['Amidis Bawen','Amidis Pandaan'],
        'B-1'       => ['Amidis','Amidis (Droptest)'],
        'B.2.B-3'   => ['Ayya'],
        'B.3.B-2'   => ['LeeQuil','Shimizu','UFIA'],
        'B.'        => ['Qiana','Quat'],
        'B+'        => ['Umaxx'],
        'C.2.B-3'   => ['212','2 Tang','AIA','Aquaran','Ayya','BISTO','Blank','Bolesa','DD Water','Delqua','Ghamir','Gunung','Pelangi','O Senangnya"','Ma soem Arias"','Quazam','UIN Cyber','Viz','Winro','Zayan'],
        'C'         => ['Adea','Airiz','Airness','Akuasis','Arthess','Avi','Benair','BMC','Bintang Mulia','700','Bumdes','Cirill','Citra','Citrabass','Club','Cool Plus','Eazy','Edina','Elqua','Estolia','Fujiro','HIDO','Inzon','Jatiluhur','Kiwater','Lamira','New Normal','Petriqua','Prisma','OK Oce','Montoya','Quat','Quelle','Salimdo','Sankey','Sofia','Super 5','Tirmas','Ufia','UIN SGD','Viro','Viva Celebrity','VTG','Yqua','Zodiak'],
        'C.3.B-2'   => ['Aekta108','Andalas','Vivian'],
        'C.1.B-3'   => ['Al Masoem','AMC','Blio','BW','Pristine TML/JT','Pristine BDG (MAM)','Pristine All Plant','Pristine DROPTEST','Pulo Air','Sajuak','Viona','Bumi Siliwangi'],
        'C.1.V-2'   => ['MPP','BLU 9','Hills','U-MAXX'],
        'C.1.V-3'   => ['Blank','Blio','Hevitro'],
        'C+'        => ['Pristine Astro','Quavit'],
        'D.0.V-2'   => ['168','AEKTA108','Berton','BW','Citra','Edura','FreshG','Hevitro','Hido','Jatiluhur','LeeQuil','Prisma','Blank','(MMP)','Qiana','VIZ','Viva Celebrity','Water Store'],
        'D'         => ['AGUA','Airmu','Air Sehat','Aquatha','Atamin','Kausar','AMC','AMS 318','Airiz','BMC','Bonanza','Cirill','Estolia','Genio','Good Qua','MDI','MMG','MC Water','Passy','PULO AIR','Ocean','OKE OCE','Q-Bic','Ravella','Sama','Senang','Vica'],
        'D.0.B-2'   => ['Aquality','(Asabaland)','Club Bogor - UI','Club Ciranjang/ cianjur','Dufia','Fountain','Great','Lamira','Lucky water','LW8','Maskua','Mountoya','Prisma','Blank','(Biru)','Sajuak','Tripanca','Water Store','TMP','Wigo'],
        'D+'        => ['Club Palembang','Club Boja'],
        'D New'     => ['Quavit'],
        'D.0.V-2 New' => ['Summit'],
        'AQUA'      => ['AQUA'],
    ];

    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'grade'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => false],
            'brand_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['grade']);
        $this->forge->createTable('mesin_becum_brands', true);

        // Seed all hardcoded brands
        $rows = [];
        $now  = date('Y-m-d H:i:s');
        foreach (self::SEED_DATA as $grade => $brands) {
            foreach ($brands as $brand) {
                $rows[] = ['grade' => $grade, 'brand_name' => $brand, 'created_at' => $now, 'updated_at' => $now];
            }
        }
        $this->db->table('mesin_becum_brands')->insertBatch($rows);
    }

    public function down()
    {
        $this->forge->dropTable('mesin_becum_brands', true);
    }
}
