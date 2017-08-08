<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 * @author       Christian SONNENBERG
 */

$lang['leaves_summary_title'] = 'Meine Übersicht';
$lang['leaves_summary_title_overtime'] = 'Überstundendetails (addiert mit Zeitausgleich)';
$lang['leaves_summary_key_overtime'] = 'Nachholen für/wegen';
$lang['leaves_summary_thead_type'] = 'Art des Urlaubs';
$lang['leaves_summary_thead_available'] = 'Verfügbar';
$lang['leaves_summary_thead_taken'] = 'Belegt';
$lang['leaves_summary_thead_entitled'] = 'Bezugsberechtigt';
$lang['leaves_summary_thead_description'] = 'Beschreibung';
$lang['leaves_summary_thead_actual'] = 'tatsächlich';
$lang['leaves_summary_thead_simulated'] = 'simuliert';
$lang['leaves_summary_tbody_empty'] = 'Keine bezugsberechtigten oder bezogenen Urlaubstage für diesen Zeitraum gefunden. Bitte wenden Sie sich an Ihre Personalabteilung oder Ihren Vorgesetzten.';
$lang['leaves_summary_flash_msg_error'] = 'Es scheint als hätten Sie keinen Vertrag. Bitte kontaktieren Sie Ihre Personalabteilung oder Ihren Vorgesetzten.';
$lang['leaves_summary_date_field'] = 'Datum des Reports';

$lang['leaves_index_title'] = 'Meine Urlaubsanfragen';
$lang['leaves_index_thead_tip_view'] = 'anzeigen';
$lang['leaves_index_thead_tip_edit'] = 'bearbeiten';
$lang['leaves_index_thead_tip_cancel'] = 'cancel';
$lang['leaves_index_thead_tip_delete'] = 'löschen';
$lang['leaves_index_thead_tip_history'] = 'show history';
$lang['leaves_index_thead_id'] = 'ID';
$lang['leaves_index_thead_start_date'] = 'Anfangsdatum';
$lang['leaves_index_thead_end_date'] = 'Enddatum';
$lang['leaves_index_thead_cause'] = 'Grund';
$lang['leaves_index_thead_duration'] = 'Dauer';
$lang['leaves_index_thead_type'] = 'Typ';
$lang['leaves_index_thead_status'] = 'Status';
$lang['leaves_index_thead_requested_date'] = 'Requested';
$lang['leaves_index_thead_last_change'] = 'Last change';
$lang['leaves_index_button_export'] = 'Diese Liste exportieren';
$lang['leaves_index_button_create'] = 'Neue Anfrage';
$lang['leaves_index_popup_delete_title'] = 'Urlaubsanfrage löschen';
$lang['leaves_index_popup_delete_message'] = 'Sie sind dabei eine Urlaubsanfrage zu löschen, dieser Vorgang kann nicht rückgängig gemacht werden.';
$lang['leaves_index_popup_delete_question'] = 'Möchten Sie fortfahren?';
$lang['leaves_index_popup_delete_button_yes'] = 'Ja';
$lang['leaves_index_popup_delete_button_no'] = 'Nein';

$lang['leaves_history_thead_changed_date'] = 'Changed Date';
$lang['leaves_history_thead_change_type'] = 'Change Type';
$lang['leaves_history_thead_changed_by'] = 'Changed By';
$lang['leaves_history_thead_start_date'] = 'Start Date';
$lang['leaves_history_thead_end_date'] = 'End Date';
$lang['leaves_history_thead_cause'] = 'Reason';
$lang['leaves_history_thead_duration'] = 'Duration';
$lang['leaves_history_thead_type'] = 'Type';
$lang['leaves_history_thead_status'] = 'Status';

$lang['leaves_create_title'] = 'Urlaubsanfrage übermitteln';
$lang['leaves_create_field_start'] = 'Anfangsdatum';
$lang['leaves_create_field_end'] = 'Enddatum';
$lang['leaves_create_field_type'] = 'Art des Urlaubs';
$lang['leaves_create_field_duration'] = 'Dauer';
$lang['leaves_create_field_duration_message'] = 'Sie überschreiten die bezugsberechtigten Tage';
$lang['leaves_create_field_overlapping_message'] = 'Es wurde bereits eine Urlaubsanfrage für die genannten Daten übermittelt.';
$lang['leaves_create_field_cause'] = 'Grund (optional)';
$lang['leaves_create_field_status'] = 'Status';
$lang['leaves_create_button_create'] = 'Urlaubsanfrage';
$lang['leaves_create_button_cancel'] = 'Abbrechen';

$lang['leaves_create_flash_msg_success'] = 'Urlaubsanfrage erfolgreich erstellt';
$lang['leaves_create_flash_msg_error'] = 'Urlaubsanfrage erfolgreich erstellt, Sie haben jedoch keinen eingetragenen Vorgesetzten.';

$lang['leaves_flash_spn_list_days_off'] = '%s non-working days in the period';
$lang['leaves_flash_msg_overlap_dayoff'] = 'Your leave request matches with a non-working day.';

$lang['leaves_cancellation_flash_msg_error'] = 'You can\'t cancel this leave request';
$lang['leaves_cancellation_flash_msg_success'] = 'The cancellation request has been successfully sent';
$lang['requests_cancellation_accept_flash_msg_success'] = 'The leave request has been successfully cancelled';
$lang['requests_cancellation_accept_flash_msg_error'] = 'An error occured while trying to accept the cancellation';
$lang['requests_cancellation_reject_flash_msg_success'] = 'The leave request has now its original status of Accepted';
$lang['requests_cancellation_reject_flash_msg_error'] = 'An error occured while trying to reject the cancellation';

$lang['leaves_edit_html_title'] = 'Urlaubsanfrage bearbeiten';
$lang['leaves_edit_title'] = 'Urlaubsanfrage # bearbeiten';
$lang['leaves_edit_field_start'] = 'Anfangsdatum';
$lang['leaves_edit_field_end'] = 'Enddatum';
$lang['leaves_edit_field_type'] = 'Art des Urlaubs';
$lang['leaves_edit_field_duration'] = 'Dauer';
$lang['leaves_edit_field_duration_message'] = 'Sie überschreiten die bezugsberechtigten Tage';
$lang['leaves_edit_field_cause'] = 'Grund (optional)';
$lang['leaves_edit_field_status'] = 'Status';
$lang['leaves_edit_button_update'] = 'Urlaub aktualisieren';
$lang['leaves_edit_button_cancel'] = 'Abbrechen';
$lang['leaves_edit_flash_msg_error'] = 'Bereits übermittelte Urlaubsanfragen können nicht bearbeitet werden.';
$lang['leaves_edit_flash_msg_success'] = 'Urlaubsanfrage erfolgreich aktualisiert';

$lang['leaves_validate_mandatory_js_msg'] = '"Das Feld " + fieldname + " ist zwingend erforderlich."';
$lang['leaves_validate_flash_msg_no_contract'] = 'Es scheint als hätten Sie keinen Vertrag. Bitte kontaktieren Sie Ihre Personalabteilung oder Ihren Vorgesetzten.';
$lang['leaves_validate_flash_msg_overlap_period'] = 'Sie können einen Urlaubsantrag für zwei jährliche Urlaubszeiten nicht zu schaffen. Bitte erstellen Sie zwei verschiedene Urlaubsanträge.';

$lang['leaves_cancel_flash_msg_error'] = 'You can\'t cancel this leave request';
$lang['leaves_cancel_flash_msg_success'] = 'The leave request has been successfully cancelled';
$lang['leaves_cancel_unauthorized_msg_error'] = 'You can\'t cancel a leave request starting in the past. Ask your manager for rejecting it.';

$lang['leaves_delete_flash_msg_error'] = 'Diese Urlaubsanfrage kann nicht gelöscht werden';
$lang['leaves_delete_flash_msg_success'] = 'Urlaubsanfrage erfolgreich gelöscht';

$lang['leaves_view_title'] = 'Urlaubsanfrage # anzeigen';
$lang['leaves_view_html_title'] = 'Eine Urlaubsanfrage anzeigen';
$lang['leaves_view_field_start'] = 'Anfangsdatum';
$lang['leaves_view_field_end'] = 'Enddatum';
$lang['leaves_view_field_type'] = 'Art des Urlaubs';
$lang['leaves_view_field_duration'] = 'Dauer';
$lang['leaves_view_field_cause'] = 'Grund';
$lang['leaves_view_field_status'] = 'Status';
$lang['leaves_view_button_edit'] = 'Bearbeiten';
$lang['leaves_view_button_back_list'] = 'Zurück zur Liste';

$lang['leaves_export_title'] = 'Liste der Urlaube';
$lang['leaves_export_thead_id'] = 'ID';
$lang['leaves_export_thead_start_date'] = 'Anfangsdatum';
$lang['leaves_export_thead_start_date_type'] = 'Vormittag/Nachmittag';
$lang['leaves_export_thead_end_date'] = 'Enddatum';
$lang['leaves_export_thead_end_date_type'] = 'Vormittag/Nachmittag';
$lang['leaves_export_thead_cause'] = 'Grund';
$lang['leaves_export_thead_duration'] = 'Dauer';
$lang['leaves_export_thead_type'] = 'Typ';
$lang['leaves_export_thead_status'] = 'Status';

$lang['leaves_button_send_reminder'] = 'Send a reminder';
$lang['leaves_reminder_flash_msg_success'] = 'The reminder email was sent to the manager';

$lang['leaves_comment_title'] = 'Comments';
$lang['leaves_comment_new_comment'] = 'New comment';
$lang['leaves_comment_send_comment'] = 'Send comment';
$lang['leaves_comment_author_saying'] = ' says';
$lang['leaves_comment_status_changed'] = 'The status of the leave have been changed to ';
