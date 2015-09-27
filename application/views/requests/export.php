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
$sheet->setTitle(mb_strimwidth(lang('requests_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('requests_export_thead_id'));
$sheet->setCellValue('B1', lang('requests_export_thead_fullname'));
$sheet->setCellValue('C1', lang('requests_export_thead_startdate'));
$sheet->setCellValue('D1', lang('requests_export_thead_startdate_type'));
$sheet->setCellValue('E1', lang('requests_export_thead_enddate'));
$sheet->setCellValue('F1', lang('requests_export_thead_enddate_type'));
$sheet->setCellValue('G1', lang('requests_export_thead_duration'));
$sheet->setCellValue('H1', lang('requests_export_thead_type'));
$sheet->setCellValue('I1', lang('requests_export_thead_cause'));
$sheet->setCellValue('J1', lang('requests_export_thead_status'));
$sheet->getStyle('A1:J1')->getFont()->setBold(true);
$sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

($filter == 'all')? $showAll = TRUE : $showAll = FALSE;
$requests = $this->leaves_model->getLeavesRequestedToManager($this->user_id, $showAll);
$line = 2;
foreach ($requests as $request) {
    $date = new DateTime($request['startdate']);
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($request['enddate']);
    $enddate = $date->format(lang('global_date_format'));
    $sheet->setCellValue('A' . $line, $request['id']);
    $sheet->setCellValue('B' . $line, $request['firstname'] . ' ' . $request['lastname']);
    $sheet->setCellValue('C' . $line, $startdate);
    $sheet->setCellValue('D' . $line, lang($request['startdatetype']));
    $sheet->setCellValue('E' . $line, $enddate);
    $sheet->setCellValue('F' . $line, lang($request['enddatetype']));
    $sheet->setCellValue('G' . $line, $request['duration']);
    $sheet->setCellValue('H' . $line, $request['type_name']);
    $sheet->setCellValue('I' . $line, $request['cause']);
    $sheet->setCellValue('J' . $line, lang($request['status_name']));
    $line++;
}

//Autofit
foreach(range('A', 'J') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

$filename = 'requests.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
        