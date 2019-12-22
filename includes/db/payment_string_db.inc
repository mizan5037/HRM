<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

// function db_has_overtime() {
// 	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."emp_pmt_str_tmp");
// }

//--------------------------------------------------------------------------

function write_payment_string($emp_pmt_str_id = false, $emp_pmt_str_name, $emp_pmt_str_name_bd, $emp_pmt_type, $emp_pmt_amount_type, $emp_pmt_amount, $activity) {

	if($emp_pmt_str_id==false) {
		$sql = "INSERT INTO ".TB_PREF."emp_pmt_str_tmp(emp_pmt_str_name,emp_pmt_str_name_bd,emp_pmt_type,emp_pmt_amount_type,emp_pmt_amount,activity) VALUES (".db_escape($emp_pmt_str_name).",".db_escape($emp_pmt_str_name_bd).", ".db_escape($emp_pmt_type).", ".db_escape($emp_pmt_amount_type).", ".db_escape($emp_pmt_amount).", ".db_escape($activity)." )";
	}
	else {
		$sql = "UPDATE ".TB_PREF."emp_pmt_str_tmp SET 
		emp_pmt_str_name = ".db_escape($emp_pmt_str_name).", 
		emp_pmt_str_name_bd = ".db_escape($emp_pmt_str_name_bd).", 
		emp_pmt_type = ".db_escape($emp_pmt_type).", 
		emp_pmt_amount_type = ".db_escape($emp_pmt_amount_type).",
		emp_pmt_amount = ".db_escape($emp_pmt_amount).",
		activity = ".db_escape($activity)." 
		WHERE emp_pmt_str_id = ".db_escape($emp_pmt_str_id);
	}
	
	db_query($sql, _('Could not write payment string data'));
}


function write_allEmployee_paymentString($unit='',$department='')
{
	$cnt= 0;
	if ($department != '') {
		$department = ' AND department_id ='.$department;
		$cnt++;
	}
	if ($unit != '') {
		$unit = ' AND UnitID ='.$unit;
		$cnt++;
	}
	if ($cnt == 0) {
		display_error('Please Select Uint or Department');
		return false;
	}

	$sql="SELECT emp_id,emp_salary FROM 0_employee WHERE !inactive AND emp_salary > 0 AND emp_id > 0 ".$unit.$department." ORDER BY emp_id ASC";
	$employees = db_query($sql,'Failed to get Data');
	while ($emp = db_fetch($employees)) {
		write_template_salary($emp["emp_id"],$emp["emp_salary"]);
	}
	display_notification('All Employees Payment String has been updated.');
	// display_notification($sql);
}

//--------------------------------------------------------------------------


function get_payment_string($id = false, $all = false) {
	
	$sql = "SELECT * FROM ".TB_PREF."emp_pmt_str_tmp ";
	
	if($id > 0)
		$sql .= "WHERE emp_pmt_str_id=".db_escape($id);
	
	$result = db_query($sql, _('Could not get payment string data.'));
	
	if($id > 0)
		return db_fetch($result);
	else
		return $sql;

	

}


//--------------------------------------------------------------------------

function delete_payment_string($emp_pmt_str_id) {
	$sql = "DELETE FROM ".TB_PREF."emp_pmt_str_tmp WHERE emp_pmt_str_id = ".db_escape($emp_pmt_str_id);
	db_query($sql, _('Could not delete payment string.'));
}

//--------------------------------------------------------------------------

function payment_string_used($emp_pmt_str_id) {
	return check_empty_result("SELECT emp_pmt_str_id FROM ".TB_PREF."emp_pmt_str_tmp WHERE emp_pmt_str_id = ".db_escape($emp_pmt_str_id));
}

//--------------------------------------------------------------------------


