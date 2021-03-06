﻿<?php
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

page(_($help_context = "Increment after gadget"));
simple_page_mode(false);

$third_floor = false;
$fifth_floor = false;
$six_floor = false;
$eight_floor = false;
$ninth_floor = false;
$rishal_group = false;


if(isset($_POST['process_name']) && isset($_POST['process'])){
	if (isset($_POST['year']) && isset($_POST['month'])) {
		$year = $_POST['year'];
		$month = $_POST['month'];
		$unit = $_POST['unit'];
		$process = $_POST['process'];
		$process($month,$year);
	}
}
if (isset($_POST['year']) && isset($_POST['month'])) {
	$year = $_POST['year'];
	$month = $_POST['month'];
	if (isset($_POST['pro_sts_del'])) {
		delete_increment($month,$year);
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
	$result = get_increment_process_status($month,$year);
	while($row = db_fetch($result)){
		$new = $row['step'];
		$$new = $row['status'];
	}
	$third_floor = isset($third_floor) && $third_floor == 1 ? true : false; 
	$fifth_floor = isset($fifth_floor) && $fifth_floor == 1 ? true : false; 
	$six_floor = isset($six_floor) && $six_floor == 1 ? true : false; 
	$eight_floor = isset($eight_floor) && $eight_floor == 1 ? true : false; 
	$ninth_floor = isset($ninth_floor) && $ninth_floor == 1 ? true : false; 
	$rishal_group = isset($rishal_group) && $rishal_group == 1 ? true : false; 

	// echo $att_bns;
	// var_dump($result);
}


// $third_floor


start_table(TABLESTYLE);
$th = array(_("Id"), _("Process"), _("Status"), "");
inactive_control_column($th);
table_header($th);
$k = 0;
	alt_table_row_color($k);
	start_form();
		start_row();
			label_cell('1');
			label_cell('3rd Floor');
			hidden_input('process','third_floor');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($third_floor);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$third_floor);
		end_row();
	end_form();

	start_form();
		start_row();
			label_cell('1');
			label_cell('5th Floor');
			hidden_input('process','fifth_floor');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($fifth_floor);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$fifth_floor);
		end_row();
	end_form();

	start_form();
		start_row();
			label_cell('1');
			label_cell('6th Floor');
			hidden_input('process','six_floor');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($six_floor);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$six_floor);
		end_row();
	end_form();

	start_form();
		start_row();
			label_cell('1');
			label_cell('8th Floor');
			hidden_input('process','eight_floor');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($eight_floor);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$eight_floor);
		end_row();
	end_form();
	start_form();
		start_row();
			label_cell('1');
			label_cell('9th Floor');
			hidden_input('process','ninth_floor');
			hidden_input('month',$month);
			hidden_input('year',$year);
			process_status($ninth_floor);
			btn_sub_Add($type='submit',$name='process_name','','',$title='Process',$ninth_floor);
		end_row();
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