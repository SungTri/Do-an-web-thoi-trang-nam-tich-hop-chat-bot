<?php
session_start();
require_once __DIR__ . '/../Models/Report.php';
require_once __DIR__ . '/../../Config/db.php';

// Load PhpSpreadsheet
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reportModel = new Report($conn);

// Lấy tham số lọc từ GET
$start_date  = $_GET['start_date'] ?? null;
$end_date    = $_GET['end_date'] ?? null;
$type_export = $_GET['type'] ?? ''; // Loại xuất Excel

// --- Lấy dữ liệu ---
$revenue_day   = $reportModel->revenueByDay($start_date, $end_date);
$revenue_month = $reportModel->revenueByMonth($start_date, $end_date);
$revenue_year  = $reportModel->revenueByYear($start_date, $end_date);
$stock         = $reportModel->stockReport();
$sold_products = $reportModel->soldProducts($start_date, $end_date);

// --- Xuất Excel ---
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = 1;

    switch ($type_export) {
        case 'revenue_day':
            $sheet->setCellValue("A{$row}", "DOANH THU THEO NGÀY"); $row += 2;
            $sheet->fromArray(['Ngày', 'Số đơn', 'Doanh thu (VNĐ)'], NULL, "A{$row}"); $row++;
            foreach ($revenue_day as $r) {
                $sheet->fromArray([$r['revenue_date'], $r['total_orders'], $r['total_revenue']], NULL, "A{$row}");
                $row++;
            }
            $filename = 'DoanhThu_Theo_Ngay.xlsx';
            break;

        case 'revenue_month':
            $sheet->setCellValue("A{$row}", "DOANH THU THEO THÁNG"); $row += 2;
            $sheet->fromArray(['Tháng', 'Số đơn', 'Doanh thu (VNĐ)'], NULL, "A{$row}"); $row++;
            foreach ($revenue_month as $r) {
                $sheet->fromArray([$r['revenue_month'], $r['total_orders'], $r['total_revenue']], NULL, "A{$row}");
                $row++;
            }
            $filename = 'DoanhThu_Theo_Thang.xlsx';
            break;

        case 'revenue_year':
            $sheet->setCellValue("A{$row}", "DOANH THU THEO NĂM"); $row += 2;
            $sheet->fromArray(['Năm', 'Số đơn', 'Doanh thu (VNĐ)'], NULL, "A{$row}"); $row++;
            foreach ($revenue_year as $r) {
                $sheet->fromArray([$r['revenue_year'], $r['total_orders'], $r['total_revenue']], NULL, "A{$row}");
                $row++;
            }
            $filename = 'DoanhThu_Theo_Nam.xlsx';
            break;

        case 'stock':
            $sheet->setCellValue("A{$row}", "TỒN KHO SẢN PHẨM"); $row += 2;
            $sheet->fromArray(['ID','Tên SP','Số lượng tồn','Giá (VNĐ)'], NULL, "A{$row}"); $row++;
            foreach ($stock as $s) {
                $sheet->fromArray([$s['id'],$s['name'],$s['stock'],$s['price']], NULL, "A{$row}");
                $row++;
            }
            $filename = 'TonKho.xlsx';
            break;

        case 'sold':
            $sheet->setCellValue("A{$row}", "SẢN PHẨM BÁN RA"); $row += 2;
            $sheet->fromArray(['ID','Tên SP','Số lượng bán','Doanh thu (VNĐ)'], NULL, "A{$row}"); $row++;
            foreach ($sold_products as $p) {
                $sheet->fromArray([$p['id'],$p['name'],$p['quantity_sold'],$p['revenue']], NULL, "A{$row}");
                $row++;
            }
            $filename = 'SanPhamBanRa.xlsx';
            break;

        default:
            die('❌ Loại thống kê không hợp lệ!');
    }

    // Xuất file Excel
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
