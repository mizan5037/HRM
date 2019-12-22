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
$proot  = __DIR__;

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

page(_($help_context = "Shift"));
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {
	$input_error = 0; 
	if (strlen($_POST['shift_name']) == 0 || $_POST['shift_name'] == '') {
		$input_error = 1;
		display_error(_("The Shift name cannot be empty."));
		set_focus('shift_name');
	}
	if (strlen($_POST['starting_time']) == 0 || $_POST['starting_time'] == '') {
		$input_error = 1;
		display_error(_("The Starting Time cannot be empty."));
		set_focus('starting_time');
	}
	if (strlen($_POST['ending_time']) == 0 || $_POST['ending_time'] == '') {
		$input_error = 1;
		display_error(_("The Ending Time cannot be empty."));
		set_focus('ending_time');
	}
	if ($input_error !=1) {
    	write_shift($selected_id, $_POST['shift_name'], $_POST['starting_time'], $_POST['ending_time'],$_POST['activity'],$_POST['break_start'],$_POST['break_end'] );
		if($selected_id != -1)
			display_notification(_('Selected Shift has been updated'));
		else
			display_notification(_('New Shift item has been added'));
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {

	if(shift_used($selected_id)){
			display_notification(_('Selected Shift has been deleted'));
		}
	else {
		delete_shift($selected_id);
		display_notification(_('Selected Shift has been deleted'));
	}
	
	$Mode = 'RESET';
}

if($Mode == 'RESET') {
	$selected_id = -1;
	$_POST['shift_name'] = '';
	$_POST['starting_time'] = '';
	$_POST['ending_time'] = '';
	$_POST['activity'] = '';
	$_POST['break_start'] = '';
	$_POST['break_end'] = '';
}

//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);
$th = array(_("Id"), _("Shift Name"), _("Starting Time"), _("Ending Time"),  _("Next day"), _("Break Start"), _("Break End"), _(""), _(""));
inactive_control_column($th);
table_header($th);
$result = db_query(get_shift(false, check_value('show_inactive')));
// display_notification(get_shift(false, check_value('show_inactive')));
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);
	//$pay_basis = $myrow['pay_basis'] == 0 ? _('Monthly') : _('Daily');

	label_cell($myrow["id"]);
	label_cell($myrow["shift_name"]);
	label_cell($myrow["starting_time"]);
	label_cell($myrow["ending_time"]);
	$nxt_day = $myrow["next_day_check"] == 0 ? 'No' : 'Yes';
	label_cell($nxt_day);
	label_cell($myrow["break_start"]);
	label_cell($myrow["break_end"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'id', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != -1) {
	
 	if ($Mode == 'Edit') {
		$shift_result = get_shift($selected_id);
		while($myrow = db_fetch($shift_result))
		{
			$_POST['shift_name']  = $myrow["shift_name"];
			$_POST['starting_time']  = $myrow["starting_time"];
			$_POST['ending_time']  = $myrow["ending_time"];
			$_POST['activity'] = $myrow["next_day_check"];
			$_POST['break_start']  = $myrow["break_start"];
			$_POST['break_end']  = $myrow["break_end"];
			hidden('selected_id', $selected_id);
		}				
 	}
		
}
text_row_ex(_('Shift Name').':', 'shift_name', 37, 50);
text_row_ex(_('Starting Time').':', 'starting_time', 37, 50);
text_row_ex(_('Ending Time').':', 'ending_time', 37, 50);
label_row(_('Next Day').':', radio(_('No'), 'activity', 0,1).'&nbsp;&nbsp;'.radio(_('Yes'), 'activity', 1));
text_row_ex(_('Break Start').':', 'break_start', 37, 50);
text_row_ex(_('Break End').':', 'break_end', 37, 50);

end_table(1);

submit_add_or_update_center($selected_id == -1, '','both');

end_form();
end_page();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	tag = document.getElementsByTagName("input");
	setInterval(function(){
		i=0;
		while(tag[i]){
			if (tag[i].name == 'starting_time' || tag[i].name == 'ending_time' || tag[i].name == 'break_start' || tag[i].name == 'break_end') {
				tag[i].type = 'time';
			}
			i++;
		}
	}, 100);
	// .match(regex)
</script>