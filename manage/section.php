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

page(_($help_context = "Section"));
simple_page_mode(false);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	if(strlen($_POST['name']) == 0 || $_POST['name'] == '' || strlen($_POST['name_bd']) == 0 || $_POST['name_bd'] == '' ) {
		display_error( _("The section name cannot be empty."));
		set_focus('name');
	}
	else {
		write_section($selected_id, $_POST['name'], $_POST['name_bd']);
		
    	if ($selected_id != "")
			display_notification(_('Selected section has been updated'));
    	else
			display_notification(_('New section has been added'));
		
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {

	if(section_has_employees($selected_id))
		display_error( _("The Section cannot be deleted."));
	else {
		delete_section($selected_id);
		display_notification(_('Selected section has been deleted'));
	}
	$Mode = 'RESET';
}

if($Mode == 'RESET')
	$selected_id = $_POST['selected_id']  = $_POST['name'] = $_POST['name_bd'] ='';

//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);
$th = array(_("Section Id"), _("Section Name"), _("Section Name (Bangla)"), "", "");
inactive_control_column($th);
table_header($th);

$result = db_query(get_sections(false, check_value('show_inactive')));
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);

	label_cell($myrow["sec_id"]);
	label_cell($myrow['name']);
	label_cell($myrow['name_bd']);
    inactive_control_cell($myrow["sec_id"], $myrow["inactive"], 'section', 'section_id');
	edit_button_cell("Edit".$myrow["sec_id"], _("Edit"));
	delete_button_cell("Delete".$myrow["sec_id"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != '') {
	
 	if ($Mode == 'Edit') {
		
		$myrow = get_sections($selected_id);
		$_POST['name']  = $myrow["name"];
		$_POST['name_bd']  = $myrow["name_bd"];
		hidden('selected_id', $myrow['sec_id']);
 	}
}

text_row_ex(_("Section Name:"), 'name', 50, 60);
text_row_ex(_("Section Name(Bangla):"), 'name_bd', 50, 60);

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();
end_page();
