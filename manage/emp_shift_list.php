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

page(_($help_context = "Shift Wise Employee"));
simple_page_mode(false);
// display_notification($selected_id);
// display_notification($_POST['selected_id']);
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	$input_error = 0;
	$selected_id = isset($_POST['selected_id']) ? $_POST['selected_id'] : false;
	// $selected_id =  ? $_POST['selected_id'] : false;
	if (strlen($_POST['emp_id']) == 0 || $_POST['emp_id'] == '') {
		$input_error++;
		display_error(_("The Employee field cannot be empty."));
		set_focus('emp_id');
	}
	if (isset($_POST['shift_id']) == 0 || $_POST['shift_id'] == '') {
		$input_error++;
		display_error(_("The Shift field cannot be empty."));
		set_focus('emp_id');
	}
	if (isset($_POST['from']) == 0 || $_POST['from'] == '') {
		$input_error++;
		display_error(_("The From field cannot be empty."));
		set_focus('emp_id');
	}
	if (isset($_POST['to']) == 0 || $_POST['to'] == '') {
		$input_error++;
		display_error(_("The To field cannot be empty."));
		set_focus('emp_id');
	}
	if ($input_error == 0) {
		// display_notification($selected_id);
    	write_emp_shift_list($selected_id, $_POST['emp_id'], $_POST['shift_id'], $_POST['from'], $_POST['to']);
		if($selected_id != '')
			display_notification(_('Employee shift has been updated'));
		else
			display_notification(_('Employee shift has been added'));
		$Mode = 'RESET';
	}
}

//--------------------------------------------------------------------------

if ($Mode == 'Delete') {
    delete_emp_shift_list($selected_id);
	display_notification(_('Selected Employee shift has been deleted'));
	$Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = isset($_POST['selected_id']) ? $_POST['selected_id'] : false;
	$selected_id = $_POST['selected_id'] = $_POST['emp_id'] = $_POST['shift_id'] = $_POST['from'] = $_POST['to'] = '';
}
 //display_notification(get_payment_string(false, check_value('show_inactive')));

//---------------------------------Serch Bar-----------------------------------------


	

start_form();

start_table(TABLESTYLE2);

text_row_ex(_("Enter Name or Card Number:"), 'name', 50, 60);

end_table(1);
// submit_add_or_update_center($selected_id == '', 'Search', 'both');
btn_submit_search('submit','search','','','Search');
// submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_form();
if (isset($_POST['search'])) {
	start_table(TABLESTYLE);
	$th = array(_("Id"), _("Name"), _("Card Number"), _("Designation"), "");
	inactive_control_column($th);
	table_header($th);
	$result = get_emp($_POST['name'], $_POST['employee_id'], check_value('show_inactive'));
	$k = 0;
	while ($myrow = db_fetch($result)) {
		start_form();
		alt_table_row_color($k);
		label_cell($myrow["emp_id"]);
		label_cell($myrow["emp_first_name"]);
		label_cell($myrow['emp_card_no']);
		label_cell($myrow["Designation"]);
		hidden('emp_id', $myrow["emp_id"]);
		hidden('e_name', $myrow["emp_first_name"]);
		hidden('emp_card_no', $myrow["emp_card_no"]);
		btn_submit_select('submit','search','','','Select');
		end_row();
		end_form();
	}
	inactive_control_row($th);
	end_table(1);

}
//--------------------------------------------------------------------------
start_form();
	start_table(TABLESTYLE2);
	if ($selected_id != '') {
	 	if ($Mode == 'Edit') {
			$myrow = get_emp_shift_list($selected_id);
			$_POST['id']  = $myrow["id"];
			$_POST['emp_id']  = $myrow["emp_id"];
			$_POST['shift_id'] = $myrow["shift_id"];
			$_POST['emp_card_no'] = $myrow["emp_card_no"];
			$_POST['from']  = $myrow["shift_from"];
			$_POST['to']  = $myrow["shift_to"];
			$_POST['e_name']  = $myrow["name"];
			hidden('selected_id', $myrow["id"]);
		}
	}
	label_row('Employee Name :', $_POST["e_name"]);
	label_row('Employee Card Number :', $_POST["emp_card_no"]);
	hidden('emp_id',$_POST['emp_id']);
	input_select_box('Shifts', 'shift_id','shift','id', 'shift_name',$myrow["shift_id"]);
	input_date(_('From').':', 'from',$_POST['from']);
	input_date(_('To').':', 'to',$_POST['to']);

	end_table(1);

	submit_add_or_update_center($selected_id == '', '', 'both');

	end_form();

	$result = db_query(get_emp_shift_list());

	start_form();
	start_table(TABLESTYLE);
	$th = array(_('Id'), _('Employee Name'), _('Card Number'), _('Shift Name'),_('From'),_('To'), "", "");

	// inactive_control_column($th);

	table_header($th);
	$k = 0;
$cnt =1;
	while ($myrow = db_fetch($result)) {
		alt_table_row_color($k);
		label_cell($cnt++);
		label_cell($myrow["name"]);
		label_cell($myrow["emp_card_no"]);
		label_cell($myrow["shift_name"]);
		label_cell($myrow["shift_from"]);
		label_cell($myrow["shift_to"]);
		// hidden('selected_id',$myrow["id"]);
	 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
		end_row();
	}

end_table(1);
end_form();



end_page();