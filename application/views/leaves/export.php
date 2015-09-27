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
$sheet->setTitle(mb_strimwidth(lang('leaves_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('leaves_export_thead_id'));
$sheet->setCellValue('B1', lang('leaves_export_thead_start_date'));
$sheet->setCellValue('C1', lang('leaves_export_thead_start_date_type'));
$sheet->setCellValue('D1', lang('leaves_export_thead_end_date'));
$sheet->setCellValue('E1', lang('leaves_export_thead_end_date_type'));
$sheet->setCellValue('F1', lang('leaves_export_thead_duration'));
$sheet->setCellValue('G1', lang('leaves_export_thead_type'));
$sheet->setCellValue('H1', lang('leaves_export_thead_status'));
$sheet->setCellValue('I1', lang('leaves_export_thead_cause'));
$sheet->getStyle('A1:I1')->getFont()->setBold(true);
$sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$leaves = $this->leaves_model->getLeavesOfEmployee($this->user_id);
$line = 2;
foreach ($leaves as $leave) {
    $date = new DateTime($leave['startdate']);
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($leave['enddate']);
    $enddate = $date->format(lang('global_date_format'));
    $sheet->setCellValue('A' . $line, $leave['id']);
    $sheet->setCellValue('B' . $line, $startdate);
    $sheet->setCellValue('C' . $line, lang($leave['startdatetype']));
    $sheet->setCellValue('D' . $line, $enddate);
    $sheet->setCellValue('E' . $line, lang($leave['enddatetype']));
    $sheet->setCellValue('F' . $line, $leave['duration']);
    $sheet->setCellValue('G' . $line, $leave['type_name']);
    $sheet->setCellValue('H' . $line, lang($leave['status_name']));
    $sheet->setCellValue('I' . $line, $leave['cause']);
    $line++;
}

//Autofit
foreach(range('A', 'I') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

$filename = 'leaves.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
