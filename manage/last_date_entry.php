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

page(_($help_context = "Last Date Entry"));
simple_page_mode(false);

if (isset($_POST['set']) && $_POST['set'] == 'set' && isset($_POST['att_date']) && isset($_POST['time'])) {
	if ($_POST['att_date'] != '' && $_POST['time'] != '') {
		// echo $_POST['time'];
		$hr = date('H',strtotime($_POST['time']));
		$sql = "UPDATE 0_daily_attendance d SET d.exit_date = d.att_date, ExitTime = concat(".$hr.",':0',(d.emp_id % 10))  WHERE d.att_date = '".$_POST['att_date']."' AND d.AttdStatus < 8";
		db_query($sql, _('Could not delete attandence.'));
		$sql = "UPDATE 0_daily_attendance d SET d.rate = get_ot_rate(d.emp_id), d.hours_no = get_ot_hour(d.emp_id,timestamp(d.att_date,d.EntryTime),timestamp(d.exit_date,d.ExitTime)) WHERE d.att_date = '".$_POST['att_date']."' AND d.AttdStatus < 8";
		db_query($sql, _('Could not delete attandence.'));
		display_notification('Successfully Set Time');
	}else{
		display_error('Please Insert Data correctly');
	}
}

start_form();
start_table(TABLESTYLE2);
	input_date(_('Date').':', 'att_date');
	text_row_ex(_('Time').':', 'time', 37, 50);
end_table(1);
btn_submit_select('submit','set','','set','SET');
end_form();
end_page();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	tag = document.getElementsByTagName("input");
	setInterval(function(){
		i=0;
		while(tag[i]){
			if (tag[i].name == 'time') {
				tag[i].type = 'time';
			}
			i++;
		}
	}, 100);
	// .match(regex)
</script>