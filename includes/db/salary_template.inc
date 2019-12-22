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

function db_has_emp_pmt_str() {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."emp_pmt_str_tmp");
}

//--------------------------------------------------------------------------

function write_emp_pmt_str($id = false, $name) {

	if(!$id) {
		$sql = "INSERT INTO ".TB_PREF."emp_pmt_str_tmp(emp_pmt_str_name) VALUES (".db_escape($name).")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."emp_pmt_str_tmp SET 
		emp_pmt_str_name = ".db_escape($name)." WHERE emp_pmt_str_id = ".db_escape($id);
	}
	
	db_query($sql, _('Could not write emp_pmt_str_tmp data'));
}

//--------------------------------------------------------------------------

function get_emp_pmt_str($id = false, $all = false) {
	
	$sql = "SELECT * FROM ".TB_PREF."emp_pmt_str_tmp";
	$where = array();

	if(!$all)
		$where[] = "!inactive";
	if($id)
		$where[] = "emp_pmt_str_id=".db_escape($id);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';
		
	$result = db_query($sql, _('Could not get payment string data.'));
	
	if($id)
		return db_fetch($result);
	else
		return $sql;
}

//--------------------------------------------------------------------------

function delete_emp_pmt_str($id) {
	$sql = "DELETE FROM ".TB_PREF."emp_pmt_str_tmp WHERE emp_pmt_str_id = ".db_escape($id);
	db_query($sql, _('Could not delete department.'));
}

//--------------------------------------------------------------------------

function salary_template_has_pmt_str_tmp($id) {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."emp_pmt_str_tmp WHERE emp_pmt_str_id = ".db_escape($id));
}