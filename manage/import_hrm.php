<?php
/**********************************************
Author: MOHAMMAD RASHEDUL ISLAM
Name: Import Multiple Journal Entries/Deposits/Payments v2.3
Free software under GNU GPL
***********************************************/
$path_to_root  = '../../..';

$page_security = 'SA_EMPL';
date_default_timezone_set('Asia/Dhaka');
include_once($path_to_root . "/includes/ui/items_cart.inc");
include_once($path_to_root . "/gl/includes/db/gl_db_trans.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
add_access_extensions();
create_access_ontime($proot);

//-------------------------------------------------------------------------------------------------
function import_type_list_row($label, $name, $selected=null, $submit_on_change=false)
{
	$arr = array(
		ST_EMPLOYEE=> "Employee",
		ST_ATTENDANCE=> "Daily Attendance",
	);

	echo "<tr><td class='label'>$label</td><td>";
	echo array_selector($name, $selected, $arr, 
		array(
			'select_submit'=> $submit_on_change,
			'async' => false,
		));
	echo "</td></tr>\n";
}

//--------------------------------------------------------------------------------------------------
function import_employee($emp_card_no,$emp_card_no_bd,$emp_proximity_id,$emp_first_name,$emp_last_name,$emp_name_bd,$gender,$emp_address,$emp_present_address,$emp_mobile,$emp_email,$emp_identity_type,$emp_identity_number,$emp_birthdate,$emp_notes,$emp_hiredate,$department_id,$salary_scale_id,$emp_activity_type,$emp_releasedate,$inactive,$Designation,$Designation_bd,$emp_status,$emp_blood,$UnitID,$shift_id,$emp_attn_bonus,$emp_salary)
{
	//begin_transaction();
	if ($emp_birthdate=='0000-00-00'||$emp_birthdate=='NULL'){$emp_birthdate=NULL;}
	else {$emp_birthdate=date2sql($emp_birthdate);}

	if ($emp_hiredate=='0000-00-00'||$emp_hiredate=='NULL'){$emp_hiredate=NULL;}
	else {$emp_hiredate=date2sql($emp_hiredate);}

	if ($emp_releasedate=='0000-00-00'||$emp_releasedate=='NULL'){$emp_releasedate=NULL;}
	else {$emp_releasedate=date2sql($emp_releasedate);}


	$sql = "INSERT INTO ".TB_PREF."employee(emp_card_no,emp_card_no_bd,emp_proximity_id,emp_first_name,emp_last_name,emp_name_bd,gender,emp_address,emp_present_address,emp_mobile,emp_email,emp_identity_type,emp_identity_number,emp_birthdate,emp_notes,emp_hiredate,department_id,salary_scale_id,emp_activity_type,emp_releasedate,inactive,Designation,Designation_bd,emp_status,emp_blood,UnitID,shift_id,emp_atten_bonus,emp_salary) values ("
	//.db_escape($emp_id).", "
	
	//.db_escape($emp_id).", "
	.db_escape($emp_card_no).", "
	.db_escape($emp_card_no_bd).", "
	.db_escape($emp_proximity_id).", "
	.db_escape($emp_first_name).", "
	.db_escape($emp_last_name).","
	.db_escape($emp_name_bd).","
	.db_escape($gender).", "
	.db_escape($emp_address).", "
	.db_escape($emp_present_address).", "
	.db_escape($emp_mobile).", "
	.db_escape($emp_email).", "
	.db_escape($emp_identity_type).", "
	.db_escape($emp_identity_number).", "
	.db_escape($emp_birthdate).", "
	.db_escape($emp_notes).", "
	.db_escape($emp_hiredate).", "
	.db_escape($department_id).", "
	.db_escape($salary_scale_id).", "
	.db_escape($emp_activity_type).", "
	.db_escape($emp_releasedate).", "
	.db_escape($inactive).", "
	.db_escape($Designation).", "
	.db_escape($Designation_bd).", "
	.db_escape($emp_status).", "
	.db_escape($emp_blood).", "
	.db_escape($UnitID).", "
	.db_escape($shift_id).", "
	.db_escape($emp_attn_bonus).", "
	.db_escape($emp_salary)
	.")";


	db_query($sql, "failed to insert");
	$sql1="SELECT emp_id FROM ".TB_PREF."employee WHERE emp_card_no = ".db_escape($emp_card_no_bd)." OR emp_proximity_id = ".db_escape($emp_proximity_id);
	$empl_id = db_fetch(db_query($sql1));
	write_template_salary($empl_id["emp_id"],$emp_salary);
	// display_notification();
	// var_dump($empl_id);
	// return $sql;
	// display_error($sql);
	//commit_transaction();
}
//--------------------------------------------------------------------------------------------------
function update_employee($emp_card_no,$emp_card_no_bd,$emp_proximity_id,$emp_first_name,$emp_last_name,$emp_name_bd,$gender,$emp_address,$emp_present_address,$emp_mobile,$emp_email,$emp_identity_type,$emp_identity_number,$emp_birthdate,$emp_notes,$emp_hiredate,$department_id,$salary_scale_id,$emp_activity_type,$emp_releasedate,$inactive,$Designation,$Designation_bd,$emp_status,$emp_blood,$UnitID,$shift_id,$emp_attn_bonus,$emp_salary)
{
	//begin_transaction();
	$sql = "UPDATE ".TB_PREF." employee(emp_card_no,emp_card_no_bd,emp_proximity_id,emp_first_name,emp_last_name,emp_name_bd,gender,emp_address,emp_present_address,emp_mobile,emp_email,emp_identity_type,emp_identity_number,emp_birthdate,emp_notes,emp_hiredate,department_id,salary_scale_id,emp_activity_type,emp_releasedate,inactive,Designation,Designation_bd,emp_status,emp_blood,UnitID,shift_id,emp_atten_bonus,emp_salary) values ("
	.db_escape($emp_card_no).", "
	.db_escape($emp_card_no_bd).", "
	.db_escape($emp_proximity_id).", "
	.db_escape($emp_first_name).", "
	.db_escape($emp_last_name).","
	.db_escape($emp_name_bd).","
	.db_escape($gender).", "
	.db_escape($emp_address).", "
	.db_escape($emp_present_address).", "
	.db_escape($emp_mobile).", "
	.db_escape($emp_email).", "
	.db_escape($emp_identity_type).", "
	.db_escape($emp_identity_number).", "
	.db_escape($emp_birthdate).", "
	.db_escape($emp_notes).", "
	.db_escape($emp_hiredate).", "
	.db_escape($department_id).", "
	.db_escape($salary_scale_id).", "
	.db_escape($emp_activity_type).", "
	.db_escape($emp_releasedate).", "
	.db_escape($inactive).", "
	.db_escape($Designation).", "
	.db_escape($Designation_bd).", "
	.db_escape($emp_status).", "
	.db_escape($emp_blood).", "
	.db_escape($UnitID).", "
	.db_escape($shift_id).", "
	.db_escape($emp_attn_bonus).", "
	.db_escape($emp_salary)
	.")";


	db_query($sql, "failed to insert");
	return $sql;
	display_error($sql);
	//commit_transaction();
}

//---------------------------------------------------------------------------
function import_salary_scale($scale_id,$scale_name,$inactive,$pay_basis,$gl_debit,$gl_credit)
{
	//begin_transaction();
	$sql = "INSERT INTO ".TB_PREF."salaryscale (scale_id,scale_name,inactive,pay_basis,gl_debit,gl_credit) values ("
		.db_escape($scale_id).","
		.db_escape($scale_name).", "
		.db_escape($inactive).","
		.db_escape($pay_basis).", "
		.db_escape($gl_debit).", "
		.db_escape($gl_credit).")";
	db_query($sql, "failed to insert");

	return $sql;
	//commit_transaction();
}
//--------------------------------------------------------------------------------------------------
function import_pmt_tmp($scale_id,$emp_pmt_str_id,$emp_pmt_tmp_amt)
{
	//begin_transaction();
	$sql = "INSERT INTO ".TB_PREF."emp_pmt_tmp (scale_id,emp_pmt_str_id,emp_pmt_tmp_amt) values ("
		.db_escape($scale_id).","
		.db_escape($emp_pmt_str_id).", "
		.db_escape($emp_pmt_tmp_amt).")";
	db_query($sql, "failed to insert");
	return $sql;
	//commit_transaction();
}
//----------------------------------------------------------------------------------------------------
// Begin the UI
include_once($path_to_root . "/includes/ui.inc");

page("Import HRM data from csv");
$sql="";
// If the import button was selected, we'll process the form here.  (If not, skip to actual content below.)
if (isset($_POST['import']))
{
	$type = $_POST['type'];
	if ($type == ST_ATTENDANCE)
	{
		$c=insert_txt2tmp();
		$att_dt_res= db_query("Select att_date from 0_daily_attendance_tmp LIMIT 1");
	while ($row_dt_res = db_fetch($att_dt_res))
		{
			$att_date=$row_dt_res ['att_date'];
			$prev_date= date('Y-m-d', strtotime('-1 day', strtotime($att_date)));
		}
	$c .= delete_existing($att_date);
	$c .= insert_absent($att_date);
	$c .= insert_wo_shift($att_date);
	$c .= insert_shift($att_date,$prev_date);
	display_notification('Attendance of ('.$att_date.') Inserted Successfully.');
	}
	else
	{
		if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '')
		{
			$filename = $_FILES['imp']['tmp_name'];
			$sep = $_POST['sep'];
			begin_transaction();
			// Open the file
			$fp = @fopen($filename, "r");
			$c=1;
			$truncate="";
			while ($data = fgetcsv($fp, 4096, $sep))
			{
				if ($type == ST_EMPLOYEE)
				{

					list($emp_id,$emp_card_no,$emp_card_no_bd,$emp_proximity_id,$emp_first_name,$emp_last_name,$emp_name_bd,$gender,$emp_address,$emp_present_address,$emp_mobile,$emp_email,$emp_identity_type,$emp_identity_number,$emp_birthdate,$emp_notes,$emp_hiredate,$department_id,$salary_scale_id,$emp_activity_type,$emp_releasedate,$inactive,$Designation,$Designation_bd,$emp_status,$emp_blood,$UnitID,$shift_id,$emp_attn_bonus,$emp_salary) = $data;
					if ($emp_id!='emp_id')
						{
							$sql = import_employee($emp_card_no,$emp_card_no_bd,$emp_proximity_id,$emp_first_name,$emp_last_name,$emp_name_bd,$gender,$emp_address,$emp_present_address,$emp_mobile,$emp_email,$emp_identity_type,$emp_identity_number,$emp_birthdate,$emp_notes,$emp_hiredate,$department_id,$salary_scale_id,$emp_activity_type,$emp_releasedate,$inactive,$Designation,$Designation_bd,$emp_status,$emp_blood,$UnitID,$shift_id,$emp_attn_bonus,$emp_salary);
							$c=$c+1;
						}
					/*
					else {
						if (check_value('truncate_tbl')==true)
							{
								$sql_truncate="Truncate ".TB_PREF."employee";
								db_query($sql_truncate, "failed to truncate");	
								$truncate="truncated";
								$c=$c+1;
							}
					}
					*/
				}
				elseif ($type == ST_SALARY_SCALE)
				{
					list($scale_id,$scale_name,$inactive,$pay_basis,$gl_debit,$gl_credit) = $data;
					if ($scale_id!='scale_id')
						{$sql = import_salary_scale($scale_id,$scale_name,$inactive,$pay_basis,$gl_debit,$gl_credit);
							$c=$c+1;
						}
					/*
					else {
						if (check_value('truncate_tbl')==true)
							{
								$sql_truncate="Truncate ".TB_PREF."salaryscale";
								db_query($sql_truncate, "failed to truncate");	
								$truncate="truncated";
								$c=$c+1;
							}
					}
					*/
				}
				elseif ($type == ST_PMT_TMP)
				{
					list($scale_id,$emp_pmt_str_id,$emp_pmt_tmp_amt) = $data;
					if ($scale_id!='scale_id')
						{$sql = import_pmt_tmp($scale_id,$emp_pmt_str_id,$emp_pmt_tmp_amt);
							$c=$c+1;
						}
					/*
					else {
						if (check_value('truncate_tbl')==true)
							{
								$sql_truncate="Truncate ".TB_PREF."salaryscale";
								db_query($sql_truncate, "failed to truncate");	
								$truncate="truncated";
								$c=$c+1;
							}
					}
					*/
				}
			}
			commit_transaction();
		}
	}
		display_notification('Data imported:'.$c.$truncate);
		// display_notification($sql); 
}

start_form(true);

start_table(TABLESTYLE2);

if (!isset($_POST['type']))
	$_POST['type'] = ST_ATTENDANCE;

if (!isset($_POST['sep']))
	$_POST['sep'] = " ";



table_section_title("Import Settings");
import_type_list_row("Import Type:", 'type', $_POST['type'], true);

if($_POST['type'] == ST_EMPLOYEE)
{
label_row(_('Operation').':', radio(_('Insert'), 'opeartion', 0,1).'&nbsp;&nbsp;'.radio(_('Update'),
 'opeartion', 1));
};

text_row("Field Separator:", 'sep', $_POST['sep'], 2, 1);
// check_cells(_('Truancte Table?').':', 'truncate_tbl', null, true);
label_row("Import File:", "<input type='file' id='imp' name='imp'>");

end_table(1);

submit_center('import', "Perform Import");//,true,false,'process',ICON_SUBMIT);

end_form();

end_page();


function insert_txt2tmp()
{
	$filename = $_FILES['imp']['tmp_name'];
	$sep = $_POST['sep'];
	
	$fp = fopen( $filename, 'r') or die( 'Cannot open');
	$sql = 'Truncate table 0_daily_attendance_tmp;';	
	db_query($sql,'Cannot delete');
	$sql = ' INSERT INTO 0_daily_attendance_tmp (counter_no,emp_proximity_id,att_date,EntryTime,entry_serial,AttdStatus) VALUES ';
	while( $row = fgets( $fp))
	{
		$values = explode( ' ', $row);
		$sql .= "('".rtrim(implode( "','", $values),' ')."'),";
	}
	fclose( $fp);
	$sql= rtrim($sql,',');
	//****** INSERT INTO TEMPORARTY TABLE************ 
	begin_transaction();
	db_query($sql,'Cannot insert into temp table');
	commit_transaction();
	// return ' insert into temporary table successful';
	return '';
}

function delete_existing($att_date)
{
	$sql = "Delete from 0_daily_attendance Where Att_date='".$att_date."'";	
	db_query($sql,'Error');
	// return 'Existing Data Deleted for '.$att_date;
	return '';
}

function insert_absent($att_date)
{
	//************ INSERT ABSENT EMPLOYEES************ 
	if (db_num_rows(db_query("Select holiday_date from 0_emp_holiday where holiday_date='".$att_date."'"))==0)
	{
		$sql ="INSERT INTO 0_daily_attendance (proximity_id, emp_id,att_date,AttdStatus)";
		// $sql .="SELECT proximity_id, emp_id, '".$att_date."', '8' FROM (SELECT npe.proximity_id,IFNULL(npe.emp_proximity_id, 0) as pro_id, ".
		// 	" npe.emp_id,npe.counter_no,npe.att_date,npe.EntryTime,npe.entry_serial,npe.AttdStatus ".
		// 	" FROM (SELECT e.emp_id,e.emp_proximity_id proximity_id ,d.* FROM 0_employee e ".
		// 	" LEFT JOIN 0_daily_attendance_tmp d ON e.emp_proximity_id = d.emp_proximity_id ".
		// 	" WHERE e.inactive = 0) as npe) as npee WHERE npee.pro_id = 0";
		$sql .="SELECT temp.pro_id,emp_id,'$att_date',8 FROM (SELECT e.emp_id,IFNULL(da.emp_proximity_id,0) as pro_id FROM 0_employee e LEFT JOIN (SELECT DISTINCT(emp_proximity_id) FROM 0_daily_attendance_tmp) da ON e.emp_proximity_id = da.emp_proximity_id WHERE !e.inactive) temp WHERE temp.pro_id = 0";
		begin_transaction();
		db_query($sql);
		commit_transaction();
		return '';
		// return '<br>ABSENT insert into ATTENDANCE table successful</br>';
	}
	else{return 'Holiday';}
}

function insert_wo_shift($att_date)
{
	//********INSERT INTO DAILY ATTENDANCE TABLE WITHOUT SHIFT **************
	$sql1 ="INSERT INTO 0_daily_attendance (proximity_id, emp_id,att_date,EntryTime,ExitTime,AttdStatus,overtime_id, hours_no, rate,exit_date)";
	$sql1 .=" SELECT proximity_id, emp_id,att_date,EntryTime,ExitTime,AttdStatus,overtime_id, hours_no, rate,exit_date 
					FROM (SELECT distinct(0_daily_attendance_tmp.`emp_proximity_id`) as proximity_id, emp_id, att_date, min(`EntryTime`) as EntryTime,max(`EntryTime`) as ExitTime, ";
	$sql1 .=" get_att_status(emp_id, TIMESTAMP(att_date, min(`EntryTime`))) as AttdStatus , '1' as overtime_id, ";
	$sql1 .=" get_ot_hour(emp_id, TIMESTAMP(att_date, min(`EntryTime`)), TIMESTAMP(att_date, max(`EntryTime`))) as hours_no ,get_ot_rate(emp_id) as rate, ";
	$sql1 .=" att_date as exit_date, chk_next_day_shift(emp_id, '".$att_date."') as 'ckk'  FROM `0_daily_attendance_tmp` left join 0_employee ";
	$sql1 .=" on 0_daily_attendance_tmp.emp_proximity_id=0_employee.emp_proximity_id group by 0_daily_attendance_tmp.`emp_proximity_id`,";
	$sql1 .=" 0_daily_attendance_tmp.att_date) as dt where ckk=0";
	begin_transaction();
	db_query($sql1);
	commit_transaction();
	//return $sql1;
	// return '<br>insert into ATTENDANCE table without shift successful</br>';
	return true;
}

function insert_shift($att_date,$prev_date)
{

	//********INSERT INTO DAILY ATTENDANCE TABLE WITH SHIFT **************
	$sql =  "INSERT INTO 0_daily_attendance (proximity_id, emp_id,att_date,EntryTime,ExitTime,AttdStatus,overtime_id, hours_no, rate,exit_date)";
	$sql .= "SELECT get_proximity_from_emp_id(emp_id) as 'emp_proximity_id' , emp_id,'".$att_date."' as Att_date,  fn_shift_entry_time(emp_id, '".$att_date."',  'Entry')  as EntryTime, 
			fn_shift_entry_time(emp_id,'".$att_date."',  'Exit')  as  ExitTime, 
			get_att_status(emp_id,timestamp('".$att_date."', fn_shift_entry_time(emp_id,'".$att_date."', 'Entry')))  as AttdStatus, '1'  as overtime_id, get_ot_hour(emp_id, TIMESTAMP('$att_date',fn_shift_entry_time(emp_id,'".$att_date."', 'Entry')), TIMESTAMP(DATE_ADD('$att_date', INTERVAL 1 DAY), fn_shift_entry_time(emp_id,'".$att_date."','Exit'))) as hours_no,
			get_ot_rate(emp_id) as Ot_rate, DATE_ADD('$att_date', INTERVAL 1 DAY) as exit_date FROM 0_emp_shift_list WHERE shift_from<='".$att_date."' and shift_to>='".$att_date."'";
								
	begin_transaction();
	db_query($sql);
	commit_transaction();
	// display_notification($sql);
	
	// return '<br> INSERT INTO DAILY ATTENDANCE TABLE WITH SHIFT SUCCESSFUL</br>';
	return '';

/*	//************** UPDATE DAILY ATTENDANCE FOR SHIFT CORRECTIONS****************
	$sql = "SELECT get_proximity_from_emp_id(emp_id) as 'emp_proximity_id' , emp_id,'".$att_date."' as Att_date,  get_entry_time_from_daily_attendance(emp_id, '".$prev_date."')  as EntryTime, 
			fn_shift_entry_time(emp_id,'".$att_date."',  'Exit')  as  ExitTime, 
			get_ot_hour(emp_id, TIMESTAMP('".$prev_date."', get_entry_time_from_daily_attendance(emp_id, '".$prev_date."')) , TIMESTAMP('".$att_date."', fn_shift_entry_time(emp_id,'".$att_date."',  'Exit'))) as  OT_hr,
			get_ot_rate(emp_id) as Ot_rate  FROM 0_emp_shift_list WHERE shift_from<='".$prev_date."' and shift_to>='".$prev_date."'";
					
	
	$sqlupdate=array();
	$counter=0;
	$sqlCust = db_query($sql);
	while ($rowCust = db_fetch($sqlCust))
		{
			$EntryTime=$rowCust ['EntryTime'];
			$ending_time=$rowCust ['ending_time'];
			$EntryTime = strtotime($att_date.$EntryTime);
			$ending_time = strtotime($att_date.$ending_time);
			$reg_time=($EntryTime - $ending_time)/3600;
			$sqlupdate[$counter] = "Update 0_daily_attendance Set ExitTime='".$rowCust['ExitTime'].
				"' ,  hours_no = '".$rowCust['OT_hr'].
				"'  , exit_date= '".$att_date."'   WHERE att_date='".$prev_date.
				"' AND emp_id='".$rowCust ['emp_id']."'";
				$counter=$counter+1;
		}	
	for ($x = 0; $x <= $counter; $x++) 
	{
		if (strlen($sqlupdate[$x])>10)
		{
			db_query($sqlupdate[$x]);
		}
	}
	// return '<br>update for previous day SUCCESSFUL</br>';
	return '';
	*/
}
