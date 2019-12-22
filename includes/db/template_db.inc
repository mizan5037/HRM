<?php

function get_emp($id = false,$emp_id, $all = false) {
	if ($id==false && isset($emp_id)==true) {
		$sql = "SELECT *, CONCAT(IFNULL(emp_first_name,' '), ' ', IFNULL(emp_last_name,' ')),emp_salary FROM ".TB_PREF."employee WHERE emp_id =".$emp_id;
		$result = db_query($sql, _("Could not get employee data."));
		return $result;
	}
	$sql = "SELECT *, CONCAT(IFNULL(emp_first_name,' '), ' ', IFNULL(emp_last_name,' ')) AS name ,emp_salary FROM ".TB_PREF."employee";
	if($id != null){
		$sql .= " WHERE emp_first_name LIKE '%".$id."%' OR emp_last_name LIKE '%".$id."%' OR emp_card_no LIKE '%".$id."%' LIMIT 10";
		$result = db_query($sql, _("Could not get employee data."));
		return $result;
	}else{
		return null;
	}
	
}


function emp_ext_pmt_str($id)
{
	if($id != null){
	$sql = "SELECT * FROM ".TB_PREF."emp_pmt_tmp";
		$sql .= " WHERE emp_id = ".$id;
		$result = db_query($sql, _("Could not get employee data."));
		return $result;
	}else{
		return null;
	}
}

function act_pmt_str($act=0,$all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_pmt_str_tmp";
	if(!$all){
		$sql .= " WHERE activity = ".$act;
	}
	
	$result = db_query($sql, _("Could not get employee data."));
	return $result;
}

function write_template_salary($id, $salary) {
	
	// display_notification($id.' '.$salary);
	$sal = array();
	$add = 0;
	$deduct = 0;
	$str = act_pmt_str();
	$tracker = array();
	$i = 0;
	$var = array();
	$id_amount = array();
	while ($s = db_fetch($str)) {
		$tracker[$i]=$s["emp_pmt_str_id"];
		if ($s["emp_pmt_type"] == 'Add') {
			if ($s["emp_pmt_amount_type"] == 'Fixed') {
				$id_amount[$s["emp_pmt_str_id"]] = (int)$s["emp_pmt_amount"];
				$add += (int)$s["emp_pmt_amount"];
			}else{
				$id_amount[$s["emp_pmt_str_id"]] = 0;
				$var[$s["emp_pmt_str_id"]]=$s["emp_pmt_amount"];
			}
			// $add += $s["emp_pmt_amount_type"] == 'Fixed' ? (int)$s["emp_pmt_amount"] : 0;
			// $s["emp_pmt_amount_type"] != 'Fixed' ? $var[$s["emp_pmt_str_id"]]=$s["emp_pmt_amount"] : 0;
		}else{
			$id_amount[$s["emp_pmt_str_id"]] = (int)$s["emp_pmt_amount"];
			$deduct += (int)$s["emp_pmt_amount"];
		}
		$i++;
	}
	// display_notification($add.' '.$deduct);
	$gross = $salary-$add;
	foreach ($var as $key => $value) {
		$id_amount[$key] = (float)$gross*$value/100;
		// display_notification($key.' '.$value);
	}
	// var_dump($var);
	// var_dump($tracker);
	$sql='';
	if ($id_amount != null) {
		$sql = "DELETE FROM ".TB_PREF."emp_pmt_tmp WHERE emp_id =".$id;
		db_query($sql);
		foreach ($id_amount as $key => $value) {
			$value = round($value);
			$sql = "INSERT INTO ".TB_PREF."emp_pmt_tmp(emp_id,emp_pmt_str_id,emp_pmt_tmp_amt) VALUES (".db_escape($id).",".db_escape($key).",".db_escape($value).");";
			db_query($sql, _('Could not write Salary Template data'));
		}

	}
	return ['deduct'=>$deduct,'total'=>$salary];
}

function write_template_salary_update($id,$tracker)
{
	
	foreach ($tracker as $key => $value) {
		// display_notification($value.' '.$_POST[$value]);
		$sql = "UPDATE ".TB_PREF."emp_pmt_tmp SET 
		emp_pmt_tmp_amt = ".db_escape(round($_POST[$value]))." 
		WHERE emp_id = ".db_escape($id)."
		AND emp_pmt_str_id=".db_escape($value).";";
		// display_notification($sql);
		db_query($sql);
	}

	display_notification('Salary Template Updated.');
}