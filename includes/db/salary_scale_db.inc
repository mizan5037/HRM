<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

function db_has_salary_scale() {

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."salaryscale");
}

//--------------------------------------------------------------------------

function set_basic_salary($account, $amount, $id, $new=false) {
    $date = date2sql(Today());

	if($new)
	    $sql = "INSERT INTO ".TB_PREF."salary_structure(date, salary_scale_id, pay_rule_id, pay_amount, type, is_basic) VALUES ('$date', ".db_escape($id).", ".db_escape($account).", ".db_escape($amount).", 1, 1)";
	else
		$sql = "UPDATE ".TB_PREF."salary_structure SET pay_rule_id = ".db_escape($account).", pay_amount = ".db_escape($amount)." WHERE salary_scale_id = ".db_escape($id);
	
	    db_query($sql, _('Could not write salary structure data'));
}

function write_scale($id = false, $name, $pay_basis,$gl_debit,$gl_credit) {
	
	if(!$id)
		$sql = "INSERT INTO ".TB_PREF."salaryscale(scale_name, pay_basis, gl_debit, gl_credit) VALUES (".db_escape($name).", ".db_escape($pay_basis).", ".db_escape($gl_debit).", ".db_escape($gl_credit).")";
	else
		$sql = "UPDATE ".TB_PREF."salaryscale SET scale_name = ".db_escape($name).", pay_basis = ".db_escape($pay_basis).", gl_debit = ".db_escape($gl_debit).", gl_credit = ".db_escape($gl_credit)." WHERE scale_id = ".db_escape($id);
	
	db_query($sql, _('Could not write salary scale data'));
}
//--------------------------------------------------------------------------

function delete_salary_scale($id) {
	
	$sql = "DELETE FROM ".TB_PREF."salaryscale WHERE scale_id = ".db_escape($id);;
	db_query($sql, _('could not delete salary scale'));
}

//--------------------------------------------------------------------------

function get_salary_scale($id = false, $all = false) {
    

	$sql = "SELECT sc.*, 0 as pay_amount, 0 as pay_rule_id FROM ".TB_PREF."salaryscale sc";
	
	if($id)
		$where[] = "scale_id=".db_escape($id);
	if(count($where))
		$sql .= ' WHERE '.implode(' ', $where);
	else
		$sql .= ' WHERE 1';	
	if($id)
		return $sql;
	else
		return $sql;

}

//--------------------------------------------------------------------------

function salary_scale_used($id) {

	return  check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."employee WHERE salary_scale_id = ".db_escape($id));
}

//--------------------------------------------------------------------------

function get_emp_basic_salary($id) {
	$sql = "SELECT s.pay_amount, s.pay_rule_id FROM ".TB_PREF."salary_structure s, ".TB_PREF."employee e WHERE e.salary_scale_id = s.salary_scale_id AND s.is_basic = 1 AND e.emp_id = ".db_escape($id);

	$result = db_query($sql, _('could not get salary structure'));
	return db_fetch($result);
}

function rashed_add_payslip_tmp ($scale_id, $emp_pmt_str_id, $emp_pmt_tmp_amt){
	$sql = "INSERT INTO ".TB_PREF."emp_pmt_tmp (scale_id,emp_pmt_str_id,emp_pmt_tmp_amt) VALUES ("
		.db_escape($scale_id).", "
		.db_escape($emp_pmt_str_id).", "		
		.db_escape($emp_pmt_tmp_amt).")";

	db_query($sql,"The employee payslip could not be added");
	return  db_insert_id(); 
	//return $sql;
}

function rashed_edit_payslip_tmp($emp_pmt_tmp_id, $emp_pmt_str_id, $emp_pmt_tmp_amt){
	$amt = floatval(str_replace(',', '', $emp_pmt_tmp_amt));
	$sql = "Update ".TB_PREF."emp_pmt_tmp set emp_pmt_tmp_amt='".$amt."', emp_pmt_str_id=".$emp_pmt_str_id." Where emp_pmt_tmp_id=".db_escape($emp_pmt_tmp_id);

	db_query($sql,"The employee payslip could not be added");
	return $emp_pmt_tmp_id;
}
function rashed_delete_payslip_tmp($emp_pmt_tmp_id){
	$sql = "delete from ".TB_PREF."emp_pmt_tmp Where emp_pmt_tmp_id = ".db_escape($emp_pmt_tmp_id);

	db_query($sql,"The employee payslip could not be added");
	return $emp_pmt_tmp_id;
}