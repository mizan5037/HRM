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

$year = isset($_POST['year']) ? $_POST['year'] : date('Y');
$mon = date('n') == 1 ? 12 : date('n');

$month = isset($_POST['month']) ? $_POST['month'] : $mon;
$card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
$dept_id = isset($_POST['dept_id']) ? $_POST['dept_id'] : '';
$unit_id = isset($_POST['unit_id']) ? $_POST['unit_id'] : '';
$status = get_process_status($month,$year);
if (isset($_POST['SrchAttd'])) {
	if ($status != 7) {
		$year =  date('Y');
		$month = $mon;
		display_error('Please Complete Pre Salary Sheet Process');
	}else{
		display_notification("Search Completed.");
	}
}

if (isset($_POST['update']) && isset($_POST['emp_ids'])) {
	if ($status != 7) {
		$year =  date('Y');
		$month = $mon;
		display_error('Sorry can not update. Complete Pre Salary Sheet Process First.');
	}else{
		$emp_ids = $_POST['emp_ids'];
		// display_notification(gettype($emp_ids));
		foreach ($emp_ids as $key => $emp_id) {
			
			write_pre_salarysheet($emp_id,$month,$year, $_POST['work_day_'.$emp_id],$_POST['total_leave_'.$emp_id],$_POST['total_absent_'.$emp_id], $_POST['total_overtime_hr_'.$emp_id], $_POST['total_overtime_hr_Less_2_'.$emp_id] , $_POST['without_pay_'.$emp_id] , $_POST['rate_'.$emp_id],$_POST['attn_bonus_'.$emp_id],$_POST['ExtraAdd_'.$emp_id],$_POST['ExtraCutt_'.$emp_id]  ,$_POST['abs_b_join_'.$emp_id] , $_POST['abs_a_release_'.$emp_id] , $_POST['salary_'.$emp_id], $_POST['physical_present_'.$emp_id]);
		}
		display_notification('Successfully Updated..!');
	}
}
// $EmpAttds=db_query($sql);



//--------------------------------------------------------------------------

page(_($help_context = "Emp PSS"), false, false, "", $js);
simple_page_mode(false);

//-------------------Searching form-------------------------


start_form();
start_table(TABLESTYLE_NOBORDER);

kv_hrm_year_list( _("Year:"), 'year', $year);
kv_hrm_months_list( _("Month:"), 'month', $month);

input_select_box('Department','dept_id','department','dept_id', 'dept_name',$dept_id);

input_select_box('Unit','unit_id','unit','UnitID', 'UnitName',$unit_id);

input_text_td('Card NO','card_no',$card);

end_table(1);

start_table(TABLESTYLE_NOBORDER);

start_row();
btn_submit_search('submit','SrchAttd','','','Search');
end_row();

end_table(1);

end_form();


start_table(TABLESTYLE);
label_cell('Process Status of : '.date("F", mktime(0, 0, 0, $month, 10)).', '.$year);
end_table(1);
//--------------------Attendance Table Code-----------------------

$sql = get_pre_salarysheet($month,$year,$card,$dept_id,$unit_id);

// display_notification($sql);
$Emp_Presal = db_query($sql);

$totalrows = db_num_rows($Emp_Presal);
if ($totalrows > 50 ) {
	$totalrow = ceil((int)$totalrows/50);
	$PAGENO = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 0;
	$sql .= ' limit '.$PAGENO.', 50';
	$Emp_Presal = db_query($sql);
	$active_pagignation = true;
	$nmbrpage = $PAGENO == 0 ? 1 : ($PAGENO/50)+1;
	start_table(TABLESTYLE);
	label_cell('Page NO : '.$nmbrpage.' / '.$totalrow);
	end_table(1); 
}




//--------------------Attendance Table-----------------------


start_form();
start_table(TABLESTYLE2, "",'0','5');

$th = array(_("SL"), _("Name"), _("Card NO"),"Join AB","Left AB", "Present", "Pay Day", "Leave", "Absent", "Overtime", "OT<<2", "Cutting", "Rate","Att Bonus","Salary","Extra Add","Extra Cut","Total");
// inactive_control_column($th);
table_header($th);

$k = 0;
$i = 1;
while ($myrow = db_fetch($Emp_Presal)) {
	alt_table_row_color($k);
		hidden('emp_ids[]',$myrow["emp_id"]);
		label_cell($i);
		$name = explode(' ', $myrow["name"]);
		label_cell("$name[0] $name[1]");
		label_cell($myrow["card"]);
		input_text_td_wl('abs_b_join_'.$myrow["emp_id"],$myrow["abs_b_join"],true);
		input_text_td_wl('abs_a_release_'.$myrow["emp_id"],$myrow["abs_a_release"],true);
		input_text_td_wl('physical_present_'.$myrow["emp_id"],$myrow["physical_present"],true);
		input_text_td_wl('work_day_'.$myrow["emp_id"],$myrow["work_day"],true);
		input_text_td_wl('total_leave_'.$myrow["emp_id"],$myrow["total_leave"],true);
		input_text_td_wl('total_absent_'.$myrow["emp_id"],$myrow["total_absent"],true);
		input_text_td_wl('total_overtime_hr_'.$myrow["emp_id"], $myrow["total_overtime_hr"],true);
		input_text_td_wl('total_overtime_hr_Less_2_'.$myrow["emp_id"], $myrow["total_overtime_hr_Less_2"],true);
		input_text_td_wl('without_pay_'.$myrow["emp_id"],$myrow["without_pay"],true);
		input_text_td_wl('rate_'.$myrow["emp_id"], $myrow["rate"] );
		input_text_td_wl('attn_bonus_'.$myrow["emp_id"],$myrow["attn_bonus"],true);
		input_text_td_wl('salary_'.$myrow["emp_id"],$myrow["salary"],true);
		input_text_td_wl('ExtraAdd_'.$myrow["emp_id"],$myrow["ExtraAdd"],true);
		input_text_td_wl('ExtraCutt_'.$myrow["emp_id"],$myrow["ExtraCutt"],true);
		amount_cell($myrow["total_paid"],true);
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
hidden('month',$month);
hidden('year',$year);
hidden('dept_id',$dept_id );
hidden('unit_id',$unit_id );
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

	if (isset($_POST['card_no'])) {
		hidden('card_no',$_POST['card_no']);
	}
		hidden('month',$month);
		hidden('year',$year);
		hidden('dept_id',$dept_id );
		hidden('unit_id',$unit_id );
		hidden('page',$i*50);
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