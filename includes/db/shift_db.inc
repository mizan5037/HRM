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

//--------------------------------------------------------------------------

function shift_used($id) {

	return  check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."employee WHERE shift_id = ".db_escape($id));
}
//--------------------------------------------------------------------------

function write_shift($id = false, $shift_name, $starting_time, $ending_time,$activity,$bstart,$bend) {

	if ($starting_time > $ending_time) {
			$activity = 1;
		}
	if($id<=0){

			$sql = "INSERT INTO ".TB_PREF."shift(shift_name, starting_time,next_day_check, ending_time,break_start, break_end) VALUES (".db_escape($shift_name).", ".db_escape($starting_time).",".db_escape($activity).", ".db_escape($ending_time).",".db_escape($bstart).",".db_escape($bend).")";
		}
	else{
			$sql = "UPDATE ".TB_PREF."shift SET 
			shift_name = ".db_escape($shift_name).",
			starting_time = ".db_escape($starting_time).", 
			next_day_check = ".db_escape($activity).", 
			ending_time = ".db_escape($ending_time).", 
			break_start = ".db_escape($bstart).", 
			break_end = ".db_escape($bend)." 
			WHERE id = ".db_escape($id);
		}
		// display_notification($sql);
	
	db_query($sql, _('Could not write shift data'));
}
//--------------------------------------------------------------------------

function delete_shift($id) {
	
	$sql = "DELETE FROM ".TB_PREF."shift WHERE id = ".db_escape($id);
	db_query($sql, _('could not delete shift'));
}

//--------------------------------------------------------------------------

function get_shift($id = false, $all = false) {
    

	$sql = "SELECT * FROM ".TB_PREF."shift";
	
	if($id>0)
		$sql.=" WHERE id = ".$id;


	if($id)
		return db_query($sql, _('Could not write shift data'));
	else
		return $sql;

}