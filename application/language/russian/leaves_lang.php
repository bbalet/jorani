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

$lang['leaves_summary_title'] = 'Моя сводная информация';
$lang['leaves_summary_title_overtime'] = 'Overtime details (added to compensate leaves)';
$lang['leaves_summary_key_overtime'] = 'нагнать';
$lang['leaves_summary_thead_type'] = 'тип отпуска';
$lang['leaves_summary_thead_available'] = 'Доступно';
$lang['leaves_summary_thead_taken'] = 'взятый';
$lang['leaves_summary_thead_entitled'] = 'предоставляемые дни';
$lang['leaves_summary_thead_description'] = 'Описание';
$lang['leaves_summary_tbody_empty'] = 'No entitled or taken days for this period. Please contact your HR Officer / Manager.';
$lang['leaves_summary_flash_msg_error'] = 'It appears you have no contract. Please contact your HR Officer / Manager.';
$lang['leaves_summary_date_field'] = 'Дата отчёта';
$lang['leaves_index_title'] = 'Мои заявления на отпуск';
$lang['leaves_index_thead_tip_view'] = 'просмотреть';
$lang['leaves_index_thead_tip_edit'] = 'редактировать';
$lang['leaves_index_thead_tip_delete'] = 'удалить';
$lang['leaves_index_thead_id'] = 'ИД';
$lang['leaves_index_thead_start_date'] = 'Дата начала';
$lang['leaves_index_thead_end_date'] = 'Дата окончания';
$lang['leaves_index_thead_cause'] = 'Причина';
$lang['leaves_index_thead_duration'] = 'Продолжительность';
$lang['leaves_index_thead_type'] = 'Тип';
$lang['leaves_index_thead_status'] = 'Состояние';
$lang['leaves_index_button_export'] = 'Экспортировать список';
$lang['leaves_index_button_create'] = 'Новый запрос';
$lang['leaves_index_popup_delete_title'] = 'Удалить заявление на отпуск';
$lang['leaves_index_popup_delete_message'] = 'You are about to delete one leave request, this procedure is irreversible.';
$lang['leaves_index_popup_delete_question'] = 'Продолжить?';
$lang['leaves_index_popup_delete_button_yes'] = 'Да';
$lang['leaves_index_popup_delete_button_no'] = 'Нет';
$lang['leaves_date_type_morning'] = 'Утро';
$lang['leaves_date_type_afternoon'] = 'После полудня';
$lang['leaves_create_title'] = 'Отправить заявление на отпуск';
$lang['leaves_create_field_start'] = 'Дата начала';
$lang['leaves_create_field_end'] = 'Дата окончания';
$lang['leaves_create_field_type'] = 'тип отпуска';
$lang['leaves_create_field_duration'] = 'Продолжительность';
$lang['leaves_create_field_duration_message'] = 'You are exceeding your entitled days';
$lang['leaves_create_field_overlapping_message'] = 'Вы отправили еще одно заявление на отпуск на те же даты.';
$lang['leaves_create_field_cause'] = 'Причина (необязательный)';
$lang['leaves_create_field_status'] = 'Состояние';
$lang['leaves_create_button_create'] = 'Запросить отпуск';
$lang['leaves_create_button_cancel'] = 'Отмена';
$lang['leaves_create_flash_msg_success'] = 'Заявление на отпуск успешно создан.';
$lang['leaves_create_flash_msg_error'] = 'The leave request has been succesfully created or updated, but you don\'t have a manager.';
$lang['leaves_edit_html_title'] = 'Редактировать заявление на отпуск';
$lang['leaves_edit_title'] = 'Редактировать заявление на отпуск №';
$lang['leaves_edit_field_start'] = 'Дата начала';
$lang['leaves_edit_field_end'] = 'Дата окончания';
$lang['leaves_edit_field_type'] = 'тип отпуска';
$lang['leaves_edit_field_duration'] = 'Продолжительность';
$lang['leaves_edit_field_duration_message'] = 'You are exceeding your entitled days';
$lang['leaves_edit_field_cause'] = 'Причина (необязательный)';
$lang['leaves_edit_field_status'] = 'Состояние';
$lang['leaves_edit_button_update'] = 'Обновить отпуск';
$lang['leaves_edit_button_cancel'] = 'Отмена';
$lang['leaves_edit_flash_msg_error'] = 'Невозможно изменить уже отправленное заявление на отпуск';
$lang['leaves_edit_flash_msg_success'] = 'Заявление на отпуск успешно обновлено';
$lang['leaves_validate_mandatory_js_msg'] = '"Поле " + fieldname + " является обязательным."';
$lang['leaves_validate_flash_msg_no_contract'] = 'It appears you have no contract. Please contact your HR Officer / Manager.';
$lang['leaves_validate_flash_msg_overlap_period'] = 'You can\'t create a leave request for two yearly leave periods. Please create two different leave requests.';
$lang['leaves_delete_flash_msg_error'] = 'You can\'t delete this leave request';
$lang['leaves_delete_flash_msg_success'] = 'Заявление на отпуск успешно удалено';
$lang['leaves_view_title'] = 'Просмотреть заявление на отпуск №';
$lang['leaves_view_html_title'] = 'Просмотреть заявление на отпуск';
$lang['leaves_view_field_start'] = 'Дата начала';
$lang['leaves_view_field_end'] = 'Дата окончания';
$lang['leaves_view_field_type'] = 'тип отпуска';
$lang['leaves_view_field_duration'] = 'Продолжительность';
$lang['leaves_view_field_cause'] = 'Причина';
$lang['leaves_view_field_status'] = 'Состояние';
$lang['leaves_view_button_edit'] = 'Редактировать';
$lang['leaves_view_button_back_list'] = 'Вернуться к списку';
$lang['leaves_export_title'] = 'Список отпусков';
$lang['leaves_export_thead_id'] = 'ИД';
$lang['leaves_export_thead_start_date'] = 'Дата начала';
$lang['leaves_export_thead_start_date_type'] = 'Утро/День';
$lang['leaves_export_thead_end_date'] = 'Дата окончания';
$lang['leaves_export_thead_end_date_type'] = 'Утро/День';
$lang['leaves_export_thead_cause'] = 'Причина';
$lang['leaves_export_thead_duration'] = 'Продолжительность';
$lang['leaves_export_thead_type'] = 'Тип';
$lang['leaves_export_thead_status'] = 'Состояние';
