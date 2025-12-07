<?php
namespace Tests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportHelper
{
    private static $rows = [];

    public static function addRow($input, $output, $expected, $actual, $result)
    {
        self::$rows[] = [
            'Input'    => $input,
            'Output'   => $output,
            'Expected' => $expected,
            'Actual'   => $actual,
            'Result'   => $result ? 'PASS' : 'FAIL'
        ];
    }

    public static function exportExcel($file = 'TestReport.xlsx')
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Input', 'Output', 'Expected', 'Actual', 'Result'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Style header
        $sheet->getStyle("A1:E1")->getFont()->setBold(true);
        $sheet->getStyle("A1:E1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:E1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        // Rows
        $rowNum = 2;
        foreach (self::$rows as $row) {
            $sheet->setCellValue("A$rowNum", $row['Input']);
            $sheet->setCellValue("B$rowNum", $row['Output']);
            $sheet->setCellValue("C$rowNum", $row['Expected']);
            $sheet->setCellValue("D$rowNum", $row['Actual']);
            $sheet->setCellValue("E$rowNum", $row['Result']);

            // Style PASS/FAIL
            $color = ($row['Result'] === 'PASS') ? 'FF92D050' : 'FFFF0000'; // xanh / đỏ
            $sheet->getStyle("E$rowNum")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $sheet->getStyle("E$rowNum")->getFont()->getColor()->setARGB('FFFFFFFF'); // chữ trắng

            $rowNum++;
        }

        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border
        $sheet->getStyle("A1:E" . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $writer->save($file);
    }
}
