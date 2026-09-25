<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: MPenimbanganJam
 * 
 * Handles CRUD operations for timed weighing data (galon & flash) per hour (1-8)
 * for Becum machine operations.
 * 
 * Data structure:
 * - id: Primary key
 * - becum_id: FK to mesin_becum
 * - hour: Hour number (1-8)
 * - galon: Weight/quantity for galon (decimal, max 99.99)
 * - flash: Weight/quantity for flash (decimal, max 99.99)
 * - timestamps: created_at, updated_at
 * 
 * Relationships:
 * - Many PenimbanganJam belong to One MesinBecum
 * - When MesinBecum is deleted, all related records cascade delete
 */
class MPenimbanganJam extends Model
{
    protected $table      = 'penimbangan_jam';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'becum_id',
        'hour',
        'galon',
        'flash',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'becum_id' => [
            'label' => 'Becum ID',
            'rules' => 'required|integer|is_not_empty',
        ],
        'hour' => [
            'label' => 'Hour',
            'rules' => 'required|integer|greater_than[0]|less_than_equal_to[8]',
        ],
        'galon' => [
            'label' => 'Galon',
            'rules' => 'required_with[flash]|numeric|greater_than_equal_to[0]|less_than_equal_to[999.99]',
        ],
        'flash' => [
            'label' => 'Flash',
            'rules' => 'required_with[galon]|numeric|greater_than_equal_to[0]|less_than_equal_to[999.99]',
        ],
    ];

    protected $validationMessages = [];

    /**
     * Get all penimbangan records for a specific becum machine
     * Returns as array indexed by hour (1-8) for easy access
     * 
     * @param int $becumId
     * @return array Array of penimbangan data keyed by hour, or empty array if not found
     * 
     * @example
     * $penimbangan = $this->getPenimbanganByBecumId(123);
     * // Result: [1 => ['galon' => 10.5, 'flash' => 8.2], 2 => [...], ...]
     */
    public function getPenimbanganByBecumId(int $becumId): array
    {
        $data = $this->where('becum_id', $becumId)
            ->orderBy('hour', 'ASC')
            ->findAll();

        $indexed = [];
        foreach ($data as $record) {
            $indexed[(int)$record['hour']] = $record;
        }

        return $indexed;
    }

    /**
     * Save or update penimbangan data for a becum machine
     * Accepts array of hours [1-8] with galon & flash values
     * 
     * @param int $becumId
     * @param array $hoursData Array format: [
     *     1 => ['galon' => 10.5, 'flash' => 8.2],
     *     2 => ['galon' => 12.0, 'flash' => 9.5],
     *     ...
     * ]
     * @return bool True if successful, false otherwise
     * @throws \Exception if validation fails
     */
    public function savePenimbanganData(int $becumId, array $hoursData): bool
    {
        try {
            $this->transStart();

            // First, delete existing records for this becum
            $this->where('becum_id', $becumId)->delete();

            // Then, insert new records
            $toInsert = [];
            for ($hour = 1; $hour <= 8; $hour++) {
                $hourData = $hoursData[$hour] ?? ['galon' => 0, 'flash' => 0];
                
                $record = [
                    'becum_id' => $becumId,
                    'hour' => $hour,
                    'galon' => $this->sanitizeNumeric($hourData['galon'] ?? 0),
                    'flash' => $this->sanitizeNumeric($hourData['flash'] ?? 0),
                ];

                // Validate record before insert
                if (!$this->validate($record)) {
                    throw new \Exception('Validation failed for hour ' . $hour . ': ' . implode(', ', $this->errors()));
                }

                $toInsert[] = $record;
            }

            // Batch insert
            $this->insertBatch($toInsert);

            $this->transComplete();
            return $this->transStatus() === true;

        } catch (\Exception $e) {
            $this->transRollback();
            throw $e;
        }
    }

    /**
     * Get penimbangan for a specific hour of a becum machine
     * 
     * @param int $becumId
     * @param int $hour (1-8)
     * @return array|null Penimbangan record or null if not found
     */
    public function getPenimbanganByHour(int $becumId, int $hour): ?array
    {
        return $this->where('becum_id', $becumId)
            ->where('hour', $hour)
            ->first();
    }

    /**
     * Delete all penimbangan records for a becum machine
     * (typically called when mesin_becum is soft-deleted or hard-deleted)
     * 
     * @param int $becumId
     * @return bool
     */
    public function deletePenimbanganByBecum(int $becumId): bool
    {
        return $this->where('becum_id', $becumId)->delete();
    }

    /**
     * Get summary statistics for penimbangan data
     * Useful for reporting/analytics
     * 
     * @param int $becumId
     * @return array|null Array with 'total_galon', 'total_flash', 'avg_galon', 'avg_flash' or null
     */
    public function getPenimbanganStats(int $becumId): ?array
    {
        return $this->select('
            SUM(galon) as total_galon,
            SUM(flash) as total_flash,
            AVG(galon) as avg_galon,
            AVG(flash) as avg_flash
        ')
            ->where('becum_id', $becumId)
            ->first();
    }

    /**
     * Sanitize numeric input - convert to float or 0
     * 
     * @param mixed $value
     * @return float
     */
    private function sanitizeNumeric($value): float
    {
        if (empty($value)) {
            return 0.0;
        }
        return (float) $value;
    }

    /**
     * Validate hour value is in range 1-8
     * 
     * @param int $hour
     * @return bool
     */
    public static function isValidHour(int $hour): bool
    {
        return $hour >= 1 && $hour <= 8;
    }

    /**
     * Convert penimbangan data to array format for API response
     * 
     * @param array $penimbanganByHour Output from getPenimbanganByBecumId()
     * @return array Array format: [1 => [...], 2 => [...], ...]
     */
    public static function formatForApi(array $penimbanganByHour): array
    {
        $result = [];
        for ($hour = 1; $hour <= 8; $hour++) {
            $result[$hour] = [
                'hour' => $hour,
                'galon' => $penimbanganByHour[$hour]['galon'] ?? 0,
                'flash' => $penimbanganByHour[$hour]['flash'] ?? 0,
            ];
        }
        return $result;
    }
}
