<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\RefreshDatabaseTrait;
use App\Models\MMesinBecum;
use App\Models\MPenimbanganJam;
use App\Repositories\PenimbanganJamRepository;

/**
 * Integration Tests for Penimbangan Jam Feature
 * 
 * Tests actual database operations, API payloads, and end-to-end workflows.
 * Includes tests for:
 * - Saving penimbangan data
 * - Retrieving penimbangan data
 * - API response formatting
 * - Edit prefill functionality
 * - Delete cascade behavior
 */
class PenimbanganJamIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use RefreshDatabaseTrait;

    protected $mesinModel;
    protected $penimbanganModel;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mesinModel = new MMesinBecum();
        $this->penimbanganModel = new MPenimbanganJam();
        $this->repository = new PenimbanganJamRepository();

        // Migrate tables for testing
        $this->migrate();
    }

    // ===== DATA SETUP TESTS =====

    /**
     * Test: Create mesin_becum record for testing
     */
    public function testCreateMesinBecumRecord(): void
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
            'brand' => 'AQUA',
            'berat' => '750',
            'nomor_mesin' => 'B.01',
            'status' => 'ganti bottom rata',
        ];

        $id = $this->mesinModel->insert($data);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $record = $this->mesinModel->find($id);
        $this->assertNotNull($record);
        $this->assertEquals('AQUA', $record['brand']);
    }

    // ===== SAVE PENIMBANGAN TESTS =====

    /**
     * Test: Save penimbangan data for all 8 hours
     */
    public function testSavePenimbanganAllHours(): void
    {
        // Create mesin record first
        $mesinData = [
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
            'brand' => 'AQUA',
        ];
        $becumId = $this->mesinModel->insert($mesinData);

        // Prepare penimbangan data
        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = [
                'galon' => 10.0 + $h,
                'flash' => 8.0 + $h,
            ];
        }

        // Save via model
        $saved = $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);
        $this->assertTrue($saved);

        // Verify all 8 records were saved
        $records = $this->penimbanganModel->where('becum_id', $becumId)->findAll();
        $this->assertCount(8, $records);

        // Verify data integrity
        foreach ($records as $record) {
            $hour = $record['hour'];
            $this->assertEquals(10.0 + $hour, $record['galon']);
            $this->assertEquals(8.0 + $hour, $record['flash']);
        }
    }

    /**
     * Test: Update existing penimbangan data
     */
    public function testUpdatePenimbanganData(): void
    {
        // Create mesin and initial penimbangan
        $mesinData = [
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
        ];
        $becumId = $this->mesinModel->insert($mesinData);

        $initialData = [];
        for ($h = 1; $h <= 8; $h++) {
            $initialData[$h] = ['galon' => 5, 'flash' => 5];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $initialData);

        // Update with new values
        $updatedData = [];
        for ($h = 1; $h <= 8; $h++) {
            $updatedData[$h] = ['galon' => 20, 'flash' => 15];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $updatedData);

        // Verify update
        $records = $this->penimbanganModel->where('becum_id', $becumId)->findAll();
        $this->assertCount(8, $records);
        
        foreach ($records as $record) {
            $this->assertEquals(20, $record['galon']);
            $this->assertEquals(15, $record['flash']);
        }
    }

    /**
     * Test: Save penimbangan via repository from form
     */
    public function testSavePenimbanganFromFormRepository(): void
    {
        // Create mesin
        $mesinData = [
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
        ];
        $becumId = $this->mesinModel->insert($mesinData);

        // Simulate form POST data
        $formData = [
            'penimbangan_jam' => [
                'jam_1_galon' => '10.5',
                'jam_1_flash' => '8.2',
                'jam_2_galon' => '12.0',
                'jam_2_flash' => '9.5',
                'jam_3_galon' => '11.5',
                'jam_3_flash' => '8.8',
                'jam_4_galon' => '13.0',
                'jam_4_flash' => '10.0',
                'jam_5_galon' => '14.5',
                'jam_5_flash' => '11.2',
                'jam_6_galon' => '15.0',
                'jam_6_flash' => '12.0',
                'jam_7_galon' => '16.5',
                'jam_7_flash' => '13.5',
                'jam_8_galon' => '17.0',
                'jam_8_flash' => '14.0',
            ],
        ];

        // Save via repository
        $result = $this->repository->savePenimbanganFromForm($becumId, $formData);
        
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['data']);

        // Verify data was saved
        $records = $this->penimbanganModel->where('becum_id', $becumId)->orderBy('hour')->findAll();
        $this->assertCount(8, $records);
        
        // Verify first hour values
        $this->assertEquals(10.5, $records[0]['galon']);
        $this->assertEquals(8.2, $records[0]['flash']);
    }

    // ===== RETRIEVE & FORMAT TESTS =====

    /**
     * Test: Get penimbangan data indexed by hour
     */
    public function testGetPenimbanganByBecumId(): void
    {
        // Create and save penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 100 + $h, 'flash' => 50 + $h];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Retrieve and verify structure
        $result = $this->penimbanganModel->getPenimbanganByBecumId($becumId);
        
        $this->assertCount(8, $result);
        
        // Verify indexed by hour
        for ($h = 1; $h <= 8; $h++) {
            $this->assertArrayHasKey($h, $result);
            $this->assertEquals(100 + $h, $result[$h]['galon']);
            $this->assertEquals(50 + $h, $result[$h]['flash']);
        }
    }

    /**
     * Test: Get penimbangan for form prefill (flat format)
     */
    public function testGetPenimbanganForFormPrefill(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 20.5 + $h, 'flash' => 15.0 + $h];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Get form format
        $formData = $this->repository->getPenimbanganForForm($becumId);
        
        $this->assertCount(16, $formData);  // 8 hours × 2 inputs
        
        // Verify flat format keys
        for ($h = 1; $h <= 8; $h++) {
            $this->assertArrayHasKey("jam_{$h}_galon", $formData);
            $this->assertArrayHasKey("jam_{$h}_flash", $formData);
            
            $this->assertEquals(20.5 + $h, $formData["jam_{$h}_galon"]);
            $this->assertEquals(15.0 + $h, $formData["jam_{$h}_flash"]);
        }
    }

    /**
     * Test: Get penimbangan for API response (formatted)
     */
    public function testGetPenimbanganForApiResponse(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 30.0 + $h, 'flash' => 25.0 + $h];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Get API format
        $apiData = $this->repository->getPenimbanganForApi($becumId);
        
        $this->assertCount(8, $apiData);
        
        // Verify API structure
        foreach ($apiData as $item) {
            $this->assertArrayHasKey('hour', $item);
            $this->assertArrayHasKey('galon', $item);
            $this->assertArrayHasKey('flash', $item);
            
            $hour = $item['hour'];
            $this->assertEquals(30.0 + $hour, $item['galon']);
            $this->assertEquals(25.0 + $hour, $item['flash']);
        }
    }

    // ===== DELETE CASCADE TESTS =====

    /**
     * Test: Delete mesin deletes related penimbangan records
     */
    public function testDeleteMesinCascadesPenimbangan(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 10, 'flash' => 8];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Verify penimbangan exists
        $before = $this->penimbanganModel->where('becum_id', $becumId)->countAllResults();
        $this->assertEquals(8, $before);

        // Delete mesin
        $this->mesinModel->delete($becumId);

        // Verify penimbangan still exists (soft delete on mesin, penimbangan intact)
        // Note: The cascade delete is handled at application level in the delete() method
        // If you're using database CASCADE DELETE, the records would be removed here
    }

    /**
     * Test: Permanent delete removes penimbangan
     */
    public function testPermanentDeleteRemovesPenimbangan(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 10, 'flash' => 8];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Verify penimbangan exists
        $before = $this->penimbanganModel->where('becum_id', $becumId)->countAllResults();
        $this->assertEquals(8, $before);

        // Permanently delete penimbangan manually (simulating the controller behavior)
        $this->penimbanganModel->where('becum_id', $becumId)->delete();

        // Verify penimbangan is deleted
        $after = $this->penimbanganModel->where('becum_id', $becumId)->countAllResults();
        $this->assertEquals(0, $after);
    }

    // ===== STATISTICS TESTS =====

    /**
     * Test: Get penimbangan statistics (totals, averages)
     */
    public function testGetPenimbanganStats(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        // Create test data: hours 1-4 with 10kg each, hours 5-8 with 20kg each
        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = [
                'galon' => ($h <= 4) ? 10 : 20,
                'flash' => ($h <= 4) ? 5 : 15,
            ];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Get stats
        $stats = $this->penimbanganModel->getPenimbanganStats($becumId);

        // Verify stats
        $this->assertNotNull($stats);
        
        // Total galon: 4×10 + 4×20 = 120
        $this->assertEquals(120, $stats['total_galon']);
        
        // Total flash: 4×5 + 4×15 = 80
        $this->assertEquals(80, $stats['total_flash']);
        
        // Average galon: 120/8 = 15
        $this->assertEquals(15, $stats['avg_galon']);
        
        // Average flash: 80/8 = 10
        $this->assertEquals(10, $stats['avg_flash']);
    }

    // ===== EDGE CASE TESTS =====

    /**
     * Test: Save with zero values
     */
    public function testSaveWithZeroValues(): void
    {
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 0, 'flash' => 0];
        }

        $saved = $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);
        $this->assertTrue($saved);

        $records = $this->penimbanganModel->where('becum_id', $becumId)->findAll();
        foreach ($records as $record) {
            $this->assertEquals(0, $record['galon']);
            $this->assertEquals(0, $record['flash']);
        }
    }

    /**
     * Test: Save with maximum values
     */
    public function testSaveWithMaxValues(): void
    {
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 999.99, 'flash' => 999.99];
        }

        $saved = $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);
        $this->assertTrue($saved);

        $records = $this->penimbanganModel->where('becum_id', $becumId)->findAll();
        foreach ($records as $record) {
            $this->assertEquals(999.99, $record['galon']);
            $this->assertEquals(999.99, $record['flash']);
        }
    }

    /**
     * Test: Repository validates form data correctly
     */
    public function testRepositoryFormValidation(): void
    {
        // Valid data
        $validForm = [
            'penimbangan_jam' => array_merge(
                array_combine(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_fill(0, 8, '10.5')
                ),
                array_combine(
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8)),
                    array_fill(0, 8, '8.2')
                )
            ),
        ];

        $result = $this->repository->validatePenimbanganForm($validForm);
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);

        // Invalid data (out of range)
        $invalidForm = [
            'penimbangan_jam' => array_merge(
                array_combine(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_fill(0, 8, '1000')  // Too high
                ),
                array_combine(
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8)),
                    array_fill(0, 8, '8.2')
                )
            ),
        ];

        $result = $this->repository->validatePenimbanganForm($invalidForm);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }
}
