<?php
/**
 * This view exports to Excel the native report listing the approved leave requests of employees attached to an entity.
 * This report is launched by the user from the view reports/leaves.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('reports_export_leaves_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.

$month = $this->input->get("month") === FALSE ? 0 : $this->input->get("month");
$year = $this->input->get("year") === FALSE ? 0 : $this->input->get("year");
$entity = $this->input->get("entity") === FALSE ? 0 : $this->input->get("entity");
$children = filter_var($this->input->get("children"), FILTER_VALIDATE_BOOLEAN);

//Compute facts about dates and the selected month
if ($month == 0) $month = date('m', strtotime('last month'));
if ($year == 0) $year = date('Y', strtotime('last month'));
$start = sprintf('%d-%02d-01', $year, $month);
$lastDay = date("t", strtotime($start));    //last day of selected month
$end = sprintf('%d-%02d-%02d', $year, $month, $lastDay);
$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$types = $this->types_model->getTypes();

//Iterate on all employees of the entity 
$users = $this->organization_model->allEmployees($entity, $children);
$result = array();
foreach ($users as $user) {
    $result[$user->id]['identifier'] = $user->identifier;
    $result[$user->id]['firstname'] = $user->firstname;
    $result[$user->id]['lastname'] = $user->lastname;
    $date = new DateTime($user->datehired);
    $result[$user->id]['datehired'] = $date->format(lang('global_date_format'));
    $result[$user->id]['department'] = $user->department;
    $result[$user->id]['position'] = $user->position;
    $result[$user->id]['contract'] = $user->contract;
    $non_working_days = $this->dayoffs_model->lengthDaysOffBetweenDates($user->contract_id, $start, $end);
    $opened_days = $total_days - $non_working_days;
    $linear = $this->leaves_model->linear($user->id, $month, $year, FALSE, FALSE, TRUE, FALSE);
    $leave_duration = $this->leaves_model->monthlyLeavesDuration($linear);
    $work_duration = $opened_days - $leave_duration;
    $leaves_detail = $this->leaves_model->monthlyLeavesByType($linear);

    //Init type columns
    foreach ($types as $type) {
        if (array_key_exists($type['name'], $leaves_detail)) {
            $result[$user->id][$type['name']] = $leaves_detail[$type['name']];
        } else {
            $result[$user->id][$type['name']] = '';
        }
    }

    $result[$user->id]['leave_duration'] = $leave_duration;
    $result[$user->id]['total_days'] = $total_days;
    $result[$user->id]['non_working_days'] = $non_working_days;
    $result[$user->id]['work_duration'] = $work_duration;
}

$max = 0;
$line = 2;
$i18n = array("identifier", "firstname", "lastname", "datehired", "department", "position", "contract");
foreach ($result as $row) {
    $index = 1;
    foreach ($row as $key => $value) {
        if ($line == 2) {
            $colidx = $this->excel->column_name($index) . '1';
            if (in_array($key, $i18n)) {
                $sheet->setCellValue($colidx, lang($key));
            } else {
                $sheet->setCellValue($colidx, $key);
            }
            $max++;
        }
        $colidx = $this->excel->column_name($index) . $line;
        $sheet->setCellValue($colidx, $value);
        $index++;
    }
    $line++;
}

$colidx = $this->excel->column_name($max) . '1';
$sheet->getStyle('A1:' . $colidx)->getFont()->setBold(true);
$sheet->getStyle('A1:' . $colidx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Autofit
for ($ii=1; $ii <$max; $ii++) {
    $col = $this->excel->column_name($ii);
    $sheet->getColumnDimension($col)->setAutoSize(TRUE);
}

$filename = 'leave_requests_'. $month . '_' . $year .'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
