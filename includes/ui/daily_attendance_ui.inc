<?php 

function input_date_lv($label='', $name='', $value='', $id='' ,$required = false)
{
	$required = $required ? 'required' : '';
	echo '<td class="label">'.$label.'</td>';
	echo '<td>';
	$output  = '<input type="date" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$required.'>';
	echo $output;
	echo "</td>";

}

function input_date_wl($name='', $value='' ,$required = false)
{
	$required = $required ? 'required' : '';
	echo "<td>";
	$output  = '<input type="date" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$required.'>';
	echo $output;
	echo "</td>";

}

function input_text_td_wl($name='', $value='' ,$required = false)
{
	$required = $required ? 'required' : '';
	echo "<td>";
	$output  = '<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$required.' size="8">';
	echo $output;
	echo "</td>";
}

function input_num_td_wl($name='', $value='' ,$required = false)
{
	$required = $required ? 'required' : '';
	echo '<td>';
	$output  = '<input type="number" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$required.' >';
	echo $output;
	echo "</td>";
}


function input_text_time($name='', $value='',$required = false)
{
	$required = $required ? 'required' : '';
	echo "<td>";
	$output  = '<input type="time" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$required.'>';
	echo $output;
	echo "</td>";
}

function input_select_box_td($label='', $name='',$table='',$column_id='', $column_name='', $value='', $id='',$required = false)
{
	if ($label == '' || $name=='' || $table=='' || $column_name=='' || $column_id=='') {
		return '';
	}

	$required = $required ? 'required' : '';

	$sql ="Select ".$column_id.",".$column_name." FROM ".TB_PREF.$table;
	$lists = db_query($sql);
	$output .= '<td>';
	$output .='<select name="'.$name.'" id="'.$id.'" '.$required.'>';
	$output .='<option value="" selected >'.$label.'</option>';
	while ($list = db_fetch($lists)) {
		$selected = $value == $list[$column_id] ? 'selected' : '';
		$output .='<option value="'.$list[$column_id].'" '.$selected.'>'.$list[$column_name].'</option>';
	}
	$output .='</select>';
	$output .='</td>';
	echo $output;
}