<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelExport
{
    /**
     * Export data to Excel (.xlsx)
     * 
     * @param string $filename Filename without extension
     * @param array $headers Column headers
     * @param array $data 2D array of data
     */
    public function export(string $filename, array $headers, array $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set Headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // 2. Style Headers
        $lastCol = $this->getColLetter(count($headers));
        $headerRange = 'A1:' . $lastCol . '1';
        
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4361EE'], // Professional blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 3. Set Data
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // 4. Formatting
        $lastRow = $row - 1;
        if ($lastRow < 1) $lastRow = 1;
        $fullRange = 'A1:' . $lastCol . $lastRow;

        // Borders
        $sheet->getStyle($fullRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto width
        $colIndex = 1;
        while ($colIndex <= count($headers)) {
            $c = $this->getColLetter($colIndex);
            $sheet->getColumnDimension($c)->setAutoSize(true);
            $colIndex++;
        }

        // Zebra rows (light styling)
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }
        }

        // 5. Freeze Header
        $sheet->freezePane('A2');

        // 6. Write to output
        $writer = new Xlsx($spreadsheet);
        
        // Ensure no output has been sent
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Helper to get Excel column letter from index (1-based)
     */
    private function getColLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intval(($index - $mod) / 26);
        }
        return $letter;
    }
}
