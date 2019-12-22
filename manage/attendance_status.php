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

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------
function attendance_status_list($label, $name, $selected_id,$all_option)
{
	echo "<td>$label</td>\n";
	echo "<td>";
	$output  = "<select id=\"".$name."\" name=\"".$name."\">";	
	$output .= "<option value=\"".$all_option."\">".$all_option."</option>";	
	if ($selected_id=='Present') 
		{$output .= "<option value=\"Present\" selected=\"selected\">Present</option>";}
	else {$output .= "<option value=\"Present\">Present</option>";}
	if ($selected_id=='Leave') 
		{$output .= "<option value=\"Leave\" selected=\"selected\">Leave</option>";}
	else {$output .= "<option value=\"Leave\">Leave</option>";}
	if ($selected_id=='Late') 
		{$output .= "<option value=\"Late\" selected=\"selected\">Late</option>";}
	else {$output .= "<option value=\"Late\">Late</option>";}
	$output .= "</select>";
	echo $output;
	echo "</td>\n";
}
//--------------------------------------------------------------------------
function insert_edit_attendance_status($StatusID, $StatusName, $StatusFullName,$FlagStatus,$MaxLateMin)
{
	
  	if ($StatusID == false)
	  {
			$sql = "INSERT INTO ".TB_PREF."attendance_status (StatusName, StatusFullName,FlagStatus,MaxLateMin) VALUES ("		
				.db_escape($StatusName).", "
				.db_escape($StatusFullName).", "	
				.db_escape($FlagStatus).","		
				.db_escape($MaxLateMin).")";

			
			// return  db_insert_id(); 
		}
		else
		{
			$sql = "UPDATE ".TB_PREF."attendance_status SET StatusName = ".db_escape($StatusName)
			.", StatusFullName = ".db_escape($StatusFullName)
			.", FlagStatus = ".db_escape($FlagStatus)
			.", MaxLateMin = ".db_escape($MaxLateMin)
			." where StatusID = ".db_escape($StatusID);
			// db_query($sql,"The employee payslip could not be added");
			// return $StatusID;
		} 
		// display_notification($sql);
		db_query($sql,"The employee payslip could not be added");
		//return $sql;
}
//------------------------------------------------------------------------------
function delete_attendance_status($StatusID)
{
	$sql = "DELETE FROM  ".TB_PREF."attendance_status where StatusID = ".db_escape($StatusID);
	db_query($sql,"The employee payslip could not be added");
}
//------------------------------------------------------------------------------
page(_($help_context = "Manage Attendance Status"));
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {
	$selected_id = isset($_POST['selected_id']) ? $_POST['selected_id'] : false;
	// display_notification($selected_id);
		$sql=insert_edit_attendance_status($selected_id,$_POST['StatusName'], $_POST['StatusFullName'],$_POST['FlagStatus'],$_POST['MaxLateMin']);
		
    	if ($selected_id != '')
			display_notification(_('Selected attendance status has been updated'));
    	else
			display_notification(_('New attendance status has been added'));
		
		$Mode = 'RESET';
	
}

if ($Mode == 'Delete') {

	if(salary_scale_used($selected_id))
		display_error( _("This Status cannot be deleted."));
	else {
		delete_attendance_status($selected_id);
		display_notification(_('Selected Status has been deleted'));
	}
	$Mode = 'RESET';
}

if($Mode == 'RESET') {
	$_POST['StatusID'] = '';
	$_POST['StatusName'] = '';
	$_POST['StatusFullName'] = '';
	$_POST['FlagStatus'] = '';
	$_POST['MaxLateMin'] = '';
	$selected_id = '';
}

//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);
$th = array(_("Id"), _("Name"), _('Full Name'), _('Flag Status'), _('Maximum Limit'), "", "");
inactive_control_column($th);
table_header($th);

$result = db_query("SELECT StatusID,StatusName,StatusFullName,FlagStatus,MaxLateMin FROM ".TB_PREF."attendance_status");
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);
	label_cell($myrow["StatusID"]);
	label_cell($myrow['StatusName']);
	label_cell($myrow['StatusFullName']);
	label_cell($myrow['FlagStatus']);
	label_cell($myrow['MaxLateMin']);
	//inactive_control_cell($myrow["StatusID"], $myrow["inactive"], 'salaryscale', 'StatusID');
	edit_button_cell("Edit".$myrow["StatusID"], _("Edit"));
	delete_button_cell("Delete".$myrow["StatusID"], _("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != '') {
	
 	if ($Mode == 'Edit') {
		$result1 = db_query("SELECT * FROM ".TB_PREF."attendance_status where StatusID = ".db_escape($selected_id));
		$myrow1 = db_fetch($result1);
		$_POST['StatusID']  = $myrow1["StatusID"];
		$_POST['StatusName']  = $myrow1["StatusName"];
		$_POST['StatusFullName']  = $myrow1["StatusFullName"];
		$_POST['FlagStatus']  = $myrow1["FlagStatus"];
		$_POST['MaxLateMin']  = $myrow1["MaxLateMin"];
		hidden('selected_id', $myrow1["StatusID"]);
 	}
}
text_row_ex(_('Attendance Status').':', 'StatusName', 37, 50);
text_row_ex(_('Attendance Full Status').':', 'StatusFullName', 37, 50);
attendance_status_list('FlagStatus', 'FlagStatus', $_POST['FlagStatus'],'Select');
amount_row(_("Maximum Limit").':', 'MaxLateMin', null, null, null, null, true);

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();
end_page();
