<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('positions_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('positions_export_thead_id'));
$sheet->setCellValue('B1', lang('positions_export_thead_name'));
$sheet->setCellValue('C1', lang('positions_export_thead_description'));
$sheet->getStyle('A1:C1')->getFont()->setBold(true);
$sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$positions = $this->positions_model->getPositions();
$line = 2;
foreach ($positions as $position) {
    $sheet->setCellValue('A' . $line, $position['id']);
    $sheet->setCellValue('B' . $line, $position['name']);
    $sheet->setCellValue('C' . $line, $position['description']);
    $line++;
}

//Autofit
foreach(range('A', 'C') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

$filename = 'positions.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
