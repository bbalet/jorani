<?php
/**
 * This view builds a Spreadsheet file containing the list of leave types.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('leavetypes_type_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('leavetypes_type_export_thead_id'));
$sheet->setCellValue('B1', lang('leavetypes_type_export_thead_name'));
$sheet->getStyle('A1:B1')->getFont()->setBold(true);
$sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$types = $this->types_model->getTypes();
$line = 2;
foreach ($types as $type) {
    $sheet->setCellValue('A' . $line, $type['id']);
    $sheet->setCellValue('B' . $line, $type['name']);
    $line++;
}

//Autofit
foreach(range('A', 'B') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

exportSpreadsheet($this, 'leave_types');
