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

page(_($help_context = "Create Employee List"));
simple_page_mode(false);
if (isset($_POST['set']) && $_POST['set'] == 'set' && isset($_POST['date'])) {
	if ($_POST['date'] != '') {
		$month = date('m',strtotime($_POST['date']));
		$year = date('Y',strtotime($_POST['date']));
		$months  = strlen($month) == 2 ? $month : '0'.$month;
		$date = $year.'-'.$months.'-01';
		$sql = "UPDATE 0_employee e SET e.inactive = 1 WHERE !e.inactive AND !e.emp_releasedate ='0000-00-00' AND e.emp_releasedate <= ".$date;
		db_query($sql, _('Could not delete attandence.'));
		display_notification("New Employee List Created Successfuly.");
	}else{
		display_error('Please Insert Data correctly');
	}
}
start_form();
start_table(TABLESTYLE2);
	input_date(_('Date').':', 'date');
end_table(1);
btn_submit_select('submit','set','','set','Create List');
end_form();
end_page();
?>