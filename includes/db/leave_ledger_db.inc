<?php
 function write_ledger($emp_id,$leave_type,$pay_method,$leave_requested_from,$leave_requested_to,$leave_approved_from,$leave_approved_to,$approval_status,$approved_by,$total_day,$designation,$reason,$reliever) {

 	  // var_dump($emp_id);
		$sql = "SELECT emp_proximity_id,emp_name_bd FROM ".TB_PREF."employee";
		$sql .= " WHERE emp_id = ".db_escape($emp_id);
		$result = db_query($sql, _("Could not get employee data."));
		$r = db_fetch($result);
	    $proximity_id=$r['emp_proximity_id'];

	    $check = "SELECT count(*) total FROM 0_leave_ledger where ((".db_escape($leave_approved_from)." between leave_approved_from and leave_approved_to) or (".db_escape($leave_approved_to)." between leave_approved_from and leave_approved_to)) and emp_id=".db_escape($emp_id);
	    $cont_leave = db_fetch(db_query($check));
	    if ($cont_leave["total"]>0) {
	    	display_error("Date is Already Exists");
	    	return ;
	    }
		$sql = "INSERT INTO ".TB_PREF."leave_ledger(emp_id,leave_type,pay_method,leave_requested_from,leave_requested_to,leave_approved_from,leave_approved_to,approval_status,approved_by,proximity_id,total_day,designation,reason,reliever,inactive) VALUES (".db_escape($emp_id).",".db_escape($leave_type).",".db_escape($pay_method).",".db_escape($leave_requested_from).",".db_escape($leave_requested_to).",".db_escape($leave_approved_from).",".db_escape($leave_approved_to).",".db_escape($approval_status).",".db_escape($approved_by).",".db_escape($proximity_id).",".db_escape($total_day).",".db_escape($designation).",".db_escape($reason).",".db_escape($reliever).",'0')";
		// var_dump($sql);
        
      //  display_notification($sql);

	
		db_query($sql, _('Could not write overtime data'));
	
	
}

function wp_payment($emp_id,$pay_method,$leave_requested_from,$leave_to,$leave_approved_from,$leave_approved_to,$status,$total_day)
{
		//var_dump($total_day);
		$sql = "SELECT emp_id,emp_proximity_id,emp_name_bd FROM ".TB_PREF."employee";
		$sql .= " WHERE emp_id = ".db_escape($emp_id);
		$result = db_query($sql, _("Could not get employee data."));
		$r = db_fetch($result);
	 	$proximity_id=$r['emp_proximity_id'];
	 	$emp_id=$r['emp_id'];
	 	 $check = "SELECT count(*) total FROM 0_leave_ledger where ((".db_escape($leave_approved_from)." between leave_approved_from and leave_approved_to) or (".db_escape($leave_approved_to)." between leave_approved_from and leave_approved_to)) and emp_id=".db_escape($emp_id);
	    $cont_leave = db_fetch(db_query($check));
	    if ($cont_leave["total"]>0) {
	    	display_error("Date is Already Exists");
	    	return ;
	    }

	 	 $sql = "INSERT INTO ".TB_PREF."leave_ledger(emp_id,pay_method,leave_requested_from,leave_requested_to,leave_approved_from,leave_approved_to,approval_status,proximity_id,total_day,inactive) VALUES (".db_escape($emp_id).",".db_escape($pay_method).",".db_escape($leave_requested_from).",".db_escape($leave_to).",".db_escape($leave_approved_from).",".db_escape($leave_approved_to).",".db_escape($status).",".db_escape($proximity_id).",".db_escape($total_day).",'0')";

	 	 db_query($sql, _('Could not write overtime data'));

	 	  display_notification($sql);


}

function get_employee($id) {
	
	if(isset($id)) {
		$sql = "SELECT * FROM ".TB_PREF."employee WHERE emp_card_no =".$id;

		$result = db_query($sql, _("Could not get employee data."));
		return $result;
	}
}



function get_ledger($id = false, $all = false) {
	
	$sql = "SELECT  id,emp_id,leave_type,leave_requested_from,leave_requested_to,leave_approved_from,leave_approved_to,approval_status,approved_by,proximity_id,total_day,designation,reason,
	reliever FROM ".TB_PREF."leave_ledger";
    
	$where = array();
	
	if(!$all)
		$where[] = "!inactive";


	if($id)
		$where[] = "id=".db_escape($id);
		
	if(count($where))
		$sql .= ' WHERE '.implode(' AND ', $where);

	else
		$sql .= ' WHERE 1';	
	
	$result = db_query($sql, _('Could not get overtime data.'));


	
	if($id)
		return db_fetch($result);
	else
		return $sql;

}

function employee_ledger($id)
{


	$sql = "SELECT emp_proximity_id FROM ".TB_PREF."employee WHERE emp_card_no=".$id;
	$result = db_query($sql);
	
	foreach ($result as $key) {
		$proximity_id = $key['emp_proximity_id'];
	}

	$sql = "SELECT  id,emp_id,leave_type,leave_requested_from,leave_requested_to,leave_approved_from,leave_approved_to,approval_status,approved_by,proximity_id,total_day,designation,reason,
	reliever FROM ".TB_PREF."leave_ledger where proximity_id=".$proximity_id;
    
	$result = db_query($sql, _('Could not get overtime data.'));

	return $result;

    


}

function delete_ledger($id) {
	$sql = "DELETE FROM ".TB_PREF."leave_ledger WHERE id = ".db_escape($id);
	db_query($sql, _('Could not delete ledger.'));
}


function leavetype($label, $name, $selected_id,$all_option)
{
	
	echo "<td>$label</td>\n";
	echo "<td>";
	 $sql = "SELECT StatusFullName,StatusId FROM ".TB_PREF."attendance_status";
	 $sql .= " WHERE FlagStatus = ".db_escape('Leave');
	 $result = db_query($sql, _("Could not get employee data."));
	
	$output ="<select id=\"".$name."\" name=\"".$name."\">";
	$output .= "<option value=\"".$all_option."\">".$all_option."</option>";
	 while($r = db_fetch($result)){

	 	$output .= "<option value=\"".$r['StatusId']."\">".$r["StatusFullName"]."</option>";	
	 }
	$output .= "</select>";
	echo $output;
	echo "</td>\n";

}
function payment_method($label, $name, $selected_id,$all_option)
{
	echo "<tr>";
	echo "<td>$label</td>\n";
	echo "<td>";
	$output  = "<select id=\"".$name."\" name=\"".$name."\">";	
		
	
	if ($selected_id=='p') 
		{$output .= "<option value=\"p\" selected=\"selected\">With Pay</option>";}
	else {$output .= "<option value=\"p\">With Pay</option>";}
	if ($selected_id=='wp') 
		{$output .= "<option value=\"wp\" selected=\"selected\">Without Pay</option>";}
	else {$output .= "<option value=\"wp\">Without Pay</option>";}
	$output .= "</select>";
	echo $output;
	echo "</td>\n";
	echo "</tr>\n";

}
function emp_ext_ledger($id)
{
	if($id != null){
	$sql = "SELECT * FROM ".TB_PREF."leave_ledger";
		$sql .= " WHERE emp_id = ".$id;
		$result = db_query($sql, _("Could not get employee data."));
		//var_dump($result);
		return $result;

	}else{
		return null;
	}
}
function update_emp_ext_ledger($id)
{
	if($id != null){
	$sql = "SELECT * FROM ".TB_PREF."leave_ledger";
		$sql .= " WHERE id = ".$id;
		$result = db_query($sql, _("Could not get employee data."));
		//var_dump($result);
		return $result;

	}else{
		return null;
	}
}

function update_ledger($selected_id,$leave_type,$pay_method,$leave_requested_from,$leave_requested_to,$leave_approved_from,$leave_approved_to,$approval_status,$approved_by,$proximity_id,$total_day,$designation,$reason,$reliever)
{

	$sql = "UPDATE ".TB_PREF."leave_ledger SET 
		leave_type = ".db_escape($leave_type)." ,pay_method =".db_escape($pay_method).",leave_requested_from =".db_escape($leave_requested_from).",leave_requested_to =".db_escape($leave_requested_to).",leave_approved_from =".db_escape($leave_approved_from).",leave_approved_to =".db_escape($leave_approved_to).",approval_status =".db_escape($approval_status).",approved_by =".db_escape($approved_by).",total_day =".db_escape($total_day).",designation =".db_escape($designation).",reason =".db_escape($reason).",reliever =".db_escape($reliever)." WHERE id = ".db_escape($selected_id);

	db_query($sql, _('Could not write department data'));
}

function get_employee_info($id)
{
	//var_dump($id);
	if($id != null){
	$sql = "SELECT CONCAT(IFNULL(emp_first_name,''),' ',IFNULL(emp_last_name,'')) AS fullName,emp_proximity_id FROM ".TB_PREF."employee";
		$sql .= " WHERE emp_proximity_id= ".db_escape($id);
		$result = db_query($sql, _("Could not get employee data."));
//var_dump($result);
		return $result;
	}else{
		return null;
	}
	
}


function get_employee_information($id)
{
	//var_dump($id);
	if($id != null){
	$sql = "SELECT CONCAT(IFNULL(emp_first_name,''),' ',IFNULL(emp_last_name,'')) AS fullName,emp_proximity_id FROM ".TB_PREF."employee";
		$sql .= " WHERE emp_id = ".db_escape($id);
		$result = db_query($sql, _("Could not get employee data."));
//var_dump($result);
		return $result;
	}else{
		return null;
	}
	
}

function get_leave_type($id)
{
	
	if($id != null){
	$sql = "SELECT StatusFullName FROM ".TB_PREF."attendance_status";
		$sql .= " WHERE StatusID = ".db_escape($id);
		$result = db_query($sql, _("Could not get employee data."));
//var_dump($result);
		return $result;
	}else{
		return null;
	}
	
}

function get_employee_ledger_info($id)
{
	//var_dump($id);
	if($id != null){
	$sql = "SELECT * FROM ".TB_PREF."leave_ledger";
		
		$result = db_query($sql, _("Could not get employee data."));
//var_dump($result);
		return $result;
	}else{
		return null;
	}
	
}

function leave_calculation($leave_type,$emp_id)
{
	
	$sql = "SELECT COUNT(leave_type) as l FROM  ".TB_PREF."leave_ledger";
	$sql .= " WHERE emp_id = ".db_escape($emp_id);
	$sql .= " AND leave_type = ".db_escape($leave_type);
	
	 $result = db_query($sql, _("Could not get employee data."));
	
	return $result;


}
function leave_cal($leave_type,$proximity_id)
{
	
	$sql = "SELECT COUNT(leave_type) as l FROM  ".TB_PREF."leave_ledger";
	$sql .= " WHERE proximity_id = ".db_escape($proximity_id);
	$sql .= " AND leave_type = ".db_escape($leave_type);
	
	 $result = db_query($sql, _("Could not get employee data."));
	
	return $result;


}

function total_leave($leave_type)
{

	$sql = "SELECT a.Limit as t FROM  ".TB_PREF."attendance_status a";
	$sql .= " WHERE StatusID = ".db_escape($leave_type);

	 $limit = db_query($sql, _("Could not get employee data."));
	 
	 return $limit;

	

}



function text_requested_leave_from($name)
{
	echo "<tr>
	<td>Leave Requested From</td>
	<td><input type='date' name='leave_requested_from' value=\"" . $_POST[$name]. "\"  autocomplete='off' ></td></tr>";
}
function text_requested_leave_to($name)
{
	echo "<tr>
	<td>Leave Requested To</td>
	<td><input type='date' name='leave_requested_to' value=\"" . $_POST[$name]. "\"  autocomplete='off'></td></tr>";
}
function leave_ledger()
{
	echo "<tr>
	<td>Leave Requested To</td>
	<td><input type='date' name='leave_requested_to'  autocomplete='off'></td></tr>";
}

function text_approved_from($name)
{
	echo "<tr>
	<td>Approved From</td>
	<td><input type='date' name='leave_approved_from' value=\"" . $_POST[$name]. "\"   autocomplete='off' ></td></tr>";
}
function text_approved_to($name)
{
	echo "<tr>
	<td>Approved To</td>
	<td><input type='date' name='leave_approved_to' value=\"" . $_POST[$name]. "\"  autocomplete='off'></td></tr>";
}




function requested_leave_from()
{
	echo "<tr>
	<td>Leave Requested From</td>
	<td><input type='date' name='leave_from'   autocomplete='off' ></td></tr>";
}
function requested_leave_to()
{
	echo "<tr>
	<td>Leave Requested To</td>
	<td><input type='date' name='leave_to' autocomplete='off'></td></tr>";
}

function approved_from()
{
	echo "<tr>
	<td>Approved From</td>
	<td><input type='date' name='approved_from'   autocomplete='off' ></td></tr>";
}
function approved_to()
{
	echo "<tr>
	<td>Approved To</td>
	<td><input type='date' name='approved_to'  autocomplete='off'></td></tr>";
}












function name_information($id)
{


	if($id != null){
	$sql = "SELECT emp_name_bd FROM ".TB_PREF."employee";
		$sql .= "WHERE emp_id = ".$id;
		$result = db_query($sql, _("Could not get employee data."));
		//var_dump($result);
		return $result;

	}else{
		return null;
	}
}




			