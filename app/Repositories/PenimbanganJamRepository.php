<?php

namespace App\Repositories;

use App\Models\MPenimbanganJam;

/**
 * Repository: PenimbanganJamRepository
 * 
 * Provides business logic abstraction for penimbangan_jam operations.
 * Decouples controllers from model implementation details.
 * 
 * Key responsibilities:
 * - Data validation and transformation
 * - Business rule enforcement
 * - Transaction management
 * - Error handling
 */
class PenimbanganJamRepository
{
    protected $model;

    public function __construct(MPenimbanganJam $model = null)
    {
        $this->model = $model ?? new MPenimbanganJam();
    }

    /**
     * Create or update penimbangan data for a becum machine
     * 
     * Input format from form:
     * ```
     * penimbangan_jam => [
     *     'jam_1_galon' => '10.5',
     *     'jam_1_flash' => '8.2',
     *     'jam_2_galon' => '12.0',
     *     'jam_2_flash' => '9.5',
     *     ...
     *     'jam_8_galon' => '11.0',
     *     'jam_8_flash' => '8.8',
     * ]
     * ```
     * 
     * @param int $becumId
     * @param array $formData Raw form data with 'penimbangan_jam' key
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public function savePenimbanganFromForm(int $becumId, array $formData): array
    {
        try {
            // Extract penimbangan data from form
            $penimbanganData = $formData['penimbangan_jam'] ?? [];

            if (empty($penimbanganData)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data penimbangan untuk disimpan',
                    'data' => null,
                ];
            }

            // Parse form data to hour format
            $hoursData = $this->parseFormDataToHours($penimbanganData);

            // Validate that we have all 8 hours
            if (count($hoursData) !== 8) {
                return [
                    'success' => false,
                    'message' => 'Data penimbangan harus lengkap untuk 8 jam',
                    'data' => null,
                ];
            }

            // Save to database
            $this->model->savePenimbanganData($becumId, $hoursData);

            return [
                'success' => true,
                'message' => 'Data penimbangan berhasil disimpan',
                'data' => $hoursData,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Parse form data (flat key-value) to hour-indexed format
     * 
     * Input:  ['jam_1_galon' => 10, 'jam_1_flash' => 8, 'jam_2_galon' => 12, ...]
     * Output: [1 => ['galon' => 10, 'flash' => 8], 2 => ['galon' => 12, ...], ...]
     * 
     * @param array $formData
     * @return array Indexed by hour (1-8)
     */
    protected function parseFormDataToHours(array $formData): array
    {
        $hoursData = [];

        for ($hour = 1; $hour <= 8; $hour++) {
            $galonKey = "jam_{$hour}_galon";
            $flashKey = "jam_{$hour}_flash";

            $hoursData[$hour] = [
                'galon' => isset($formData[$galonKey]) ? (float)$formData[$galonKey] : 0,
                'flash' => isset($formData[$flashKey]) ? (float)$formData[$flashKey] : 0,
            ];
        }

        return $hoursData;
    }

    /**
     * Get penimbangan data in format suitable for form prefill
     * 
     * @param int $becumId
     * @return array Flat format: ['jam_1_galon' => 10, 'jam_1_flash' => 8, ...]
     */
    public function getPenimbanganForForm(int $becumId): array
    {
        $penimbanganByHour = $this->model->getPenimbanganByBecumId($becumId);

        $formData = [];
        for ($hour = 1; $hour <= 8; $hour++) {
            $data = $penimbanganByHour[$hour] ?? ['galon' => 0, 'flash' => 0];
            $formData["jam_{$hour}_galon"] = $data['galon'] ?? 0;
            $formData["jam_{$hour}_flash"] = $data['flash'] ?? 0;
        }

        return $formData;
    }

    /**
     * Get penimbangan data in API format
     * 
     * @param int $becumId
     * @return array Hour-indexed array: [1 => ['hour' => 1, 'galon' => 10, ...], ...]
     */
    public function getPenimbanganForApi(int $becumId): array
    {
        $penimbanganByHour = $this->model->getPenimbanganByBecumId($becumId);
        return MPenimbanganJam::formatForApi($penimbanganByHour);
    }

    /**
     * Validate penimbangan form data
     * 
     * @param array $formData
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validatePenimbanganForm(array $formData): array
    {
        $penimbanganData = $formData['penimbangan_jam'] ?? [];
        $errors = [];

        // Check if data exists
        if (empty($penimbanganData)) {
            $errors['penimbangan_jam'] = 'Data penimbangan tidak boleh kosong';
            return ['valid' => false, 'errors' => $errors];
        }

        // Validate each hour
        for ($hour = 1; $hour <= 8; $hour++) {
            $galonKey = "jam_{$hour}_galon";
            $flashKey = "jam_{$hour}_flash";

            $galon = isset($penimbanganData[$galonKey]) ? (float)$penimbanganData[$galonKey] : 0;
            $flash = isset($penimbanganData[$flashKey]) ? (float)$penimbanganData[$flashKey] : 0;

            // Validate ranges
            if ($galon < 0 || $galon > 999.99) {
                $errors[$galonKey] = "Galon jam {$hour} harus antara 0 dan 999.99";
            }

            if ($flash < 0 || $flash > 999.99) {
                $errors[$flashKey] = "Flash jam {$hour} harus antara 0 dan 999.99";
            }

            // Optional: Require at least one value per hour
            // if ($galon == 0 && $flash == 0) {
            //     $errors[$galonKey] = "Minimal salah satu nilai (galon atau flash) harus diisi untuk jam {$hour}";
            // }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Delete penimbangan data for a becum machine
     * 
     * @param int $becumId
     * @return bool
     */
    public function deletePenimbangan(int $becumId): bool
    {
        return $this->model->deletePenimbanganByBecum($becumId);
    }

    /**
     * Get penimbangan statistics
     * 
     * @param int $becumId
     * @return array|null
     */
    public function getPenimbanganStats(int $becumId): ?array
    {
        return $this->model->getPenimbanganStats($becumId);
    }

    /**
     * Create default penimbangan data structure (all zeros)
     * Used for initializing new forms
     * 
     * @return array Format: ['jam_1_galon' => 0, 'jam_1_flash' => 0, ...]
     */
    public static function getDefaultPenimbanganData(): array
    {
        $data = [];
        for ($hour = 1; $hour <= 8; $hour++) {
            $data["jam_{$hour}_galon"] = 0;
            $data["jam_{$hour}_flash"] = 0;
        }
        return $data;
    }
}
