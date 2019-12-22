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
$proot  = __DIR__;

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

page(_($help_context = "Unit"));
simple_page_mode(false);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	if(strlen($_POST['name']) == 0 || $_POST['name'] == '') {
		display_error( _("The unit name cannot be empty."));
		set_focus('name');
	}
	else {
		write_unit($selected_id, $_POST['name'], $_POST['code']);
		
    	if ($selected_id != "")
			display_notification(_('Selected unit has been updated'));
    	else
			display_notification(_('New unit has been added'));
		
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {

	if(unit_has_employees(!$selected_id)){
		display_error( _("The unit cannot be deleted."));
	}
	else {
		delete_unit($selected_id);
		display_notification(_('Selected unit has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET') {
    
	$selected_id = $_POST['selected_id'] = $_POST['name'] = '';
}


//--------------------------------------------------------------------------

start_form();
// display_notification(get_units());

start_table(TABLESTYLE);
$th = array(_("Unit Id"), _("Unit Name"), _("Unit Code"), "", "");
inactive_control_column($th);
table_header($th);

$result = db_query(get_units(false, check_value('show_inactive')));
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);
	label_cell($myrow["UnitID"]);
	label_cell($myrow['UnitName']);
	label_cell($myrow['UnitCode']);
	inactive_control_cell($myrow["UnitID"], $myrow["inactive"], 'unit', 'UnitID');
	edit_button_cell("Edit".$myrow["UnitID"], _("Edit"));
	delete_button_cell("Delete".$myrow["UnitID"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != '') {
	
 	if ($Mode == 'Edit') {
		$myrow = get_units($selected_id);
		$_POST['name']  = $myrow["UnitName"];
		$_POST['code']  = $myrow["UnitCode"];
		hidden('selected_id', $myrow['UnitID']);
 	}
}

text_row_ex(_("Unit Name:"), 'name', 50, 60);
text_row_ex(_("Unit Code:"), 'code', 50, 60);

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();
end_page();
