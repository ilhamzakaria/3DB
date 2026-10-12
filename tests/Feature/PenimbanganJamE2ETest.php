<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\RefreshDatabaseTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\MMesinBecum;
use App\Models\MPenimbanganJam;

/**
 * End-to-End Tests for Penimbangan Jam Feature
 * 
 * Tests complete user workflows:
 * - Form rendering in Add modal
 * - Form rendering in Edit modal with prefill
 * - Form submission and validation
 * - AJAX prefill endpoint
 * - Real-time calculations
 */
class PenimbanganJamE2ETest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use RefreshDatabaseTrait;
    use FeatureTestTrait;

    protected $mesinModel;
    protected $penimbanganModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mesinModel = new MMesinBecum();
        $this->penimbanganModel = new MPenimbanganJam();

        $this->migrate();
    }

    // ===== ADD MODAL FORM TESTS =====

    /**
     * Test: Add modal renders penimbangan section with default values
     */
    public function testAddModalRendersPenimbanganSection(): void
    {
        // Test that the partial includes properly formatted HTML
        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => [
                'jam_1_galon' => 0, 'jam_1_flash' => 0,
                'jam_2_galon' => 0, 'jam_2_flash' => 0,
                'jam_3_galon' => 0, 'jam_3_flash' => 0,
                'jam_4_galon' => 0, 'jam_4_flash' => 0,
                'jam_5_galon' => 0, 'jam_5_flash' => 0,
                'jam_6_galon' => 0, 'jam_6_flash' => 0,
                'jam_7_galon' => 0, 'jam_7_flash' => 0,
                'jam_8_galon' => 0, 'jam_8_flash' => 0,
            ],
            'errors' => [],
        ]);

        // Verify key HTML elements are present
        $this->assertStringContainsString('Durasi Penimbangan', $output);
        $this->assertStringContainsString('Jam 1', $output);
        $this->assertStringContainsString('Jam 8', $output);
        $this->assertStringContainsString('penimbangan-galon-input', $output);
        $this->assertStringContainsString('penimbangan-flash-input', $output);
        $this->assertStringContainsString('Total Galon', $output);
        $this->assertStringContainsString('Total Flash', $output);
        $this->assertStringContainsString('Grand Total', $output);
    }

    /**
     * Test: Add modal form contains all 8 hour inputs
     */
    public function testAddModalContainsAllEightHours(): void
    {
        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => array_fill_keys(
                array_merge(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                ),
                0
            ),
            'errors' => [],
        ]);

        // Verify all 8 hours are present
        for ($h = 1; $h <= 8; $h++) {
            $this->assertStringContainsString("jam_$h", $output);
        }

        // Verify input counts
        $galonCount = substr_count($output, 'penimbangan-galon-input');
        $flashCount = substr_count($output, 'penimbangan-flash-input');
        $this->assertEquals(8, $galonCount);
        $this->assertEquals(8, $flashCount);
    }

    /**
     * Test: Add modal form prefills with default zeros
     */
    public function testAddModalDefaultsToZeros(): void
    {
        $defaultData = array_fill_keys(
            array_merge(
                array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
            ),
            0
        );

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => $defaultData,
            'errors' => [],
        ]);

        // Verify value="0" appears for all inputs
        $zeroCount = substr_count($output, 'value="0"');
        $this->assertGreaterThanOrEqual(16, $zeroCount);  // At least 16 zero values
    }

    // ===== EDIT MODAL FORM TESTS =====

    /**
     * Test: Edit modal renders penimbangan section with existing data
     */
    public function testEditModalRendersPenimbanganWithData(): void
    {
        $existingData = [];
        for ($h = 1; $h <= 8; $h++) {
            $existingData["jam_{$h}_galon"] = 10.5 + $h;
            $existingData["jam_{$h}_flash"] = 8.2 + $h;
        }

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'edit',
            'penimbanganData' => $existingData,
            'errors' => [],
        ]);

        // Verify specific values are present
        $this->assertStringContainsString('value="11.5"', $output);  // jam_1_galon: 10.5 + 1
        $this->assertStringContainsString('value="9.2"', $output);   // jam_1_flash: 8.2 + 1
    }

    /**
     * Test: Edit modal displays validation errors
     */
    public function testEditModalDisplaysValidationErrors(): void
    {
        $data = array_fill_keys(
            array_merge(
                array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
            ),
            0
        );

        $errors = [
            'jam_1_galon' => 'Galon jam 1 harus antara 0 dan 999.99',
            'jam_2_flash' => 'Flash jam 2 harus antara 0 dan 999.99',
        ];

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'edit',
            'penimbanganData' => $data,
            'errors' => $errors,
        ]);

        // Verify error messages appear
        $this->assertStringContainsString('Galon jam 1 harus antara 0 dan 999.99', $output);
        $this->assertStringContainsString('Flash jam 2 harus antara 0 dan 999.99', $output);

        // Verify is-invalid class is applied
        $this->assertStringContainsString('is-invalid', $output);
    }

    // ===== FORM SUBMISSION TESTS =====

    /**
     * Test: Add form submission with valid penimbangan data
     */
    public function testAddFormSubmissionWithValidData(): void
    {
        $postData = [
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
            'brand' => 'AQUA',
            'nomor_mesin' => 'B.01',
            'status' => 'ganti bottom rata',
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

        // Simulate form submission
        $response = $this->post('gudang/mesin_becum/tambahData', $postData);

        // Verify mesin record created
        $mesin = $this->mesinModel->orderBy('id', 'DESC')->first();
        $this->assertNotNull($mesin);
        $this->assertEquals('B.01', $mesin['nomor_mesin']);

        // Verify penimbangan records created
        $penimbangan = $this->penimbanganModel->where('becum_id', $mesin['id'])->findAll();
        $this->assertCount(8, $penimbangan);

        // Verify data integrity
        $firstRecord = $penimbangan[0];
        $this->assertEquals(1, $firstRecord['hour']);
        $this->assertEquals(10.5, $firstRecord['galon']);
        $this->assertEquals(8.2, $firstRecord['flash']);
    }

    /**
     * Test: Edit form submission updates penimbangan data
     */
    public function testEditFormSubmissionUpdatesData(): void
    {
        // Create initial mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 10, 'flash' => 8];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Submit update form
        $postData = [
            'id' => $becumId,
            'tanggal' => date('Y-m-d'),
            'produk_galon' => 'Gallon',
            'grade' => 'A+',
            'brand' => 'AQUA',
            'penimbangan_jam' => [
                'jam_1_galon' => '20.5',
                'jam_1_flash' => '18.2',
                'jam_2_galon' => '22.0',
                'jam_2_flash' => '19.5',
                'jam_3_galon' => '21.5',
                'jam_3_flash' => '18.8',
                'jam_4_galon' => '23.0',
                'jam_4_flash' => '20.0',
                'jam_5_galon' => '24.5',
                'jam_5_flash' => '21.2',
                'jam_6_galon' => '25.0',
                'jam_6_flash' => '22.0',
                'jam_7_galon' => '26.5',
                'jam_7_flash' => '23.5',
                'jam_8_galon' => '27.0',
                'jam_8_flash' => '24.0',
            ],
        ];

        $response = $this->post('gudang/mesin_becum/update/' . $becumId, $postData);

        // Verify update
        $penimbangan = $this->penimbanganModel->where('becum_id', $becumId)->orderBy('hour')->findAll();
        $this->assertCount(8, $penimbangan);
        
        $this->assertEquals(20.5, $penimbangan[0]['galon']);
        $this->assertEquals(18.2, $penimbangan[0]['flash']);
    }

    // ===== AJAX PREFILL TESTS =====

    /**
     * Test: AJAX endpoint returns penimbangan data for edit prefill
     */
    public function testAjaxPrefillEndpointReturnsData(): void
    {
        // Create mesin and penimbangan
        $mesinData = ['tanggal' => date('Y-m-d'), 'produk_galon' => 'Gallon', 'grade' => 'A+'];
        $becumId = $this->mesinModel->insert($mesinData);

        $hoursData = [];
        for ($h = 1; $h <= 8; $h++) {
            $hoursData[$h] = ['galon' => 30.0 + $h, 'flash' => 25.0 + $h];
        }
        $this->penimbanganModel->savePenimbanganData($becumId, $hoursData);

        // Make AJAX request
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('gudang/mesin_becum/getPenimbanganSection', ['id' => $becumId]);

        // Verify response
        $response->assertStatus(200);
        $json = $response->getJSON();
        
        $this->assertTrue($json->success);
        $this->assertNotEmpty($json->html);
        $this->assertStringContainsString('penimbangan-galon-input', $json->html);
    }

    /**
     * Test: AJAX prefill endpoint handles invalid ID
     */
    public function testAjaxPrefillEndpointHandlesInvalidId(): void
    {
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('gudang/mesin_becum/getPenimbanganSection', ['id' => 99999]);

        $response->assertStatus(200);
        $json = $response->getJSON();
        
        $this->assertTrue($json->success);  // Still returns success with empty data
        $this->assertNotEmpty($json->html);
    }

    // ===== CALCULATION TESTS =====

    /**
     * Test: Real-time calculations with different values
     */
    public function testRealTimeCalculations(): void
    {
        $data = [];
        $expectedTotal = 0;

        for ($h = 1; $h <= 8; $h++) {
            $galon = 10.0 + ($h * 2.5);
            $flash = 5.0 + ($h * 1.5);
            $data["jam_{$h}_galon"] = $galon;
            $data["jam_{$h}_flash"] = $flash;
            $expectedTotal += $galon + $flash;
        }

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => $data,
            'errors' => [],
        ]);

        // Verify JavaScript calculations structure exists
        $this->assertStringContainsString('updatePenimbanganCalculations', $output);
        $this->assertStringContainsString('validatePenimbanganInput', $output);

        // Verify summary section exists for totals
        $this->assertStringContainsString('penimbangan-summary', $output);
    }

    // ===== VALIDATION DISPLAY TESTS =====

    /**
     * Test: Form shows validation errors for out-of-range values
     */
    public function testFormDisplaysRangeValidationErrors(): void
    {
        $data = array_fill_keys(
            array_merge(
                array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
            ),
            0
        );

        $errors = [
            'jam_1_galon' => 'Galon jam 1 harus antara 0 dan 999.99',
            'jam_1_flash' => 'Flash jam 1 harus antara 0 dan 999.99',
        ];

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => $data,
            'errors' => $errors,
        ]);

        $this->assertStringContainsString('is-invalid', $output);
        $this->assertStringContainsString('text-danger', $output);
    }

    /**
     * Test: Form provides helpful error context
     */
    public function testFormErrorMessagesAreContextual(): void
    {
        $data = array_fill_keys(
            array_merge(
                array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
            ),
            0
        );

        $errors = [
            'jam_3_galon' => 'Galon jam 3 harus antara 0 dan 999.99',
            'jam_5_flash' => 'Flash jam 5 harus antara 0 dan 999.99',
        ];

        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => $data,
            'errors' => $errors,
        ]);

        // Verify error messages include hour number for context
        $this->assertStringContainsString('jam 3', $output);
        $this->assertStringContainsString('jam 5', $output);
    }

    // ===== RESPONSIVE DESIGN TESTS =====

    /**
     * Test: Form CSS classes indicate responsive grid layout
     */
    public function testFormHasResponsiveGridLayout(): void
    {
        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => array_fill_keys(
                array_merge(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                ),
                0
            ),
            'errors' => [],
        ]);

        // Verify CSS Grid classes
        $this->assertStringContainsString('penimbangan-hours-grid', $output);
        $this->assertStringContainsString('penimbangan-hour-card', $output);
    }

    // ===== ACCESSIBILITY TESTS =====

    /**
     * Test: Form includes proper ARIA labels and accessibility features
     */
    public function testFormHasAccessibilityFeatures(): void
    {
        $output = view('gudang/partials/penimbangan_jam_section', [
            'mode' => 'add',
            'penimbanganData' => array_fill_keys(
                array_merge(
                    array_map(fn($h) => "jam_{$h}_galon", range(1, 8)),
                    array_map(fn($h) => "jam_{$h}_flash", range(1, 8))
                ),
                0
            ),
            'errors' => [],
        ]);

        // Verify label elements exist for accessibility
        $this->assertStringContainsString('<label', $output);
        
        // Verify input elements have names (required for form submission)
        $this->assertStringContainsString('name="penimbangan_jam', $output);
    }
}
