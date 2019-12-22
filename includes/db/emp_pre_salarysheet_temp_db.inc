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

function delete_emp_temp($department='',$unit='')
{
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
	$sql = "DELETE tp FROM temp_presalary_sheet tp INNER JOIN 0_employee e ON tp.emp_id=e.emp_id ".$exSql;
	db_query($sql, _('Failde to Update Data.'));
}

function process_temp($department='',$unit='')
{
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
	$sql = "SELECT count(*) as total FROM (SELECT * FROM temp_presalary_sheet) ps LEFT JOIN 0_employee e ON ps.emp_id = e.emp_id ".$exSql;
	$total = db_fetch(db_query($sql, _('Failde to Update Data.')));
	if ($total["total"]  > 0) {
		return false;
	}else{
		return true;
	}
}
//-----------------------------------------------------------------
function write_pre_salarysheet_temp($emp_id = false, $wd=0, $tl='', $ta='', $tot='', $tot2='', $wop='', $rt='',$atb='',$ea='',$ec='',$abj=0,$aar=0,$sal=0,$pd=0,$month=0,$year=0)
{

	$d = cal_days_in_month(CAL_GREGORIAN,$month,$year);

	if ($emp_id == false) {
		return false;
	}
	$sql = "DELETE FROM temp_presalary_sheet  where emp_id = ".$emp_id;
	// display_notification($sql);
	db_query($sql, _('Failde to Update Data.'));

		$sql = "INSERT INTO temp_presalary_sheet (emp_id, work_day, physical_present, total_leave, total_absent, total_overtime_hr, total_overtime_hr_Less_2, without_pay, rate, attn_bonus, ExtraAdd, ExtraCutt, abs_b_join, abs_a_release, salary,month,year ) VALUES (".$emp_id." , ".$wd." , ".$pd." , ".$tl." , ".$ta." , ".$tot." , ".$tot2." , ".$wop." , ".$rt." ,  ".$atb." ,".$ea.", ".$ec.", ".$abj.", ".$aar.", ".$sal.",".$month.",".$year.")";
	// display_notification($sql);
	db_query($sql, _('Failde to Update Data.'));

	//$sql = "UPDATE temp_presalary_sheet ps SET ps.total_paid = IF(ps.work_day = 0,0,((ps.salary+ps.attn_bonus+ps.ExtraAdd+(ps.total_overtime_hr_Less_2*ps.rate))-(ps.without_pay+ps.ExtraCutt))) WHERE ps.emp_id = ".$emp_id;
	//db_query($sql, _('Sorry to over write data..!'));

	$sql = "SELECT * FROM temp_presalary_sheet WHERE emp_id=".$emp_id;
	$cutting = db_fetch(db_query($sql, _('Failde to Update Data.')));

	if ($cutting['abs_b_join'] || $cutting['abs_a_release']) {
		$sql = "UPDATE temp_presalary_sheet SET without_pay = (abs_b_join + abs_a_release)*(salary/".$d.") WHERE (abs_b_join + abs_a_release) > 0 AND month = ".$month." AND year = ".$year ;
		db_query($sql, _('Sorry to over write data..!'));
	}
	else
	{
		$sql = "UPDATE temp_presalary_sheet SET without_pay = ((salary-1850)/1.5)*(total_absent/".$d.") WHERE emp_id=".$emp_id." AND month = ".$month." AND year = ".$year ;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE temp_presalary_sheet SET total_paid = (salary-without_pay)  WHERE emp_id=".$emp_id." AND month = ".$month." AND year = ".$year ;
		db_query($sql, _('Sorry to over write data..!'));
	}




	
	// display_notification($sql);

	$sql = "UPDATE temp_presalary_sheet ps SET ps.total_paid = IF(ps.work_day = 0,0,((ps.salary+ps.attn_bonus+ps.ExtraAdd+(ps.total_overtime_hr_Less_2*ps.rate))-(ps.without_pay+ps.ExtraCutt))) WHERE ps.emp_id = ".$emp_id;
	db_query($sql, _('Sorry to over write data..!'));


}

function get_process_status_temp($month=false,$year=false){
	if ($month == false || $year == false) {
		display_error('Failde to get data.');
		return false;
	}
	$sql = "SELECT SUM(status) result FROM 0_payslip_process_status WHERE month = ".$month." AND year = ".$year;
	$result = db_fetch(db_query($sql));
	return $result["result"];
}

function get_pre_salarysheet_temp($card='',$department='',$unit='',$month,$year)
{
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
		}
		else{
			$exSql = ' WHERE '.$unit;
		}
	}
	else{
		$exSql = " WHERE ps.month=".$month." and ps.year=".$year."  ";
	}
	if ($card != '') {
		$exSql =" WHERE e.emp_card_no = ".$card." and ps.month=".$month." and ps.year=".$year." ";
	}
	$sql = "SELECT ps.*,concat(e.emp_first_name,'',IFNULL(e.emp_last_name,'')) name, e.emp_card_no card FROM (SELECT * FROM temp_presalary_sheet) ps LEFT JOIN 0_employee e ON ps.emp_id = e.emp_id ".$exSql."  order by e.emp_card_no";
  //display_notification($sql);
	return $sql;
}

function create_emp_temp($month = false, $year = false,$department,$unit){
	// echo $month,$year;
	if($month != false && year !=false) {
		$months  = strlen($month) == 2 ? $month : '0'.$month;
		$date = $year.'-'.$months.'%';
		$d = cal_days_in_month(CAL_GREGORIAN,$months,$year);
		$newdate = $year.'-'.$months.'-'.$d;

	$cnt = 0;
	if ($department != '') {
		$department = ' department_id ='.$department;
		$cnt++;
		$dp = true;
	}
	if ($unit != '') {
		$unit = ' UnitID ='.$unit;
		$cnt++;
		$un = true;
	}

	$exSql = '';
	if ($cnt>0) {
		if ($dp && $un) {
			$exSql = ' and '.$department.' AND '.$unit;
		}elseif ($dp) {
			$exSql = ' and '.$department;
		}else{
			$exSql = ' and '.$unit;
		}
	}

		$sql = "INSERT INTO temp_presalary_sheet (emp_id,salary,attn_bonus, month, year) SELECT DISTINCT(emp_id),emp_salary,emp_atten_bonus,".$month.",".$year." FROM 0_employee WHERE inactive = 0 AND ((!date_format(emp_releasedate,'%C') < ".$month." AND !date_format(emp_releasedate,'%Y') < ".$year.") OR (date_format(emp_releasedate,'%C') = 0 AND date_format(emp_releasedate,'%Y') = 0)) ".$exSql;
		 display_notification($sql);
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE temp_presalary_sheet ps SET ps.abs_b_join = (SELECT IFNULL(DAY(emp_hiredate)-1,0) FROM 0_employee WHERE emp_hiredate LIKE '".$date."' AND ps.emp_id = emp_id), abs_a_release = (SELECT IFNULL( DAY(LAST_DAY(emp_releasedate))-DAY(DATE_SUB(emp_releasedate, INTERVAL 1 DAY)),0) FROM 0_employee WHERE emp_releasedate LIKE '".$date."' AND ps.emp_id = emp_id)";
		// display_notification($sql);
		db_query($sql, _('Sorry to over write data..!'));
	}
}
