<?php
/**
 * This view exports a tabular calendar of the leave taken by a group of users
 * It builds a Spreadsheet file downloaded by the browser.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */

$sheet = $this->excel->setActiveSheetIndex(0);


//Print the header with the values of the export parameters
$sheet->setTitle(mb_strimwidth(lang('calendar_tabular_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('calendar_tabular_export_param_entity'));
$sheet->setCellValue('A2', lang('calendar_tabular_export_param_month'));
$sheet->setCellValue('A3', lang('calendar_tabular_export_param_year'));
$sheet->setCellValue('A4', lang('calendar_tabular_export_param_children'));
$sheet->getStyle('A1:A4')->getFont()->setBold(true);
$sheet->setCellValue('B1', $this->organization_model->getName($id));
$sheet->setCellValue('B2', $month);
$sheet->setCellValue('B3', $year);
if ($children == TRUE) {
    $sheet->setCellValue('B4', lang('global_true'));
} else {
    $sheet->setCellValue('B4', lang('global_false'));
}

//Print two lines : the short name of all days for the selected month (horizontally aligned)
$start = $year . '-' . $month . '-' . '1';    //first date of selected month
$lastDay = date("t", strtotime($start));    //last day of selected month
for ($ii = 1; $ii <=$lastDay; $ii++) {
    $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
    $col = $this->excel->column_name(3 + $ii);
    //Print day number
    $sheet->setCellValue($col . '9', $ii);
    //Print short name of the day
    switch ($dayNum)
    {
        case 1: $sheet->setCellValue($col . '8', lang('calendar_monday_short')); break;
        case 2: $sheet->setCellValue($col . '8', lang('calendar_tuesday_short')); break;
        case 3: $sheet->setCellValue($col . '8', lang('calendar_wednesday_short')); break;
        case 4: $sheet->setCellValue($col . '8', lang('calendar_thursday_short')); break;
        case 5: $sheet->setCellValue($col . '8', lang('calendar_friday_short')); break;
        case 6: $sheet->setCellValue($col . '8', lang('calendar_saturday_short')); break;
        case 7: $sheet->setCellValue($col . '8', lang('calendar_sunday_short')); break;
    }
}
//Label for employee name
$sheet->setCellValue('C8', lang('calendar_tabular_export_thead_employee'));
$sheet->mergeCells('C8:C9');
//The header is horizontally aligned
$col = $this->excel->column_name(3 + $lastDay);
$sheet->getStyle('C8:' . $col . '9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Get the tabular data
$tabular = $this->leaves_model->tabular($id, $month, $year, $children);

//Box around the lines for each employee
$styleBox = array(
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$dayBox =  array(
    'borders' => array(
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
            'rgb' => '808080'
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
            'rgb' => '808080'
        )
    )
);

//Background colors for the calendar according to the type of leave
$styleBgPlanned = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => $this->config->item('styleBgPlanned'))
    )
);
$styleBgRequested = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => $this->config->item('styleBgRequested'))
    )
);
$styleBgAccepted = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => $this->config->item('styleBgAccepted'))
    )
);
$styleBgRejected = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => $this->config->item('styleBgRejected'))
    )
);
$styleBgDayOff = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => $this->config->item('styleBgDayOff'))
    )
);

$line = 10;
$displayLeaveInCell = $this->config->item('displayLeaveInCell');
$displayLeaveInComment = $this->config->item('displayLeaveInComment');
//Iterate on all employees of the selected entity
foreach ($tabular as $employee) {
    //Merge the two line containing the name of the employee and apply a border around it
    $sheet->setCellValue('C' . $line, $employee->name);
    $sheet->mergeCells('C' . $line . ':C' . ($line + 1));
    $col = $this->excel->column_name($lastDay + 3);
    $sheet->getStyle('C' . $line . ':' . $col . ($line + 1))->applyFromArray($styleBox);

    //Iterate on all days of the selected month
    $dayNum = 0;
    foreach ($employee->days as $day) {
        $dayNum++;
        $col = $this->excel->column_name(3 + $dayNum);
        if (strstr($day->display, ';')) {//Two statuses in the cell
            $statuses = explode(";", $day->status);
            $types = explode(";", $day->type);
            //0 - Working day  _
            //1 - All day           []
            //2 - Morning        |\
            //3 - Afternoon      /|
            //4 - All Day Off       []
            //5 - Morning Day Off   |\
            //6 - Afternoon Day Off /|
            if($displayLeaveInCell != null) {
                $sheet->setCellValue($col . $line,$displayLeaveInCell($types[0]));
                $sheet->setCellValue($col . ($line + 1),$displayLeaveInCell($types[1]));
            }
            if($displayLeaveInComment != null) {
                $sheet->getComment($col . $line)->getText()->createTextRun($displayLeaveInComment($types[0]));
                $sheet->getComment($col . ($line + 1))->getText()->createTextRun($displayLeaveInComment($types[1]));
            }
            switch (intval($statuses[1]))
            {
                case 1: if($this->config->item('showPlanned'))$sheet->getStyle($col . $line)->applyFromArray($styleBgPlanned); break;  // Planned
                case 2: if($this->config->item('showRequested'))$sheet->getStyle($col . $line)->applyFromArray($styleBgRequested); break;  // Requested
                case 3: if($this->config->item('showAccepted'))$sheet->getStyle($col . $line)->applyFromArray($styleBgAccepted); break;  // Accepted
                case 4: if($this->config->item('showRejected'))$sheet->getStyle($col . $line)->applyFromArray($styleBgRejected); break;  // Rejected
                case '5': $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff); break;    //Day off
                case '6': $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff); break;    //Day off
            }
            switch (intval($statuses[0]))
            {
                case 1: if($this->config->item('showPlanned'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                case 2: if($this->config->item('showRequested'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRequested); break;  // Requested
                case 3: if($this->config->item('showAccepted'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                case 4: if($this->config->item('showRejected'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                case '5': $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff); break;    //Day off
                case '6': $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff); break;    //Day off
            }//Two statuses in the cell
        } else {//Only one status in the cell
            switch ($day->display) {
                case '1':   //All day
                    if($displayLeaveInCell != null) {
                        $sheet->setCellValue($col . $line, $displayLeaveInCell($day->type));
                        $sheet->setCellValue($col . ($line + 1), $displayLeaveInCell($day->type));
                    }
                    if($displayLeaveInComment != null){
                        $sheet->getComment($col . $line)->getText()->createTextRun($displayLeaveInComment($day->type));
                        $sheet->getComment($col . ($line + 1))->getText()->createTextRun($displayLeaveInComment($day->type));
                    }

                    switch ($day->status)
                    {
                        // 1 : 'Planned';
                        // 2 : 'Requested';
                        // 3 : 'Accepted';
                        // 4 : 'Rejected';
                        case 1: if($this->config->item('showPlanned'))$sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                        case 2: if($this->config->item('showRequested'))$sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgRequested); break; // Requested
                        case 3: if($this->config->item('showAccepted'))$sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                        case 4: if($this->config->item('showRejected'))$sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                    }
                    break;
                case '2':   //AM
                    if($displayLeaveInCell != null)$sheet->setCellValue($col . $line,$displayLeaveInCell($day->type));
                    if($displayLeaveInComment != null)$sheet->getComment($col . $line)->getText()->createTextRun($displayLeaveInComment($day->type));
                    switch ($day->status)
                    {
                        case 1: if($this->config->item('showPlanned'))$sheet->getStyle($col . $line)->applyFromArray($styleBgPlanned); break;  // Planned
                        case 2: if($this->config->item('showRequested'))$sheet->getStyle($col . $line)->applyFromArray($styleBgRequested); break;  // Requested
                        case 3: if($this->config->item('showAccepted'))$sheet->getStyle($col . $line)->applyFromArray($styleBgAccepted); break;  // Accepted
                        case 4: if($this->config->item('showRejected'))$sheet->getStyle($col . $line)->applyFromArray($styleBgRejected); break;  // Rejected
                    }
                    break;
                case '3':   //PM
                    if($displayLeaveInCell != null)$sheet->setCellValue($col . ($line+1),$displayLeaveInCell($day->type));
                    if($displayLeaveInComment != null)$sheet->getComment($col . ($line + 1))->getText()->createTextRun($displayLeaveInComment($day->type));
                    switch ($day->status)
                    {
                        case 1: if($this->config->item('showPlanned'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                        case 2: if($this->config->item('showRequested'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRequested); break;  // Requested
                        case 3: if($this->config->item('showAccepted'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                        case 4: if($this->config->item('showRejected'))$sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                    }
                    break;
                case '4': //Full day off
                    $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgDayOff);
                    $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                    $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                    break;
                case '5':  //AM off
                    $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff);
                    $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                    break;
                case '6':   //PM off
                    $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff);
                    $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                    break;
            }
        }//Only one status in the cell
    }//day
    $line += 2;
}//Employee

//Autofit for all column containing the days
for ($ii = 1; $ii <=$lastDay; $ii++) {
    $col = $this->excel->column_name($ii + 3);
    $sheet->getStyle($col . '8:' . $col . ($line - 1))->applyFromArray($dayBox);
    $sheet->getColumnDimension($col)->setAutoSize(TRUE);
}
$sheet->getColumnDimension('A')->setAutoSize(TRUE);
$sheet->getColumnDimension('B')->setAutoSize(TRUE);
$sheet->getColumnDimension('C')->setWidth(40);

//Set layout to landscape and make the Excel sheet fit to the page
$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

exportSpreadsheet($this, 'tabular');
