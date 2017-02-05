<?php
//This is a sample page showing how to export a custom report to Excel
//If your custom page is making an export to a file (Excel, etc.) simply include 'export' into its name
//http://localhost/jorani/excel-export

//Excel Header
$title = 'All Leave Requests';
$this->lang->load('requests', $this->language);
$this->lang->load('global', $this->language);
$ci = get_instance();
$ci->load->library('excel');
$sheet = $ci->excel->setActiveSheetIndex(0);
$sheet->setTitle($title);
$sheet->setCellValue('A1', lang('requests_index_thead_id'));
$sheet->setCellValue('B1', lang('requests_index_thead_fullname'));
$sheet->setCellValue('C1', lang('requests_index_thead_startdate'));
$sheet->setCellValue('D1', lang('requests_index_thead_enddate'));
$sheet->setCellValue('E1', lang('requests_index_thead_duration'));
$sheet->setCellValue('F1', lang('requests_index_thead_type'));
$sheet->setCellValue('G1', lang('requests_index_thead_status'));
$sheet->getStyle('A1:G1')->getFont()->setBold(true);
$sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Database query
$this->db->select('users.firstname, users.lastname, leaves.*');
$this->db->select('status.name as status_name, types.name as type_name');
$this->db->from('leaves');
$this->db->join('status', 'leaves.status = status.id');
$this->db->join('types', 'leaves.type = types.id');
$this->db->join('users', 'leaves.employee = users.id');
$this->db->order_by('users.lastname, users.firstname, leaves.startdate', 'desc');
$rows = $this->db->get()->result_array();

//Results
$line = 2;
foreach ($rows as $row) {
    $date = new DateTime($row['startdate']);
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($row['enddate']);
    $enddate = $date->format(lang('global_date_format'));
    $sheet->setCellValue('A' . $line, $row['id']);
    $sheet->setCellValue('B' . $line, $row['firstname'] . ' ' . $row['lastname']);
    $sheet->setCellValue('C' . $line, $startdate . ' (' . lang($row['startdatetype']). ')');
    $sheet->setCellValue('D' . $line, $enddate . ' (' . lang($row['enddatetype']) . ')');
    $sheet->setCellValue('E' . $line, $row['duration']);
    $sheet->setCellValue('F' . $line, $row['type_name']);
    $sheet->setCellValue('G' . $line, lang($row['status_name']));
    $line++;
}
//Autofit
foreach(range('A', 'G') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

//Export Excel file
$filename = 'excel-export.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
