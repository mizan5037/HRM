<?php
function input_date($label='', $name='', $value='', $id='')
{
	echo '<tr><td class="label">'.$label.'</td>';
	echo "<td>";
	$output  = '<input type="date" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
	echo $output;
	echo "</td>";
	echo "</tr>";

}


function input_text_td($label='', $name='', $value='', $id='')
{
	echo '<td class="label">'.$label.'</td>';
	echo "<td>";
	$output  = '<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
	echo $output;
	echo "</td>";
}


function input_select_box($label='', $name='',$table='',$column_id='', $column_name='', $value='', $id='',$required = false)
{
	if ($label == '' || $name=='' || $table=='' || $column_name=='' || $column_id=='') {
		return '';
	}
	$required = $required ? 'required' : '';

	$sql ="Select ".$column_id.",".$column_name." FROM ".TB_PREF.$table;
	$lists = db_query($sql);
	$output = '<tr><td class="label">'.$label.'</td>';
	$output .= '<td>';
	$output .='<select name="'.$name.'" id="'.$id.'" '.$required.'>';
	$output .='<option value="" selected >Select</option>';
	while ($list = db_fetch($lists)) {
		$selected = $value == $list[$column_id] ? 'selected' : '';
		$output .='<option value="'.$list[$column_id].'" '.$selected.'>'.$list[$column_name].'</option>';
	}
	$output .='</select>';
	$output .='</td>';
	$output .='</tr>';
	echo $output;
}

function Add_delete($label, $name) {
    echo "<tr><td class='label'>$label</td><td>";
    if($gender==0){
        echo "<input type='radio' name=$name value='add'>Add";
        echo "<input type='radio' name=$name value='delete'>Delete";


    }
    echo "</td></tr>";
}
