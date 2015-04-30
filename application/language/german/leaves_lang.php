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
 */

$lang['leaves_summary_title'] = 'Meine Übersicht';
$lang['leaves_summary_title_overtime'] = 'Überstundendetails (addiert mit Zeitausgleich)';
$lang['leaves_summary_key_overtime'] = 'Nachholen für/wegen';
$lang['leaves_summary_thead_type'] = 'Art des Urlaubs';
$lang['leaves_summary_thead_available'] = 'Verfügbar';
$lang['leaves_summary_thead_taken'] = 'Belegt';
$lang['leaves_summary_thead_entitled'] = 'Bezugsberechtigt';
$lang['leaves_summary_thead_description'] = 'Beschreibung';
$lang['leaves_summary_tbody_empty'] = 'Keine bezugsberechtigten oder bezogenen Urlaubstage für diese Periode gefunden. Bitte wenden Sie sich an Ihre Personalabteilung oder Ihren direkten Vorgesetzten.';
$lang['leaves_summary_flash_msg_error'] = 'Es sieht so aus als hätten Sie keinen Vertrag. Bitte kontaktieren Sie Ihre Personalabteilung oder Ihren direkten Vorgesetzten.';
$lang['leaves_summary_date_field'] = 'Datum des Reports';

$lang['leaves_index_title'] = 'Meine Urlaubsanfragen';
$lang['leaves_index_thead_tip_view'] = 'anzeigen';
$lang['leaves_index_thead_tip_edit'] = 'bearbeiten';
$lang['leaves_index_thead_tip_delete'] = 'löschen';
$lang['leaves_index_thead_id'] = 'ID';
$lang['leaves_index_thead_start_date'] = 'Anfangsdatum';
$lang['leaves_index_thead_end_date'] = 'Enddatum';
$lang['leaves_index_thead_cause'] = 'Grund';
$lang['leaves_index_thead_duration'] = 'Dauer';
$lang['leaves_index_thead_type'] = 'Typ';
$lang['leaves_index_thead_status'] = 'Status';
$lang['leaves_index_button_export'] = 'Diese Liste exportieren';
$lang['leaves_index_button_create'] = 'Neue Anfrage';
$lang['leaves_index_popup_delete_title'] = 'Urlaubsanfrage löschen';
$lang['leaves_index_popup_delete_message'] = 'Sie sind dabei eine Urlaubsanfrage zu löschen, dieser Vorgang kann nicht rückgängig gemacht werden.';
$lang['leaves_index_popup_delete_question'] = 'Möchten Sie fortfahren?';
$lang['leaves_index_popup_delete_button_yes'] = 'Ja';
$lang['leaves_index_popup_delete_button_no'] = 'Nein';

$lang['leaves_date_type_morning'] = 'Vormittag';
$lang['leaves_date_type_afternoon'] = 'Nachmittag';

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
$lang['leaves_validate_flash_msg_no_contract'] = 'Es sieht so aus als hätten Sie keinen Vertrag. Bitte kontaktieren Sie Ihre Personalabteilung oder Ihren direkten Vorgesetzten.';
$lang['leaves_validate_flash_msg_overlap_period'] = 'Sie können einen Urlaubsantrag für zwei jährliche Urlaubszeiten nicht zu schaffen. Bitte erstellen Sie zwei verschiedene Urlaubsanträge.';

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
