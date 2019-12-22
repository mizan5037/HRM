<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-07-2017                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_HRSETUP';
$path_to_root  = '../../..';

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");

//--------------------------------------------------------------------------

page(_($help_context = "Manage Payment String"));
simple_page_mode(false);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	if(strlen($_POST['name']) == 0 || $_POST['name'] == '') {
		display_error( _("The Payment String name cannot be empty."));
		set_focus('name');
	}
	else {
		write_emp_pmt_str($selected_id, $_POST['name']);
		
    	if ($selected_id != "")
			display_notification(_('Selected Payment String has been updated'));
    	else
			display_notification(_('New Payment String has been added'));
		
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {

	if(salary_template_has_pmt_str_tmp(!$selected_id))
		display_error( _("The Payment String cannot be deleted."));
	else {
		delete_emp_pmt_str($selected_id);
		display_notification(_('Selected Payment String has been deleted'));
	}
	$Mode = 'RESET';
}

if($Mode == 'RESET')
	$selected_id = $_POST['selected_id']  = $_POST['name'] = '';

//--------------------------------------------------------------------------

start_form();
start_table(TABLESTYLE);
$th = array(_("Payment String Id"), _("Payment String Name"), "", "");
inactive_control_column($th);
table_header($th);
$result = db_query(get_emp_pmt_str(false, check_value('show_inactive')));
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);

	label_cell($myrow["emp_pmt_str_id"]);
	label_cell($myrow['emp_pmt_str_name']);
	inactive_control_cell($myrow["emp_pmt_str_id"], $myrow["inactive"], 'department', 'emp_pmt_str_id');
	edit_button_cell("Edit".$myrow["emp_pmt_str_id"], _("Edit"));
	delete_button_cell("Delete".$myrow["emp_pmt_str_id"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != '') {
	
 	if ($Mode == 'Edit') {	
		$myrow = get_emp_pmt_str($selected_id);
		display_notification($myrow);
		$_POST['name']  = $myrow["emp_pmt_str_name"];
		hidden('selected_id', $myrow['emp_pmt_str_id']);
 	}
}

text_row_ex(_("Payment String Name:"), 'name', 50, 60);

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();
end_page();
