<?php

function holidaytype($label, $name, $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null)
{
	
	echo "<tr><td class='label'>".$label."</td>";
	echo "<td>";
	$output  = "<select name=".$name.">";
    if($selected_id == null && $selected_id>0){
	    $output .= '<option value="w">Weekly</option>';
	 	$output .= '<option value="c">Casual</option>';
	}
 	else{
 		$h =db_fetch(get_holiday($selected_id, $all = false));

 		if ($h["holiday_type"] == "w") {
 			$output .= '<option value="w" selected>Weekly</option>';
	 		$output .= '<option value="c">Casual</option>';
 		} elseif ($h["holiday_type"] == "c") {
 			$output .= '<option value="w">Weekly</option>';
	 		$output .= '<option value="c" selected>Casual</option>';
 		}else{
 			$output .= '<option value="w">Weekly</option>';
	 		$output .= '<option value="c">Casual</option>';
 		}

 	}

	$output .= "</select>";
	echo $output;
	echo "</td>";
	echo "</tr>";

}