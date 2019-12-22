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

function department_list ($name, $id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false)
{
	$sql = "SELECT dept_id, dept_name, inactive FROM ".TB_PREF."department ";

	return combo_input($name, $id, $sql, 'dept_id', 'dept_name',
	array(       
		'spec_option' => $spec_option,
		'select_submit' => $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	));
}

//--------------------------------------------------------------------------

function department_list_cells($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo department_list($name, $id, $all_option, $submit_on_change, $show_inactive);
	echo "</td>\n";
}

//--------------------------------------------------------------------------

function department_list_row($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false)
{
	echo "<tr><td class='label'>$label</td>";
	department_list_cells(null, $name, $id, $all_option, 
	$submit_on_change, $show_inactive);
	echo "</tr>";
}

//-------------------------------------------------------------------------------------------------
function attendance_list ($name, $id, $selected_id, $table, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{

	
	$sql = "SELECT ".$valfield.", ".$namefield." FROM ".TB_PREF.$table;
	$Result=db_query($sql);
	$output  = "<select id=\"".$name."\" name=\"".$name."\">";	
	$output .= "<option value=\"".$all_option."\">".$all_option."</option>";	
	if (db_num_rows($Result)>0)
		{			
			while($EmpAttd = db_fetch($Result)) 
			{ 
				if ($EmpAttd[0]==$selected_id)
				{$output .= "<option value=\"".$EmpAttd[0]."\" selected=\"selected\">".$EmpAttd[1]."</option>";}
				else {$output .= "<option value=\"".$EmpAttd[0]."\">".$EmpAttd[1]."</option>";}
			}
		}
	$output .= "</select>";
return $output;
}

//--------------------------------------------------------------------
function attendance_list_cells ($label, $name,$id, $selected_id, $table, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{

	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo attendance_list($name, $id, $selected_id, $table, $all_option, $submit_on_change, $show_inactive, $valfield, $namefield);
	echo "</td>\n";
}

?>