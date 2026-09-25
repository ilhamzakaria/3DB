<?php

namespace App\Controllers\Traits;

trait MesinModuleTrait
{
    abstract protected function mesinSlug(): string;

    protected function isPpicContext(): bool
    {
        return uri_string() === 'ppic' || str_starts_with(uri_string(), 'ppic/');
    }

    /**
     * Returns the appropriate model instance for the current context.
     *
     * Each controller sets:
     *   $this->MMesin     — gudang model (BaseGudangModel / orogudang)
     *   $this->MMesinPpic — ppic model   (BasePpicMesinModel / oroppic)
     *
     * When accessed from the ppic route the ppic model is used so that
     * reads and writes go to oroppic instead of orogudang.
     */
    protected function activeMesin()
    {
        if ($this->isPpicContext() && isset($this->MMesinPpic)) {
            return $this->MMesinPpic;
        }
        return $this->MMesin;
    }

    protected function indexRoute(): string
    {
        if ($this->isPpicContext()) {
            return '/ppic/mesin_' . str_replace('mesin_', '', $this->mesinSlug());
        }
        return '/gudang/' . $this->mesinSlug();
    }

    protected function canEditMesin(): bool
    {
        $role = session()->get('role');
        if ($role === 'admin') return true;
        if ($role === 'manager') return true;
        if ($role === 'manager_ppic') return true;
        if ($this->isPpicContext()) return $role === 'ppic';
        return $role === 'gudang';
    }

    protected function mesinViewContext(): array
    {
        $isPpic = $this->isPpicContext();
        $slug   = $this->mesinSlug(); // e.g. "mesin_becum"
        $m      = str_replace('mesin_', '', $slug); // e.g. "becum"

        return [
            'is_ppic_context' => $isPpic,
            'back_url'        => $isPpic ? 'ppic' : 'gudang',
            'can_edit'        => $this->canEditMesin(),
            'current_machine' => $slug,  // Add this explicitly
            'export_url'      => $isPpic
                ? 'ppic/export/mesin_' . $m   // → /ppic/export/mesin_becum
                : 'export/' . $slug,           // → /export/mesin_becum (gudang)
            'pager_group'     => $slug,
        ];
    }

    /**
     * Fields baru yang sama untuk semua mesin — ambil dari POST.
     */
    protected function getNewCommonFields(): array
    {
        return [
            'jam'         => trim((string) $this->request->getPost('jam')),
            'nama_mesin'  => trim((string) $this->request->getPost('nama_mesin')),
            'nama_produk' => trim((string) $this->request->getPost('nama_produk')),
            'nomor_mesin' => trim((string) $this->request->getPost('nomor_mesin')),
            'operator'    => trim((string) $this->request->getPost('operator')),
            'target'      => trim((string) $this->request->getPost('target')),
            'realisasi'   => trim((string) $this->request->getPost('realisasi')),
            'revisi'      => trim((string) $this->request->getPost('revisi')),
        ];
    }

    // ----------------------------------------------------------------
    // Soft Delete
    // ----------------------------------------------------------------

    public function delete($id = null)
    {
        $id = $id ?: $this->request->getPost('id');

        // Before deleting the mesin record, also delete related penimbangan data
        // (This is redundant with CASCADE DELETE at DB level, but ensures consistency)
        if (isset($this->MMesin) && method_exists($this->MMesin, 'getTable')) {
            $table = $this->MMesin->getTable();
            
            // If this is a mesin_becum table, delete its penimbangan records
            if (strpos($table, 'mesin_becum') !== false) {
                try {
                    $db = $this->isPpicContext() ? $this->dbPpic() : $this->dbGudang();
                    $db->table('penimbangan_jam')
                        ->where('becum_id', (int)$id)
                        ->delete();
                    
                    log_message('info', "Deleted penimbangan_jam records for becum_id={$id}");
                } catch (\Throwable $e) {
                    log_message('error', "Failed to delete penimbangan_jam for becum_id={$id}: " . $e->getMessage());
                    // Continue with mesin delete even if penimbangan delete fails
                }
            }
        }

        if ($this->activeMesin()->delete($id)) {
            session()->setFlashdata('success', 'Data dipindahkan ke tempat sampah');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data');
        }

        return redirect()->to($this->indexRoute());
    }

    public function restore($id)
    {
        // Bypass soft-delete guard with a direct DB query on the correct database.
        $db = $this->isPpicContext() ? $this->dbPpic() : $this->dbGudang();
        $db->table($this->activeMesin()->getTable())
            ->where('id', (int) $id)
            ->update(['deleted_at' => null]);

        session()->setFlashdata('success', 'Data berhasil dipulihkan');
        return redirect()->to($this->indexRoute());
    }

    public function deletePermanent($id)
    {
        // Before permanently deleting the mesin record, also delete related penimbangan data
        if (isset($this->MMesin) && method_exists($this->MMesin, 'getTable')) {
            $table = $this->MMesin->getTable();
            
            // If this is a mesin_becum table, delete its penimbangan records
            if (strpos($table, 'mesin_becum') !== false) {
                try {
                    $db = $this->isPpicContext() ? $this->dbPpic() : $this->dbGudang();
                    $db->table('penimbangan_jam')
                        ->where('becum_id', (int)$id)
                        ->delete();
                    
                    log_message('info', "Permanently deleted penimbangan_jam records for becum_id={$id}");
                } catch (\Throwable $e) {
                    log_message('error', "Failed to permanently delete penimbangan_jam for becum_id={$id}: " . $e->getMessage());
                    // Continue with mesin delete even if penimbangan delete fails
                }
            }
        }

        $this->activeMesin()->delete((int) $id, true);
        session()->setFlashdata('success', 'Data berhasil dihapus permanen');
        return redirect()->to($this->indexRoute());
    }

    public function getTrash()
    {
        $trash = $this->activeMesin()
            ->onlyDeleted()
            ->orderBy('deleted_at', 'DESC')
            ->findAll();

        return $this->response->setJSON($trash);
    }

    // ----------------------------------------------------------------
    // SPK Completion — PPIC → Produksi sync
    // ----------------------------------------------------------------

    /**
     * Toggle selesai_spk on the PPIC mesin row and — when marking as selesai —
     * propagate the status to every matching row in produksi_headers so the
     * production module stays in sync automatically.
     *
     * Route: POST /ppic/mesin_<slug>/completeSpk/<id>
     *        POST /gudang/mesin_<slug>/completeSpk/<id>
     *
     * The sync only runs in the PPIC context; the gudang context toggles the
     * mesin row flag only (no produksi_headers involved there).
     */
    public function completeSpk($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to($this->indexRoute());
        }

        $id = $id ?: $this->request->getPost('id');

        $currentData = $this->activeMesin()->find($id);
        if (!$currentData) {
            session()->setFlashdata('error', 'Data SPK tidak ditemukan');
            return redirect()->to($this->indexRoute());
        }

        $newStatus  = $currentData['selesai_spk'] == 1 ? 0 : 1;
        $statusText = $newStatus === 1 ? 'selesai' : 'belum selesai';
        $noSpk      = trim((string) ($currentData['no_spk'] ?? ''));

        if ($this->activeMesin()->update($id, ['selesai_spk' => $newStatus])) {
            // Mirror the status change to produksi_headers when in PPIC context.
            if ($this->isPpicContext() && $noSpk !== '') {
                $this->syncSpkStatusToProduksi($noSpk, $newStatus);
            }

            session()->setFlashdata('success', "SPK {$noSpk} berhasil diubah menjadi {$statusText}");
        } else {
            session()->setFlashdata('error', 'Gagal mengubah status SPK');
        }

        return redirect()->to($this->indexRoute());
    }

    /**
     * Update selesai_spk on all produksi_headers rows that share the same
     * nomor_spk.  SPK values may be stored as an integer (e.g. 2926) in the
     * PPIC mesin tables but as a trimmed string in produksi_headers, so we
     * build a small candidate list covering both forms.
     *
     * This is a best-effort operation: any DB error is logged but does NOT
     * roll back the mesin row update that already succeeded.
     */
    protected function syncSpkStatusToProduksi(string $noSpk, int $selesai): void
    {
        if ($noSpk === '') {
            return;
        }

        // Build candidate strings: the raw value plus its plain-integer form.
        $candidates = array_values(array_unique(array_filter(
            [$noSpk, (string) (int) $noSpk],
            static fn($s) => $s !== '' && $s !== '0'
        )));

        if (empty($candidates)) {
            return;
        }

        try {
            $db = $this->dbProduksi();

            // Use a raw IN() so we cover both "2926" and " 2926 " variants via TRIM.
            $placeholders = implode(',', array_fill(0, count($candidates), '?'));

            $db->query(
                "UPDATE produksi_headers
                    SET selesai_spk = ?
                  WHERE deleted_at IS NULL
                    AND TRIM(nomor_spk) IN ({$placeholders})",
                array_merge([$selesai], $candidates)
            );

            $affected = $db->affectedRows();
            log_message(
                'info',
                "syncSpkStatusToProduksi: SPK={$noSpk} selesai={$selesai} updated {$affected} row(s) in produksi_headers"
            );
        } catch (\Throwable $e) {
            // Non-fatal: log and continue — the mesin row is already saved.
            log_message(
                'error',
                "syncSpkStatusToProduksi: failed for SPK={$noSpk}: " . $e->getMessage()
            );
        }
    }
}
