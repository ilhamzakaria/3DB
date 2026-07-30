<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndExtraShiftsToMesinTables extends Migration
{
    private array $tables = [
        'mesin_powerjet1', 'mesin_powerjet2',
        'mesin_ccm1',      'mesin_ccm2',
        'mesin_ips1',      'mesin_ips2',
        'mesin_ips3',      'mesin_ips4',
    ];

    public function up(): void
    {
        foreach (['gudang', 'ppic'] as $dbGroup) {
            $db = db_connect($dbGroup);
            foreach ($this->tables as $table) {
                if (!$db->fieldExists('status', $table)) {
                    $db->query("ALTER TABLE `{$table}` ADD COLUMN `status` VARCHAR(100) NULL DEFAULT NULL AFTER `shif`");
                }
                if (!$db->fieldExists('extra_target_shifts', $table)) {
                    $db->query("ALTER TABLE `{$table}` ADD COLUMN `extra_target_shifts` TEXT NULL DEFAULT NULL AFTER `target_shift3`");
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['gudang', 'ppic'] as $dbGroup) {
            $db = db_connect($dbGroup);
            foreach ($this->tables as $table) {
                if ($db->fieldExists('status', $table)) {
                    $db->query("ALTER TABLE `{$table}` DROP COLUMN `status`");
                }
                if ($db->fieldExists('extra_target_shifts', $table)) {
                    $db->query("ALTER TABLE `{$table}` DROP COLUMN `extra_target_shifts`");
                }
            }
        }
    }
}
