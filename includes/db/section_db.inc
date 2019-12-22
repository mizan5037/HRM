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

function db_has_section() {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."section");
}

//--------------------------------------------------------------------------

function write_section($id = false, $name, $name_bd) {

	if(!$id) {
		$sql = "INSERT INTO ".TB_PREF."section(name,name_bd) VALUES (".db_escape($name).",".db_escape($name_bd).")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."section SET 
		name = ".db_escape($name)." ,name_bd =".db_escape($name_bd)." WHERE sec_id = ".db_escape($id);
	}
	
	db_query($sql, _('Could not write section data'));
}

//--------------------------------------------------------------------------

function get_sections($id = false, $all = false) {
	
	$sql = "SELECT * FROM ".TB_PREF."section";
	$where = array();
	
	if(!$all)
		$where[] = "!inactive";
	if($id)
		$where[] = "sec_id=".db_escape($id);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';

	$result = db_query($sql, _('Could not get section data.'));
	
	if($id)
		return db_fetch($result);
	else
		return $sql;
}

//--------------------------------------------------------------------------

function delete_section($id) {
	$sql = "DELETE FROM ".TB_PREF."section WHERE sec_id = ".db_escape($id);
	db_query($sql, _('Could not delete section.'));
}

//--------------------------------------------------------------------------

function section_has_employees($id) {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."employee WHERE section_id = ".db_escape($id));
}