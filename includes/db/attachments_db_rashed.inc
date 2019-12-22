<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/

//----------------------------------------------------------------------------------------

function add_attachment_rashed($description, $subject,$unique_name, $filename, $filesize, $filetype)
{
	$sql = "INSERT INTO ".TB_PREF."attachments_rashed (description, subject,unique_name, filename, filesize, filetype) VALUES (".db_escape($description).", ".db_escape($subject).", ".db_escape($unique_name).", ".db_escape($filename).", ".db_escape($filesize).", ".db_escape($filetype).")";
	db_query($sql, "Attachment could not be inserted");	
	return $sql;	
}
//----------------------------------------------------------------------------------------

function update_attachment_rashed($selected_id, $description, $subject, $filename, $filesize, $filetype)
{
	$sql = "UPDATE ".TB_PREF."attachments_rashed SET
		description=".db_escape($description).", subject=".db_escape($subject).", ";
	if ($filename != "")
	{
		$sql .= "filename=".db_escape($filename).",	filesize=".db_escape($filesize).", filetype=".db_escape($filetype);
	}	
	$sql .= " WHERE id=".db_escape($selected_id);
	db_query($sql, "Attachment could not be updated");		
}

//----------------------------------------------------------------------------------------

function delete_attachment_rashed($id)
{
	$sql = "DELETE FROM ".TB_PREF."attachments_rashed WHERE id = ".db_escape($id);
	db_query($sql, "Could not delete attachment");
}

//----------------------------------------------------------------------------------------

function get_attachment_string_rashed($type, $id)
{
	global $path_to_root;
    $str_return = "";    
    $sql = "SELECT * FROM ".TB_PREF."attachments_rashed WHERE id=".db_escape($id)." ORDER BY trans_no";
    $return = db_query($sql, "Could not retrieve attachments");
    while ($attachment = db_fetch($return))
    {
        if (strlen($str_return))
            $str_return = $str_return . " \n";    
        $str_return .= _("Attached File:")." <a href='$path_to_root/admin/attachments.php?vw=".$attachment["id"]." ' target='blanc_'> ". 
        	$attachment["id"] . " " . $attachment["description"]. " - ". $attachment["filename"]."</a><br>";
    }
    return $str_return . "<br>";
}

//----------------------------------------------------------------------------------------

function get_attached_documents_rashed($subject)
{
	$sql = "SELECT * FROM ".TB_PREF."attachments_rashed WHERE subject=".db_escape($subject)
	." ORDER BY id";
	return db_query($sql, "Could not retrieve attachments");
}

function get_attachment_rashed($id)
{
	$sql = "SELECT * FROM ".TB_PREF."attachments_rashed WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	return db_fetch($result);
}

function has_attachment_rashed($id)
{
	$sql = "SELECT DISTINCT * FROM ".TB_PREF."attachments_rashed WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	$myrow = db_fetch($result);
	if ($myrow === false)
		return 0;
	else
		return $myrow['id'];
}

function get_sql_for_attached_documents_rashed($id,$description='%')
{
	$sql="SELECT subject from ".TB_PREF."attachments_rashed WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	$myrow = db_fetch($result);
	$subject=$myrow[0];
	return "SELECT id, description, subject, unique_name,filename, filesize, filetype FROM ".TB_PREF."attachments_rashed WHERE subject=".db_escape($subject)." and description like '%".$description."%' ORDER BY id DESC";
	//return "SELECT id, description, subject, unique_name,filename, filesize, filetype FROM ".TB_PREF."attachments_rashed WHERE subject=".db_escape($subject)." ORDER BY id DESC";
}

function move_trans_attachments_rashed($id, $subject_from, $subject_to)
{
	$sql = "UPDATE ".TB_PREF."attachments_rashed SET subject=".db_escape($subject_to)." WHERE subject=".db_escape($subject_from);
	db_query($sql, 'cannot move atachments');
}


function rashed_subject_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql="SELECT id, subject FROM ".TB_PREF."attachments_rashed";

	if ($editkey)
		set_editor('subject', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'id', 'subject',
	array_merge(
	  array(
		'format' => '_format_subject',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("id", "subject"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
	
		'order' => array('subject'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_subject($row){
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function rashed_subject_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo rashed_subject_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n";
}