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

function write_overtime_tmp($id = false, $name, $rate, $scale_id) {

	if(!$id) {
		$sql = "INSERT INTO ".TB_PREF."overtime_template (overtime_id, overtime_rate, scale_id) VALUES (".db_escape($name).", ".db_escape($rate).", ".db_escape($scale_id).")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."overtime_template SET 
		overtime_id = ".db_escape($name).", overtime_rate = ".db_escape($rate).", scale_id = ".db_escape($scale_id)." WHERE overtime_id = ".db_escape($id);
	}
	
	db_query($sql, _('Could not write overtime data'));
	//return $sql;
}

//--------------------------------------------------------------------------

function get_overtime_tmp ($id = false, $all = false) {
	
	$sql="SELECT ot_tmp.overtime_tmp_id,  ot_tmp.overtime_id, ot.overtime_name, ot_tmp.overtime_rate, ot_tmp.inactive, 
		sc.scale_id, sc.scale_name 
		FROM ".TB_PREF."overtime_template ot_tmp 
		left join ".TB_PREF."overtime ot on ot_tmp.overtime_id=ot.overtime_id
		left join ".TB_PREF."salaryscale sc on ot_tmp.scale_id=sc.scale_id";

	$where = array();
	
	if(!$all)
		$where[] = "!ot.inactive";
	if($id)
		$where[] = "overtime_tmp_id=".db_escape($id);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';	
	$result = db_query($sql, _('Could not get overtime data.'));
	
	if($id)
		return db_fetch($result);
	else
		return $sql;

}

//--------------------------------------------------------------------------

function delete_overtime_tmp($id) {
	$sql = "DELETE FROM ".TB_PREF."overtime_template WHERE overtime_tmp_id = ".db_escape($id);
	db_query($sql, _('Could not delete overtime.'));
}

//--------------------------------------------------------------------------
function overtime_tmp_used($id) {
	//return check_empty_result("SELECT overtime_id FROM ".TB_PREF."attendance WHERE overtime_id = ".db_escape($id));
}

//--------------------------------------------------------------------------