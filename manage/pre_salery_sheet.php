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

page(_($help_context = "Pay Slip Process Status"));
simple_page_mode(false);

$create_emp = false;
$total_workday = false;
$total_leave_day = false;
$total_absent = false;
$overtime_hr = false;
$without_pay = false;
$att_bns = false;

if(isset($_POST['process_name']) && isset($_POST['process'])){
	if (isset($_POST['year']) && isset($_POST['month'])) {
		$year = $_POST['year'];
		$month = $_POST['month'];
		$process = $_POST['process'];
		$process($month,$year);
	}
}
if (isset($_POST['year']) && isset($_POST['month'])) {
	$year = $_POST['year'];
	$month = $_POST['month'];
	if (isset($_POST['pro_sts_del'])) {
		delete_pre_pro_sal($month,$year);
		$notifi = true;
	}
}



//--------------------------------------------------------------------------

$year = isset($_POST['year']) ? $_POST['year'] : date('Y');
$mon = date('n') == 1 ? 12 : date('n');
$month = isset($_POST['month']) ? $_POST['month'] : $mon;

start_form();
start_table(TABLESTYLE);
kv_hrm_year_list( _("Year:"), 'year', $year);
kv_hrm_months_list( _("Month:"), 'month', $month);
btn_submit_select($type='submit',$name='pro_sts','','','Select');
end_table(1);
end_form();

start_table(TABLESTYLE);
label_cell('Process Status of : '.date("F", mktime(0, 0, 0, $month, 10)).', '.$year);
end_table(1);



// var_dump(date('F'));

//get process status form db

if (isset($_POST['year']) && isset($_POST['month'])) {
	$year = $_POST['year'];
	$month = $_POST['month'];
	$result = get_payslip_process_status($month,$year);
	while($row = db_fetch($result)){
		$new = $row['step'];
		$$new = $row['status'];
	}
	$create_emp = isset($create_emp) && $create_emp == 1 ? true : false; 
	$total_workday = isset($total_workday) && $total_workday == 1 ? true : false; 
	$total_leave_day = isset($total_leave_day) && $total_leave_day == 1 ? true : false; 
	$total_absent = isset($total_absent) && $total_absent == 1 ? true : false; 
	$overtime_hr = isset($overtime_hr) && $overtime_hr == 1 ? true : false; 
	$without_pay = isset($without_pay) && $without_pay == 1 ? true : false; 
	$att_bns = isset($att_bns) && $att_bns == 1 ? true : false; 
	// echo $att_bns;
	// var_dump($result);
}


// $create_emp
// $total_workday
// $total_leave_day
// $total_absent
// $overtime_hr
// $without_pay
// $att_bns

start_table(TABLESTYLE);
$th = array(_("Id"), _("Process"), _("Status"), "");
inactive_control_column($th);
table_header($th);
$k = 0;
	alt_table_row_color($k);
	start_form();
		start_row();
			label_cell('1');
			label_cell('Create Employee List');
			hidden_input('process','create_emp');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($create_emp);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$create_emp);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('3');
			label_cell('Calculate Leave');
			hidden_input('process','total_leave_day');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($total_leave_day);
			$action = $create_emp == false ? true : $total_leave_day;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('4');
			label_cell('Total Absent');
			hidden_input('process','total_absent');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($total_absent);
			$action = $total_leave_day == false ? true : $total_absent;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('2');
			label_cell('Work Day');
			hidden_input('process','total_workday');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($total_workday);
			$action = $total_absent == false ? true : $total_workday;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('5');
			label_cell('Total Over Time Hour and Rate');
			hidden_input('process','overtime_hr');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($overtime_hr);
			$action = $total_workday == false ? true : $overtime_hr;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('6');
			label_cell('Without Pay / Salary Deduction');
			hidden_input('process','without_pay');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($without_pay);
			$action = $overtime_hr == false ? true : $without_pay;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('7');
			label_cell('Attendance Bonus');
			hidden_input('process','att_bns');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($att_bns);
			$action = $without_pay == false ? true : $att_bns;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();
	start_form();
		start_row();
			label_cell('8');
			label_cell('Update Temporary To Presalary');
			hidden_input('process','update_temp_to_presalary');
			hidden_input('month',$month);
			hidden_input('year',$year);
			label_cell('Update');
			$action = $att_bns == false ? true : false;
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$action);
		end_row();
		//var_dump($result);
	end_form();


end_table(1);
start_form();
start_table(TABLESTYLE);
start_row();
hidden_input('month',$month);
hidden_input('year',$year);
btn_submit_delete($type='',$name='pro_sts_del','pro_sts_del','','Delete');
end_row();
end_table(1);
end_form();
if ($notifi) {
	$notifi = false;
	echo '<script type="text/javascript">alert("All Data has beed deleted.")</script>';

}
end_page();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
	$(document).ready(function(){
	  $("button").click(function(){
	  	$(this).hide();
	  	// $(this).closest("form").submit();
	  	// $(this).prop('disabled', true);
	  });
	});
</script>