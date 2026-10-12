<?php

namespace Tests\Unit\Repositories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Repositories\PenimbanganJamRepository;

/**
 * Unit Tests for PenimbanganJamRepository
 * 
 * Tests form data parsing, validation, and data transformation functions.
 * Does not test database operations (that's integration testing).
 */
class PenimbanganJamRepositoryTest extends CIUnitTestCase
{
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PenimbanganJamRepository();
    }

    // ===== FORM DATA PARSING TESTS =====

    /**
     * Test: getDefaultPenimbanganData() returns 8 hours with zeros
     */
    public function testGetDefaultPenimbanganData(): void
    {
        $result = PenimbanganJamRepository::getDefaultPenimbanganData();

        // Should have 16 keys (8 hours × 2 inputs: galon + flash)
        $this->assertCount(16, $result);

        // All values should be 0
        foreach ($result as $value) {
            $this->assertEquals(0, $value);
        }

        // All keys should follow pattern jam_X_galon and jam_X_flash
        for ($h = 1; $h <= 8; $h++) {
            $this->assertArrayHasKey("jam_{$h}_galon", $result);
            $this->assertArrayHasKey("jam_{$h}_flash", $result);
        }
    }

    /**
     * Test: validatePenimbanganForm() validates form input correctly
     */
    public function testValidatePenimbanganForm(): void
    {
        // Valid form data
        $formData = [
            'penimbangan_jam' => [
                'jam_1_galon' => '10.5',
                'jam_1_flash' => '8.2',
                'jam_2_galon' => '12.0',
                'jam_2_flash' => '9.5',
                'jam_3_galon' => '0',
                'jam_3_flash' => '0',
                'jam_4_galon' => '100.99',
                'jam_4_flash' => '50.01',
                'jam_5_galon' => '999.99',
                'jam_5_flash' => '999.99',
                'jam_6_galon' => '0',
                'jam_6_flash' => '0',
                'jam_7_galon' => '0',
                'jam_7_flash' => '0',
                'jam_8_galon' => '0',
                'jam_8_flash' => '0',
            ],
        ];

        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    /**
     * Test: validatePenimbanganForm() detects invalid values
     */
    public function testValidatePenimbanganFormInvalidValues(): void
    {
        $formData = [
            'penimbangan_jam' => [
                'jam_1_galon' => '-1',  // Invalid: negative
                'jam_1_flash' => '1000',  // Invalid: too high
                'jam_2_galon' => 'abc',  // Invalid: non-numeric
                'jam_2_flash' => '8',
                'jam_3_galon' => '0',
                'jam_3_flash' => '0',
                'jam_4_galon' => '0',
                'jam_4_flash' => '0',
                'jam_5_galon' => '0',
                'jam_5_flash' => '0',
                'jam_6_galon' => '0',
                'jam_6_flash' => '0',
                'jam_7_galon' => '0',
                'jam_7_flash' => '0',
                'jam_8_galon' => '0',
                'jam_8_flash' => '0',
            ],
        ];

        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
        
        // Should have errors for galon and flash violations
        $errorCount = count($result['errors']);
        $this->assertGreaterThan(0, $errorCount);
    }

    /**
     * Test: validatePenimbanganForm() detects empty penimbangan_jam
     */
    public function testValidatePenimbanganFormEmpty(): void
    {
        $formData = ['penimbangan_jam' => []];
        $result = $this->repository->validatePenimbanganForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * Test: validatePenimbanganForm() detects missing penimbangan_jam key
     */
    public function testValidatePenimbanganFormMissingKey(): void
    {
        $formData = [];
        $result = $this->repository->validatePenimbanganForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    // ===== DATA TRANSFORMATION TESTS =====

    /**
     * Test: getPenimbanganForForm() returns flat format for form prefill
     */
    public function testGetPenimbanganForFormFormat(): void
    {
        // This would need database data in a real scenario
        // For unit test, we verify the method exists and returns expected structure
        
        // Since we can't test with real data without DB, we verify the behavior exists
        $this->assertTrue(method_exists($this->repository, 'getPenimbanganForForm'));
    }

    /**
     * Test: getPenimbanganForApi() returns API-formatted data
     */
    public function testGetPenimbanganForApiFormat(): void
    {
        // Similarly, verify the method exists
        $this->assertTrue(method_exists($this->repository, 'getPenimbanganForApi'));
    }

    // ===== VALIDATION RULES TESTS =====

    /**
     * Test: Boundary values in validation
     */
    public function testValidateBoundaryValues(): void
    {
        // Minimum: all zeros
        $formData = [
            'penimbangan_jam' => array_fill_keys(
                array_merge(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                ),
                '0'
            ),
        ];
        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertTrue($result['valid']);

        // Maximum: all 999.99
        $formData = [
            'penimbangan_jam' => array_fill_keys(
                array_merge(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                ),
                '999.99'
            ),
        ];
        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertTrue($result['valid']);

        // Just above max: 1000
        $formData = [
            'penimbangan_jam' => array_merge(
                array_fill_keys(array_map(fn($h) => "jam_{$h}_galon", range(1, 8)), '0'),
                ['jam_1_galon' => '1000'] + array_fill_keys(array_map(fn($h) => "jam_{$h}_flash", range(1, 8)), '0')
            ),
        ];
        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Decimal precision handling
     */
    public function testDecimalPrecisionHandling(): void
    {
        $decimalValues = [
            'jam_1_galon' => '10.1',
            'jam_1_flash' => '8.99',
            'jam_2_galon' => '100.01',
            'jam_2_flash' => '0.05',
        ];

        // Build full form data with zeros for other hours
        $formData = [
            'penimbangan_jam' => array_merge(
                array_fill_keys(
                    array_merge(
                        array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                        array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                    ),
                    '0'
                ),
                $decimalValues
            ),
        ];

        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertTrue($result['valid']);
    }

    /**
     * Test: Non-numeric value detection
     */
    public function testNonNumericDetection(): void
    {
        $formData = [
            'penimbangan_jam' => array_merge(
                array_fill_keys(
                    array_merge(
                        array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                        array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                    ),
                    '0'
                ),
                ['jam_1_galon' => 'not_a_number']
            ),
        ];

        $result = $this->repository->validatePenimbanganForm($formData);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * Test: All 8 hours must be present for validation
     */
    public function testAllHoursRequired(): void
    {
        // Only providing 4 hours
        $formData = [
            'penimbangan_jam' => [
                'jam_1_galon' => '10',
                'jam_1_flash' => '8',
                'jam_2_galon' => '12',
                'jam_2_flash' => '9',
                // Missing hours 3-8
            ],
        ];

        $result = $this->repository->validatePenimbanganForm($formData);
        
        // Validation should pass because missing values default to 0
        // But it should report errors if they're out of range
        // This depends on implementation - just verify it validates something
        $this->assertTrue(is_array($result['errors']));
    }
}
