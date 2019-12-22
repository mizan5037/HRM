<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_HRSETUP';
$path_to_root  = '../../..';
$proot = __DIR__;
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();


$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();


include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

page(_($help_context = "Holiday"));
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {
	if(strlen($_POST['holiday_name']) == 0 || $_POST['holiday_name'] == '') {
		display_error( _("holiday_name field cannot be empty."));
		set_focus('holiday_name');
	}
	elseif(strlen($_POST['holiday_date']) == 0 || $_POST['holiday_date'] == '') {
		display_error( _("Date cannot be empty."));
		set_focus('holiday_date');
	}
	elseif(strlen($_POST['holiday_type']) == 0 || $_POST['holiday_type'] == '') {
		display_error( _("Date cannot be empty."));
		set_focus('holiday_type');
	}
	else {
		$holiday_date = str_replace("/","-",$_POST['holiday_date']);
		$id = $selected_id == -1 ? false : $selected_id;
		$sql=write_holiday($_POST['selected_id'], $_POST['holiday_name'], $holiday_date, $_POST['holiday_type']);
		//display_error($sql);
		if($selected_id == -1) {
			$new = true;
			$added_scale = db_insert_id();
		}
		else {
			$new = false;
			$added_scale = $selected_id;
		}
		
    	if ($selected_id != -1)
			display_notification(_('Selected Holiday has been updated'));
    	else
			display_notification(_('New Holiday has been added'));

		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {
	delete_holiday($selected_id);
	display_notification(_('Selected salary scale has been deleted'));
	$Mode = 'RESET';
}

if($Mode == 'RESET') {
	$selected_id = -1;
	$_POST['holiday_name'] = '';
	$_POST['holiday_date'] = '';
	$_POST['holiday_type'] = '';
}

//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if($selected_id != -1) {
	
 	if ($Mode == 'Edit') {
		
		$holiday_result = get_holiday($selected_id);
		while($myrow = db_fetch($holiday_result))
		{
			$_POST['holiday_name']  = $myrow["holiday_name"];
			$_POST['holiday_date']  = $myrow["holiday_date"];
			$_POST['holiday_type']  = $myrow["holiday_type"];
			hidden('selected_id', $selected_id);
		}				
 	}
}
text_row_ex(_('Holiday Name').':', 'holiday_name', 37, 50);
date_row(_('Holiday Date').':', 'holiday_date', null, null, 0, 0, 0);
holidaytype(_('Holiday Type'), 'holiday_type', $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);


end_table(1);

submit_add_or_update_center($selected_id == -1, '','both');

br(2);

// display_notification($_POST['holiday_date']);
start_table(TABLESTYLE);
$th = array(_("Id"), _("Holiday Name"), _("Holiday Date"), _("Holiday Type"), _(""), _(""));
inactive_control_column($th);
table_header($th);

$result = db_query(get_holiday(false, check_value('show_inactive')));
$k = 0;
$cnt = 0;
while ($myrow = db_fetch($result)) {
	$cnt++;
	alt_table_row_color($k);
	label_cell($cnt);
	// label_cell($myrow["serial"]);
	label_cell($myrow["holiday_name"]);
	label_cell($myrow["holiday_date"]);
	$tp = $myrow["holiday_type"] == 'w' ? 'Weekly' : 'Casually';
	label_cell($tp);
	inactive_control_cell($myrow["serial"], $myrow["inactive"], 'serial', 'serial');
	edit_button_cell("Edit".$myrow["serial"], _("Edit"));
	delete_button_cell("Delete".$myrow["serial"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

end_form();
end_page();
