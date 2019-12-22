<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   daily_attendance.php                                 |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';
$proot  = __DIR__;

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

$date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
$dept_id = isset($_POST['dept_id']) ? $_POST['dept_id'] : '';
$att_sts = isset($_POST['att_sts']) ? $_POST['att_sts'] : '';

if (isset($_POST['SrchAttd'])) {
	$date = $_POST['date'];
	$card = $_POST['card_no'];
	$dept_id = $_POST['dept_id'];
	$att_sts = $_POST['att_sts'];
	if ($date > date('Y-m-d')) {
		display_error('Invalide Date..!!');
		$date = date('Y-m-d');
	}
	display_notification('Searching Completed Daily Attendance('.date("Y-m", strtotime($date)).')');
}
if (isset($_POST['delete_attendance']) && isset($_POST['date'])) {
	$card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
	$date = $_POST['date'];
	if ($card == '' && isset($_POST['date'])) {
		display_error('Invalide Date or Card..!!');
		$date = date('Y-m-d');
	}else{
		delete_emp_attendance($date,$card);
		display_notification('Successfully Deleted..! Daily Attendance('.date("Y-m", strtotime($date)).')');
	}
}

if (isset($_POST['update']) && isset($_POST['attdate'])) {
		$emp_date = $_POST['attdate'];
		$emp_id = $_POST['emp_id'];
		// display_notification(gettype($emp_ids));
		foreach ($emp_date as $key => $emp_date) {
			write_daily_attendance($emp_id, $_POST['hours_no_'.$emp_date],$_POST['rate_'.$emp_date],$_POST['att_date_'.$emp_date], $_POST['exit_date_'.$emp_date], $_POST['EntryTime_'.$emp_date] , $_POST['ExitTime_'.$emp_date] , $_POST['AttdStatus_'.$emp_date],$_POST['proximity_id_'.$emp_date]);
		}
		display_notification('Successfully Updated..! Daily Attendance('.date("Y-m", strtotime($date)).')');
}


// $EmpAttds=db_query($sql);



//--------------------------------------------------------------------------

page(_($help_context = "Monthly Attendance"), false, false, "", $js);
simple_page_mode(false);

//-------------------Searching form-------------------------

start_form();
start_table(TABLESTYLE_NOBORDER);



start_row();
input_date_lv('Date', 'date',$date);
echo "<td>";
btn_submit_delete('submit','delete_attendance','','',$title='Delete All Attendance',true);
echo "</td>";
end_row();

input_select_box('Attendance Status','att_sts','attendance_status','StatusID', 'StatusFullName',$att_sts);

input_text_td('Card NO','card_no',$card);

end_table(1);

start_table(TABLESTYLE_NOBORDER);

start_row();
btn_submit_search('submit','SrchAttd','','','Search');
// submit_center('SrchAttd', _("Search attendance"), true, '', 'default');
end_row();

end_table(1);

end_form();

//------------leave ledger--------------
$emp_id_leave = array();
$leaves = get_leave($date,$card);
$leave = db_fetch($leaves);


//--------------------Attendance Table Code-----------------------

$sql = get_emp_daily_attend($date,$card,$att_sts);
$EmpAttds = db_query($sql);
// display_notification($sql);

$totalrows = db_num_rows($EmpAttds);
if ($totalrows > 50 ) {
	$totalrow = ceil((int)$totalrows/50);
	$PAGENO = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 0;
	$sql .= ' limit '.$PAGENO.', 50';
	$EmpAttds = db_query($sql);
	$active_pagignation = true;
	$nmbrpage = $PAGENO == 0 ? 1 : ($PAGENO/50)+1;
	start_table(TABLESTYLE);
	label_cell('Page NO : '.$nmbrpage.' / '.$totalrow);
	end_table(1); 
}


// display_notification($sql);


//--------------------Attendance Table-----------------------


start_form();
start_table(TABLESTYLE);
$th = array(_("SL"), _("Name"), _("Card NO"), "Entry Date", "Exit Date", "Entry Time", "Exit Time", "Working Hr", "Over Time", "Rate","Attendance Status");
// inactive_control_column($th);
table_header($th);

$k = 0;
$i = 1;
while ($myrow = db_fetch($EmpAttds)) {
	alt_table_row_color($k);
	
		$atst = $myrow["AttdStatus"];
		$atdat = isset( $myrow["att_date"]) ?  $myrow["att_date"] : $date;
		if (isset($leave["leave_approved_from"]) && isset($leave["leave_approved_to"])) {
			if ($leave["leave_approved_from"] <= $atdat && $leave["leave_approved_to"] >= $atdat) {
				$atst = $leave["leave_type"];
			}
		}
		$id = $myrow["emp_id"];
		$sql = "SELECT * FROM (SELECT * FROM `0_leave_ledger` where ('$atdat' BETWEEN leave_requested_from and leave_requested_to) and emp_id='$id') l left JOIN 0_attendance_status a ON l.leave_type = a.StatusID";
		$result = db_query($sql);
		$leave = db_fetch($result);
		
		if($leave['leave_type']){
			$atst = $leave["leave_type"];
		}

		hidden('attdate[]',$atdat);
		hidden('emp_id',$myrow["emp_id"]);
		label_cell($i);
		label_cell($myrow["name"]);
		label_cell($myrow["emp_card_no"]);
		input_date_wl('att_date_'.$atdat,$atdat,true);
		input_date_wl('exit_date_'.$atdat, $myrow["exit_date"]);
		input_text_time('EntryTime_'.$atdat, $myrow["EntryTime"] );
		input_text_time('ExitTime_'.$atdat, $myrow["ExitTime"] );
		input_text_td_wl('work_hr_'.$atdat, $myrow["work_hr"]);
		input_text_td_wl('hours_no_'.$atdat, $myrow["hours_no"] );
		input_text_td_wl('rate_'.$atdat, $myrow["rate"] );
		input_select_box_td('Status','AttdStatus_'.$atdat,'attendance_status','StatusID','StatusFullName',$atst,'');
		hidden('proximity_id_'.$atdat,$myrow["emp_proximity_id"]);
	end_row();

	$i++;
	$k++;
}

end_table(1);

if ($totalrows > 0) {
start_table();
start_row();
hidden('page',$PAGENO);
if (isset($_POST['card_no'])) {
	hidden('card_no',$_POST['card_no']);
}
hidden('att_sts',$att_sts);
hidden('date',$date);
btn_submit_Add('submit','update','','','Update');
end_row();
end_table();
}

end_form();


//-------------------------------------------------Pagignation---------------------------

if ($active_pagignation === true) {
	start_table();
	echo "<tr>";
	$i = 0;
	// display_notification($totalrow);
	while ( $i < $totalrow) {
		echo '<td>';
		start_form();
		
		hidden('date',$date);
		hidden('att_sts',$_POST['att_sts'] );
// $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
// $card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
// $dept_id = isset($_POST['dept_id']) ? $_POST['dept_id'] : '';
// $att_sts = isset($_POST['att_sts']) ? $_POST['att_sts'] : 1;
		hidden('page',$i*50);
		// hidden('Emp_Card_srch');
		$num = $i+1;
		echo '<button  type="submit"  name="page">'.$num.'</button>';
		end_form();
		echo '<td>';
		$i++;
	}
	echo "<tr>";
	end_table();
}

end_page();
