<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author Oleg Koptev
 */

$lang['requests_index_title'] = 'Заявление на отпуск, представленные на ваше рассмотрение';
$lang['requests_index_description'] = 'This screen lists the leave requests submitted to you. If you are not a manager, this list will always be empty.';
$lang['requests_index_thead_tip_view'] = 'просмотреть';
$lang['requests_index_thead_tip_accept'] = 'принять';
$lang['requests_index_thead_tip_reject'] = 'отказать';
$lang['requests_index_thead_id'] = 'ИД';
$lang['requests_index_thead_fullname'] = 'ФИО';
$lang['requests_index_thead_startdate'] = 'Дата начала';
$lang['requests_index_thead_enddate'] = 'Дата окончания';
$lang['requests_index_thead_duration'] = 'Продолжительность';
$lang['requests_index_thead_type'] = 'Тип';
$lang['requests_index_thead_status'] = 'Состояние';

$lang['requests_collaborators_title'] = 'Список моих сотрудников';
$lang['requests_collaborators_description'] = 'This screen lists your collaborators. If you are not a manager, this list will always be empty.';
$lang['requests_collaborators_thead_id'] = 'ИД';
$lang['requests_collaborators_thead_link_balance'] = 'сальдо по отпускам';
$lang['requests_collaborators_thead_link_presence'] = 'отчет посещаемость';
$lang['requests_collaborators_thead_link_year'] = 'Календарь на год';
$lang['requests_collaborators_thead_link_create_leave'] = 'Create a leave request in behalf of this collaborator';
$lang['requests_collaborators_thead_firstname'] = 'Имя';
$lang['requests_collaborators_thead_lastname'] = 'Фамилия';
$lang['requests_collaborators_thead_email'] = 'Эл. почта';

$lang['requests_summary_title'] = 'сальдо по отпускам, для сотрудника N°';
$lang['requests_summary_thead_type'] = 'тип отпуска';
$lang['requests_summary_thead_available'] = 'Доступно';
$lang['requests_summary_thead_taken'] = 'взятый';
$lang['requests_summary_thead_entitled'] = 'предоставляемые дни';
$lang['requests_summary_thead_description'] = 'Описание';
$lang['requests_summary_flash_msg_error'] = 'This employee has no contract.';
$lang['requests_summary_flash_msg_forbidden'] = 'Вы не являетесь руководителем подразделения, к которому относится данный сотрудник.';
$lang['requests_summary_button_list'] = 'Список сотрудников';

$lang['requests_index_button_export'] = 'Экспортировать список';
$lang['requests_index_button_show_all'] = 'Все запросы';
$lang['requests_index_button_show_pending'] = 'Запросы на рассмотрении';

$lang['requests_accept_flash_msg_error'] = 'You are not the line manager of this employee. You cannot accept this leave request.';
$lang['requests_accept_flash_msg_success'] = 'Заявление на отпуск успешно одобрен.';
$lang['requests_reject_flash_msg_error'] = 'You are not the line manager of this employee. You cannot reject this leave request.';
$lang['requests_reject_flash_msg_success'] = 'Заявление на отпуск успешно отклонен.';

$lang['requests_export_title'] = 'Список заявлений на отпуск';
$lang['requests_export_thead_id'] = 'ИД';
$lang['requests_export_thead_fullname'] = 'ФИО';
$lang['requests_export_thead_startdate'] = 'Дата начала';
$lang['requests_export_thead_startdate_type'] = 'Утро/после полудня';
$lang['requests_export_thead_enddate'] = 'Дата окончания';
$lang['requests_export_thead_enddate_type'] = 'Утро/после полудня';
$lang['requests_export_thead_duration'] = 'Продолжительность';
$lang['requests_export_thead_type'] = 'Тип';
$lang['requests_export_thead_cause'] = 'Причина';
$lang['requests_export_thead_status'] = 'Состояние';

$lang['requests_delegations_title'] = 'Список передачи обязанностей';
$lang['requests_delegations_description'] = 'Это список работников, которые могут принимать или отклонять заявления от вашего имени.';
$lang['requests_delegations_thead_employee'] = 'Сотрудник';
$lang['requests_delegations_thead_tip_delete'] = 'Отозвать полномочия';
$lang['requests_delegations_button_add'] = 'Добавить';
$lang['requests_delegations_popup_delegate_title'] = 'Добавить уполномоченное лицо';
$lang['requests_delegations_popup_delegate_button_ok'] = 'Ок';
$lang['requests_delegations_popup_delegate_button_cancel'] = 'Отмена';
$lang['requests_delegations_confirm_delete_message'] = 'Вы уверены, что хотите отозвать данное полномочие?';
$lang['requests_delegations_confirm_delete_cancel'] = 'Отмена';
$lang['requests_delegations_confirm_delete_yes'] = 'Да';
