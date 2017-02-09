<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 * @author       Christian SONNENBERG
 */

$lang['contract_index_title'] = 'Liste der Verträge';
$lang['contract_index_thead_id'] = 'ID';
$lang['contract_index_thead_name'] = 'Name';
$lang['contract_index_thead_start'] = 'Anfangsdatum';
$lang['contract_index_thead_end'] = 'Enddatum';
$lang['contract_index_tip_delete'] = 'Vertrag löschen';
$lang['contract_index_tip_edit'] = 'Vertrag bearbeiten';
$lang['contract_index_tip_entitled'] = 'bezugsberechtigte Tage';
$lang['contract_index_tip_dayoffs'] = 'Ferientage und Wochenenden';
$lang['contract_index_tip_exclude_types'] = 'Exclude leave types';
$lang['contract_index_button_export'] = 'Diese Liste exportieren';
$lang['contract_index_button_create'] = 'Vertrag erstellen';
$lang['contract_index_popup_delete_title'] = 'Vertrag löschen';
$lang['contract_index_popup_delete_description'] = 'Sie sind dabei einen Vertrag zu löschen, diese Prozedur kann nicht rückgängig gemacht werden.';
$lang['contract_index_popup_delete_confirm'] = 'Möchten Sie fortfahren?';
$lang['contract_index_popup_delete_button_yes'] = 'Ja';
$lang['contract_index_popup_delete_button_no'] = 'Nein';
$lang['contract_index_popup_entitled_title'] = 'Bezugsberechtigte Tage';
$lang['contract_index_popup_entitled_button_cancel'] = 'Abbrechen';
$lang['contract_index_popup_entitled_button_close'] = 'Schließen';

$lang['contract_exclude_title'] = 'Exclude leave types from a contract';
$lang['contract_exclude_description'] = 'You cannot exclude leave types already in use (used at least one time by en employee attached to the contract) and the default leave type (set on the contract or into the configuration file).';
$lang['contract_exclude_title_included'] = 'Included leave types';
$lang['contract_exclude_title_excluded'] = 'Excluded leave types';
$lang['contract_exclude_tip_include_type'] = 'Include this leave type';
$lang['contract_exclude_tip_exclude_type'] = 'Exclude this leave type';
$lang['contract_exclude_tip_already_used'] = 'This leave type is already in use';
$lang['contract_exclude_tip_default_type'] = 'You cannot exclude the default leave type';

$lang['contract_edit_title'] = 'Einen Vertrag bearbeiten';
$lang['contract_edit_description'] = 'Vertrag mit Nummer # bearbeiten';
$lang['contract_edit_field_name'] = 'Name';
$lang['contract_edit_field_start_month'] = 'Monat / Anfang';
$lang['contract_edit_field_start_day'] = 'Tag / Anfang';
$lang['contract_edit_field_end_month'] = 'Monat / Ende';
$lang['contract_edit_field_end_day'] = 'Tag / Ende';
$lang['contract_edit_default_leave_type'] = 'Default leave type';
$lang['contract_edit_button_update'] = 'Vertrag aktualisieren';
$lang['contract_edit_button_cancel'] = 'Abbrechen';
$lang['contract_edit_msg_success'] = 'Der Vertrag wurde erfolgreich aktualisiert';

$lang['contract_create_title'] = 'Neuen Vertrag erstellen';
$lang['contract_create_field_name'] = 'Name';
$lang['contract_create_field_start_month'] = 'Monat / Anfang';
$lang['contract_create_field_start_day'] = 'Tag / Anfang';
$lang['contract_create_field_end_month'] = 'Monat / Ende';
$lang['contract_create_field_end_day'] = 'Tag / Ende';
$lang['contract_create_default_leave_type'] = 'Default leave type';
$lang['contract_create_button_create'] = 'Vertrag erstellen';
$lang['contract_create_button_cancel'] = 'Abbrechen';
$lang['contract_create_msg_success'] = 'Der Vertrag wurde erfolgreich erstellt';

$lang['contract_delete_msg_success'] = 'Der Vertrag wurde erfolgreich gelöscht';

$lang['contract_export_title'] = 'Liste der Verträge';
$lang['contract_export_thead_id'] = 'ID';
$lang['contract_export_thead_name'] = 'Name';
$lang['contract_export_thead_start'] = 'Anfangsdatum';
$lang['contract_export_thead_end'] = 'Enddatum';

$lang['contract_calendar_title'] = 'Kalender für arbeitsfreie Tage';
$lang['contract_calendar_description'] = 'Arbeitsfreie Tage und Wochenenden sind standarmäßig nicht konfiguriert. Klicken Sie auf einen Tag um diesen zu bearbeiten oder wählen Sie den Knopf "Serie" aus.';
$lang['contract_calendar_legend_title'] = 'Legende:';
$lang['contract_calendar_legend_allday'] = 'ganztägig';
$lang['contract_calendar_legend_morning'] = 'Vormittag';
$lang['contract_calendar_legend_afternoon'] = 'Nachmittag';
$lang['contract_calendar_button_back'] = 'Zurück zu Verträgen';
$lang['contract_calendar_button_series'] = 'Serie von arbeitsfreien Tagen';
$lang['contract_calendar_popup_dayoff_title'] = 'Ändere Ferientag';
$lang['contract_calendar_popup_dayoff_field_title'] = 'Titel';
$lang['contract_calendar_popup_dayoff_field_type'] = 'Typ';
$lang['contract_calendar_popup_dayoff_type_working'] = 'Arbeitstag';
$lang['contract_calendar_popup_dayoff_type_off'] = 'ganztägig frei';
$lang['contract_calendar_popup_dayoff_type_morning'] = 'Vormittag frei';
$lang['contract_calendar_popup_dayoff_type_afternoon'] = 'Nachmittag frei';
$lang['contract_calendar_popup_dayoff_button_delete'] = 'Löschen';
$lang['contract_calendar_popup_dayoff_button_ok'] = 'OK';
$lang['contract_calendar_popup_dayoff_button_cancel'] = 'Abbrechen';
$lang['contract_calendar_popup_series_title'] = 'Serie von freien Tagen bearbeiten';
$lang['contract_calendar_popup_series_field_occurences'] = 'Alles markieren';
$lang['contract_calendar_popup_series_field_from'] = 'Von';
$lang['contract_calendar_popup_series_button_current'] = 'Aktuell';
$lang['contract_calendar_popup_series_field_to'] = 'Bis';
$lang['contract_calendar_popup_series_field_as'] = 'Als ein';
$lang['contract_calendar_popup_series_field_as_working'] = 'Arbeitstag';
$lang['contract_calendar_popup_series_field_as_off'] = 'ganztägig frei';
$lang['contract_calendar_popup_series_field_as_morning'] = 'Vormittag frei';
$lang['contract_calendar_popup_series_field_as_afternnon'] = 'Nachmittag frei';
$lang['contract_calendar_popup_series_field_title'] = 'Titel';
$lang['contract_calendar_popup_series_button_ok'] = 'OK';
$lang['contract_calendar_popup_series_button_cancel'] = 'Abbrechen';
$lang['contract_calendar_button_import'] = 'Import iCal';
$lang['contract_calendar_prompt_import'] = 'URL of non-working days iCal file';

$lang['contract_calendar_button_copy'] = 'Kopieren';
$lang['contract_calendar_copy_destination_js_msg'] = 'You must select a contract.';
$lang['contract_calendar_copy_msg_success'] = 'Data has been copied successfully.';
