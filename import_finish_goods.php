<?php
/**
 * Finish Goods Import Script
 * 
 * This script imports finish goods data from catatan.md file into the 3DB database.
 * It parses 4 tables: Gallon, Cap Gallon, Freeform, and Screw Cap.
 * 
 * Usage: 
 * - CLI: php import_finish_goods.php
 * - Web: http://localhost/3DB/import_finish_goods.php?run_import=1
 */

// Verify we're running from the correct location
if (!file_exists(__DIR__ . '/app') || !file_exists(__DIR__ . '/system')) {
    die('Error: This script must be run from the 3DB root directory.');
}

// Load autoloader
require 'vendor/autoload.php';

// Define FCPATH for compatibility
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Load CodeIgniter bootstrap
require_once 'system/Test/bootstrap.php';

// Import the models
use App\Models\MFinishGoodGalon;
use App\Models\MFinishGoodCapGalon;
use App\Models\MFinishGoodFreeform;
use App\Models\MFinishGoodScrewCap;

// Check if script is being accessed via web
if (php_sapi_name() !== 'cli' && !isset($_GET['run_import'])) {
    echo '<h2>Finish Goods Import Script</h2>';
    echo '<p>This script is protected. To run it via web, use:</p>';
    echo '<p><a href="?run_import=1">Click here to run import</a></p>';
    echo '<p>Or use CLI: <code>php import_finish_goods.php</code></p>';
    exit;
}

class FinishGoodsImporter
{
    private $galonModel;
    private $capGalonModel;
    private $freeformModel;
    private $screwCapModel;
    private $markdownFile;
    private $htmlContent;
    
    public function __construct()
    {
        $this->galonModel = new MFinishGoodGalon();
        $this->capGalonModel = new MFinishGoodCapGalon();
        $this->freeformModel = new MFinishGoodFreeform();
        $this->screwCapModel = new MFinishGoodScrewCap();
        $this->markdownFile = __DIR__ . '/catatan/catatan.md';
    }
    
    /**
     * Main import method
     */
    public function import()
    {
        $this->log("=== Finish Goods Import Started ===");
        
        if (!file_exists($this->markdownFile)) {
            $this->log("ERROR: catatan.md file not found at: {$this->markdownFile}", true);
            return;
        }
        
        // Read markdown file
        $this->htmlContent = file_get_contents($this->markdownFile);
        
        $summary = [
            'galon' => 0,
            'cap_galon' => 0,
            'freeform' => 0,
            'screw_cap' => 0,
            'errors' => []
        ];
        
        try {
            // Import Gallon data
            $this->log("\n--- Importing Gallon Data ---");
            $summary['galon'] = $this->importGallon();
            
            // Import Cap Gallon data
            $this->log("\n--- Importing Cap Gallon Data ---");
            $summary['cap_galon'] = $this->importCapGalon();
            
            // Import Freeform data
            $this->log("\n--- Importing Freeform Data ---");
            $summary['freeform'] = $this->importFreeform();
            
            // Import Screw Cap data
            $this->log("\n--- Importing Screw Cap Data ---");
            $summary['screw_cap'] = $this->importScrewCap();
            
        } catch (Exception $e) {
            $this->log("ERROR during import: " . $e->getMessage(), true);
            $summary['errors'][] = $e->getMessage();
        }
        
        // Display summary
        $this->displaySummary($summary);
    }
    
    /**
     * Import Gallon data
     */
    private function importGallon()
    {
        $count = 0;
        $rows = $this->extractTableRows('Gallon');
        
        foreach ($rows as $row) {
            try {
                $data = [
                    'bagian' => 'Gallon',
                    'nomor' => $this->sanitize($row[0]),
                    'nama_produk' => $this->sanitize($row[1]),
                    'grade' => $this->sanitize($row[2]),
                    'berat' => $this->sanitizeNumber($row[3]),
                    'wh_oro' => $this->sanitizeNumber($row[4]),
                    'wh_bam' => $this->sanitizeNumber($row[5]),
                    'tanggal' => date('Y-m-d H:i:s')
                ];
                
                $this->galonModel->insert($data);
                $count++;
                
            } catch (Exception $e) {
                $this->log("Error inserting Gallon row: " . $e->getMessage());
            }
        }
        
        $this->log("Imported {$count} Gallon records");
        return $count;
    }
    
    /**
     * Import Cap Gallon data
     */
    private function importCapGalon()
    {
        $count = 0;
        $rows = $this->extractTableRows('Cap Gallon');
        
        foreach ($rows as $row) {
            try {
                $data = [
                    'bagian' => 'Cap Gallon',
                    'nomor' => $this->sanitize($row[0]),
                    'nama_produk' => $this->sanitize($row[1]),
                    'warna' => $this->sanitize($row[2]),
                    'berat' => $this->sanitizeNumber($row[3]),
                    'jumlah' => $this->sanitizeNumber($row[4]),
                    'packing' => $this->sanitize($row[5]),
                    'tanggal' => date('Y-m-d H:i:s')
                ];
                
                $this->capGalonModel->insert($data);
                $count++;
                
            } catch (Exception $e) {
                $this->log("Error inserting Cap Gallon row: " . $e->getMessage());
            }
        }
        
        $this->log("Imported {$count} Cap Gallon records");
        return $count;
    }
    
    /**
     * Import Freeform data
     */
    private function importFreeform()
    {
        $count = 0;
        $rows = $this->extractTableRows('Preform');
        
        foreach ($rows as $row) {
            try {
                $data = [
                    'bagian' => 'Freeform',
                    'nomor' => $this->sanitize($row[0]),
                    'nama_produk' => $this->sanitize($row[1]),
                    'warna' => $this->sanitize($row[2]),
                    'berat' => $this->sanitizeNumber($row[3]),
                    'jumlah' => $this->sanitizeNumber($row[4]),
                    'packing' => $this->sanitizeNumber($row[5]),
                    'tanggal' => date('Y-m-d H:i:s')
                ];
                
                $this->freeformModel->insert($data);
                $count++;
                
            } catch (Exception $e) {
                $this->log("Error inserting Freeform row: " . $e->getMessage());
            }
        }
        
        $this->log("Imported {$count} Freeform records");
        return $count;
    }
    
    /**
     * Import Screw Cap data
     */
    private function importScrewCap()
    {
        $count = 0;
        $rows = $this->extractTableRows('Screw Cap');
        
        foreach ($rows as $row) {
            try {
                $data = [
                    'bagian' => 'Screw Cap',
                    'nomor' => $this->sanitize($row[0]),
                    'nama_produk' => $this->sanitize($row[1]),
                    'warna' => $this->sanitize($row[2]),
                    'berat' => $this->sanitizeNumber($row[3]),
                    'jumlah' => $this->sanitizeNumber($row[4]),
                    'keterangan' => $this->sanitize($row[5]),
                    'tanggal' => date('Y-m-d H:i:s')
                ];
                
                $this->screwCapModel->insert($data);
                $count++;
                
            } catch (Exception $e) {
                $this->log("Error inserting Screw Cap row: " . $e->getMessage());
            }
        }
        
        $this->log("Imported {$count} Screw Cap records");
        return $count;
    }
    
    /**
     * Extract table rows from HTML content
     * 
     * @param string $tableTitle The title of the table section to extract
     * @return array Array of rows, each row is an array of cell values
     */
    private function extractTableRows($tableTitle)
    {
        $rows = [];
        
        // Find the table section based on title
        if (preg_match("/<h4[^>]*>Finish Goods - {$tableTitle}<\/h4>.*?<tbody>(.*?)<\/tbody>/is", $this->htmlContent, $matches)) {
            $tbody = $matches[1];
            
            // Extract all table rows
            preg_match_all("/<tr>(.*?)<\/tr>/is", $tbody, $rowMatches);
            
            foreach ($rowMatches[1] as $rowHtml) {
                // Extract cells
                preg_match_all("/<td[^>]*>(.*?)<\/td>/is", $rowHtml, $cellMatches);
                
                if (!empty($cellMatches[1])) {
                    $cells = [];
                    foreach ($cellMatches[1] as $cell) {
                        // Remove HTML tags and decode entities
                        $cell = strip_tags($cell);
                        $cell = html_entity_decode($cell);
                        $cells[] = trim($cell);
                    }
                    
                    // Only add if row has data
                    if (count($cells) > 0 && !empty($cells[0])) {
                        $rows[] = $cells;
                    }
                }
            }
        }
        
        return $rows;
    }
    
    /**
     * Sanitize string values
     */
    private function sanitize($value)
    {
        $value = trim($value);
        // Return null for empty values
        return empty($value) ? null : $value;
    }
    
    /**
     * Sanitize and convert number values
     */
    private function sanitizeNumber($value)
    {
        $value = trim($value);
        
        if (empty($value)) {
            return null;
        }
        
        // Remove dots and commas used for thousands separator
        $value = str_replace(['&nbsp;', '.'], '', $value);
        $value = str_replace(',', '.', $value);
        
        // Convert to float
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        return null;
    }
    
    /**
     * Log output message
     */
    private function log($message, $isError = false)
    {
        if (php_sapi_name() === 'cli') {
            echo $message . "\n";
        } else {
            echo htmlspecialchars($message) . "<br>\n";
            flush();
        }
    }
    
    /**
     * Display import summary
     */
    private function displaySummary($summary)
    {
        $this->log("\n=== Import Summary ===");
        $this->log("Gallon: {$summary['galon']} records imported");
        $this->log("Cap Gallon: {$summary['cap_galon']} records imported");
        $this->log("Freeform: {$summary['freeform']} records imported");
        $this->log("Screw Cap: {$summary['screw_cap']} records imported");
        
        $total = $summary['galon'] + $summary['cap_galon'] + $summary['freeform'] + $summary['screw_cap'];
        $this->log("\nTOTAL: {$total} records imported");
        
        if (!empty($summary['errors'])) {
            $this->log("\nErrors encountered:");
            foreach ($summary['errors'] as $error) {
                $this->log("- {$error}");
            }
        }
        
        $this->log("\n=== Import Completed ===");
    }
}

// Run the import
$importer = new FinishGoodsImporter();
$importer->import();
