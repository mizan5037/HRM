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
$att_sts = isset($_POST['att_sts']) ? $_POST['att_sts'] : 1;

if (isset($_POST['SrchAttd'])) {
	$date = $_POST['date'];
	$card = $_POST['card_no'];
	$dept_id = $_POST['dept_id'];
	$att_sts = $_POST['att_sts'];
	if ($date > date('Y-m-d')) {
		display_error('Invalide Date..!!');
		$date = date('Y-m-d');
	}
	display_notification('Searching Completed Daily Attendance('.$date.')');
}
if (isset($_POST['delete_attendance']) && isset($_POST['date'])) {
	$card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
	$date = $_POST['date'];
	if ($date > date('Y-m-d')) {
		display_error('Invalide Date..!!');
		$date = date('Y-m-d');
	}else{
		delete_attendance($date,$card);
		display_notification('Successfully Deleted..! Daily Attendance('.$date.')');
	}
}

if (isset($_POST['update']) && isset($_POST['date']) && isset($_POST['emp_ids'])) {
	if ($date > date('Y-m-d')) {
		display_error('Invalide Date..!! Can'."'t".' Update');
		$date = date('Y-m-d');
	}else{
		$date = $_POST['date'];
		$emp_ids = $_POST['emp_ids'];
		// display_notification(gettype($emp_ids));
		foreach ($emp_ids as $key => $emp_id) {
			write_daily_attendance($emp_id, $_POST['hours_no_'.$emp_id],$_POST['rate_'.$emp_id],$_POST['att_date_'.$emp_id], $_POST['exit_date_'.$emp_id], $_POST['EntryTime_'.$emp_id] , $_POST['ExitTime_'.$emp_id] , $_POST['AttdStatus_'.$emp_id],$_POST['proximity_id_'.$emp_id]);
		}
		display_notification('Successfully Updated..! Daily Attendance('.$date.')');
	}
}


// $EmpAttds=db_query($sql);



//--------------------------------------------------------------------------

page(_($help_context = "Daily Attendance"), false, false, "", $js);
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

input_select_box('Department','dept_id','department','dept_id', 'dept_name',$dept_id);

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
$leaves = get_leave($date);
while ($leave =  db_fetch($leaves)) {
	$emp_id_leave[$leave["emp_id"]] = $leave["leave_type"];
}


//--------------------Attendance Table Code-----------------------

$sql = get_daily_attend($date,$card,$dept_id,$att_sts);
$EmpAttds = db_query($sql);

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
		if (isset($emp_id_leave[$myrow["emp_id"]]) != true) {
			hidden('emp_ids[]',$myrow["emp_id"]);
		}else{
			$atst = $emp_id_leave[$myrow["emp_id"]];
		}
		label_cell($i);
		label_cell($myrow["name"]);
		label_cell($myrow["emp_card_no"]);
		input_date_wl('att_date_'.$myrow["emp_id"],$atdat,true);
		input_date_wl('exit_date_'.$myrow["emp_id"], $myrow["exit_date"]);
		input_text_time('EntryTime_'.$myrow["emp_id"], $myrow["EntryTime"] );
		input_text_time('ExitTime_'.$myrow["emp_id"], $myrow["ExitTime"] );
		input_text_td_wl('work_hr_'.$myrow["emp_id"], $myrow["work_hr"]);
		input_text_td_wl('hours_no_'.$myrow["emp_id"], $myrow["hours_no"] );
		input_text_td_wl('rate_'.$myrow["emp_id"], $myrow["rate"] );
		input_select_box_td('Status','AttdStatus_'.$myrow["emp_id"],'attendance_status','StatusID','StatusFullName',$atst,'');
		hidden('proximity_id_'.$myrow["emp_id"],$myrow["emp_proximity_id"]);
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
		hidden('dept_id',$_POST['dept_id'] );
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
//---------------------------------------------------


?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	tag = document.getElementsByTagName("input");
	setInterval(function(){
		i=0;
		while(tag[i]){
			if (tag[i].name == 'starting_time' || tag[i].name == 'ending_time') {
				tag[i].type = 'time';
			}
			i++;
		}
	}, 100);
	
</script>