<?php
/*=======================================================|
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   employee.php                                         |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';
$proot  = __DIR__;

include_once($path_to_root . '/includes/db_pager.inc');
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

include_once($path_to_root . '/includes/ui.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_db.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_ui.inc');
create_access_ontime($proot);
//--------------------------------------------------------------------------

foreach(db_query(get_employees(false, true)) as $emp_row) {
	
	if(isset($_POST[$emp_row['emp_id']])) {
		
		$_SESSION['EmpId'] = $emp_row['emp_id'];
		// $_SESSION['emp_card_no'] = isset($_POST['EmpCardNo']) ? $_POST['EmpCardNo'] : $emp_row['emp_card_no'];
		$_POST['_tabs_sel'] = 'add';
		$Ajax -> activate('_page_body');
	}
}

$cur_id = isset($_SESSION['EmpId']) ? $_SESSION['EmpId'] : '';
$_SESSION['emp_card_no'] = isset($_POST['EmpCardNo']) ? $_POST['EmpCardNo'] : $emp_row['emp_card_no'];
//--------------------------------------------------------------------------

if (isset($_POST['addupdate'])) {
	
	if(!can_process($cur_id))
		return;
	$Add_id=write_employee(
		$cur_id,
		$_POST['EmpFirstName'],
		$_POST['EmpLastName'],
		$_POST['EmpNameBd'],
		$_POST['mothername'],
		$_POST['fathername'],
		$_POST['spousename'],
		$_POST['EmpGender'],
		$_POST['EmpAddress'],
		$_POST['EmpPreAddress'],
		$_POST['EmpMobile'],
		$_POST['EmpEmail'],
		$_POST['EmpIdentityType'],
		$_POST['EmpIdentityNumber'],
		$_POST['EmpBirthDate'],
		$_POST['EmpNotes'],
		$_POST['EmpHireDate'],
		$_POST['DepartmentId'],
		$_POST['SectionId'],
		$_POST['EmpSalary'],
		$_POST['EmpActivityType'],
		$_POST['EmpReleaseDate'],
		$_POST['EmpInactive'],
		$_POST['EmpDesignation'],
		$_POST['EmpDesignation_bd'],
		$_POST['EmpStatus'],
		$_POST['EmpBlood'],
		$_POST['EmpUnit'],
		$_POST['EmpCardNo'],
		$_POST['EmpCardNoBd'],
		$_POST['EmpProximityId'],
		$_POST['shift_id'],
		$_POST['EmpAttendanceBonus'],
		$_POST['EmpTotalSalary']
	);

	write_template_salary($Add_id["employee_id"],$Add_id["total_salary"]);
    //display_error($sql);
	if (check_value('del_image')) {
		$avatar_path = company_path();
		$filename = $avatar_path.'/FrontHrm/images/'.emp_img_name($_SESSION['emp_card_no']).".jpg";
		
		if (file_exists($filename)){
			unlink($filename);
			display_notification("Image is Deleted.");
		}
	}
	if($cur_id) {
		$_SESSION['EmpId'] = $cur_id;
		display_notification(_('Employee details has been updated.'));
	}
	else {
		$_SESSION['EmpId'] = $Add_id["employee_id"];
		$cur_id = $_SESSION['EmpId'];
		display_notification(_('A new employee has been added.'));
	}
	
	$Ajax->activate('_page_body');
}
elseif(isset($_POST['delete'])) {

	if(!can_delete($cur_id))
		return;
	if(delete_employee($cur_id)){
		$avatar_path = company_path();
		$filename = $avatar_path.'/FrontHrm/images/'.emp_img_name($_SESSION['emp_card_no']).".jpg";
		unlink($filename);
		display_notification(_('Employee details has been deleted.'));
		$Ajax -> activate('_page_body');
		$cur_id = '';
	}
}

//--------------------------------------------------------------------------

$upload_file = "";
$avatar_path = company_path()."/FrontHrm/images/";
if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
	
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes';
	$filename = $avatar_path;
    
    if(!file_exists(company_path().'/FrontHrm')) {
		mkdir(company_path().'/FrontHrm');
		copy(company_path().'/index.php', company_path().'/FrontHrm/index.php');
    }
	if(!file_exists($filename)) {
		mkdir($filename);
		copy(company_path().'/index.php', $filename.'index.php');
	}
	
	$filename .= $_SESSION['emp_card_no'].'.jpg';
	
	if($_FILES['pic']['error'] == UPLOAD_ERR_INI_SIZE) {

		display_error(_('The file size is over the maximum allowed.'));
		$upload_file = 'No';
	}
	elseif($_FILES['pic']['error'] > 0) {

		display_error(_('Error uploading file.'));
		$upload_file = 'No';
	}
	if((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
		$imagetype = $type;
	else
		$imagetype = false;

	if($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG) {

		display_warning( _('Only graphics files can be uploaded'));
		$upload_file = 'No';
	}
	elseif(!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF'))) {

		display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
		$upload_file ='No';
	}
	elseif( $_FILES['pic']['size'] > ($SysPrefs->max_image_size * 1024)) {

		display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $SysPrefs->max_image_size);
		$upload_file ='No';
	} 
	elseif( $_FILES['pic']['type'] == "text/plain" ) {

		display_warning( _('Only graphics files can be uploaded'));
        $upload_file ='No';
	}
	elseif(file_exists($filename)) {

		$result = unlink($filename);
		if(!$result) {
			display_error(_('The existing image could not be removed'));
			$upload_file ='No';
		}
	}
	if($upload_file == 'Yes')
		$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
	// display_warning($result);
	$Ajax->activate('_page_body');
}

//--------------------------------------------------------------------------

function can_process($id) {
	if (isset($_POST['EmpCardNo']) && $_POST['EmpCardNo'] == '') {
		display_error( _('Invalid Card No.'));
		set_focus('EmpCardNo');
		return false;
	}

	if ($id=='') {
		$emp_id_f_c = get_employees_emp_id($_POST['EmpCardNo']);
		$id_f_c = $emp_id_f_c["emp_id"];	
		if ($id_f_c > 0) {
			display_error( _('Card No. Already Exists.'));
			return false;
		}
		if (isset($_POST['EmpProximityId']) && $_POST['EmpProximityId'] != '') {
			$emp_id_f_p = get_employees_emp_id_from_proxi($_POST['EmpProximityId']);
			$id_f_p = $emp_id_f_p["emp_id"];
			if ($id_f_p > 0) {
				display_error( _('Proximity No. Already Exists.'));
				return false;
			}
		}
	}

	if (is_numeric($id)) {
		$emp_id_f_c = get_employees_emp_id($_POST['EmpCardNo']);
		$id_f_c = $emp_id_f_c["emp_id"];	
		if ($id_f_c != $id) {
			display_error( _('Card No. cannot updated.'));
			return false;
		}
		if (isset($_POST['EmpProximityId']) && $_POST['EmpProximityId'] != '') {
			$emp_info = get_employees_count_emp_id($_POST['EmpCardNo'],$_POST['EmpProximityId']);
			$emp_card_db = $emp_info["card"];
			$emp_proximity_db = $emp_info["proximity"];
			$emp_id_db = $emp_info["emp_id"];
			if (($emp_proximity_db == 1 && $emp_id_db != $id) || $emp_proximity_db > 1) {
				display_error( _('Proximity No. Already Exists.'));
				return false;
			}
		}
		// return false;
	}
	
	if(strlen($_POST['EmpFirstName']) == 0 || $_POST['EmpFirstName'] == '') {
		display_error(_("Invalid Employee Name."));
		set_focus('EmpFirstName');
		return false;
	}	
	if (isset($_POST['shift_id']) && $_POST['shift_id'] == '') {
		display_error( _('Invalid Shift.'));
		set_focus('shift_id');
		return false;
	}
	if (isset($_POST['EmpHireDate']) && ($_POST['EmpHireDate'] == '0000-00-00' ||$_POST['EmpHireDate'] == '')) {
		display_error( _('Invalid hire date.'));
		set_focus('EmpHireDate');
		return false;
	}
	if (isset($_POST['DepartmentId']) && $_POST['DepartmentId'] == '') {
		display_error( _('Invalid Department.'));
		set_focus('DepartmentId');
		return false;
	}
	if (isset($_POST['SectionId']) && $_POST['SectionId'] == '') {
		display_error( _('Invalid Section.'));
		set_focus('SectionId');
		return false;
	}
	
	if (get_post('EmpInactive') == 1) {
	    if (!is_date($_POST['EmpReleaseDate'])) {
		display_error( _('Invalid EmpInactive.'));
		set_focus('EmpInactive');
		return false;
	    }
	}
	if (isset($_POST['EmpUnit']) && !$_POST['EmpUnit'] > 0) {
		display_error( _('Invalid Unit.'));
		set_focus('EmpUnit');
		return false;
	}
	if (isset($_POST['EmpDesignation']) && $_POST['EmpDesignation'] == '') {
		display_error( _('Invalid Designation.'));
		set_focus('EmpDesignation');
		return false;
	}
	if (isset($_POST['EmpAttendanceBonus']) &&  $_POST['EmpAttendanceBonus'] == '') {
		display_error( _('Invalid Attendance Bonus.'));
		set_focus('EmpAttendanceBonus');
		return false;
	}
	if (isset($_POST['EmpTotalSalary']) && (!$_POST['EmpTotalSalary'] > 0 || $_POST['EmpTotalSalary'] == '')) {
		display_error( _('Invalid Salary.'));
		set_focus('EmpTotalSalary');
		return false;
	}
	return true;
}

//--------------------------------------------------------------------------

function can_delete($cur_id) {
	$employee = get_employees($cur_id, true);
	if(isset($employee['emp_hiredate']) && isset($_POST['EmpHireDate']) && ($_POST['EmpHireDate'] == '0000-00-00' ||$_POST['EmpHireDate'] == '')) {
		return true;
	}
	else{
		display_error('Employed person cannot be deleted.');
		return false;
	}
}

//--------------------------------------------------------------------------

function id_link($row) {
	return button($row['emp_id'], $row['emp_id']);
}
function get_name($row) {
	return "<b>".button($row['emp_id'], $row['emp_first_name'].' '.$row['emp_last_name'])."</b>";
}
function gender_name($row) {
	if($row['gender'] == 0)
		return  'Female';
	elseif($row['gender'] == 1)
	    return 'Male';
	else
	    return 'Other';
}
function emp_hired($row) {
	return ($row['emp_hiredate'] == '0000-00-00') ? _('Not hired') : "<center>".sql2date($row['emp_hiredate'])."</center>";
}
function emp_department($row) {
	
	if($row['emp_hiredate'] == '0000-00-00' || $row['department_id'] == 0)
		return _('Not selected');
	else
		return get_departments($row['department_id'])['dept_name'];

}

function emp_designation($row) {
	return ($row['Designation'])."</center>";
}

function emp_card($row) {
	return ($row['emp_card_no'])."</center>";
}

function emp_proximity($row) {
	return ($row['emp_proximity_id'])."</center>";
}

function employees_table() {
	
	$_SESSION['EmpId'] = '';
	if(db_has_employee()) {
		
		//$sql = get_employees(false, check_value('show_inactive'), get_post('DeptId'));
		$sql = srch_employees(false, check_value('show_inactive'), get_post('DeptId'), get_post('sr_name'), get_post('sr_proximity'), get_post('sr_card'));

		start_table(TABLESTYLE_NOBORDER);
		start_row();
		department_list_cells(_('Department').':', 'DeptId', null, _('All departments'), true);
		check_cells(_('inactive').':', 'show_inactive', null, true);
		end_row();
		start_row();
		text_row_ex(_("Name :"), 'sr_name', 50, 60);
		end_row();
		start_row();
		text_row_ex(_("Proximity ID:"), 'sr_proximity', 50, 60);
		end_row();
		start_row();
		text_row_ex(_("Card No:"), 'sr_card', 50, 60);
		end_row();
		end_table(1);
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		btn_submit_search('submit','search','','','Search');
		// submit_center('search', "Search");//,true,false,'process',ICON_SUBMIT);
		end_row();
		end_table(1);
		
        $cols = array(
          _('ID'),
		  'first_name' => 'skip',
          _('Name') => array('fun'=>'get_name'),
		  _('Card NO') => array('fun'=>'emp_card'),
		  _('Proximity ID') => array('fun'=>'emp_proximity'),
		  _('Gender') => array('fun'=>'gender_name'),
		  // _('Address') => array('fun'=>'emp_address'),
		  // 'address' => 'skip',
		  // _('Mobile') => array('fun'=>'emp_mobile'),
		  // _('Email') => array('fun'=>'emp_email'),
		  // _('Birth') => array('fun'=>'emp_birthdate'),
		  // 'notes' => 'skip',
		  _('Hired Date') => array('fun'=>'emp_hired'),
		  _('Department') => array('fun'=>'emp_department'),
		  _('Designation') => array('fun'=>'emp_designation')
        );

        $table =& new_db_pager('student_tbl', $sql, $cols, null,  null, 50);
        $table->width = "80%";
	
	    // display_note(_('Press name to edit employee details.'));
        display_db_pager($table);
        //display_notification($sql);
 
	}
	else
		display_note(_('No employee defined.'), 1);
}

//--------------------------------------------------------------------------

function employee_settings($cur_id) {
	global $path_to_root, $avatar_path;
	
	if($cur_id) {
		$employee = get_employees($cur_id, true);
		$_POST['EmpFirstName'] = $employee['emp_first_name'];
		$_POST['EmpLastName'] = $employee['emp_last_name'];
		$_POST['EmpNameBd'] = $employee['emp_name_bd'];
		$_POST['mothername'] = $employee['mothername'];
		$_POST['fathername'] = $employee['fathername'];
		$_POST['spousename'] = $employee['spousename'];
		$_POST['EmpGender'] = $employee['gender'];
		$_POST['EmpAddress'] = $employee['emp_address'];
		$_POST['EmpPreAddress'] = $employee['emp_present_address'];
		$_POST['EmpMobile'] = $employee['emp_mobile'];
		$_POST['EmpEmail'] = $employee['emp_email'];
		$_POST['EmpIdentityType'] = $employee['emp_identity_type'];
		$_POST['EmpIdentityNumber'] = $employee['emp_identity_number'];
		$_POST['EmpBirthDate'] = sql2date($employee['emp_birthdate']);
		$_POST['EmpNotes'] = $employee['emp_notes'];
		$_POST['EmpHireDate'] = sql2date($employee['emp_hiredate']);
		$_POST['DepartmentId'] = $employee['department_id'];
		$_POST['SectionId'] = $employee['section_id'];
		$_POST['EmpSalary'] = $employee['salary_scale_id'];
		$_POST['EmpActivityType'] = $employee['emp_activity_type'];
		$_POST['EmpReleaseDate'] = sql2date($employee['emp_releasedate']);
		$_POST['EmpInactive'] = $employee['inactive'];
		$_POST['EmpDesignation'] = $employee['Designation'];
		$_POST['EmpDesignation_bd'] = $employee['Designation_bd'];
		$_POST['EmpStatus'] = $employee['emp_status'];
		$_POST['EmpBlood'] = $employee['emp_blood'];
		$_POST['EmpCardNo'] = $employee['emp_card_no'];
		$_POST['EmpCardNoBd'] = $employee['emp_card_no_bd'];
		$_POST['EmpProximityId'] = $employee['emp_proximity_id'];
		$_POST['EmpUnit'] = $employee['UnitID'];
		$_POST['shift_id'] = $employee['shift_id'];
		$_POST['EmpAttendanceBonus'] = $employee['emp_atten_bonus'];
		$_POST['EmpTotalSalary'] = $employee['emp_salary'];
	}
	else{
		$_POST['EmpFirstName'] = '';
		$_POST['EmpLastName'] = '';
		$_POST['EmpNameBd'] = '';
		$_POST['mothername'] = '';
		$_POST['fathername'] = '';
		$_POST['spousename'] = '';
		$_POST['EmpGender'] = '';
		$_POST['EmpAddress'] = '';
		$_POST['EmpPreAddress'] = '';
		$_POST['EmpMobile'] = '';
		$_POST['EmpEmail'] = '';
		$_POST['EmpIdentityType'] = '';
		$_POST['EmpIdentityNumber'] = '';
		$_POST['EmpBirthDate'] = '';
		$_POST['EmpNotes'] = '';
		$_POST['EmpHireDate'] = '';
		$_POST['DepartmentId'] = '';
		$_POST['SectionId'] = '';
		$_POST['EmpSalary'] = '';
		$_POST['EmpActivityType'] = '';
		$_POST['EmpReleaseDate'] = '';
		$_POST['EmpInactive'] = '';
		$_POST['EmpDesignation'] = '';
		$_POST['EmpDesignation_bd'] = '';
		$_POST['EmpStatus'] = '';
		$_POST['EmpBlood'] = '';
		$_POST['EmpCardNo'] = '';
		$_POST['EmpCardNoBd'] = '';
		$_POST['EmpProximityId'] = '';
		$_POST['EmpUnit'] = '';
		$_POST['shift_id'] = '';
		$_POST['EmpAttendanceBonus'] = '';
		$_POST['EmpTotalSalary'] = '';
	}
	start_outer_table(TABLESTYLE2);

	table_section(1);
	hidden('emp_id');

	file_row(_('Image File').':', 'pic', 'pic');
	$emp_img_link = '';
	$check_remove_image = false;
	if ($employee['emp_card_no'] && file_exists($avatar_path.emp_img_name($employee['emp_card_no']).'.jpg')) {
		$emp_img_link .= "<img id='emp_img' alt = '[".$employee['emp_card_no'].".jpg".
			"]' src='".$avatar_path.emp_img_name($employee['emp_card_no']).
			".jpg?nocache=".rand()."'"." height='100'>";
		$check_remove_image = true;
			} 
	else 
		$emp_img_link .= "<img id='emp_img' alt = 'noimage.jpg' src='".$path_to_root."/modules/FrontHrm/images/avatar/no_image.svg' height='100'>";

	label_row("&nbsp;", $emp_img_link);
	if ($check_remove_image)
		check_row(_('Delete Image').':', 'del_image');
	
	table_section_title(_('Personal Information'));

	if($cur_id)
		label_row(_('Employee Id').':', $cur_id);
	text_row(_('Card No').':', 'EmpCardNo', get_post('EmpCardNo'), 37, 50);
	text_row(_('Card No (Bangla)').':', 'EmpCardNoBd', get_post('EmpCardNoBd'), 37, 50);
	text_row(_('Proximity ID').':', 'EmpProximityId', get_post('EmpProximityId'), 37, 50);
	text_row(_('First Name').':', 'EmpFirstName', get_post('EmpFirstName'), 37, 50);
	text_row(_('Last Name').':', 'EmpLastName', get_post('EmpLastName'), 37, 50);
	text_row(_('Full Name (Bangla)').':', 'EmpNameBd', get_post('EmpNameBd'), 37, 50);
	text_row(_('Mother Name').':', 'mothername', get_post('mothername'), 37, 50);
	text_row(_('Father Name').':', 'fathername', get_post('fathername'), 37, 50);
	text_row(_('Spouse Name').':', 'spousename', get_post('spousename'), 37, 50);
	gender_radio_row(_('Gender').':', 'EmpGender', get_post('EmpGender'));
	textarea_row(_('Permanent Address').':', 'EmpAddress', get_post('EmpAddress'), 35, 5);
	textarea_row(_('Present Address').':', 'EmpPreAddress', get_post('EmpPreAddress'), 35, 5);
	text_row(_('Mobile').':', 'EmpMobile', get_post('EmpMobile'), 37, 30);
	email_row(_('E-Mail').':', 'EmpEmail', get_post('EmpEmail'), 37, 100);
	number_radio_row(_('ID Type:').':', 'EmpIdentityType', get_post('EmpIdentityType'));
	text_row(_('ID Number:').':', 'EmpIdentityNumber', get_post('EmpIdentityNumber'), 37, 50);
	date_row(_('Birth Date').':', 'EmpBirthDate', null, null, 0, 0, -13);
	ShiftStatus(_('Shifts').':', 'shift_id', $id, $cur_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);
	
	
	table_section(2);
	
	table_section_title(_('Job Information'));
	
	textarea_row(_('Notes').':', 'EmpNotes', null, 35, 5);
	date_row(_('Joining Date').':', 'EmpHireDate', null, null, 0, 0, 1001);
	//date_row(_('Release Date').':', 'EmpHireDate', null, null, 0, 0, 1001);
	
	if($cur_id) {
		if($employee['emp_hiredate'] != '0000-00-00')
			departments(_('Department').':', 'DepartmentId', $id, $cur_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);
			// department_list_row(_('Department').':', 'DepartmentId', null, _('Select department'));
		else {
			label_row(_('Department').':', _('Set hire date first'));
			hidden('DepartmentId');
		}
	}
	else
		departments(_('Department').':', 'DepartmentId', $id, $cur_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);
		// department_list_row(_('Department').':', 'DepartmentId', null, _('Select department'));
		
	salaryscale_list_row(_('Salary (Grade)').':', 'EmpSalary', null, _('Select salary scale'));
	unit_list_row(_('Unit').':', 'EmpUnit', null, _('Select unit'));
	if($cur_id) {
		number_activity_row(_('Status:').':', 'EmpActivityType', get_post('EmpActivityType'));
		
	}
	if($cur_id) {
		
		date_row(_('Left/Release Date').':', 'EmpReleaseDate', null, null, 0, 0, 1001);
		check_row(_('Inactive').':', 'EmpInactive');
	}
	else{
		hidden('EmpInactive');
		hidden('EmpReleaseDate');
	}
	text_row(_('Designation').':', 'EmpDesignation', get_post('EmpDesignation'), 0, 1001);
	text_row(_('Designation(Bangla)').':', 'EmpDesignation_bd', get_post('EmpDesignation_bd'), 0, 1001);
	sections(_('Section').':', 'SectionId', $id, $cur_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);

	EmployeeStatuslist(_('Status').':', 'EmpStatus', null, get_post('EmpStatus'), _('Select Status'));
	//EmployeeStatuslist(_('Status'));
	EmployeeBloodlist(_('Blood Group').':', 'EmpBlood', null, get_post('EmpBlood'), _('Select Blood Group'));
	text_row(_('Attendance Bonus').':', 'EmpAttendanceBonus', get_post('EmpAttendanceBonus'), 0, 1001);
	text_row(_('Total Salary').':', 'EmpTotalSalary', get_post('EmpTotalSalary'), 37, 50);
	if ($cur_id) {
		table_section_title(_('Salary Information'));
		$results = emp_ext_pmt_str($cur_id);
		$emp_str = array();
		//setting array of pmt_str_id wise ammount
		while ($result = db_fetch($results)) {
			$emp_str[$result["emp_pmt_str_id"]] = $result["emp_pmt_tmp_amt"];
		}
		$str = act_pmt_str();
		$i = 0;
		$status['total']=0;
		$status['deduct'] =0;
		while ($r = db_fetch($str)){
			$value = 0;
			$tds[$i] = '0';
			if ($emp_str[$r["emp_pmt_str_id"]] != null) {
				if ($r["emp_pmt_type"]=='Add') {
					$status['total'] += $emp_str[$r["emp_pmt_str_id"]];
				}else{
					$status['deduct'] += $emp_str[$r["emp_pmt_str_id"]];
				}
				$value = $emp_str[$r["emp_pmt_str_id"]];
				$tds[$i] = $emp_str[$r["emp_pmt_str_id"]];
			}
			$tbh[$i] = $r["emp_pmt_str_name"];
			label_row($r["emp_pmt_str_name"],$value);
			// text_row_ex(, $r["emp_pmt_str_id"], 50, 60,$title=null,$value);	
			$i++;	
		}
		label_row('Total',$status['total']);
		label_row('Deduct',$status['deduct']);
		label_row('Paid',$status['total']-$status['deduct']);

	}

	end_outer_table(1);
	
	div_start('controls');
	
	if ($cur_id) {
		
		submit_center_first('addupdate', _('Update Employee'), _('Update employee details'), 'default');
		submit_return('select', get_post('emp_id'), _('Select this employee and return to document entry.'));
		submit_center_last('delete', _('Delete Employee'), _('Delete employee data if have been never used'), true);
	}
	else
		submit_center('addupdate', _('Add New Employee Details'), true, '', 'default');
	
	div_end();
}

page(_($help_context = 'Employees'), false, false, '', $js);

start_form(true);

tabbed_content_start(
	'tabs',
	array(
		'list' => array(_('Employees &List'), 999),
		'add' => array(_('&Add/Edit Employee'), 999)
	)
);

if(get_post('_tabs_sel') == 'list' or get_post('search'))
	employees_table();
elseif(get_post('_tabs_sel') == 'add')
	employee_settings($cur_id);
//if(get_post('search'))
//	employees_table();
	//srch_employees_table();
	
br();

tabbed_content_end();

end_form();
end_page();


function srch_employees_table() 
{
$sql = srch_employees(false, check_value('show_inactive'), get_post('DeptId'), get_post('sr_name'), get_post('sr_proximity'), get_post('sr_card'));

 $cols = array(
          _('ID'),
		  'first_name' => 'skip',
          _('Name') => array('fun'=>'get_name'),
		  _('Gender') => array('fun'=>'gender_name'),
		  _('Address') => array('fun'=>'emp_address'),
		  // 'address' => 'skip',
		  _('Mobile') => array('fun'=>'emp_mobile'),
		  _('Email') => array('fun'=>'emp_email'),
		  _('Birth') => array('fun'=>'emp_birthdate'),
		  // 'notes' => 'skip',
		  _('Hired Date') => array('fun'=>'emp_hired'),
		  _('Department') => array('fun'=>'emp_department'),
		  _('Designation') => array('fun'=>'Designation')
        );

        $table =& new_db_pager('student_tbl', $sql, $cols);
        $table->width = "80%";
	
	    // display_note(_('Press name to edit employee details.'));
        display_db_pager($table);
        display_error($sql);
}