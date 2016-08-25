<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.6
 */

$lang['admin_diagnostic_title'] = 'Data & Config Diagnostic';
$lang['admin_diagnostic_description'] = 'Detection of configuration and data problems';
$lang['admin_diagnostic_no_error'] = 'No error';

$lang['admin_diagnostic_requests_tab'] = 'Leave Requests';
$lang['admin_diagnostic_requests_description'] = 'Accepted but duplicated Leave Requests';
$lang['admin_diagnostic_requests_thead_id'] = 'ID';
$lang['admin_diagnostic_requests_thead_employee'] = 'Employee';
$lang['admin_diagnostic_requests_thead_start_date'] = 'Start Date';
$lang['admin_diagnostic_requests_thead_status'] = 'Status';
$lang['admin_diagnostic_requests_thead_type'] = 'Type';

$lang['admin_diagnostic_datetype_tab'] = 'Afternoon/Morning';
$lang['admin_diagnostic_datetype_description'] = 'Leave Requests with a wrong start/end type.';
$lang['admin_diagnostic_datetype_thead_id'] = 'ID';
$lang['admin_diagnostic_datetype_thead_employee'] = 'Employee';
$lang['admin_diagnostic_datetype_thead_start_date'] = 'Date';
$lang['admin_diagnostic_datetype_thead_start_type'] = 'Start';
$lang['admin_diagnostic_datetype_thead_end_type'] = 'End';
$lang['admin_diagnostic_datetype_thead_status'] = 'Status';

$lang['admin_diagnostic_entitlements_tab'] = 'Entitled days';
$lang['admin_diagnostic_entitlements_description'] = 'List of contracts and employees having entitlements for more than one year.';
$lang['admin_diagnostic_entitlements_thead_id'] = 'ID';
$lang['admin_diagnostic_entitlements_thead_type'] = 'Type';
$lang['admin_diagnostic_entitlements_thead_name'] = 'Name';
$lang['admin_diagnostic_entitlements_thead_start_date'] = 'Start Date';
$lang['admin_diagnostic_entitlements_thead_end_date'] = 'End Date';
$lang['admin_diagnostic_entitlements_type_contract'] = 'Contract';
$lang['admin_diagnostic_entitlements_type_employee'] = 'Employee';
$lang['admin_diagnostic_entitlements_deletion_problem'] = 'Incomplete deletion into database.' ;

$lang['admin_diagnostic_daysoff_tab'] = 'Non-working days';
$lang['admin_diagnostic_daysoff_description'] = 'Number of days (per contract) for which a non-working duration has been defined.';
$lang['admin_diagnostic_daysoff_thead_id'] = 'ID';
$lang['admin_diagnostic_daysoff_thead_name'] = 'Name';
$lang['admin_diagnostic_daysoff_thead_ym1'] = 'Last year';
$lang['admin_diagnostic_daysoff_thead_y'] = 'This year';
$lang['admin_diagnostic_daysoff_thead_yp1'] = 'Next year';

$lang['admin_diagnostic_overtime_tab'] = 'Overtime';
$lang['admin_diagnostic_overtime_description'] = 'Overtime requests with a negative duration';
$lang['admin_diagnostic_overtime_thead_id'] = 'ID';
$lang['admin_diagnostic_overtime_thead_employee'] = 'Employee';
$lang['admin_diagnostic_overtime_thead_date'] = 'Date';
$lang['admin_diagnostic_overtime_thead_duration'] = 'Duration';
$lang['admin_diagnostic_overtime_thead_status'] = 'Status';

$lang['admin_diagnostic_contract_tab'] = 'Contracts';
$lang['admin_diagnostic_contract_description'] = 'Unused contracts (check if the contract is not duplicated).';
$lang['admin_diagnostic_contract_thead_id'] = 'ID';
$lang['admin_diagnostic_contract_thead_name'] = 'Name';

$lang['admin_diagnostic_balance_tab'] = 'Balance';
$lang['admin_diagnostic_balance_description'] = 'Leave requests for which there are no entitlments.';
$lang['admin_diagnostic_balance_thead_id'] = 'ID';
$lang['admin_diagnostic_balance_thead_employee'] = 'Employee';
$lang['admin_diagnostic_balance_thead_contract'] = 'Contract';
$lang['admin_diagnostic_balance_thead_start_date'] = 'Start Date';
$lang['admin_diagnostic_balance_thead_status'] = 'Status';
