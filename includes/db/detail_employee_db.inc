<?php
/**********************************************************************
 AuthoR : Kvvaradha
 
 Module : HRM 
 
 Developed to 1Stop WebSolution.com
 
 detail_employee_db.inc
 
***********************************************************************/

function kv_get_employee($employee_id){
	$sql = "SELECT emp_id as empl_id, concat(emp_first_name,' ', emp_last_name) as empl_name,Designation,emp_salary FROM ".TB_PREF."employee WHERE emp_id=".db_escape($employee_id);
	$result = db_query($sql, "could not get employee");
	return db_fetch($result);
	//return $sql;
}

//--------------------------------------------------------------------------------------------
function kv_db_has_employees(){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."employee");
}

function kv_delete_employee($empl_id){
	$sql="DELETE FROM ".TB_PREF."employee WHERE emp_id=".db_escape($empl_id);
	db_query($sql, "could not delete the selected Employee");
	
	$sql="DELETE FROM ".TB_PREF."kv_empl_payslip WHERE empl_id=".db_escape($empl_id);
	db_query($sql, "could not delete the selected Employee");
}

function kv_employee_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	//$sql = "SELECT empl_id, empl_name FROM ".TB_PREF."kv_empl_info";
	$sql="SELECT `emp_id` as empl_id,CONCAT(emp_first_name,' ',IFNULL(emp_last_name,' ')) as empl_name FROM ".TB_PREF."employee`";

	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'empl_id', 'empl_name',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("empl_id", "empl_name"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
	
		'order' => array('empl_name'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_empl($row){
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function kv_employee_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo kv_employee_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n";
}

global $hrm_months_list, $hrm_year_list; 

$hrm_months_list = array(
    1 => _('January'),   
    2 => _('February'), 
	3 => _('March '), 
    4 => _('April'), 
    5 => _('May'),
    6 => _('June'),
    7 => _('July'),
    8 => _('August'),
    9 => _('September'),
    10 => _('October'),
    11 => _('November'),
    12 => _('December')
);

function  kv_hrm_months_list($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {
	global $hrm_months_list;
		
	echo "<td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	echo array_selector($name, $selected_id, $hrm_months_list, $options);
	echo "</td>\n";
}


$hrm_year_list = array(
    2015 => _('2015'),   
    2016 => _('2016'), 
	2017 => _('2017'), 
    2018 => _('2018'), 
    2019 => _('2019'),
    2020 => _('2020'),
    2021 => _('2021'),
    2022 => _('2022'),
    2023 => _('2023'),
    2024 => _('2024')
);

function  kv_hrm_year_list($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {
	global $hrm_year_list;
		
	echo "<td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	echo array_selector($name, $selected_id, $hrm_year_list, $options);
	echo "</td>\n";
}

function kv_add_payslip($year,$month, $empl_id, $basic, $da, $hra, $convey_allow, $edu_other_allow, $pf, $lop_amount, $tds, $total_ded, $total_net, $date_of_pay){
	$dat_of_pay = date2sql($date_of_pay); 
		
	$sql = "INSERT INTO ".TB_PREF."kv_empl_payslip (year,month, empl_id, basic, da, hra, convey_allow, edu_other_allow, pf, lop_amount, tds, total_ded, total_net, date_of_pay) VALUES ("
		
		.db_escape($year).", "
		.db_escape($month).", "
		.db_escape($empl_id).", "		
		.db_escape($basic).", "
		.db_escape($da).", "
		.db_escape($hra).", " 
		.db_escape($convey_allow).", " 
		.db_escape($edu_other_allow).", " 
		.db_escape($pf).", " 
		.db_escape($lop_amount).", " 
		.db_escape($tds).","
		.db_escape($total_ded).","
		.db_escape($total_net).","
		.db_escape($dat_of_pay).")";

	db_query($sql,"The employee payslip could not be added");
	return  db_insert_id(); 

}

function rashed_get_empl_grosspay($Type,$id) {
	
	if ($Type=='Template')
	{
		$sql = "SELECT sum(case when str_tmp.emp_pmt_type='Add' then tmp.emp_pmt_tmp_amt else 0 end) as TotalEarning,
		sum(case when str_tmp.emp_pmt_type='Ded' then tmp.emp_pmt_tmp_amt else 0 end) as TotalDeduction,
		sum(case when str_tmp.emp_pmt_type='Add' then tmp.emp_pmt_tmp_amt else tmp.emp_pmt_tmp_amt*-1 end) as NetPay FROM ".TB_PREF."emp_pmt_tmp tmp, ".TB_PREF."employee emp, ".TB_PREF."emp_pmt_str_tmp str_tmp WHERE emp.emp_id=".db_escape($id)." and emp.salary_scale_id=tmp.scale_id and str_tmp.emp_pmt_str_id = tmp.emp_pmt_str_id";

		$result = db_query($sql, "could not get sales type");
	}
	else if ($Type=='Payslip')
	{
		$sql = "SELECT sum(case when str_tmp.emp_pmt_type='Add' then tmp.emp_pmt_tmp_amt else 0 end) as TotalEarning,
		sum(case when str_tmp.emp_pmt_type='Ded' then tmp.emp_pmt_tmp_amt else 0 end) as TotalDeduction,
		sum(case when str_tmp.emp_pmt_type='Add' then tmp.emp_pmt_tmp_amt else tmp.emp_pmt_tmp_amt*-1 end) as NetPay FROM ".TB_PREF."emp_pmt_tmp tmp, ".TB_PREF."employee emp, ".TB_PREF."emp_pmt_str_tmp str_tmp WHERE emp.emp_id=".db_escape($id)." and emp.salary_scale_id=tmp.scale_id and str_tmp.emp_pmt_str_id = tmp.emp_pmt_str_id";

		$result = db_query($sql, "could not get sales type");
	}
	return $result;
}

function kv_get_empl_name($id) {
	$sql = "SELECT concat(`emp_first_name`,' ', `emp_last_name`) as empl_name FROM ".TB_PREF."employee WHERE emp_id=".db_escape($id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function kv_truncate_my_tables(){
	$sql_first =" TRUNCATE TABLE ".TB_PREF."employee" ;
	$sql_second =" TRUNCATE TABLE ".TB_PREF."kv_empl_payslip" ;
	$result = db_query($sql_first, "could not clean it");
	$result = db_query($sql_second, "could not clean it");
} 

function  emp_pmt_str_list($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {
	global $emp_pmt_list;
	$emp_pmt_list = array(0 => _('Select'));
	$sqlqry='SELECT emp_pmt_str_id, emp_pmt_str_name FROM '.TB_PREF.'emp_pmt_str_tmp';
	$emp_pmt_str_tmp = db_query($sqlqry);
	while($emp_pmt_str_tmpT = db_fetch($emp_pmt_str_tmp)) {
			$emp_pmt_list = array_push_assoc($emp_pmt_list, $emp_pmt_str_tmpT['emp_pmt_str_id'], $emp_pmt_str_tmpT['emp_pmt_str_name']);
		}
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	echo array_selector($name, $selected_id, $emp_pmt_list, $options);
	echo "</td>\n";
}
function array_push_assoc($array, $key, $value){
$array[$key] = $value;
return $array;
}


function rashed_add_edit_payslip_no($emp_id,$year,$month,$payment_status,$date_of_pay){
	$payslip_no = rashed_get_payslip_no($year, $month, $emp_id);
	$date_of_pay = date2sql($date_of_pay); 
  	if ($payslip_no =="")
	  {
			$sql = "INSERT INTO ".TB_PREF."emp_payslip_no (emp_id,year,month,payment_status,date_of_pay) VALUES ("		.db_escape($emp_id).", "
				.db_escape($year).", "
				.db_escape($month).", "	
				.db_escape($payment_status).", "		
				.db_escape($date_of_pay).")";

			db_query($sql,"The employee payslip could not be added");
			return  db_insert_id(); 
		}
		
		else
		{
			$sql = "UPDATE ".TB_PREF."emp_payslip_no SET payment_status = ".db_escape($payment_status).", date_of_pay = ".db_escape($date_of_pay)." where emp_payslip_no = ".db_escape($payslip_no);
			db_query($sql,"The employee payslip could not be added");
			return $payslip_no;
		} 
		//return $sql;
}

function rashed_import_payslip_det($payslip_no,$emp_id){
	$sql = "Delete from ".TB_PREF."emp_payslip_det Where emp_payslip_No = ".db_escape($payslip_no);
	db_query($sql,"The employee payslip could not be deleted");

	//$sql = "INSERT INTO ".TB_PREF."emp_payslip_det (emp_payslip_No,emp_id,emp_pmt_str_id,emp_pmt_amt) SELECT "		
	//	.db_escape($payslip_no).", "
	//	."emp_id,emp_pmt_str_id,emp_pmt_tmp_amt FROM ".TB_PREF."emp_pmt_tm where emp_id='".$emp_id."'";
	$sql = "INSERT INTO 0_emp_payslip_det (emp_payslip_No,emp_id,emp_pmt_str_id,emp_pmt_amt) (SELECT " .db_escape($payslip_no).", "
		."emp_id,emp_pmt_str_id,emp_pmt_tmp_amt FROM 0_emp_pmt_tmp where emp_id='".$emp_id."')";	
	db_query($sql,"The employee payslip could not be added");
	//return  db_insert_id(); 
	return $sql;
}

function rashed_add_payslip_det($emp_payslip_No,$emp_id, $emp_pmt_str_id, $emp_pmt_amt){
	$sql = "INSERT INTO ".TB_PREF."emp_payslip_det (emp_payslip_No,emp_id,emp_pmt_str_id,emp_pmt_amt) VALUES ("		
		.db_escape($emp_payslip_No).", "
		.db_escape($emp_id).", "
		.db_escape($emp_pmt_str_id).", "		
		.db_escape($emp_pmt_amt).")";

	db_query($sql,"The employee payslip could not be added");
	return  db_insert_id(); 
}

function rashed_edit_payslip_det($emp_payslip_id, $emp_payslip_No, $emp_pmt_str_id, $emp_pmt_amt){
	$amt = floatval(str_replace(',', '', $emp_pmt_amt));
	$sql = "Update ".TB_PREF."emp_payslip_det set emp_pmt_amt='".$amt."', emp_pmt_str_id=".$emp_pmt_str_id." Where emp_payslip_id=".db_escape($emp_payslip_id);

	db_query($sql,"The employee payslip could not be added");
	return $emp_payslip_id;
}
function rashed_delete_payslip_det($emp_payslip_id){
	$sql = "delete from ".TB_PREF."emp_payslip_det Where emp_payslip_id = ".db_escape($emp_payslip_id);

	db_query($sql,"The employee payslip could not be added");
	return $emp_payslip_id;
}

function rashed_get_no_leave($emp_id, $month,$year){
	$sql = "SELECT count(att.att_date) FROM ".TB_PREF."daily_attendance att, "
	.TB_PREF."attendance_status sts where att.emp_id='"
	.$emp_id."' and sts.StatusID=att.AttdStatus and sts.FlagStatus='Leave' and month(att.att_date)='"
	.$month."'  and year(att.att_date)='".$year."'";
	$result = db_query($sql,"The employee payslip could not be added");
	$row = db_fetch_row($result);
	return $row[0];
	//return $sql;
}

function rashed_get_no_late($emp_id, $month,$year){
	$sql = "SELECT count(att.att_date) FROM ".TB_PREF."daily_attendance att, "
	.TB_PREF."attendance_status sts where att.emp_id='"
	.$emp_id."' and sts.StatusID=att.AttdStatus and sts.FlagStatus='Late' and month(att.att_date)='"
	.$month."'  and year(att.att_date)='".$year."'";
	$result = db_query($sql,"The employee payslip could not be added");
	$row = db_fetch_row($result);
	return $row[0];
}

function rashed_get_payslip_no($year, $month, $empl_id){
	$sql= "select emp_payslip_no, payment_status FROM ".TB_PREF."emp_payslip_no WHERE year = ".db_escape($year)." AND month = ".db_escape($month)." AND emp_id = ".db_escape($empl_id);
	
	$result = db_query($sql,"The employee payslip could not be added");
	
	$num_rows=db_num_rows($result);
	if ($num_rows==0){
		$emp_payslip_No="";
	}
	else {
		$row = db_fetch_row($result);
		$emp_payslip_No= $row[0];
	}
	return $emp_payslip_No;
}

function rashed_emp_payslip_result($year, $month, $empl_id){
	$sql= "select emp_payslip_no, payment_status FROM ".TB_PREF."emp_payslip_no WHERE year = ".db_escape($year)." AND month = ".db_escape($month)." AND emp_id = ".db_escape($empl_id);
	
	$result = db_query($sql,"The employee payslip could not be added");
	return $result;
}
function rashed_ot_bonus ($emp_id, $month,$year){
	$sql = "SELECT sum(hours_no*rate) FROM ".TB_PREF."daily_attendance att where att.emp_id='"
	.$emp_id."' and month(att.att_date)='"
	.$month."'  and year(att.att_date)='".$year."'";
	$result = db_query($sql,"The employee payslip could not be added");
	$row = db_fetch_row($result);
	return $row[0];
	//return $sql;
}
function rashed_att_bonus ($emp_id, $month,$year){
	$sql = "SELECT emp_atten_bonus FROM ".TB_PREF."employee att where emp_id='"
	.$emp_id."'";
	$result = db_query($sql,"The employee could not found");
	$row = db_fetch_row($result);
	return $row[0];
	//return $sql;
}
?>