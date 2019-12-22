<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   employee_db.inc                                      |
\=======================================================*/

function db_has_employee() {
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."employee");
}

//--------------------------------------------------------------------------

function write_employee($id=false, $first_name, $last_name, $name_bd,$mothername,$fathername,$spousename, $gender, $adrs, $padrs, $mobile, $mail,$emp_identity_type,$emp_identity_number, $birth, $notes, $hire_date, $department,$section, $salary=false,$emp_activity_type, $release, $active = false, $designation, $designation_bd, $status, $blood, $UnitID, $card_no,$card_no_bd,$emp_proximity_id, $shift_id, $emp_atten_bonus, $emp_salary)
{
	// display_notification($department.' '.$salary);
	if(!$id) {
		$sql = "INSERT INTO ".TB_PREF."employee (emp_first_name, emp_last_name,emp_name_bd,mothername,fathername,spousename,gender,emp_address, emp_present_address, emp_mobile,emp_email,emp_identity_type,emp_identity_number, emp_birthdate, emp_notes, emp_hiredate, department_id,section_id, salary_scale_id,emp_activity_type, emp_releasedate, inactive, Designation, Designation_bd, emp_status, emp_blood, UnitID, emp_card_no, emp_card_no_bd,emp_proximity_id, shift_id, emp_atten_bonus, emp_salary) VALUES ("
		.db_escape($first_name).","
		.db_escape($last_name).","
		.db_escape($name_bd).","
		.db_escape($mothername).","
		.db_escape($fathername).","
		.db_escape($spousename).","
		.db_escape($gender).","
		.db_escape($adrs).","
		.db_escape($padrs).","
		.db_escape($mobile).","
		.db_escape($mail).","
		.db_escape($emp_identity_type).","
		.db_escape($emp_identity_number).",'"
		.date2sql($birth)."',"
		.db_escape($notes).",'"
		.date2sql($hire_date)."',"
		.db_escape($department).","
		.db_escape($section).","
		.db_escape($salary).","
		.db_escape($emp_activity_type).",'"
		.date2sql($release)."',"
		.db_escape($active).","
		.db_escape($designation).","
		.db_escape($designation_bd).","
		.db_escape($status).","
		.db_escape($blood).","
		.db_escape($UnitID).","
		.db_escape($card_no).","
		.db_escape($card_no_bd).","
		.db_escape($emp_proximity_id).","
		.db_escape($shift_id).","
		.db_escape($emp_atten_bonus).","
		.db_escape($emp_salary)
		.")";

	}
	else {
		$sql = "UPDATE ".TB_PREF."employee SET 
		emp_first_name = ".db_escape($first_name).", 
		emp_last_name = ".db_escape($last_name).", 
		emp_name_bd = ".db_escape($name_bd).", 
		mothername = ".db_escape($mothername).", 
		fathername = ".db_escape($fathername).", 
		spousename = ".db_escape($spousename).", 
		gender = ".db_escape($gender).", 
		emp_address = ".db_escape($adrs).", 
		emp_present_address = ".db_escape($padrs).", 
		emp_mobile = ".db_escape($mobile).", 
		emp_email = ".db_escape($mail).", 
		emp_identity_type = ".db_escape($emp_identity_type).", 
		emp_identity_number = ".db_escape($emp_identity_number).", 
		emp_birthdate = '".date2sql($birth)."', 
		emp_notes = ".db_escape($notes).", 
		emp_hiredate = '".date2sql($hire_date)."', 
		department_id = ".db_escape($department).", 
		section_id = ".db_escape($section).", 
		salary_scale_id = ".db_escape($salary).", 
		emp_activity_type = ".db_escape($emp_activity_type).",
		emp_releasedate = '".date2sql($release)."', 
		inactive = ".db_escape($active).", 
		Designation = ".db_escape($designation).", 
		Designation_bd = ".db_escape($designation_bd).", 
		emp_status = ".db_escape($status)." ,
		emp_blood = ".db_escape($blood)." , 
		UnitID = ".db_escape($UnitID)." , 
		emp_card_no = ".db_escape($card_no)." ,
		emp_card_no_bd = ".db_escape($card_no_bd)." ,
		emp_proximity_id = ".db_escape($emp_proximity_id)." ,
		shift_id = ".db_escape($shift_id).",
		emp_atten_bonus = ".db_escape($emp_atten_bonus).",
		emp_salary = ".db_escape($emp_salary)."
		WHERE emp_id = ".db_escape($id);
	}
	// display_notification($sql);
	db_query($sql, _('Could not write employee data'));
	if ($id == false) {
		$emp_id = get_employees_emp_id($card_no);
		$id = $emp_id["emp_id"];
		// display_notification($id);
	}
	if (db_escape($release) > '0000-00-00') {
		$pre_salary = "DELETE FROM 0_daily_attendance WHERE emp_id=".$id." and att_date >= '".date2sql($release)."'";
		 db_query($pre_salary, _('Could not write employee data'));
		 // $sql ="UPDATE 0_employee SET inactive = 1 WHERE DATEDIFF('".date('Y-m-d')."', emp_releasedate) > 60 AND emp_id=".$id;
		// db_query($sql, _('Sorry to over write data..!'));
	}
	
	// write_template_salary($id, $emp_salary);
	// display_notification($sql);
	return ['employee_id'=>$id,'total_salary'=>$emp_salary];
}


function get_employees_emp_id($card) {
	$sql = "SELECT emp_id FROM ".TB_PREF."employee";
	$sql .= " WHERE emp_card_no =".db_escape($card);
	$result = db_query($sql);
	return db_fetch($result);
}

function get_employees_emp_id_from_proxi($proximity) {
	$sql = "SELECT emp_id FROM ".TB_PREF."employee";
	$sql .= " WHERE emp_proximity_id =".db_escape($proximity);
	$result = db_query($sql);
	return db_fetch($result);
}
function get_employees_card_proximity_from_emp_id($id) {
	$sql = "SELECT emp_id FROM ".TB_PREF."employee";
	$sql .= " WHERE emp_id =".db_escape($id);
	$result = db_query($sql);
	return db_fetch($result);
}

function get_employees_count_emp_id($card='',$proximity='') {
	if ($card == '') {
		return false;
	}
	$sql = "SELECT COUNT(emp_card_no) as card , COUNT(emp_proximity_id) as  proximity, emp_id FROM ".TB_PREF."employee";
	$sql .= " WHERE emp_proximity_id =".db_escape($proximity);
	// display_notification($sql);
	$result = db_query($sql);
	return db_fetch($result);
}

//--------------------------------------------------------------------------

function get_employees($id = false, $all = false, $department = false) {
	
	$sql = "SELECT *, CONCAT(IFNULL(emp_first_name,' '), ' ', IFNULL(emp_last_name,' ')) AS name FROM ".TB_PREF."employee";
	$where = array();
	
	//if(!$all)
	//	$where[] = "!inactive";
	if($id)
		$where[] = "emp_id=".db_escape($id);
	if($department)
		$where[] = "department_id = ".db_escape($department);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';

	$result = db_query($sql, _('Could not get employee data.'));
	
	if($id)
		return db_fetch($result);
	else
		return $sql;
}

//--------------------------------------------------------------------------

function srch_employees($id = false, $all = false, $department = false,$sr_name=false, $sr_proximity=false, $sr_card=false) {
	
	$sql = "SELECT *, CONCAT(IFNULL(emp_first_name,' '), ' ', IFNULL(emp_last_name,' ')) AS name FROM ".TB_PREF."employee";
	$where = array();
	
	if(!$all)
		$where[] = "!inactive";
	if ($all)
		$where[] = "inactive";
	if($id)
		$where[] = "emp_id=".db_escape($id);
	if($department)
		$where[] = "department_id = ".db_escape($department);
	if($sr_name)
		$where[] = "(emp_first_name like '%".($sr_name). "%' or emp_last_name like '%".($sr_name)."%') ";
	if($sr_proximity)
		$where[] = "emp_proximity_id = ".db_escape($sr_proximity);

	if($sr_card)
		$where[] = "emp_card_no = ".db_escape($sr_card);

	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);
	else
		$sql .= ' WHERE 1';

	$result = db_query($sql, _('Could not get employee data.'));
	
	if($id)
		return db_fetch($result);
	else
		return $sql;
}
//--------------------------------------------------------------------------

function delete_employee($id) {
	$sql = "DELETE FROM ".TB_PREF."employee WHERE emp_id = ".db_escape($id);
	db_query($sql, _('Could not delete employee data.'));
	return true;
}

//--------------------------------------------------------------------------

function emp_img_name($emp_id) {

	$emp_id = strtr($emp_id, "><\\/:|*?", '________');
	return clean_file_name($emp_id);
}

//--------------------------------------------------------------------------

function check_employee_hired($id, $date) {

    $sql = "SELECT emp_hiredate FROM ".TB_PREF."employee WHERE emp_id = ".db_escape($id);
    $result = db_query($sql, _('could not get employee data'));
    $check = db_fetch($result);
    
    if(sql_date_comp(date2sql($date), $check[0]) < 0)
        return false;
    else
        return true;
}

//--------------------------------------------------------------------------

function employee_has_salary_scale($id) {
	return check_empty_result("SELECT salary_scale_id FROM ".TB_PREF."employee WHERE salary_scale_id != 0 AND emp_id = ".db_escape($id));
}

//--------------------------------------------------------------------------

function emp_salaryscale_has_structure($id) {
	return check_empty_result("SELECT e.salary_scale_id, s.* FROM ".TB_PREF."employee e, ".TB_PREF."salary_structure s WHERE e.salary_scale_id = s.salary_scale_id AND e.emp_id = ".db_escape($id));
}
