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

$lang['hr_employees_title'] = 'Liste des employés';
$lang['hr_employees_thead_tip_edit'] = 'Modifier employé';
$lang['hr_employees_thead_tip_entitlment'] = 'crédit congés';
$lang['hr_employees_thead_link_leaves'] = 'Congés';
$lang['hr_employees_thead_link_extra'] = 'Heures supp.';
$lang['hr_employees_thead_link_balance'] = 'État des congés';
$lang['hr_employees_thead_link_create_leave'] = 'Créer demande d\'absence';
$lang['hr_employees_thead_link_presence'] = 'Rapport sur la présence';
$lang['hr_employees_thead_id'] = 'N°';
$lang['hr_employees_thead_firstname'] = 'Prénom';
$lang['hr_employees_thead_lastname'] = 'Nom';
$lang['hr_employees_thead_email'] = 'E-mail';
$lang['hr_employees_thead_contract'] = 'Contrat';
$lang['hr_employees_thead_manager'] = 'Manager';
$lang['hr_employees_button_create_user'] = 'Créer';
$lang['hr_employees_button_export'] = 'Exporter cette liste';
$lang['hr_employees_popup_entitlment_title'] = 'Crédit congés';
$lang['hr_employees_popup_entitlment_button_cancel'] = 'Annuler';
$lang['hr_employees_popup_entitlment_button_close'] = 'Fermer';
$lang['hr_employees_button_select'] = 'Selectionner';
$lang['hr_employees_field_entity'] = 'Entité';
$lang['hr_employees_popup_entity_title'] = 'Selectionner une entité';
$lang['hr_employees_popup_entity_button_ok'] = 'OK';
$lang['hr_employees_popup_entity_button_cancel'] = 'Annuler';
$lang['hr_employees_description'] = 'Clic droit sur un employé pour les actions contextuelles.';
$lang['hr_employees_field_subdepts'] = 'Inclure les sous-départments';

$lang['hr_export_employees_title'] = 'Liste des employés';
$lang['hr_export_employees_thead_id'] = 'N°';
$lang['hr_export_employees_thead_firstname'] = 'Prénom';
$lang['hr_export_employees_thead_lastname'] = 'Nom';
$lang['hr_export_employees_thead_email'] = 'E-mail';
$lang['hr_export_employees_thead_contract'] = 'Contrat';
$lang['hr_export_employees_thead_manager'] = 'Manager';

$lang['hr_leaves_create_title'] = 'Créer une nouvelle demande';
$lang['hr_leaves_create_field_start'] = 'Date de début';
$lang['hr_leaves_create_field_end'] = 'Date de fin';
$lang['hr_leaves_create_field_type'] = 'Type de congés';
$lang['hr_leaves_create_field_duration'] = 'Durée';
$lang['hr_leaves_create_field_duration_message'] = 'Vous dépassez le nombre de jours permis';
$lang['hr_leaves_create_field_overlapping_message'] = 'Vous avez demandé une absence durant la même période.';
$lang['hr_leaves_create_field_cause'] = 'Cause (optionelle)';
$lang['hr_leaves_create_field_status'] = 'Statut';
$lang['hr_leaves_create_button_create'] = 'Créer la demande';
$lang['hr_leaves_create_button_cancel'] = 'Annuler';
$lang['hr_leaves_create_flash_msg_success'] = 'La demande d\'absence a été créée avec succès.';
$lang['hr_leaves_create_flash_msg_error'] = 'La demande d\'absence a été créée ou modifiée avec succès, mais vous n\'avez pas de manager.';

$lang['hr_leaves_title'] = 'Liste des demandes de congés';
$lang['hr_leaves_html_title'] = 'Liste des demandes de congés de l\'employé n°';
$lang['hr_leaves_thead_tip_edit'] = 'Modifier';
$lang['hr_leaves_thead_tip_accept'] = 'Accepter';
$lang['hr_leaves_thead_tip_reject'] = 'Refuser';
$lang['hr_leaves_thead_tip_delete'] = 'Supprimer';
$lang['hr_leaves_thead_id'] = 'ID';
$lang['hr_leaves_thead_status'] = 'Statut';
$lang['hr_leaves_thead_start'] = 'Date début';
$lang['hr_leaves_thead_end'] = 'Date fin';
$lang['hr_leaves_thead_duration'] = 'Durée';
$lang['hr_leaves_thead_type'] = 'Type';
$lang['hr_leaves_button_export'] = 'Exporter cette liste';
$lang['hr_leaves_button_list'] = 'Liste des employés';
$lang['hr_leaves_popup_delete_title'] = 'Supprimer la demande de congés';
$lang['hr_leaves_popup_delete_message'] = 'Vous êtes sur le point de supprimer une demande de congés, cette procédure est irréversible.';
$lang['hr_leaves_popup_delete_question'] = 'Voulez-vous continuer ?';
$lang['hr_leaves_popup_delete_button_yes'] = 'Oui';
$lang['hr_leaves_popup_delete_button_no'] = 'Non';

$lang['hr_export_leaves_title'] = 'Liste des demandes de congés';
$lang['hr_export_leaves_thead_id'] = 'N°';
$lang['hr_export_leaves_thead_status'] = 'Statut';
$lang['hr_export_leaves_thead_start'] = 'Date début';
$lang['hr_export_leaves_thead_end'] = 'Date fin';
$lang['hr_export_leaves_thead_duration'] = 'Durée';
$lang['hr_export_leaves_thead_type'] = 'Type';

$lang['hr_overtime_title'] = 'Déclarations d\'heures supp.';
$lang['hr_overtime_html_title'] = 'Déclarations d\'heures supp. de l\'employé n°';
$lang['hr_overtime_thead_tip_edit'] = 'Modifier';
$lang['hr_overtime_thead_tip_accept'] = 'Accepter';
$lang['hr_overtime_thead_tip_reject'] = 'Refuser';
$lang['hr_overtime_thead_tip_delete'] = 'Supprimer';
$lang['hr_overtime_thead_id'] = 'ID';
$lang['hr_overtime_thead_status'] = 'Statut';
$lang['hr_overtime_thead_date'] = 'Date';
$lang['hr_overtime_thead_duration'] = 'Durée';
$lang['hr_overtime_thead_cause'] = 'Cause';
$lang['hr_overtime_button_export'] = 'Exporter cette liste';
$lang['hr_overtime_button_list'] = 'Liste des employés';
$lang['hr_overtime_popup_delete_title'] = 'Supprimer déclaration';
$lang['hr_overtime_popup_delete_message'] = 'Vous êtes sur le point de supprimer une déclaration d\'heures supp., cette procédure est irréversible.';
$lang['hr_overtime_popup_delete_question'] = 'Voulez-vous continuer ?';
$lang['hr_overtime_popup_delete_button_yes'] = 'Oui';
$lang['hr_overtime_popup_delete_button_no'] = 'Non';

$lang['hr_export_overtime_title'] = 'Déclarations d\'heures supp.';
$lang['hr_export_overtime_thead_id'] = 'N°';
$lang['hr_export_overtime_thead_status'] = 'Statut';
$lang['hr_export_overtime_thead_date'] = 'Date';
$lang['hr_export_overtime_thead_duration'] = 'Durée';
$lang['hr_export_overtime_thead_cause'] = 'Cause';

$lang['hr_summary_title'] = 'Compteur de congés pour l\'employée #';
$lang['hr_summary_thead_type'] = 'Type de congés';
$lang['hr_summary_thead_available'] = 'Disponible';
$lang['hr_summary_thead_taken'] = 'Pris';
$lang['hr_summary_thead_entitled'] = 'Acquis';
$lang['hr_summary_thead_description'] = 'Description';
$lang['hr_summary_flash_msg_error'] = 'Cet employée n\'a pas de contrat.';
$lang['hr_summary_button_list'] = 'Liste des employés';
$lang['hr_summary_date_field'] = 'Date du rapport';

$lang['hr_presence_title'] = 'Rapport de présence';
$lang['hr_presence_description'] = 'Par défaut, ce rapport montre les valeurs du mois précédent. Veuillez noter que la liste des absences ne contient que les demandes de congé acceptées.';
$lang['hr_presence_thead_tip_edit'] = 'modifier';
$lang['hr_presence_thead_id'] = 'N°';
$lang['hr_presence_thead_start'] = 'Date début';
$lang['hr_presence_thead_end'] = 'Date de fin';
$lang['hr_presence_thead_duration'] = 'Durée';
$lang['hr_presence_thead_type'] = 'Type';
$lang['hr_presence_button_execute'] = 'Exécuter';
$lang['hr_presence_button_list'] = 'Liste des employeés';
$lang['hr_presence_employee'] = 'Employé';
$lang['hr_presence_contract'] = 'Contrat';
$lang['hr_presence_month'] = 'Mois';
$lang['hr_presence_days'] = 'Nombre de jours';
$lang['hr_presence_working_days'] = 'Nombre de jours ouvrés';
$lang['hr_presence_non_working_days'] = 'Nombre de jours non travaillés';
$lang['hr_presence_leave_duration'] = 'Durée des absences';
$lang['hr_presence_work_duration'] = 'Durée de présence';
$lang['hr_presence_overlapping_detected'] = 'Chevauchement detecté';
$lang['hr_presence_no_contract'] = 'L\'employé n\'a pas de contrat';
$lang['hr_presence_please_check'] = 'Veuillez vérifier';
$lang['hr_presence_leaves_list_title'] = 'Liste des absences du mois';
