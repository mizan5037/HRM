<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   daily_attendance_db.inc                              |
\=======================================================*/


//--------------------------------------------------------------------------
function delete_attendance($date = false,$card='')
{
	if ($date == false) {
		display_error('Failde Delete.');
		return false;
	}
	if ($card != '') {
		$sql = "SELECT emp_id FROM 0_employee WHERE emp_card_no =".$card;
		$card = db_fetch(db_query($sql));
		$card = ' AND emp_id = '.$card['emp_id'];
	}
	$sql = "DELETE FROM ".TB_PREF."daily_attendance WHERE att_date = '".$date."'".$card;
	db_query($sql, _('Could not delete attandence.'));
	// display_notification($sql);
}

function delete_emp_attendance($date = false,$card='')
{
	if ($date == false || $card == '') {
		display_error('Failed Delete.');
		return false;
	}
	if ($card != '') {
		$sql = "SELECT emp_id FROM 0_employee WHERE emp_card_no =".$card;
		$card = db_fetch(db_query($sql));
	}
	$date = date("Y-m", strtotime($date)).'%';
	$sql = "DELETE FROM ".TB_PREF."daily_attendance WHERE att_date like '".$date."' AND emp_id = ".$card['emp_id'];
	db_query($sql, _('Could not delete attandence.'));
	// display_notification($sql);
}


function get_leave($date,$emp_id='')
{

	if ($emp_id != '') {
		$emp_id = get_employees_emp_id($emp_id);
		$emp_id = $emp_id["emp_id"];
		$sql = "SELECT * FROM 0_leave_ledger WHERE emp_id = $emp_id and EXTRACT(YEAR_MONTH FROM '$date') BETWEEN EXTRACT(YEAR_MONTH FROM leave_approved_from) AND EXTRACT(YEAR_MONTH FROM leave_approved_to)";
	}
	else{
		$sql = "SELECT * FROM 0_leave_ledger WHERE '".$date."' BETWEEN leave_approved_from AND leave_approved_to AND emp_id > 0";
	}
	
	return db_query($sql);
	// display_notification($sql);
}


//--------------------------------------------------------------------------
function write_daily_attendance($emp_id = false, $hours_no=0, $rate='', $att_date='', $exit_date='', $EntryTime='', $ExitTime='', $AttdStatus='',$proximity_id='')
{
	if ($emp_id == false) {
		display_error('Failde to get data.');
		return false;
	}
	$sql = "DELETE FROM 0_daily_attendance where att_date = '".$att_date."' and emp_id = ".$emp_id;
	// display_notification($sql);
	db_query($sql, _('Failde to Update Data.'));

	if ($AttdStatus == '' || $AttdStatus == null) {
		
        
		$sql = "SELECT get_att_status(".db_escape($emp_id).",timestamp(".db_escape($att_date).",  ".db_escape($EntryTime).")) as status";
		$AttdStatusdb = db_fetch(db_query($sql, _('Failde to Update Data.')));
		$AttdStatus = $AttdStatusdb["status"];

		$sql = "DELETE FROM 0_daily_attendance where att_date='".$att_date."' and emp_id=".$emp_id;
		db_query($sql);
		return false;
	}
	if ($AttdStatus > 3 ) {
		$AttdStatus = 8;
		$EntryTime='null';
		$ExitTime='null';
		$sql = "INSERT INTO 0_daily_attendance (emp_id, hours_no, rate, att_date, exit_date, EntryTime, ExitTime, AttdStatus, proximity_id) VALUES (".$emp_id." , null , null ,'".$att_date."' ,'0000-00-00', null , null , ".$AttdStatus." , '".$proximity_id."')";
	}
	else{
		if ($exit_date == '' || !$exit_date > $att_date ) {
			$exit_date = $att_date;
		}
		// var_dump($exit_date);
		$str_hours_no = " get_ot_hour(".db_escape($emp_id).", TIMESTAMP(".db_escape($att_date).",  ".db_escape($EntryTime)."), TIMESTAMP(".db_escape($exit_date).",".db_escape($ExitTime)."))";
		if (is_numeric($hours_no)) {
			$str_hours_no = $str_hours_no;
		}
		$str_rate = " get_ot_rate(".db_escape($emp_id).")";
		if (is_numeric($rate)) {
			$str_rate = $rate;
		}
		if (!is_numeric($proximity_id)) {
			$proximity_id = 'null';
		}
		$sql = "INSERT INTO 0_daily_attendance (emp_id, hours_no, rate, att_date, exit_date, EntryTime, ExitTime, AttdStatus, proximity_id) VALUES (".db_escape($emp_id)." , ".$str_hours_no."  , ".$str_rate." , ".db_escape($att_date)." , ".db_escape($exit_date)." , ".db_escape($EntryTime)." , ".db_escape($ExitTime)." , ".db_escape($AttdStatus)." , ".$proximity_id.")";
	}
	db_query($sql, _('Failde to Update Data.'));
	// display_notification($sql);
}

function get_daily_attend($date=false,$card='',$department='',$att_sts='')
{
	if ($date == false) {
		display_error('Failde to get data.');
		return false;
	}
	
	if ($department != '') {
		$department = ' and department_id ='.$department;
	}
	if ($att_sts != '') {
		$att_sts = ' WHERE d.AttdStatus='.$att_sts;
	}
	if ($card != '') {
		$card = ' and emp_card_no ='.$card;
		$department='';
		$att_sts='';
	}
	$sql = "select e.emp_id,e.name,e.emp_proximity_id,e.emp_card_no,e.department_id,d.hours_no,d.rate,d.att_date,d.exit_date,d.EntryTime,d.ExitTime,d.AttdStatus,d.work_hr from (SELECT 0_employee.emp_id, CONCAT(0_employee.emp_first_name, ' ', IFNULL(0_employee.emp_last_name,' ')) AS name, 0_employee.emp_proximity_id, 0_employee.emp_card_no,0_employee.department_id from 0_employee where 1 ".$card.$department.") as e LEFT join (select *,get_working_hour_tmp(emp_id,timestamp(att_date,EntryTime),timestamp(exit_date,ExitTime)) as work_hr from 0_daily_attendance where 0_daily_attendance.att_date='".$date."' )as d on (e.emp_id=d.emp_id) ".$att_sts." order by e.emp_card_no";
	return $sql;
}

function get_emp_daily_attend($date=false,$card='',$att_sts='')
{
	if ($date == false) {
		display_error('Failde to get data.');
		return false;
	}
	$date = date("Y-m", strtotime($date)).'%';

	if ($att_sts != '') {
		$att_sts = ' WHERE d.AttdStatus='.$att_sts;
	}
	$sql = "select e.emp_id,e.name,e.emp_proximity_id,e.emp_card_no,e.department_id,d.hours_no,d.rate,d.att_date,d.exit_date,d.EntryTime,d.ExitTime,d.AttdStatus,d.work_hr from (SELECT 0_employee.emp_id, CONCAT(0_employee.emp_first_name, ' ', IFNULL(0_employee.emp_last_name,' ')) AS name, 0_employee.emp_proximity_id, 0_employee.emp_card_no,0_employee.department_id from 0_employee where emp_card_no =".$card.") as e LEFT join (select *,get_working_hour_tmp(emp_id,timestamp(att_date,EntryTime),timestamp(exit_date,ExitTime)) as work_hr from 0_daily_attendance where 0_daily_attendance.att_date like '".$date."' )as d on (e.emp_id=d.emp_id) ".$att_sts." order by d.att_date  ";

	//display_notification($sql);
	return $sql;
}