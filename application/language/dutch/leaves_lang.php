<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 * @author Roger WOLFS
 */

$lang['leaves_summary_title'] = 'Mij overzicht';
$lang['leaves_summary_title_overtime'] = 'Overuren details (toegevoegd aan de compensatie verlof)';
$lang['leaves_summary_key_overtime'] = 'Catch up for';
$lang['leaves_summary_thead_type'] = 'Soort afwezigheid';
$lang['leaves_summary_thead_available'] = 'Beschikbaar';
$lang['leaves_summary_thead_taken'] = 'Opgenomen';
$lang['leaves_summary_thead_entitled'] = 'Recht op';
$lang['leaves_summary_thead_description'] = 'Omschrijving';
$lang['leaves_summary_thead_actual'] = 'werkelijk';
$lang['leaves_summary_thead_simulated'] = 'nagebootst';
$lang['leaves_summary_tbody_empty'] = 'Geen beschikbare of opgenomen dagen voor deze periode. Neem aub contact op met uw HR Officer/Manager.';
$lang['leaves_summary_flash_msg_error'] = 'Geen contract gegevens gevonden. Neem contact op met uw HR Officer / manager.';
$lang['leaves_summary_date_field'] = 'Datum rapport';

$lang['leaves_index_title'] = 'Mijn afwezigheidsverzoeken';
$lang['leaves_index_thead_tip_view'] = 'bekijken';
$lang['leaves_index_thead_tip_edit'] = 'bewerken';
$lang['leaves_index_thead_tip_delete'] = 'verwijderen';
$lang['leaves_index_thead_tip_history'] = 'show history';
$lang['leaves_index_thead_id'] = 'ID';
$lang['leaves_index_thead_start_date'] = 'Begin datum';
$lang['leaves_index_thead_end_date'] = 'Eind datum';
$lang['leaves_index_thead_cause'] = 'Reden';
$lang['leaves_index_thead_duration'] = 'Duur';
$lang['leaves_index_thead_type'] = 'Type';
$lang['leaves_index_thead_status'] = 'Status';
$lang['leaves_index_thead_requested_date'] = 'Requested';
$lang['leaves_index_thead_last_change'] = 'Last change';
$lang['leaves_index_button_export'] = 'Exporteer dit overzicht';
$lang['leaves_index_button_create'] = 'Nieuw verzoek';
$lang['leaves_index_popup_delete_title'] = 'Verwijder afwezigheidsverzoek';
$lang['leaves_index_popup_delete_message'] = 'U staat op het punt om een afwezigheidsverzoek te verwijderen; dit is onomkeerbaar.';
$lang['leaves_index_popup_delete_question'] = 'Wilt u verder gaan?';
$lang['leaves_index_popup_delete_button_yes'] = 'Ja';
$lang['leaves_index_popup_delete_button_no'] = 'Nee';

$lang['leaves_history_thead_changed_date'] = 'Changed Date';
$lang['leaves_history_thead_change_type'] = 'Change Type';
$lang['leaves_history_thead_changed_by'] = 'Changed By';
$lang['leaves_history_thead_start_date'] = 'Start Date';
$lang['leaves_history_thead_end_date'] = 'End Date';
$lang['leaves_history_thead_cause'] = 'Reason';
$lang['leaves_history_thead_duration'] = 'Duration';
$lang['leaves_history_thead_type'] = 'Type';
$lang['leaves_history_thead_status'] = 'Status';

$lang['leaves_create_title'] = 'Dien een afwezigheidsverzoek in';
$lang['leaves_create_field_start'] = 'Begin datum';
$lang['leaves_create_field_end'] = 'Eind datum';
$lang['leaves_create_field_type'] = 'Soort afwezigheid';
$lang['leaves_create_field_duration'] = 'Duur';
$lang['leaves_create_field_duration_message'] = 'U overschrijdt het voor u beschikbare aantal dagen';
$lang['leaves_create_field_overlapping_message'] = 'U heeft een ander afwezigheidsverzoek ingediend binnen dezelfde data.';
$lang['leaves_create_field_cause'] = 'Reden (optioneel)';
$lang['leaves_create_field_status'] = 'Status';
$lang['leaves_create_button_create'] = 'Vraaag afwezigheid aan';
$lang['leaves_create_button_cancel'] = 'Annuleren';

$lang['leaves_create_flash_msg_success'] = 'Het afwezigheidsverzoek is succesvol aangemaakt';
$lang['leaves_create_flash_msg_error'] = 'De afwezigheidsmelding is succesvol aangemaakt/bijgewerkt, echter u heeft geen manager.';

$lang['leaves_flash_spn_list_days_off'] = '%s non-working days in the period';
$lang['leaves_flash_msg_overlap_dayoff'] = 'Your leave request matches with a non-working day.';

$lang['leaves_cancellation_flash_msg_error'] = 'You can\'t cancel this leave request';
$lang['leaves_cancellation_flash_msg_success'] = 'The cancellation request has been successfully sent';
$lang['requests_cancellation_accept_flash_msg_success'] = 'The leave request has been successfully cancelled';
$lang['requests_cancellation_accept_flash_msg_error'] = 'An error occured while trying to accept the cancellation';
$lang['requests_cancellation_reject_flash_msg_success'] = 'The leave request has now its original status of Accepted';
$lang['requests_cancellation_reject_flash_msg_error'] = 'An error occured while trying to reject the cancellation';

$lang['leaves_edit_html_title'] = 'Bewerk een afwezigheidsverzoek';
$lang['leaves_edit_title'] = 'Bewerk afwezigheidsverzoek #';
$lang['leaves_edit_field_start'] = 'Begin datum';
$lang['leaves_edit_field_end'] = 'Eind datum';
$lang['leaves_edit_field_type'] = 'Soort afwezigheid';
$lang['leaves_edit_field_duration'] = 'Duur';
$lang['leaves_edit_field_duration_message'] = 'U overschrijdt het voor u beschikbare aantal dagen';
$lang['leaves_edit_field_cause'] = 'Reden (optioneel)';
$lang['leaves_edit_field_status'] = 'Status';
$lang['leaves_edit_button_update'] = 'Update afwezigheidsverzoek';
$lang['leaves_edit_button_cancel'] = 'Annuleren';
$lang['leaves_edit_flash_msg_error'] = 'U kunt een reeds verstuurd afwezigheidsverzoek niet bewerken';
$lang['leaves_edit_flash_msg_success'] = 'Het afwezigheidsverzoek is succesvol bijgewerkt';

$lang['leaves_validate_mandatory_js_msg'] = '"Het veld" + fieldname + "is verplicht."';
$lang['leaves_validate_flash_msg_no_contract'] = 'Geen contract gegevens gevonden. Neem contact op met uw HR Officer / manager.';
$lang['leaves_validate_flash_msg_overlap_period'] = 'U kunt geen afwezigheidsverzoek aanmaken dat de jaarperiode overschrijdt. Maak a.u.b. 2 aparte verzoeken aan.';

$lang['leaves_cancel_flash_msg_error'] = 'You can\'t cancel this leave request';
$lang['leaves_cancel_flash_msg_success'] = 'The leave request has been successfully cancelled';
$lang['leaves_cancel_unauthorized_msg_error'] = 'You can\'t cancel a leave request starting in the past. Ask your manager for rejecting it.';

$lang['leaves_delete_flash_msg_error'] = 'U kunt dit afwezigheidsverzoek niet verwijderen';
$lang['leaves_delete_flash_msg_success'] = 'Het afwezigheidsverzoek is succesvol verwijderd';

$lang['leaves_view_title'] = 'Bekijk afwezigheidsverzoek #';
$lang['leaves_view_html_title'] = 'Bekijk afwezigheidsverzoek';
$lang['leaves_view_field_start'] = 'Begin datum';
$lang['leaves_view_field_end'] = 'Eind datum';
$lang['leaves_view_field_type'] = 'Soort afwezigheid';
$lang['leaves_view_field_duration'] = 'Duur';
$lang['leaves_view_field_cause'] = 'Reden';
$lang['leaves_view_field_status'] = 'Status';
$lang['leaves_view_button_edit'] = 'Edit';
$lang['leaves_view_button_back_list'] = 'Terug naar overzicht';

$lang['leaves_export_title'] = 'Overzicht afwezigheid';
$lang['leaves_export_thead_id'] = 'ID';
$lang['leaves_export_thead_start_date'] = 'Begin datum';
$lang['leaves_export_thead_start_date_type'] = 'Morgen/Middag';
$lang['leaves_export_thead_end_date'] = 'Eind datum';
$lang['leaves_export_thead_end_date_type'] = 'Morgen/Middag';
$lang['leaves_export_thead_cause'] = 'Reden';
$lang['leaves_export_thead_duration'] = 'Duur';
$lang['leaves_export_thead_type'] = 'Type';
$lang['leaves_export_thead_status'] = 'Status';

$lang['leaves_button_send_reminder'] = 'Send a reminder';
$lang['leaves_reminder_flash_msg_success'] = 'The reminder email was sent to the manager';

$lang['leaves_comment_title'] = 'Comments';
$lang['leaves_comment_new_comment'] = 'New comment';
$lang['leaves_comment_send_comment'] = 'Send comment';
$lang['leaves_comment_author_saying'] = ' says';
$lang['leaves_comment_status_changed'] = 'The status of the leave have been changed to ';
