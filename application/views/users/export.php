<?php
/**
 * This view builds a Spreadsheet file containing the list of users.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('users_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('users_export_thead_id'));
$sheet->setCellValue('B1', lang('users_export_thead_firstname'));
$sheet->setCellValue('C1', lang('users_export_thead_lastname'));
$sheet->setCellValue('D1', lang('users_export_thead_email'));
$sheet->setCellValue('E1', lang('users_export_thead_manager'));

$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$users = $this->users_model->getUsers();
$line = 2;
foreach ($users as $user) {
    $sheet->setCellValue('A' . $line, $user['id']);
    $sheet->setCellValue('B' . $line, $user['firstname']);
    $sheet->setCellValue('C' . $line, $user['lastname']);
    $sheet->setCellValue('D' . $line, $user['email']);
    $sheet->setCellValue('E' . $line, $user['manager']);
    $line++;
}

//Autofit
foreach(range('A', 'E') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

exportSpreadsheet($this, 'users');
