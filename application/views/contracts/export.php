<?php
/**
 * This view exports the list of contracts into Excel
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('contract_index_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('contract_export_thead_id'));
$sheet->setCellValue('B1', lang('contract_export_thead_name'));
$sheet->setCellValue('C1', lang('contract_export_thead_start'));
$sheet->setCellValue('D1', lang('contract_export_thead_end'));
$sheet->getStyle('A1:D1')->getFont()->setBold(true);
$sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$contracts = $this->contracts_model->getContracts();
$line = 2;
foreach ($contracts as $contract) {
    $sheet->setCellValue('A' . $line, $contract['id']);
    $sheet->setCellValue('B' . $line, $contract['name']);
    $sheet->setCellValue('C' . $line, $contract['startentdate']);
    $sheet->setCellValue('D' . $line, $contract['endentdate']);
    $line++;
}

//Autofit
foreach(range('A', 'D') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

$filename = 'contracts.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
