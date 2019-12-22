<?php

function process_status($value = '')
{
	

	if ($value == true) {
		$name = 'button_ok.png';
	} else {
		$name = 'escape.png';
	}
	$path_to_root  = '../../..';
	echo "<td>";
	echo '<center><img src="' . $path_to_root . '/themes/canvas/images/' . $name . '" height="12" alt=""><span>' . $title . '</span></center>';
	echo "</td>";
}

function hidden_input($name = '', $value = '')
{
	echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}

function btn_sub_Add($type = 'submit', $name = '', $id = '', $value = '', $title = 'Add', $disable = '')
{
	$disable = $disable ? 'disabled' : '';
	$path_to_root  = '../../..';
	echo "<td>";
	echo '<center><button  type="' . $type . '"  name="' . $name . '" id="' . $id . '" value="' . $value . '" title="Search items" ' . $disable . '><img src="' . $path_to_root . '/themes/default/images/ajax-loader.gif" height="12" alt=""><span>' . $title . '</span></button></center>';
	echo "</td>";
}

function get_payslip_process_status($month, $year)
{

	$sql = "SELECT * FROM " . TB_PREF . "payslip_process_status where month = '" . $month . "' and year = '" . $year . "'";
	// display_notification($sql);
	$result = db_query($sql, _('Could not get Payslip Process Status data.'));
	return $result;
}

function delete_pre_pro_sal($month = false, $year = false)
{
	// echo $month,$year;
	if ($month != false && year != false) {

		$sql = "DELETE FROM 0_payslip_process_status WHERE month = " . db_escape($month) . " AND year = " . db_escape($year);
		db_query($sql, _('Sorry to delete data..!'));
		// display_notification($sql);
		$sql = "DELETE FROM 0_presalary_sheet WHERE month = " . db_escape($month) . " AND year = " . db_escape($year);
		db_query($sql, _('Sorry to delete data..!'));

		$sql = "TRUNCATE TABLE temp_abs;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_abs2;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_leave_ledger;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_absent;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_overtime_l_2";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_overtime_hr";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_rate";
		db_query($sql, _('Sorry to over write data..!'));

		$sql =  "TRUNCATE TABLE temp_sal";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_emp_att_bonous";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "DELETE FROM total_ot_with_friday where month=$month and year= $year";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "DELETE FROM temp_ot_sheet_total where month=$month and year= $year";
		db_query($sql, _('Sorry to over write data..!'));
		// display_notification($sql);

	}
}

function create_emp($month = false, $year = false)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';
		$d = cal_days_in_month(CAL_GREGORIAN, $months, $year);
		$newdate = $year . '-' . $months . '-' . $d;

		$sql = "DELETE d FROM 0_daily_attendance d LEFT JOIN 0_employee e ON d.emp_id=e.emp_id WHERE e.emp_releasedate LIKE '" . $date . "' AND d.att_date >= e.emp_releasedate";
		db_query($sql, _('Sorry to over write data..!'));

		// $sql ="UPDATE 0_employee SET inactive = 1 WHERE MONTH(emp_releasedate) != 0 AND YEAR(emp_releasedate) != 0 AND abs(MONTH(emp_releasedate)-".$month.") > 1 AND YEAR(emp_releasedate) <= ".$year;
		$sql = "UPDATE 0_employee SET inactive = 1 WHERE MONTH(emp_releasedate) != 0 AND YEAR(emp_releasedate) != 0 AND DATEDIFF('" . $newdate . "', emp_releasedate) > 60";
		db_query($sql, _('Sorry to over write data..!'));
		// display_notification($sql);

		$sql = "INSERT INTO 0_presalary_sheet (emp_id,salary, month, year) SELECT DISTINCT(emp_id),emp_salary," . $month . "," . $year . " FROM 0_employee WHERE inactive = 0 AND ((!date_format(emp_releasedate,'%C') < " . $month . " AND !date_format(emp_releasedate,'%Y') < " . $year . ") OR (date_format(emp_releasedate,'%C') = 0 AND date_format(emp_releasedate,'%Y') = 0))";
		// $sql = "SELECT DISTINCT(emp_id) FROM ".TB_PREF."employee where inactive = 0 AND ((!date_format(emp_releasedate,'%C') < ".$month." AND !date_format(emp_releasedate,'%Y') < ".$year.") OR (date_format(emp_releasedate,'%C') = 0 AND date_format(emp_releasedate,'%Y') = 0)) ORDER BY emp_id";

		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.abs_b_join = (SELECT IFNULL(DAY(emp_hiredate)-1,0) FROM 0_employee WHERE emp_hiredate LIKE '" . $date . "' AND ps.emp_id = emp_id), abs_a_release = (SELECT IFNULL( DAY(LAST_DAY(emp_releasedate))-DAY(DATE_SUB(emp_releasedate, INTERVAL 1 DAY)),0) FROM 0_employee WHERE emp_releasedate LIKE '" . $date . "' AND ps.emp_id = emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('create_emp', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));
	}
}

// SELECT e.emp_id, ept.emp_pmt_tmp_amt FROM (SELECT DISTINCT(emp_id) FROM 0_employee WHERE inactive = 0 AND (emp_releasedate LIKE '2019-04%' OR emp_hiredate LIKE '2019-04%')) e INNER JOIN (SELECT emp_id, emp_pmt_tmp_amt FROM 0_emp_pmt_tmp WHERE emp_pmt_str_id = 1) ept ON e.emp_id= ept.emp_id



function total_workday($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';

		$d = cal_days_in_month(CAL_GREGORIAN, $months, $year);

		$sql = "UPDATE 0_presalary_sheet ps SET ps.work_day = (" . $d . "-ps.total_absent) WHERE ps.month = " . $month . " AND ps.year =" . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO temp_abs (emp_id) SELECT emp_id FROM 0_presalary_sheet";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO temp_abs2 SELECT emp_id, COUNT(*) present FROM 0_daily_attendance WHERE emp_id > 0 AND proximity_id > 0 AND att_date LIKE '" . $date . "' AND AttdStatus < 8 GROUP BY emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE temp_abs tt SET tt.day = IFNULL((SELECT day FROM temp_abs2 WHERE emp_id = tt.emp_id),tt.day)";
		db_query($sql, _('Sorry to over write data..!'));

		// $sql = "UPDATE 0_presalary_sheet ps SET ps.work_day = IFNULL((SELECT day FROM temp_abs WHERE day = 0 AND emp_id = ps.emp_id),ps.work_day) , ps.total_absent = ".$d;
		$sql = "UPDATE 0_presalary_sheet ps INNER JOIN temp_abs ta ON (ps.emp_id = ta.emp_id AND ta.day = 0) SET ps.work_day = IFNULL(ta.day,ps.work_day) , ps.total_absent = " . $d . " WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_abs;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO temp_abs SELECT emp_id,COUNT(*) FROM 0_daily_attendance WHERE att_date LIKE '" . $date . "' AND emp_id > 0 AND AttdStatus IN (1,2,3) GROUP BY emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.physical_present = IFNULL((SELECT day FROM temp_abs WHERE emp_id = ps.emp_id),0)  WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_abs;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_abs2;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.work_day = IFNULL(ps.work_day - (ps.abs_b_join+ps.abs_a_release),0), ps.total_absent = IFNULL(ps.total_absent + (ps.abs_b_join+ps.abs_a_release),0) WHERE (ps.abs_b_join+ps.abs_a_release) > 0 AND ps.work_day > 0 AND ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('total_workday', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}

function total_leave_day($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {

		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';

		$month2 = $month + 1;
		$month2 = strlen($month2) == 2 ? $month2 : '0' . $month2;
		$date2 = $year . '-' . $month2 . '%';


		$d = cal_days_in_month(CAL_GREGORIAN, $months, $year);
		$d  = strlen($d) == 2 ? $d : '0' . $d;
		$lastdate = $year . '-' . $months . '-' . $d;

		$sql = "INSERT INTO temp_leave_ledger SELECT tt.emp_id, SUM(tt.total_d) day FROM (SELECT SUM(total_day) total_d,emp_id FROM 0_leave_ledger WHERE leave_approved_from LIKE '" . $date . "' AND leave_approved_to LIKE '" . $date . "' GROUP BY emp_id UNION SELECT SUM(datediff('" . $lastdate . "',leave_approved_from))+1 total_d,emp_id FROM 0_leave_ledger WHERE leave_approved_from LIKE '" . $date . "' AND leave_approved_to LIKE '" . $date2 . "' GROUP BY emp_id) tt GROUP BY tt.emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_leave = (SELECT day FROM temp_leave_ledger WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));
		$sql = "TRUNCATE TABLE temp_leave_ledger;";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('total_leave_day', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}

function total_absent($month, $year)
{
	echo $month, $year;
	if ($month != false && year != false) {

		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';

		$sql = "INSERT INTO temp_absent SELECT n.emp_id,COUNT(*) absent FROM (SELECT IFNULL(l.leave_type , 8) type,d_att.emp_id FROM (SELECT * FROM 0_daily_attendance WHERE emp_id != 0 AND att_date LIKE '" . $date . "' AND AttdStatus = 8) d_att LEFT JOIN 0_leave_ledger l ON (l.emp_id = d_att.emp_id AND (d_att.att_date BETWEEN l.leave_approved_from AND l.leave_approved_to))) n WHERE n.type = 8 GROUP BY n.emp_id ORDER by n.emp_id;";

		// SELECT ne.emp_id,ne.absent FROM (SELECT n.emp_id,COUNT(*) absent FROM (SELECT IFNULL(l.leave_type , 8) type,d_att.emp_id FROM (SELECT * FROM 0_daily_attendance WHERE emp_id != 0 AND att_date LIKE '2019-04%' AND AttdStatus = 8) d_att LEFT JOIN 0_leave_ledger l ON (l.emp_id = d_att.emp_id AND (d_att.att_date BETWEEN l.leave_approved_from AND l.leave_approved_to))) n WHERE n.type = 8 GROUP BY n.emp_id) ne WHERE ne.absent < 31

		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_absent = (SELECT day FROM temp_absent WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year . ";";
		db_query($sql, _('Sorry to over write data..!'));

		// $sql = "TRUNCATE TABLE temp_absent;";
		// db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('total_absent', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}




function overtime_hr($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';

		$sql = "INSERT INTO temp_overtime_l_2 SELECT emp_id, SUM(IF(IFNULL(hours_no,0) > 2,2,hours_no)) FROM 0_daily_attendance WHERE att_date LIKE '" . $date . "' AND dayname(att_date) != 'Friday' AND emp_id>0 AND proximity_id>0 GROUP BY emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO temp_ot_sheet_total SELECT emp_id, SUM(IF(hours_no> 2,(hours_no-2),0)),$month,$year FROM 0_daily_attendance WHERE att_date LIKE '" . $date . "' AND dayname(att_date) != 'Friday' AND emp_id>0 AND proximity_id>0 GROUP BY emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO total_ot_with_friday SELECT emp_id,SUM(get_working_hour_tmp(emp_id,timestamp(att_date,EntryTime),timestamp(exit_date,ExitTime))),$month,$year as ot_hr FROM 0_daily_attendance WHERE att_date LIKE '$date' AND dayname(att_date)= 'Friday' AND emp_id>0 AND AttdStatus < 8 AND proximity_id>0 GROUP BY emp_id ";
		db_query($sql, _('Sorry to over write data..!'));


		$sql = "UPDATE temp_ot_sheet_total t INNER JOIN total_ot_with_friday f ON t.emp_id = f.emp_id SET t.hour = t.hour + f.hour";
		db_query($sql, _('Sorry to over write data..!'));



		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_overtime_hr_Less_2 = (SELECT hour FROM temp_overtime_l_2 WHERE emp_id = ps.emp_id),ps.less_than_two_hour_friday = (SELECT hour FROM temp_overtime_l_2 WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_overtime_l_2";
		db_query($sql, _('Sorry to over write data..!'));


		$sql = "INSERT INTO temp_overtime_l_2 SELECT emp_id,SUM(IF(ExitTime > '19:00:00',hours_no-get_ot_hour_friday(ExitTime),hours_no)) as ot_hr FROM 0_daily_attendance WHERE att_date LIKE '" . $date . "' AND dayname(att_date)= 'Friday' AND emp_id>0 AND AttdStatus < 8 AND proximity_id>0 GROUP BY emp_id";

		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_overtime_hr_Less_2 = ps.total_overtime_hr_Less_2+ IFNULL((SELECT hour FROM temp_overtime_l_2 WHERE emp_id = ps.emp_id),0) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_overtime_l_2";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO temp_overtime_hr SELECT emp_id,IFNULL(SUM(IFNULL(hours_no,0)),0) FROM 0_daily_attendance WHERE emp_id> 0 AND att_date LIKE '" . $date . "' GROUP BY emp_id";
		db_query($sql, _('Sorry to over write data..!'));

		// $sql = "INSERT INTO temp_overtime_friday SELECT emp_id,IFNULL(SUM(IFNULL(hours_no,0)),0) FROM 0_daily_attendance WHERE emp_id> 0 AND dayname(att_date) != 'Friday' AND att_date LIKE '".$date."' GROUP BY emp_id";
		// db_query($sql, _('Sorry to over write data..!'));

		// $sql = "INSERT INTO total_ot_with_friday SELECT emp_id,SUM(get_working_hour_tmp(emp_id,timestamp(att_date,EntryTime),timestamp(exit_date,ExitTime))) as ot_hr FROM 0_daily_attendance WHERE att_date LIKE '$date' AND dayname(att_date)= 'Friday' AND emp_id>0 AND AttdStatus < 8 AND proximity_id>0 GROUP BY emp_id ";
		// db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_overtime_hr = (SELECT time FROM temp_overtime_hr WHERE emp_id = ps.emp_id),ps.total_ot_friday = (SELECT hour FROM temp_ot_sheet_total WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));





		$sql = "TRUNCATE TABLE temp_overtime_hr";
		db_query($sql, _('Sorry to over write data..!'));

		

		$sql = "TRUNCATE TABLE temp_overtime_friday";
		db_query($sql, _('Sorry to over write data..!'));

		


		$sql = "INSERT INTO temp_rate SELECT emp_id,IFNULL(round((emp_pmt_tmp_amt/104),2),0) rate FROM 0_emp_pmt_tmp WHERE emp_pmt_str_id = 1";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.rate = (SELECT rate FROM temp_rate WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_rate";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('overtime_hr', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}




function without_pay($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';
		$d = cal_days_in_month(CAL_GREGORIAN, $months, $year);

		$sql = "UPDATE 0_presalary_sheet SET without_pay = (abs_b_join + abs_a_release)*(salary/" . $d . ") WHERE (abs_b_join + abs_a_release) > 0 AND month = " . $month . " AND year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));



		// $sql =  "INSERT INTO temp_d_sal (emp_id,sal) SELECT e.emp_id, ept.emp_pmt_tmp_amt FROM (SELECT DISTINCT(emp_id) FROM 0_employee WHERE inactive = 0 AND (emp_releasedate LIKE '".$date."' OR emp_hiredate LIKE '".$date."')) e INNER JOIN (SELECT emp_id, emp_pmt_tmp_amt FROM 0_emp_pmt_tmp WHERE emp_pmt_str_id = 1) ept ON e.emp_id= ept.emp_id";

		// $sql =  "INSERT INTO temp_d_sal SELECT DISTINCT(emp_id),emp_salary,".$d." FROM 0_employee WHERE inactive = 0 AND (emp_releasedate LIKE '".$date."' OR emp_hiredate LIKE '".$date."')";
		// db_query($sql, _('Sorry to over write data..!'));

		// INSERT INTO temp_abs_b_join SELECT emp_id,DAY(emp_hiredate)-1,emp_salary FROM 0_employee WHERE emp_hiredate LIKE '2019-07%'

		// $sql =  "INSERT INTO temp_sal SELECT DISTINCT(emp_id),emp_salary,".$d." FROM 0_employee WHERE inactive = 0 AND ((!date_format(emp_releasedate,'%C') < ".$month." AND !date_format(emp_releasedate,'%Y') < ".$year.") OR (date_format(emp_releasedate,'%C') = 0 AND date_format(emp_releasedate,'%Y') = 0))";

		// $sql =  "INSERT INTO temp_sal SELECT e.emp_id, ept.emp_pmt_tmp_amt, 30 as day FROM (SELECT DISTINCT(emp_id) FROM 0_employee WHERE  inactive = 0 AND ((!date_format(emp_releasedate,'%C') < ".$month." AND !date_format(emp_releasedate,'%Y') < ".$year.") OR (date_format(emp_releasedate,'%C') = 0 AND date_format(emp_releasedate,'%Y') = 0))) e INNER JOIN (SELECT emp_id, emp_pmt_tmp_amt FROM 0_emp_pmt_tmp WHERE emp_pmt_str_id = 1) ept ON e.emp_id= ept.emp_id";

		$sql =  "INSERT INTO temp_sal (SELECT ps.emp_id,ept.basic,30 as day FROM (SELECT DISTINCT(emp_id) FROM 0_presalary_sheet WHERE month = " . $month . " AND year = " . $year . ") ps INNER JOIN (SELECT emp_id, emp_pmt_tmp_amt as basic FROM 0_emp_pmt_tmp WHERE emp_pmt_str_id = 1) ept ON ps.emp_id= ept.emp_id)";

		db_query($sql, _('Sorry to over write data..!'));

		// $sql =  "UPDATE temp_sal ts INNER JOIN temp_d_sal tds ON ts.emp_id = tds.emp_id SET ts.sal = IFNULL(tds.sal,ts.sal),ts.day = tds.day";
		// db_query($sql, _('Sorry to over write data..!'));

		// $sql =  "UPDATE 0_presalary_sheet ps SET ps.without_pay = (SELECT IFNULL((ts.sal/ts.day)*ps.total_absent,0) FROM temp_sal ts WHERE ts.emp_id = ps.emp_id) WHERE ps.month = ".$month." and ps.year = ".$year;
		$sql = "UPDATE 0_presalary_sheet ps SET ps.without_pay = ps.without_pay+(SELECT IFNULL((ts.sal/ts.day)*(ps.total_absent-(ps.abs_b_join+ps.abs_a_release)),0) FROM temp_sal ts WHERE ts.emp_id = ps.emp_id) WHERE ps.month = " . $month . " and ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql =  "TRUNCATE TABLE temp_sal";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('without_pay', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}




function att_bns($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$months  = strlen($month) == 2 ? $month : '0' . $month;
		$date = $year . '-' . $months . '%';

		$sql = "INSERT INTO temp_emp_att_bonous SELECT aa.emp_id,0 FROM (SELECT DISTINCT(emp_id) FROM 0_daily_attendance WHERE att_date LIKE '" . $date . "' AND (AttdStatus = 3 OR AttdStatus = 8 ) UNION SELECT DISTINCT(emp_id) FROM 0_leave_ledger l WHERE (month(leave_approved_from) = " . $month . " AND YEAR(leave_approved_from) = " . $year . ") OR (month(leave_approved_to) = " . $month . " AND YEAR(leave_approved_to) = " . $year . ") )aa";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.attn_bonus = (SELECT money FROM temp_emp_att_bonous WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.attn_bonus = (SELECT emp_atten_bonus FROM 0_employee WHERE emp_id = ps.emp_id) WHERE ps.month = " . $month . " AND ps.year = " . $year . " AND ISNULL(ps.attn_bonus)";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.attn_bonus =  0 WHERE (ps.work_day = 0 OR (ps.abs_b_join + ps.abs_a_release) > 0) AND ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "TRUNCATE TABLE temp_emp_att_bonous";
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps INNER JOIN 0_employee e ON (ps.emp_id = e.emp_id AND e.department_id IN (7,8,9,18,19,20,21,22) ) SET ps.attn_bonus = 0, ps.total_overtime_hr_Less_2 = 0, ps.total_overtime_hr = 0 WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "UPDATE 0_presalary_sheet ps SET ps.total_paid = IF(ps.work_day = 0,0,((ps.salary+ps.attn_bonus+ps.ExtraAdd+(ps.total_overtime_hr_Less_2*ps.rate))-(ps.without_pay+ps.ExtraCutt))) WHERE ps.month = " . $month . " AND ps.year = " . $year;
		db_query($sql, _('Sorry to over write data..!'));

		$sql = "INSERT INTO " . TB_PREF . "payslip_process_status (step, status, month, year) VALUES ('att_bns', 1," . db_escape($month) . ", " . db_escape($year) . ")";
	}
	// display_notification($sql);
	db_query($sql, _('Sorry to over write data..!'));
}

function update_temp_to_presalary($month, $year)
{
	// echo $month,$year;
	if ($month != false && year != false) {
		$sql = "UPDATE 0_presalary_sheet ps INNER JOIN temp_presalary_sheet tp ON (ps.emp_id=tp.emp_id)
				SET ps.abs_b_join = tp.abs_b_join,
					ps.abs_a_release=ps.abs_a_release,
					ps.physical_present= tp.physical_present,
					ps.work_day= tp.work_day,
					ps.total_leave= tp.total_leave,
					ps.total_absent= tp.total_absent,
					ps.total_overtime_hr= tp.total_overtime_hr,
					ps.total_overtime_hr_Less_2= tp.total_overtime_hr_Less_2,
					ps.without_pay= tp.without_pay,
					ps.rate= tp.rate,
					ps.attn_bonus= tp.attn_bonus,
					ps.salary= tp.salary,
					ps.ExtraAdd= tp.ExtraAdd,
					ps.ExtraCutt= tp.ExtraCutt,
					ps.total_paid= tp.total_paid
				WHERE 
				 	ps.month = " . $month . " AND ps.year = " . $year;
		// display_notification($sql);
		db_query($sql, _('Sorry to over write data..!'));


		$sql = "UPDATE 0_presalary_sheet 
				SET total_paid= total_paid_round(total_paid)
				WHERE 
				 	month = " . $month . " AND year = " . $year;
		//display_notification($sql);
		db_query($sql, _('Sorry to over write data..!'));
	}
}
