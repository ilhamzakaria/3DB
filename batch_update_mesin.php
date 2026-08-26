<?php
/**
 * Batch update script for mesin view files
 * Updates remaining files with complete pattern from mesinpowerjet1.php
 */

// Files to update: mesinccm2, mesinips1, mesinips2, mesinips3, mesinips4
$files = [
    'mesinccm2' => ['type' => 'ccm', 'product_field' => 'produk_cap_galon'],
    'mesinips1' => ['type' => 'ips', 'product_field' => 'produk_preform'],
    'mesinips2' => ['type' => 'ips', 'product_field' => 'produk_preform'],
    'mesinips3' => ['type' => 'ips', 'product_field' => 'produk_preform'],
    'mesinips4' => ['type' => 'ips', 'product_field' => 'produk_preform'],
];

$base_path = 'c:/xampp/htdocs/3DB/app/Views/gudang/';
$ref_file = file_get_contents($base_path . 'mesinpowerjet1.php');

function extract_js_functions($ref) {
    // Extract from reference's <script> tag
    if (preg_match('/<script>(.*?)<\/script>/s', $ref, $m)) {
        return $m[1];
    }
    return '';
}

$js_section = extract_js_functions($ref_file);

foreach ($files as $file_base => $config) {
    $file_path = $base_path . $file_base . '.php';
    
    if (!file_exists($file_path)) {
        echo "SKIP: $file_path not found\n";
        continue;
    }
    
    $content = file_get_contents($file_path);
    
    // A. Update header status labels (already done manually for some, but ensure all have it)
    // Check if already updated
    if (strpos($content, '$statusLabels =') === false) {
        // Add status labels after formatDate
        $old_head = '<?= $this->extend(\'layout/main\') ?>';
        $new_head = '<?= $this->extend(\'layout/main\') ?>
<?php $view_version = \'20260718_01\'; ?>';
        $content = str_replace($old_head, $new_head, $content);
        
        // Add after section
        $section_marker = '<?= $this->section(\'content\') ?>';
        $status_block = <<<'EOPHP'

<?php
$statusLabels = [
    'menggiling' => 'Menggiling',
    'bahan_masuk' => 'Bahan Masuk',
    'bahan_keluar' => 'Bahan Keluar',
];

$statusBadges = [
    'menggiling' => 'badge-warning',
    'bahan_masuk' => 'badge-success',
    'bahan_keluar' => 'badge-info',
];

EOPHP;
        
        $content = str_replace($section_marker . "\n\n<?php", $section_marker . $status_block . "\n<?php", $content);
    }
    
    // B. Update JavaScript section with new functions (replace old setEditModal with new one)
    if (strpos($content, 'addExtraShiftRow') === false) {
        // Find and replace script section
        if (preg_match('/<script>(.*?)<\/script>/s', $content, $m)) {
            // Replace old script with new
            $content = str_replace($m[0], '<script>' . $js_section . '</script>', $content);
        }
    }
    
    file_put_contents($file_path, $content);
    echo "UPDATED: $file_path\n";
}

echo "\nBatch update complete!\n";
?>
