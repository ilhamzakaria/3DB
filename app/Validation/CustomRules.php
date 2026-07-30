<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Custom validation rule to ensure target_akhir is not before target_awal.
     *
     * @param string      $str    The value of the field being validated (target_akhir)
     * @param string      $fields The parameter passed to the rule (not used here)
     * @param array       $data   The array of all data submitted
     * @param string|null $error  Custom error message reference
     * @return bool
     */
    public function target_akhir_after_target_awal(string $str, ?string $fields = null, array $data = [], ?string &$error = null): bool
    {
        if (empty($data['target_awal']) || empty($data['target_akhir'])) {
            return true;
        }

        $start = strtotime($data['target_awal']);
        $end   = strtotime($data['target_akhir']);

        if ($end === false || $start === false) {
            return true; // Let valid_date rule handle invalid date format
        }

        if ($end < $start) {
            return false;
        }

        return true;
    }

    /**
     * Custom validation rule to ensure realisasi_akhir is not before realisasi_awal.
     */
    public function realisasi_akhir_after_realisasi_awal(string $str, ?string $fields = null, array $data = [], ?string &$error = null): bool
    {
        if (empty($data['realisasi_awal']) || empty($data['realisasi_akhir'])) {
            return true;
        }

        $start = strtotime($data['realisasi_awal']);
        $end   = strtotime($data['realisasi_akhir']);

        if ($end === false || $start === false) {
            return true; // Let valid_date rule handle invalid date format
        }

        if ($end < $start) {
            return false;
        }

        return true;
    }

    /**
     * Custom validation rule to ensure realisasi_awal is not before target_awal.
     */
    public function realisasi_awal_after_target_awal(string $str, ?string $fields = null, array $data = [], ?string &$error = null): bool
    {
        if (empty($data['target_awal']) || empty($data['realisasi_awal'])) {
            return true;
        }

        $target = strtotime($data['target_awal']);
        $real   = strtotime($data['realisasi_awal']);

        if ($target === false || $real === false) {
            return true; // Let valid_date rule handle invalid date format
        }

        if ($real < $target) {
            return false;
        }

        return true;
    }
}
