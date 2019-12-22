<?php 
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Rashed                                      |
|   Date :   18-02-2019                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

//-------------------------------------------------------------------------
function unit_list ($name, $id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false)
{
	$sql = "SELECT UnitID, UnitName FROM ".TB_PREF."unit ";

	return combo_input($name, $id, $sql, 'UnitID', 'UnitName',
	array(       
		'spec_option' => $spec_option,
		'select_submit' => $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	));
}

//--------------------------------------------------------------------------

function unit_list_cells($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo unit_list($name, $id, $all_option, $submit_on_change, $show_inactive);
	echo "</td>\n";
}

//--------------------------------------------------------------------------

function unit_list_row($label, $name, $id = null, $all_option = false, $submit_on_change = false, $show_inactive = false)
{
	echo "<tr><td class='label'>$label</td>";
	unit_list_cells(null, $name, $id, $all_option, 
	$submit_on_change, $show_inactive);
	echo "</tr>";
}
