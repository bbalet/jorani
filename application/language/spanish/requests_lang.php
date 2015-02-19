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
 * @author hnestar
 */

$lang['requests_index_title'] = 'Validación de solicitudes';
$lang['requests_index_description'] = 'Esta pantalla muestra las solicitudes de permisos para aprovar por usted. Si no eres ungerente, esta lista siempre estará vacía.';
$lang['requests_index_thead_tip_view'] = 'ver';
$lang['requests_index_thead_tip_accept'] = 'aceptar';
$lang['requests_index_thead_tip_reject'] = 'rechazar';
$lang['requests_index_thead_id'] = 'ID';
$lang['requests_index_thead_fullname'] = 'Nombre completo';
$lang['requests_index_thead_startdate'] = 'Primera fecha';
$lang['requests_index_thead_enddate'] = 'Última fecha';
$lang['requests_index_thead_duration'] = 'Duración';
$lang['requests_index_thead_type'] = 'Tipo';
$lang['requests_index_thead_status'] = 'Estado';

$lang['requests_collaborators_title'] = 'Lista de mis colaboradores';
$lang['requests_collaborators_description'] = 'Esta pantalla muestra sus colaboradores. Si usted no es un gerente, esta lista siempre va a estar vacía.';
$lang['requests_collaborators_thead_id'] = 'ID';
$lang['requests_collaborators_thead_link_balance'] = 'Balance de permisos';
$lang['requests_collaborators_thead_firstname'] = 'Nombre';
$lang['requests_collaborators_thead_lastname'] = 'Apellido';
$lang['requests_collaborators_thead_email'] = 'E-mail';

$lang['requests_summary_title'] = 'Balance de permisos para el usuario #';
$lang['requests_summary_thead_type'] = 'Tipo de permiso';
$lang['requests_summary_thead_available'] = 'Disponible';
$lang['requests_summary_thead_taken'] = 'Recibido';
$lang['requests_summary_thead_entitled'] = 'Asociado';
$lang['requests_summary_thead_description'] = 'Descripción';
$lang['requests_summary_flash_msg_error'] = 'Este empleado no tiene contrato.';
$lang['requests_summary_flash_msg_forbidden'] = 'Tu no eres el reponsable de este empleado';
$lang['requests_summary_button_list'] = 'Lista de colaboradores';

$lang['requests_index_button_export'] = 'Exporta a listado';
$lang['requests_index_button_show_all'] = 'Todas las solicitudes';
$lang['requests_index_button_show_pending'] = 'Pendientes de solicitud';

$lang['requests_accept_flash_msg_error'] = 'Usted no es el responsable de este empleado. No se puede aceptar este permiso solicitud.';
$lang['requests_accept_flash_msg_success'] = 'La solicitud del permiso se ha aceptado correctamente.';

$lang['requests_reject_flash_msg_error'] = 'Usted no es el reponsable de este empleado. No se puede rechazar este permiso solicitud.';
$lang['requests_reject_flash_msg_success'] = 'La solicitud de permiso se ha rechazado correctamente.';

$lang['requests_export_title'] = 'Lista de solicitud de permisos';
$lang['requests_export_thead_id'] = 'ID';
$lang['requests_export_thead_fullname'] = 'Nombre completo';
$lang['requests_export_thead_startdate'] = 'Primera fecha';
$lang['requests_export_thead_startdate_type'] = 'Mañana/Tarde';
$lang['requests_export_thead_enddate'] = 'Última fecha';
$lang['requests_export_thead_enddate_type'] = 'Mañana/Tarde';
$lang['requests_export_thead_duration'] = 'Duración';
$lang['requests_export_thead_type'] = 'Tipo';
$lang['requests_export_thead_cause'] = 'Motivo';
$lang['requests_export_thead_status'] = 'Estado';

$lang['requests_delegations_title'] = 'Lista de las delegaciones';
$lang['requests_delegations_description'] = 'Esta es la lista de los empleados que pueden aceptar o rechazar una solicitud en su nombre.';
$lang['requests_delegations_thead_employee'] = 'empleado';
$lang['requests_delegations_thead_tip_delete'] = 'revocar';
$lang['requests_delegations_button_add'] = 'Añadir';
$lang['requests_delegations_popup_delegate_title'] = 'Añadir un delegado';
$lang['requests_delegations_popup_delegate_button_ok'] = 'OK';
$lang['requests_delegations_popup_delegate_button_cancel'] = 'Cancelar';
$lang['requests_delegations_confirm_delete_message'] = '¿Seguro que desea revocar esta delegación ?';
$lang['requests_delegations_confirm_delete_cancel'] = 'Cancelar';
$lang['requests_delegations_confirm_delete_yes'] = 'Sí';
