<?php 
// ----------------------------------------------------------------
// Creator: Mizan(& Kvvaradha
// email:   admin@kvcodes.com
// Title:   Tutorial Hook For HRM
//detail_payslip.php
// ----------------------------------------------------------------
$page_security = 'SA_EMPL';
$path_to_root='../../..';
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/gl/includes/db/gl_db_trans.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/detail_employee_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
add_access_extensions();


$js = get_js_date_picker();
page(_($help_context = "PaySlip"), false, false, "", $js);
simple_page_mode(true);

if (isset($_GET['selected_emp_id'])){
	$_POST['selected_emp_id'] = $_GET['selected_emp_id'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}

$month = get_post('month','');
$year = get_post('year','');
$selected_emp_id = get_post('selected_emp_id');
if (list_updated('selected_emp_id')) {
	$_POST['empl_id'] = $selected_emp_id = get_post('selected_emp_id');
    clear_data();
	$Ajax->activate('details');
}

function clear_data(){
	unset($_POST['empl_id']);	
	unset($_POST['empl_name']);	
	unset($_POST['gross_salary']);
}

function employee_search()
{
	if (kv_db_has_employees()) 
	{
		start_table(TABLESTYLE_NOBORDER);
		start_row();
	    kv_hrm_year_list( _("Year:"), 'year', null);
		kv_hrm_months_list( _("Month:"), 'month', null);
		kv_employee_list_cells(_("Select an Employee: "), 'selected_emp_id', null,	_('New Employee'), true, check_value('show_inactive'));
		$new_item = get_post('selected_emp_id')=='';
		date_row(_("Date of Pay") . ":", 'date_of_pay');
		end_row();
		end_table();

		if (get_post('_show_inactive_update')) {
			$Ajax->activate('selected_emp_id');
			set_focus('selected_emp_id');
		}
	}
	else{
		hidden('selected_emp_id', get_post('selected_emp_id'));
	}
}

function employee_details()
{
	$myrow = kv_get_employee($_POST['empl_id']);

	//display_notification($myrow);
	//display_error($myrow);
	$_POST['leave_days']=rashed_get_no_leave($_POST['empl_id'],$_POST['month'],$_POST['year']);
	$_POST['late_days']=rashed_get_no_late($_POST['empl_id'],$_POST['month'],$_POST['year']);
	$_POST['OT_Bonus']=rashed_ot_bonus($_POST['empl_id'],$_POST['month'],$_POST['year']);
	$_POST['Attd_Bonus']=rashed_att_bonus($_POST['empl_id'],$_POST['month'],$_POST['year']);
	
	//display_notification($_POST['late_days']);
	//display_notification($myrow["empl_name"]);
	$_POST['empl_id'] = $myrow["empl_id"];			
	$_POST['empl_name'] = $myrow["empl_name"];
	$_POST['Designation'] = $myrow["Designation"];
	$gross = $_POST['emp_salary']  = $myrow["emp_salary"];	
	/*
	if (isset($_POST['emp_payslip_No']) && $_POST['emp_payslip_No']!=''){
		$result=rashed_get_empl_grosspay('Payslip', $_POST['empl_id']);
		//display_notification($result);
		$row = db_fetch_row($result);
		}
	else {
		$result=rashed_get_empl_grosspay('Template', $_POST['empl_id']);
		//display_notification($result);
		$row = db_fetch_row($result);
	 	}
	$TotalEarning = $_POST['TotalEarning']  =$row[0];
	$TotalDeduction = $_POST['TotalDeduction']  =$row[1];
	$NetPay = $_POST['NetPay']  =$row[2];
	*/
	start_table(TABLESTYLE2, "width=30%");
		
		table_section_title(_("Employee Informations"));			
		hidden('empl_id', $_POST['empl_id']);
		label_row(_("Employee Name:"), $_POST['empl_id'].'-'.$_POST['empl_name']);			
		label_row(_("Designation:"), $_POST['Designation']);

		text_row(_("Number of Leave days :"), 'leave_days', null, 2, 40);
		text_row(_("Number of Late days :"), 'late_days', null, 2, 40);
		label_row(_("Payslip No:"), $_POST['emp_payslip_No']);
		label_row(_("Payslip Status:"), $_POST['payment_status']);

		//submit_cells('RefreshInquiry', _("Show"),'',_('Show Results'), 'default');
		//$_POST['leave_days'] = $_POST['monthly_loan'] = 0; 

		table_section_title(_('Salary Information'));
		$results = emp_ext_pmt_str($_POST['empl_id']);
		$emp_str = array();
		//setting array of pmt_str_id wise ammount
		while ($result = db_fetch($results)) {
			$emp_str[$result["emp_pmt_str_id"]] = $result["emp_pmt_tmp_amt"];
		}
		$str = act_pmt_str();
		$i = 0;
		$status['total']=0;
		$status['deduct'] =0;
		while ($r = db_fetch($str)){
			$value = 0;
			$tds[$i] = '0';
			if ($emp_str[$r["emp_pmt_str_id"]] != null) {
				if ($r["emp_pmt_type"]=='Add') {
					$status['total'] += $emp_str[$r["emp_pmt_str_id"]];
				}else{
					$status['deduct'] += $emp_str[$r["emp_pmt_str_id"]];
				}
				$value = $emp_str[$r["emp_pmt_str_id"]];
				$tds[$i] = $emp_str[$r["emp_pmt_str_id"]];
			}
			$tbh[$i] = $r["emp_pmt_str_name"];
			label_row($r["emp_pmt_str_name"],$value);
			// text_row_ex(, $r["emp_pmt_str_id"], 50, 60,$title=null,$value);	
			$i++;	
		}
		text_row(_("Attendance Bonus :"), 'Attd_Bonus', null, 2, 40);
		text_row(_("Overtime Bonus :"), 'OT_Bonus', null, 2, 40);
		label_row('Total',$status['total']);
		label_row('Deduct',$status['deduct']);
		label_row('Paid',$status['total']-$status['deduct']);

	
	end_table();
	br();
	submit_center('Process_PaySlip', _("Process PaySlip"), true, '', 'default');
}

function existing_payslip()
{
	start_table(TABLESTYLE);
		$sqlqry="SELECT det.emp_payslip_id,ps.emp_payslip_no,det.emp_id,det.emp_pmt_str_id,emp_pmt_amt, date_of_pay, tmp.emp_pmt_str_name  FROM ".TB_PREF."emp_payslip_det det, ".TB_PREF."emp_payslip_no ps, ".TB_PREF."emp_pmt_str_tmp  tmp where det.emp_id='".$_POST['empl_id']."' and det.emp_payslip_No=ps.emp_payslip_No and ps.year='".$_POST['year']."' and ps.month='".$_POST['month']."' and tmp.emp_pmt_str_id=det.emp_pmt_str_id ";
		//display_error(kv_db_has_employee_payslip($_POST['year'],$_POST['month'], $_POST['empl_id']));
		//display_error($sqlqry);
		$employees = db_query($sqlqry);
		table_header(array("id","Slip No","Type","Amount","",""));
		while($employee = db_fetch($employees)) {
			start_row();
				label_cell($employee['emp_payslip_id']);
				label_cell($employee['emp_payslip_no']);
				label_cell($employee['emp_pmt_str_name']);
				$emp_payslip_No=$employee['emp_payslip_no'];
				$_POST['emp_payslip_No']=$emp_payslip_No;
				//emp_pmt_str_list( NULL, 'pmt_str-'.$employee['emp_payslip_id'].'-'.$employee['emp_pmt_str_id'], $employee['emp_pmt_str_id']);
				//text_cells(null, 'emp_pmt_amt-'.$employee['emp_payslip_id'], $employee['emp_pmt_amt'], 10, 10);	
				amount_cell($employee['emp_pmt_amt']);
				//rashed_button($employee['emp_payslip_id']);
				edit_button_cell("Edit".$employee['emp_payslip_id'], _("Edit"));
				delete_button_cell("Delete".$employee['emp_payslip_id'], _("Delete"));			
			end_row();
		}
			start_row();
				text_cells(null, 'emp_payslip_id_new','New', 10, 10);
				text_cells(null, 'emp_payslip_No_new',$emp_payslip_No, 10, 10);
				emp_pmt_str_list( NULL, 'pmt_str_new',0);
				text_cells(null, 'emp_pmt_amt_new', 0, 10, 10);	
				echo '<td colspan="2">';
					submit_center('AddNewPmt', _("Add"), true, '', 'default');
				echo '</td>';
			end_row();
 
	end_table();

}
function new_payslip()
{
	$sqlqry="SELECT tmp.emp_pmt_str_id,emp_pmt_tmp_amt, str_tmp.emp_pmt_str_name  FROM ".TB_PREF."emp_pmt_tmp tmp, ".TB_PREF."employee emp, ".TB_PREF."emp_pmt_str_tmp str_tmp  WHERE emp_id='".$_POST['selected_emp_id']."' and emp.salary_scale_id=tmp.scale_id and str_tmp.emp_pmt_str_id=tmp.emp_pmt_str_id ";
		//display_error(kv_db_has_employee_payslip($_POST['year'],$_POST['month'], $_POST['empl_id']));
		$employees = db_query($sqlqry);
		$_POST['num_row']= db_num_rows($employees);
		
	start_table(TABLESTYLE);
		table_header(array("Salary Scale Template",""));
		table_header(array("Type","Amount"));
		while($employee = db_fetch($employees)) {
			start_row();
				label_cell($employee['emp_pmt_str_name']);
				amount_cell($employee['emp_pmt_tmp_amt']);
				//emp_pmt_str_list( NULL, 'pmt_str-'.$employee['emp_id'].'-'.$employee['emp_pmt_str_id'], $employee['emp_pmt_str_id']);
				//text_cells(null, 'emp_pmt_amt'.$employee['emp_pmt_str_id'], $employee['emp_pmt_tmp_amt'], 10, 10);
			end_row();
		}
			start_row();	
				echo '<td colspan="2">';
					submit_center('Import', _("Import"), true, '', 'default');
				echo '</td>';
			end_row();
		 
	end_table();
}

function edit_table($id)
{
	start_table(TABLESTYLE2);
		$sqlqry="SELECT emp_payslip_id,emp_payslip_No,emp_pmt_str_id,emp_pmt_amt FROM ".TB_PREF."emp_payslip_det Where emp_payslip_id='".$id."'";
		$ps = db_query($sqlqry);
		while ($mypsrow=db_fetch($ps)) 
		{
			$_POST['emp_payslip_id']  = $mypsrow["emp_payslip_id"];
			$_POST['emp_payslip_No']  = $mypsrow["emp_payslip_No"];
			$_POST['emp_pmt_str_id']  = $mypsrow["emp_pmt_str_id"];
			$_POST['emp_pmt_amt']  = price_format($mypsrow["emp_pmt_amt"]);
			hidden('selected_id', $selected_id);
		}
		table_header(array("EDIT","ROW","","","",""));
			start_row();
			text_cells(null, 'emp_payslip_id',$_POST['emp_payslip_id'], 10, 10);
			text_cells(null, 'emp_payslip_No',$_POST['emp_payslip_No'], 10, 10);
			emp_pmt_str_list( NULL, 'emp_pmt_str_id', $_POST['emp_pmt_str_id']);
			text_cells(null, 'emp_pmt_amt',$_POST['emp_pmt_amt'], 10, 10);
			echo '<td colspan="2">';
				submit_center('SavePS', _("Save"), true, '', 'default');
			echo '</td>';
		end_row();
	end_table(1);
}
//*************FORM STARTS HERE ******************************
//*************FORM STARTS HERE ******************************
//*************FORM STARTS HERE ******************************
start_form();
div_start('details');
	employee_search();
	submit_center('Process_PaySlip_All', _("Process All"), true, '', 'default');
	$_POST['empl_id'] = $_POST['selected_emp_id'];
	//display_error($_POST['empl_id']);
	if (isset($selected_emp_id) && $selected_emp_id != '' ) 
	{ 
		$result = rashed_emp_payslip_result($_POST['year'],$_POST['month'], $_POST['empl_id']);
		//display_error($result);
		$num_rows=db_num_rows($result);
		if ($num_rows==0){
			$emp_payslip_No="";
		}
		else {
			$row = db_fetch_row($result);
			$emp_payslip_No= $row[0];
			$_POST['emp_payslip_No']=$emp_payslip_No;
			$_POST['payment_status']= $row[1];
		}
		//display_error($emp_payslip_No);
		$_POST['emp_payslip_No']=$emp_payslip_No;		
		employee_details();
		if($emp_payslip_No!=""){	
			existing_payslip();
		}
		else {new_payslip();
		}
	}
	br();
	br();
	
		

		if($selected_id != -1) 
		{
			if ($Mode == 'Edit') 
			{
				edit_table($selected_id);
			}
			if ($Mode == 'Delete') 
			{
				rashed_delete_payslip_det($selected_id);
				display_notification(_('Selected Payslip has been deleted'));
				$Mode = 'RESET';
			}

			if($Mode == 'RESET') 
			{
				$selected_id = -1;
			}
		}
	div_end(); 
end_form();

 if(get_post('RefreshInquiry')){
	//$Ajax->activate('gross_salary');
	//$Ajax->activate('leave_days');
	//$Ajax->activate('monthly_loan');
	//$Ajax->activate('totals_tbl');
	
}

if(get_post('Process_PaySlip_All')) {
	$ms="";
	$sql = "SELECT emp_id FROM 0_employee where inactive=0";
	$result = db_query($sql,"The employee payslip could not be added");
	begin_transaction();
	$c=1;
	$emp_payslip_id_all="";
	while ($myrow=db_fetch($result)){ 
		$pay_slip_no = rashed_add_edit_payslip_no($myrow['emp_id'],$_POST['year'],$_POST['month'],'Paid',$_POST['date_of_pay']);
		$emp_payslip_id = rashed_import_payslip_det($pay_slip_no,$myrow['emp_id']);
		$emp_payslip_id_all = $emp_payslip_id_all.$emp_payslip_id;
		/*
		$sql_sc="SELECT emp.salary_scale_id, sc.gl_debit,sc.gl_credit FROM ".TB_PREF."employee emp, ".TB_PREF."salaryscale sc where sc.scale_id=emp.salary_scale_id and emp.emp_id='".$myrow['emp_id']."' limit 1";
		$result_sc = db_query($sql_sc,"The employee payslip could not be added");
		$row_sc = db_fetch_row($result_sc);
		$gl_debit= $row_sc[1];
		$gl_credit= $row_sc[2];

		
		$result_earning=rashed_get_empl_grosspay('Template', $myrow['emp_id']);
		$row_earning = db_fetch_row($result_earning);
		$TotalEarning = $row_earning[0];
		$TotalDeduction = $row_earning[1];
		$NetPay = $row_earning[2];

		
		add_gl_trans(99, $pay_slip_no, $_POST['date_of_pay'], $gl_debit, 0,0, 'employee Salary #'.$pay_slip_no, $NetPay);
		add_gl_trans(99, $pay_slip_no, $_POST['date_of_pay'], $gl_credit, 0,0, 'employee Salary #'.$pay_slip_no, -$NetPay);
		$c=$c+1;
		$ms=$ms.", ".$pay_slip_no;
		*/
	}
	commit_transaction();
	//display_notification('GL updated transaction no:'.$ms);
	//display_notification('GL updated transaction no:'.$emp_payslip_id);
	display_notification("Inserted ".$emp_payslip_id);
	
}

if(get_post('Process_PaySlip')) {
	
	$sql="SELECT emp.salary_scale_id, sc.gl_debit,sc.gl_credit FROM ".TB_PREF."employee emp, ".TB_PREF."salaryscale sc where sc.scale_id=emp.salary_scale_id and emp.emp_id='".$_POST['empl_id']."' limit 1";
	$result = db_query($sql,"The employee payslip could not be added");
	$row = db_fetch_row($result);
	$gl_debit= $row[1];
	$gl_credit= $row[2];

	$pay_slip_no=$_POST['emp_payslip_No'];
	$sql_ps="SELECT payment_status FROM ".TB_PREF."emp_payslip_no where emp_payslip_no='".$pay_slip_no."'";
	$result_ps = db_query($sql_ps,"The employee payslip could not be added");
	$row_ps = db_fetch_row($result_ps);
	if ($row_ps[0]=='Paid'){
		display_notification('Already paid');
	}
	else
	{
	add_gl_trans(99, $pay_slip_no, $_POST['date_of_pay'], $gl_debit, 0,0, 'employee Salary #'.$_POST['empl_id'],  $_POST['NetPay']);
	add_gl_trans(99, $pay_slip_no, $_POST['date_of_pay'], $gl_credit, 0,0, 'employee Salary #'.$_POST['empl_id'], -$_POST['NetPay']);
	$pay_slip_no = rashed_add_edit_payslip_no($_POST['empl_id'],$_POST['year'],$_POST['month'],'Paid',$_POST['date_of_pay']);
	display_notification('GL updated transaction no:'.$pay_slip_no);
	}
}

if(get_post('AddNewPmt')){
	$pay_slip_id = rashed_add_payslip_det($_POST['emp_payslip_No_new'],$selected_emp_id, $_POST['pmt_str_new'], $_POST['emp_pmt_amt_new']);
	display_notification(' The Employee Payslip is added #' .$pay_slip_id);
	$Ajax->activate('details');
}
if(get_post('SavePS')){
	$pay_slip_id = rashed_edit_payslip_det($_POST['emp_payslip_id'],$_POST['emp_payslip_No'], $_POST['emp_pmt_str_id'], $_POST['emp_pmt_amt']);
	display_notification(' The Employee Payslip is edited #' .$pay_slip_id);
	$Ajax->activate('details');
}
if(get_post('Import')){
	$emp_payslip_No = rashed_add_edit_payslip_no($_POST['empl_id'],$_POST['year'],$_POST['month'],'Sent for Approval',$_POST['date_of_pay']);
	$_POST['emp_payslip_id']=$emp_payslip_No;
	//$emp_payslip_No='3';
	$emp_payslip_id = rashed_import_payslip_det($emp_payslip_No,$_POST['empl_id']);
	display_notification(' The Employee Payslip is inserted #' .$emp_payslip_id);
	$Ajax->activate('details');
}

end_page();


?>