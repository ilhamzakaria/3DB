<?php
$lines = file('c:/xampp/htdocs/3DB/catatan/catatan.md', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$data = [];
$grades = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    
    $line = trim($line);
    if (empty($line)) continue;

    // e.g. Gallon Grade A+, logo "8"
    // e.g. Gallon Grade A.1.V-3, logo " 168 "
    // e.g. Gallon Grade Club, logo "Club" Cianjur
    // e.g. Gallon Grade "AQUA"
    
    // First, let's extract the part after "Gallon Grade"
    if (preg_match('/^Gallon(?:\s+)?[Gg]rade\s+(.+)$/i', $line, $matches)) {
        $rest = trim($matches[1]);
        
        // Split by comma
        $parts = explode(',', $rest, 2);
        $grade = trim($parts[0]);
        $brand = '';
        
        if (count($parts) > 1) {
            $brandPart = trim($parts[1]);
            // Extract the brand name
            // Sometimes it's logo "xyz", sometimes Blank "xyz"
            if (preg_match('/(?:logo|Blank)\s*[\'"]?([^\'"]+)[\'"]?(.*)/i', $brandPart, $m)) {
                $brand = trim($m[1]) . ' ' . trim($m[2]);
            } else {
                $brand = str_replace(['"', "'"], '', $brandPart);
            }
        } else {
            // No comma, might be just logo or blank, e.g. Gallon Grade A, logo "xyz" without comma?
            if (preg_match('/^(.+?)\s+(?:logo|Blank)\s*[\'"]?([^\'"]+)[\'"]?(.*)$/i', $rest, $m)) {
                $grade = trim($m[1]);
                $brand = trim($m[2]) . ' ' . trim($m[3]);
            } else {
                // If it's Gallon Grade "AQUA"
                if (preg_match('/^"(.+)"$/', $grade, $m)) {
                    $brand = $m[1];
                    $grade = "AQUA"; // fallback?
                }
            }
        }
        
        $grade = trim($grade);
        $brand = trim($brand);
        
        if ($grade && !in_array($grade, $grades)) {
            $grades[] = $grade;
        }
        
        if ($grade && $brand) {
            if (!isset($data[$grade])) {
                $data[$grade] = [];
            }
            if (!in_array($brand, $data[$grade])) {
                $data[$grade][] = $brand;
            }
        }
    }
}

// Ensure all grades are in data
foreach ($grades as $g) {
    if (!isset($data[$g])) {
        $data[$g] = [];
    }
}

echo "private const BECUM_PRODUCTS = ['Gallon'];\n";
echo "private const BECUM_GRADES = [\n";
foreach ($grades as $g) {
    echo "    '$g',\n";
}
echo "];\n";

echo "private const BECUM_BRANDS = " . var_export($data, true) . ";\n";
