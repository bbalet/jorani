<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.6
 */

$lang['admin_diagnostic_title'] = 'Diagnostic des données';
$lang['admin_diagnostic_description'] = 'Détection des anomalies de saisie et de configuration.';
$lang['admin_diagnostic_no_error'] = 'Aucune erreur détectée';

$lang['admin_diagnostic_requests_tab'] = 'Demandes de congé';
$lang['admin_diagnostic_requests_description'] = 'Demandes de congé acceptées et dupliquées';
$lang['admin_diagnostic_requests_thead_id'] = 'N°';
$lang['admin_diagnostic_requests_thead_employee'] = 'Employé';
$lang['admin_diagnostic_requests_thead_start_date'] = 'Date début';
$lang['admin_diagnostic_requests_thead_status'] = 'Statut';
$lang['admin_diagnostic_requests_thead_type'] = 'Type';

$lang['admin_diagnostic_datetype_tab'] = 'Après-midi/Matin';
$lang['admin_diagnostic_datetype_description'] = 'Demandes de congé avec un mauvais type de début/fin';
$lang['admin_diagnostic_datetype_thead_id'] = 'N°';
$lang['admin_diagnostic_datetype_thead_employee'] = 'Employé';
$lang['admin_diagnostic_datetype_thead_start_date'] = 'Date';
$lang['admin_diagnostic_datetype_thead_start_type'] = 'Début';
$lang['admin_diagnostic_datetype_thead_end_type'] = 'Fin';
$lang['admin_diagnostic_datetype_thead_status'] = 'Statut';

$lang['admin_diagnostic_entitlements_tab'] = 'Crédits congé';
$lang['admin_diagnostic_entitlements_description'] = 'Liste des contrats et des employés présentant des crédits congé supérieurs à un an.';
$lang['admin_diagnostic_entitlements_thead_id'] = 'N°';
$lang['admin_diagnostic_entitlements_thead_type'] = 'Type';
$lang['admin_diagnostic_entitlements_thead_name'] = 'Nom';
$lang['admin_diagnostic_entitlements_thead_start_date'] = 'Date début';
$lang['admin_diagnostic_entitlements_thead_end_date'] = 'Date fin';
$lang['admin_diagnostic_entitlements_type_contract'] = 'Contrat';
$lang['admin_diagnostic_entitlements_type_employee'] = 'Employé';
$lang['admin_diagnostic_entitlements_deletion_problem'] = 'La suppression de l\'objet n\'est pas complète.' ;

$lang['admin_diagnostic_daysoff_tab'] = 'Journées non travaillées';
$lang['admin_diagnostic_daysoff_description'] = 'Nombre de journées (par contrat) pour lesquelles une durée non travaillée a été définie.';
$lang['admin_diagnostic_daysoff_thead_id'] = 'N°';
$lang['admin_diagnostic_daysoff_thead_name'] = 'Nom';
$lang['admin_diagnostic_daysoff_thead_ym1'] = 'L\'année dernière';
$lang['admin_diagnostic_daysoff_thead_y'] = 'Cette année';
$lang['admin_diagnostic_daysoff_thead_yp1'] = 'L\'année prochaine';

$lang['admin_diagnostic_overtime_tab'] = 'Heures supp.';
$lang['admin_diagnostic_overtime_description'] = 'Déclarations d\'heures supplémentaires avec une durée négative.';
$lang['admin_diagnostic_overtime_thead_id'] = 'N°';
$lang['admin_diagnostic_overtime_thead_employee'] = 'Employé';
$lang['admin_diagnostic_overtime_thead_date'] = 'Date';
$lang['admin_diagnostic_overtime_thead_duration'] = 'Durée';
$lang['admin_diagnostic_overtime_thead_status'] = 'Statut';

$lang['admin_diagnostic_contract_tab'] = 'Contrats';
$lang['admin_diagnostic_contract_description'] = 'Contrats non utilisés (vérifier si le contrat n\'est pas en double).';
$lang['admin_diagnostic_contract_thead_id'] = 'N°';
$lang['admin_diagnostic_contract_thead_name'] = 'Nom';

$lang['admin_diagnostic_balance_tab'] = 'Balance';
$lang['admin_diagnostic_balance_description'] = 'Demandes de congé pour lesquelles il n\'y a pas de crédit.';
$lang['admin_diagnostic_balance_thead_id'] = 'N°';
$lang['admin_diagnostic_balance_thead_employee'] = 'Employé';
$lang['admin_diagnostic_balance_thead_contract'] = 'Contrat';
$lang['admin_diagnostic_balance_thead_start_date'] = 'Date début';
$lang['admin_diagnostic_balance_thead_status'] = 'Statut';
