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


function write_holiday($serial = false, $holiday_name, $holiday_date, $holiday_type) {
	$holiday_date = date_create($holiday_date);
	$holiday_date = date_format($holiday_date,'Y-m-d');

	if(!$serial){
		$sql = "INSERT INTO ".TB_PREF."emp_holiday (holiday_name, holiday_date, holiday_type) VALUES ("
			.db_escape($holiday_name).","
			.db_escape($holiday_date).","
			.db_escape($holiday_type).
			")";
	}
	else
		$sql = "UPDATE ".TB_PREF."emp_holiday SET 
		holiday_name = ".db_escape($holiday_name).", 
		holiday_date = ".db_escape($holiday_date)." ,
		holiday_type = ".db_escape($holiday_type)." 
		WHERE serial = ".db_escape($serial);
	
	db_query($sql, _('Could not write holiday data'));
	//return $sql;
}
//--------------------------------------------------------------------------

function delete_holiday($serial) {
	
	$sql = "DELETE FROM ".TB_PREF."emp_holiday WHERE serial = ".db_escape($serial);;
	db_query($sql, _('could not delete holiday'));
}

//--------------------------------------------------------------------------

function get_holiday($serial = false, $all = false) {
	$sql = "SELECT * FROM ".TB_PREF."emp_holiday";
	if($serial > 0){
		$sql .= ' WHERE serial = '.db_escape($serial);
		$sql .= ' ORDER BY holiday_date desc';
		return db_query($sql,_('Could not get the selected holiday'));
	}
	else{
		$sql .= ' ORDER BY holiday_date desc';
		return $sql;
	}

}