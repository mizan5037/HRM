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

//-----------------------------------------------------------------
function write_pre_salarysheet($emp_id = false,$month=false,$year=false, $wd=0, $tl='', $ta='', $tot='', $tot2='', $wop='', $rt='',$atb='',$ea='',$ec='',$abj=0,$aar=0,$sal=0,$pd=0)
{
	
	$d = cal_days_in_month(CAL_GREGORIAN,$month,$year);
	

	if ($emp_id == false || $month == false || $year == false) {
		return false;
	}
	$sql = "DELETE FROM 0_presalary_sheet  where emp_id = ".$emp_id ." and month = ".$month. " and year = ".$year;
	 
	db_query($sql, _('Failde to Update Data.'));

	$sql = "INSERT INTO 0_presalary_sheet (emp_id, work_day, physical_present, total_leave, total_absent, total_overtime_hr, total_overtime_hr_Less_2, without_pay, rate, attn_bonus, ExtraAdd, ExtraCutt, abs_b_join, abs_a_release, salary , month, year) VALUES (".$emp_id." , ".$wd." , ".$pd." , ".$tl." , ".$ta." , ".$tot." , ".$tot2." , ".$wop." , ".$rt." ,  ".$atb." ,".$ea.", ".$ec.", ".$abj.", ".$aar.", ".$sal.", ".$month." , ".$year.")";
	db_query($sql, _('Failde to Update Data.'));
	 

	$sql = "UPDATE 0_presalary_sheet ps SET ps.total_paid = IF(ps.work_day = 0,0,((ps.salary+ps.attn_bonus+ps.ExtraAdd+(ps.total_overtime_hr_Less_2*ps.rate))-(ps.without_pay+ps.ExtraCutt))) WHERE ps.month = ".$month." AND ps.year = ".$year." AND ps.emp_id = ".$emp_id;
	db_query($sql, _('Sorry to over write data..!'));
	
}

function get_process_status($month=false,$year=false){
	if ($month == false || $year == false) {
		display_error('Failde to get data.');
		return false;
	}
	$sql = "SELECT SUM(status) result FROM 0_payslip_process_status WHERE month = ".$month." AND year = ".$year;
	$result = db_fetch(db_query($sql));
	return $result["result"];
}

function get_pre_salarysheet($month=false,$year=false,$card='',$department='',$unit='')
{
	if ($month == false || $year == false) {
		display_error('Failde to get data.');
		return false;
	}
	$cnt = 0;
	
	if ($department != '') {
		$department = ' e.department_id ='.$department;
		$cnt++;
		$dp = true;
	}
	if ($unit != '') {
		$unit = ' e.UnitID ='.$unit;
		$cnt++;
		$un = true;
	}

	$exSql = '';
	if ($cnt>0) {
		$exSql = ' WHERE ';
		if ($dp && $un) {
			$exSql = ' WHERE '.$department.' AND '.$unit;
		}elseif ($dp) {
			$exSql = ' WHERE '.$department;
		}else{
			$exSql = ' WHERE '.$unit;
		}
	}

	
	if ($card != '') {
		$exSql =' WHERE e.emp_card_no = '.$card;
	}
	$sql = "SELECT ps.*,concat(e.emp_first_name,'',IFNULL(e.emp_last_name,'')) name, e.emp_card_no card FROM (SELECT * FROM 0_presalary_sheet WHERE month = ".$month." AND year = ".$year.") ps LEFT JOIN 0_employee e ON ps.emp_id = e.emp_id ".$exSql." order by e.emp_card_no";

	return $sql;
}