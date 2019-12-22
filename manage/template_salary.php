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

page(_($help_context = "Salary Template"));
simple_page_mode(false);

if (isset($_POST['create_salary_temp'])) {
	if(strlen($_POST['total_salary']) == 0 || $_POST['total_salary'] == '') {
		display_error( _("Total cannot be empty."));
		set_focus('total_salary');
	}
	else {
		$status = write_template_salary($_POST['employee_id'],$_POST['total_salary']);
		if($status != null)
			display_notification(_('New Salary Template has been added'));
		else
			display_notification(_('Failed...!'));
	}
}

if (isset($_POST['update'])) {
		write_template_salary_update($_POST['employee_id'],$_POST['tracker']);
}
//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

text_row_ex(_("Enter Name or Card Number:"), 'name', 50, 60);

end_table(1);
// submit_add_or_update_center($selected_id == '', 'Search', 'both');
btn_submit_search('submit','search','','','Search');
// submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_form();

if (isset($_POST['search'])) {
start_table(TABLESTYLE);
$th = array(_("Id"), _("Name"), _("Card Number"), _("Designation"), "");
inactive_control_column($th);
table_header($th);
$result = get_emp($_POST['name'], $_POST['employee_id'], check_value('show_inactive'));
$k = 0;
while ($myrow = db_fetch($result)) {
	start_form();
	alt_table_row_color($k);
	label_cell($myrow["emp_id"]);
	echo '<td>'.html_specials_encode($myrow["emp_first_name"]).'</td>';
	label_cell($myrow['emp_card_no']);
	label_cell($myrow["Designation"]);
	hidden('employee_id', $myrow["emp_id"]);
	hidden('emp_salary', $myrow["emp_salary"]);
	btn_submit_select('submit','search','','','Select');
	end_row();
	end_form();
}
end_table(1);
unset($_POST['search']);
}

if (isset($_POST['employee_id']) && isset($_POST['emp_salary'])) {
	$empId = $_POST['employee_id'];
	$emp_salary = $_POST['emp_salary'];
	//total salary form
	start_form();
	start_table(TABLESTYLE2);
	text_row_ex(_("Enter Total Salary:"), 'total_salary', 50, 60,'',$emp_salary);
	hidden('employee_id',$empId);
	hidden('emp_salary',$emp_salary);
	end_table(1);
	btn_submit_Add($type='submit',"create_salary_temp",$id,$value,$title='Add');
	// btn_submit_select("submit","create_salary_temp",$id,$value,'Select');
	end_form();
	//end total salary form
	if(emp_ext_pmt_str($empId) != null){
		$results = emp_ext_pmt_str($empId);
		$emp_str = array();
		//setting array of pmt_str_id wise ammount
		while ($result = db_fetch($results)) {
			$emp_str[$result["emp_pmt_str_id"]] = $result["emp_pmt_tmp_amt"];
		}
		$str = act_pmt_str();
		start_table();
		$i = 0;
		if(isset($status) == false){
			$status['total']=0;
			$status['deduct'] =0;
			$p = '0';
		}
		start_form();
		while ($r = db_fetch($str)){
			$value = 0;
			$tds[$i] = '0';
			if ($emp_str[$r["emp_pmt_str_id"]] != null) {
				if ($r["emp_pmt_type"]=='Add') {
					if ($p === '0') {
						$status['total'] += $emp_str[$r["emp_pmt_str_id"]];
					}
					
				}else{
					if ($p === '0') {
						$status['deduct'] += $emp_str[$r["emp_pmt_str_id"]];
					}
				}
				$value = $emp_str[$r["emp_pmt_str_id"]];
				$tds[$i] = $emp_str[$r["emp_pmt_str_id"]];
			}
			$tbh[$i] = $r["emp_pmt_str_name"];
			text_row_ex($r["emp_pmt_str_name"], $r["emp_pmt_str_id"], 50, 60,$title=null,$value);
			hidden('tracker[]',$r["emp_pmt_str_id"]);	
			$i++;	
		}
		hidden('employee_id',$empId);
		label_row('Total',$status['total']);
		label_row('Deduct',$status['deduct']);
		label_row('Paid',$status['total']-$status['deduct']);
		end_table(1);
		
		if ($status['total']>0) {
			btn_submit_select('submit','update','','','Update');
		}
		
		end_form();
	}
}
end_page();
