<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-07-2017                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

function employee_list($name, $id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false, $dept = false)
{
	$sql = "SELECT emp_id, CONCAT(emp_first_name, ' ', emp_last_name) AS name, inactive FROM ".TB_PREF."employee ";

	return combo_input($name, $id, $sql, 'emp_id', 'name',
	array(       
		'spec_option' => $spec_option,
		'select_submit' => $submit_on_change,
		'where' => $dept == false ? 1 : array("department_id = $dept"),
		'async' => false,
		'show_inactive' => $show_inactive
	));
}

//--------------------------------------------------------------------------

function employee_list_cells($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $dept = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo employee_list($name, $id, $all_option, $submit_on_change, $show_inactive, $dept);
	echo "</td>\n";
}

//--------------------------------------------------------------------------

function employee_list_row($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $dept = false)
{
	echo "<tr><td class='label'>$label</td>";
	employee_list_cells(null, $name, $id, $all_option, 
	$submit_on_change, $show_inactive, $dept);
	echo "</tr>";
}

//--------------------------------------------------------------------------

function gender_radio_row($label, $name, $gender=null) {
    echo "<tr><td class='label'>$label</td><td>";
	
    if($gender==0){
        echo "<input type='radio' name=$name value='1'>Male&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='0' checked>Female&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='2'>Other";
    }
    elseif($gender==1) {
        echo "<input type='radio' name=$name value='1' checked>Male&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='0'>Female&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='2'>Other";
    }
    else {
        echo "<input type='radio' name=$name value='1'>Male&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='0'>Female&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='2' checked>Other";
    }
    echo "</td></tr>";
}

function number_radio_row($label, $name, $emp_identity_type=null) {
    echo "<tr><td class='label'>$label</td><td>";
	
    if($emp_identity_type=='birth_id'){
        echo "<input type='radio' name=$name value='nid'>NID Number&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='birth_id' checked>Birth Certificate&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='passport_id'>Passport&nbsp;&nbsp;&nbsp";
    }
    elseif($emp_identity_type=='nid') {
        echo "<input type='radio' name=$name value='nid' checked>NID Number&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='birth_id'>Birth Certificate&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='passport_id'>Passport&nbsp;&nbsp;&nbsp";
    }
    else {
        echo "<input type='radio' name=$name value='nid'>NID Number&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='birth_id'>Birth Certificate&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='passport_id' checked>Passport&nbsp;&nbsp;&nbsp";
    }
    echo "</td></tr>";
}


//------------------------------------------------------------------------
function EmployeeStatuslist($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	
	echo "<tr><td class='label'>$label</td>";
	echo "<td>";
	$output  = "<select name=".$name.">";

	if ($selected_id == null) 
		 {$output .= "<option value=\"".$all_option."\" selected=\"selected\">".$all_option."</option>";}	
	else {$output .= "<option value=\"".$all_option."\">".$all_option."</option>";}	


	if ($selected_id=="N")
		{$output .= "<option value=\"N\" selected=\"selected\">New</option>";}
	else {$output .= "<option value=\"N\">New</option>";}

	if ($selected_id=="R")
		{$output .= "<option value=\"R\" selected=\"selected\">Run</option>";}
	else {$output .= "<option value=\"R\">Run</option>";}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}

function EmployeeBloodlist($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	
	echo "<tr><td class='label'>$label</td>";
	echo "<td>";
	$output  = "<select name=".$name.">";

	if ($selected_id == null) 
		 {$output .= "<option value=\"".$all_option."\" selected=\"selected\">".$all_option."</option>";}	
	else {$output .= "<option value=\"".$all_option."\">".$all_option."</option>";}	


	if ($selected_id=="A+")
		{$output .= "<option value=\"A+\" selected=\"selected\">A+</option>";}
	else {$output .= "<option value=\"A+\">A+</option>";}

	if ($selected_id=="B+")
		{$output .= "<option value=\"B+\" selected=\"selected\">B+</option>";}
	else {$output .= "<option value=\"B+\">B+</option>";}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}

//--------------------------------Employee shift select box--------------------

function ShiftStatus($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	if($selected_id == null){
		$sql = 'Select * from '.TB_PREF.'shift';
	}else{
		$sql = 'Select * from '.TB_PREF.'shift';
		$emp = get_employees($selected_id, $all = false, $department = false);
		$shift_id = $emp['shift_id'];
	}
	$shifts = db_query($sql);

	echo "<tr><td class='label'>$label</td>";
	echo "<td>";
	// var_dump($emp);
	$output  = "<select name=".$name.">";
	while($shift = db_fetch($shifts)) {
		if ($selected_id == null) {
			$output .= '<option value="'.$shift["id"].'" >'.$shift["shift_name"].'</option>';	
		}
		else{
			$shift_id == $shift["id"] ? $selected ='selected' : $selected = '';
			$output .= '<option value="'.$shift["id"].'" '.$selected.'>'.$shift["shift_name"].'</option>';
		}
	}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}

//--------------------------------Employee shift select box--------------------

function departments($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	if($selected_id == null){
		$sql = 'Select * from '.TB_PREF.'department';
	}else{
		$sql = 'Select * from '.TB_PREF.'department';
		$emp = get_employees($selected_id, $all = false, $department = false);
		$dept_id = $emp['department_id'];
	}
	$depts = db_query($sql);

	echo "<tr><td class='label'>$label</td>";
	echo "<td>";
	// var_dump($emp);
	$output  = "<select name=".$name.">";
	while($dept = db_fetch($depts)) {
		if ($selected_id == null) {
			$output .= '<option value="'.$dept["dept_id"].'" >'.$dept["dept_name"].'</option>';	
		}
		else{
			$dept_id == $dept["dept_id"] ? $selected ='selected' : $selected = '';
			$output .= '<option value="'.$dept["dept_id"].'" '.$selected.'>'.$dept["dept_name"].'</option>';
		}
	}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}

function sections($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	if($selected_id == null){
		$sql = 'Select * from '.TB_PREF.'section';
	}else{
		$sql = 'Select * from '.TB_PREF.'section';
		$emp = get_employees($selected_id, $all = false, $section = false);
		$dept_id = $emp['section_id'];
	}
	$depts = db_query($sql);

	echo "<tr><td class='label'>$label</td>";
	echo "<td>";
	
	$output  = "<select name=".$name.">";
	$output	 .='<option  selected disabled>Select</option>';
	while($dept = db_fetch($depts)) {
		if ($selected_id == null) {
			$output .= '<option value="'.$dept["sec_id"].'" >'.$dept["name_bd"].'</option>';	
		}
		else{
			$dept_id == $dept["sec_id"] ? $selected ='selected' : $selected = '';
			$output .= '<option value="'.$dept["sec_id"].'" '.$selected.'>'.$dept["name_bd"].'</option>';
		}
	}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}



function number_activity_row($label, $name, $emp_activity_type=null) {
    echo "<tr><td class='label'>$label</td><td>";
	
    if($emp_activity_type=='release'){
        echo "<input type='radio' name=$name value='release' checked>Resigned&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='left'>Left&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='active'>Active&nbsp;&nbsp;&nbsp";
    }
    elseif($emp_activity_type=='left') {
        echo "<input type='radio' name=$name value='release'>Resigned&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='left' checked>Left&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='active'>Active&nbsp;&nbsp;&nbsp";
    }
    else {
        echo "<input type='radio' name=$name value='release'>Resigned&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='left'>Left&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name=$name value='active' checked>Active&nbsp;&nbsp;&nbsp";
    }
    echo "</td></tr>";
}
