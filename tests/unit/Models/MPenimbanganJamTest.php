<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\MPenimbanganJam;

/**
 * Unit Tests for MPenimbanganJam Model
 * 
 * Tests model validation rules, data type handling, and basic CRUD operations.
 */
class MPenimbanganJamTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new MPenimbanganJam();
    }

    // ===== VALIDATION RULES TESTS =====

    /**
     * Test: becum_id is required and must be integer
     */
    public function testBecumIdValidation(): void
    {
        $data = [
            'becum_id' => null,
            'hour' => 1,
            'galon' => 10.5,
            'flash' => 8.2,
        ];

        $this->assertFalse($this->model->validate($data), 'becum_id should be required');
        $this->assertArrayHasKey('becum_id', $this->model->errors());
    }

    /**
     * Test: hour is required and must be between 1-8
     */
    public function testHourValidation(): void
    {
        // Test: hour is required
        $data = ['becum_id' => 1, 'hour' => null, 'galon' => 10, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: hour < 1 is invalid
        $data = ['becum_id' => 1, 'hour' => 0, 'galon' => 10, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: hour > 8 is invalid
        $data = ['becum_id' => 1, 'hour' => 9, 'galon' => 10, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: valid hours
        for ($h = 1; $h <= 8; $h++) {
            $data = ['becum_id' => 1, 'hour' => $h, 'galon' => 10, 'flash' => 8];
            $this->assertTrue($this->model->validate($data), "Hour {$h} should be valid");
        }
    }

    /**
     * Test: galon must be numeric and between 0-999.99
     */
    public function testGalonValidation(): void
    {
        // Test: galon < 0 is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => -1, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: galon > 999.99 is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 1000, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: galon non-numeric is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 'abc', 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Test: valid galon values
        $validValues = [0, 0.01, 10.5, 99.99, 500, 999.99];
        foreach ($validValues as $val) {
            $data = ['becum_id' => 1, 'hour' => 1, 'galon' => $val, 'flash' => 8];
            $this->assertTrue($this->model->validate($data), "Galon {$val} should be valid");
        }
    }

    /**
     * Test: flash must be numeric and between 0-999.99
     */
    public function testFlashValidation(): void
    {
        // Test: flash < 0 is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10, 'flash' => -1];
        $this->assertFalse($this->model->validate($data));

        // Test: flash > 999.99 is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10, 'flash' => 1000];
        $this->assertFalse($this->model->validate($data));

        // Test: flash non-numeric is invalid
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10, 'flash' => 'xyz'];
        $this->assertFalse($this->model->validate($data));

        // Test: valid flash values
        $validValues = [0, 0.01, 8.2, 50, 999.99];
        foreach ($validValues as $val) {
            $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10, 'flash' => $val];
            $this->assertTrue($this->model->validate($data), "Flash {$val} should be valid");
        }
    }

    /**
     * Test: Valid record passes all validations
     */
    public function testValidRecordPassesValidation(): void
    {
        $data = [
            'becum_id' => 1,
            'hour' => 5,
            'galon' => 10.5,
            'flash' => 8.2,
        ];

        $this->assertTrue($this->model->validate($data));
    }

    /**
     * Test: Decimal precision is handled correctly
     */
    public function testDecimalPrecision(): void
    {
        $testCases = [
            ['galon' => 10.1, 'flash' => 8.2],
            ['galon' => 100.99, 'flash' => 99.01],
            ['galon' => 0.01, 'flash' => 0.01],
            ['galon' => 999.99, 'flash' => 999.99],
        ];

        foreach ($testCases as $test) {
            $data = [
                'becum_id' => 1,
                'hour' => 1,
                'galon' => $test['galon'],
                'flash' => $test['flash'],
            ];
            $this->assertTrue($this->model->validate($data), "Decimal values should be valid: " . json_encode($test));
        }
    }

    // ===== HELPER FUNCTION TESTS =====

    /**
     * Test: isValidHour() static function
     */
    public function testIsValidHourHelper(): void
    {
        for ($h = 1; $h <= 8; $h++) {
            $this->assertTrue(MPenimbanganJam::isValidHour($h), "Hour {$h} should be valid");
        }

        // Invalid hours
        $this->assertFalse(MPenimbanganJam::isValidHour(0));
        $this->assertFalse(MPenimbanganJam::isValidHour(9));
        $this->assertFalse(MPenimbanganJam::isValidHour(-1));
        $this->assertFalse(MPenimbanganJam::isValidHour(10));
    }

    /**
     * Test: formatForApi() static function
     */
    public function testFormatForApiHelper(): void
    {
        $penimbanganByHour = [
            1 => ['id' => 1, 'becum_id' => 100, 'hour' => 1, 'galon' => 10.5, 'flash' => 8.2],
            2 => ['id' => 2, 'becum_id' => 100, 'hour' => 2, 'galon' => 12.0, 'flash' => 9.5],
        ];

        $result = MPenimbanganJam::formatForApi($penimbanganByHour);

        // Should return 8 hours
        $this->assertCount(8, $result);

        // Hour 1 should have correct data
        $this->assertEquals(1, $result[1]['hour']);
        $this->assertEquals(10.5, $result[1]['galon']);
        $this->assertEquals(8.2, $result[1]['flash']);

        // Hour 2 should have correct data
        $this->assertEquals(2, $result[2]['hour']);
        $this->assertEquals(12.0, $result[2]['galon']);
        $this->assertEquals(9.5, $result[2]['flash']);

        // Missing hours should default to 0
        $this->assertEquals(0, $result[3]['galon']);
        $this->assertEquals(0, $result[3]['flash']);
    }

    // ===== DATA TYPE TESTS =====

    /**
     * Test: galon and flash accept integer and float types
     */
    public function testNumericDataTypes(): void
    {
        // Integer galon, integer flash
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10, 'flash' => 8];
        $this->assertTrue($this->model->validate($data));

        // Float galon, float flash
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 10.5, 'flash' => 8.2];
        $this->assertTrue($this->model->validate($data));

        // String integer galon, string float flash
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => '10', 'flash' => '8.2'];
        $this->assertTrue($this->model->validate($data));
    }

    /**
     * Test: Zero values are allowed
     */
    public function testZeroValuesAllowed(): void
    {
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 0, 'flash' => 0];
        $this->assertTrue($this->model->validate($data));

        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 0.0, 'flash' => 0.0];
        $this->assertTrue($this->model->validate($data));
    }

    /**
     * Test: Boundary values
     */
    public function testBoundaryValues(): void
    {
        // Minimum valid value
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 0, 'flash' => 0];
        $this->assertTrue($this->model->validate($data));

        // Maximum valid value
        $data = ['becum_id' => 1, 'hour' => 8, 'galon' => 999.99, 'flash' => 999.99];
        $this->assertTrue($this->model->validate($data));

        // Just above maximum
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => 1000, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));

        // Just below zero
        $data = ['becum_id' => 1, 'hour' => 1, 'galon' => -0.01, 'flash' => 8];
        $this->assertFalse($this->model->validate($data));
    }
}
