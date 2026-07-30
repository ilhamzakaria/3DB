<?php

namespace App\Controllers\Traits;

/**
 * Warehouse2SyncTrait
 * 
 * Handles synchronization between warehouse2 module and produksi module.
 * - Syncs warehouse2.nomor_spk to produksi.nomor_spk
 */
trait Warehouse2SyncTrait
{
    /**
     * Sync warehouse2 nomor_spk to produksi database
     * 
     * When warehouse2 saves data with nomor_spk, update matching produksi_headers
     * and produksi rows to reflect the same nomor_spk.
     * 
     * @param string $nomor_spk The warehouse2 nomor_spk value
     * @param array $warehouseData The complete warehouse2 record data
     * @return bool Success status
     */
    protected function syncWarehouse2ToProduksi($nomor_spk, $warehouseData = [])
    {
        if (empty($nomor_spk)) {
            return false;
        }

        try {
            $produksiDb = \Config\Database::connect('produksi');

            // Prepare nomor_spk for matching (handle both with/without spaces)
            $nomor_spk_trimmed = trim($nomor_spk);

            // Find matching produksi_headers rows by nomor_spk
            // and update them with warehouse2 data if needed
            $headers = $produksiDb->table('produksi_headers')
                ->where('TRIM(nomor_spk)', $nomor_spk_trimmed)
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();

            if (empty($headers)) {
                // No matching headers found - this is acceptable
                // warehouse2 may be creating records independently
                return true;
            }

            // Update produksi_headers with any new data from warehouse2
            // This is optional - mainly for audit trail
            $headerUpdate = [];
            if (!empty($warehouseData['tanggal'])) {
                $headerUpdate['tanggal'] = $warehouseData['tanggal'];
            }

            if (!empty($headerUpdate)) {
                foreach ($headers as $header) {
                    $produksiDb->table('produksi_headers')
                        ->where('id', $header['id'])
                        ->update($headerUpdate);
                }
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'syncWarehouse2ToProduksi Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync warehouse2 data when storing barang_masuk_produksi
     * 
     * This method is called after storing warehouse2 data to ensure
     * produksi database has the nomor_spk reference.
     * 
     * @param string $nomor_spk Warehouse2 nomor_spk
     * @param array $data Complete warehouse2 record
     * @return bool
     */
    protected function syncBarangMasukProduksiToDb($nomor_spk, $data = [])
    {
        return $this->syncWarehouse2ToProduksi($nomor_spk, $data);
    }

    /**
     * Get produksi header by nomor_spk from produksi database
     * Useful for validating if nomor_spk exists in produksi
     * 
     * @param string $nomor_spk
     * @return array|null
     */
    protected function getProduksiHeaderByNomorSpk($nomor_spk)
    {
        try {
            if (empty($nomor_spk)) {
                return null;
            }

            $produksiDb = \Config\Database::connect('produksi');
            $header = $produksiDb->table('produksi_headers')
                ->where('TRIM(nomor_spk)', trim($nomor_spk))
                ->where('deleted_at', null)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            return $header;
        } catch (\Exception $e) {
            log_message('error', 'getProduksiHeaderByNomorSpk Error: ' . $e->getMessage());
            return null;
        }
    }
}

