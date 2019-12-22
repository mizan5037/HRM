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

// function db_has_overtime() {
// 	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."emp_pmt_str_tmp");
// }

//--------------------------------------------------------------------------

function write_emp_shift_list($id = false, $emp_id, $shift_id, $from, $to) {
	if($id == false) {
		$sql = "INSERT INTO ".TB_PREF."emp_shift_list(emp_id,shift_id,shift_from,shift_to) VALUES (".db_escape($emp_id).",".db_escape($shift_id).", ".db_escape($from)." , ".db_escape($to).")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."emp_shift_list SET 
		emp_id = ".db_escape($emp_id).", 
		shift_id = ".db_escape($shift_id).", 
		shift_from = ".db_escape($from).",
		shift_to = ".db_escape($to)."
		WHERE id = ".db_escape($id);
	}
	
	db_query($sql, _('Could not write payment string data'));
	 //display_notification($sql);
}

//--------------------------------------------------------------------------


function get_emp_shift_list($id = false, $all = false) {
	
	$sql = "SELECT esf.*, s.shift_name FROM (SELECT es.*,e.emp_card_no,CONCAT(e.emp_first_name,' ',IFNULL(e.emp_last_name,' ')) as name FROM 0_emp_shift_list es LEFT JOIN 0_employee e ON es.emp_id = e.emp_id) esf LEFT JOIN 0_shift s ON esf.shift_id = s.id";
	
	if($id !=false)
		$sql .= " WHERE esf.id = ".db_escape($id);

	$sql .= " order by esf.shift_from desc, esf.emp_card_no desc";
	
	$result = db_query($sql, _('Could not get payment string data.'));
	
	if($id !=false)
		return db_fetch($result);
	else
		return $sql;

}


//--------------------------------------------------------------------------

function delete_emp_shift_list($id) {
	$sql = "DELETE FROM ".TB_PREF."emp_shift_list WHERE id = ".db_escape($id);
	db_query($sql, _('Could not delete payment string.'));
}

//--------------------------------------------------------------------------