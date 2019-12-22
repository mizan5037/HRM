<?php

function paymentstringtype($label, $name, $selected_id,$all_option)
{
	echo "<td>$label</td>\n";
	echo "<td>";
	$output  = "<select id=\"".$name."\" name=\"".$name."\">";	
	$output .= "<option value=\"".$all_option."\">".$all_option."</option>";	
	if ($selected_id=='Add') 
		{$output .= "<option value=\"Add\" selected=\"selected\">Add</option>";}
	else {$output .= "<option value=\"Add\">Add</option>";}
	if ($selected_id=='Ded') 
		{$output .= "<option value=\"Deduct\" selected=\"selected\">Deduct</option>";}
	else {$output .= "<option value=\"Deduct\">Deduct</option>";}
	$output .= "</select>";
	echo $output;
	echo "</td>\n";
}


function paymentstringamounttype($label, $name, $selected_id,$all_option)
{
	echo "<td>$label</td>\n";
	echo "<td>";
	$output  = "<select id=\"".$name."\" name=\"".$name."\">";	
	$output .= "<option value=\"".$all_option."\">".$all_option."</option>";	
	if ($selected_id=='Fixed') 
		{$output .= "<option value=\"Fixed\" selected=\"selected\">Fixed</option>";}
	else {$output .= "<option value=\"Fixed\">Fixed</option>";}
	if ($selected_id=='Variable') 
		{$output .= "<option value=\"Variable\" selected=\"selected\">Variable</option>";}
	else {$output .= "<option value=\"Variable\">Variable</option>";}
	$output .= "</select>";
	echo $output;
	echo "</td>\n";
}


