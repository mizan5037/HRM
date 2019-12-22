<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Rashed                                      |
|   Date :   17-Feb-2019                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

function db_has_unit() {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."unit");
}

//--------------------------------------------------------------------------

function write_unit($id = false, $name, $code) {

	if(!$id) {
		$sql = "INSERT INTO ".TB_PREF."unit(UnitName,UnitCode) VALUES (".db_escape($name).",".db_escape($code).")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."unit SET 
		UnitName = ".db_escape($name).", UnitCode = ".db_escape($code)." WHERE UnitID = ".db_escape($id);
	}
	
	db_query($sql, _('Could not write unit data'));
}

//--------------------------------------------------------------------------

function get_units($id = false, $all = false) {
	
	$sql = "SELECT * FROM ".TB_PREF."unit";
	$where = array();

	if(!$all)
		$where[] = "!inactive";
	if($id)
		$where[] = "UnitID=".db_escape($id);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';

	$result = db_query($sql, _('Could not get unit data.'));
	
	if($id)
		return db_fetch($result);
	else

		return $sql;
}

//--------------------------------------------------------------------------

function delete_unit($id) {
	$sql = "DELETE FROM ".TB_PREF."unit WHERE UnitID = ".db_escape($id);
	db_query($sql, _('Could not delete unit.'));
}

//--------------------------------------------------------------------------

function unit_has_employees($id) {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."unit WHERE UnitID = ".db_escape($id));
}