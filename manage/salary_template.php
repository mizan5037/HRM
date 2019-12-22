<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_HRSETUP';
$path_to_root  = '../../..';

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");

//--------------------------------------------------------------------------

page(_($help_context = "Salary Scales"));
simple_page_mode(true);


function existing_payslip()
{
	start_table(TABLESTYLE);
		$sqlqry="SELECT tmp.emp_pmt_tmp_id,tmp.scale_id,tmp.emp_pmt_str_id,tmp.emp_pmt_tmp_amt, str_tmp.emp_pmt_str_name, str_tmp.emp_pmt_type FROM ".TB_PREF."emp_pmt_tmp tmp, ".TB_PREF."emp_pmt_str_tmp str_tmp where str_tmp.emp_pmt_str_id=tmp.emp_pmt_str_id and scale_id='".$_POST['SalaryScaleId']."'";
		//display_error($sqlqry);
		$salary_scale = db_query($sqlqry);
		table_header(array("id","Head","Amount","Type","",""));
		while($salary_scale_row = db_fetch($salary_scale)) {
			start_row();
				label_cell($salary_scale_row['emp_pmt_tmp_id']);
				label_cell($salary_scale_row['emp_pmt_str_name']);
				//label_cell($salary_scale_row['emp_pmt_str_id']);
				$emp_pmt_tmp_id=$salary_scale_row['emp_pmt_tmp_id'];
				$_POST['emp_pmt_tmp_id']=$emp_pmt_tmp_id;	
				amount_cell($salary_scale_row['emp_pmt_tmp_amt']);
				label_cell($salary_scale_row['emp_pmt_type']);
				edit_button_cell("Edit".$salary_scale_row['emp_pmt_tmp_id'], _("Edit"));
				delete_button_cell("Delete".$salary_scale_row['emp_pmt_tmp_id'], _("Delete"));			
			end_row();
		}
			start_row();
				text_cells(null, 'emp_pmt_tmp_id_new','New', 10, 10);
				emp_pmt_str_list( NULL, 'emp_pmt_str_id_new',0);
				text_cells(null, 'emp_pmt_amt_new', 0, 10, 10);	
				echo '<td colspan="3">';
					submit_center('AddNewPmt', _("Add"), true, '', 'default');
				echo '</td>';
			end_row();
 
	end_table();

}
function edit_table($id)
{
	display_error($id);
	start_table(TABLESTYLE2);
		$sqlqry="SELECT tmp.emp_pmt_tmp_id,tmp.scale_id,tmp.emp_pmt_str_id,tmp.emp_pmt_tmp_amt, str_tmp.emp_pmt_str_name, str_tmp.emp_pmt_type FROM ".TB_PREF."emp_pmt_tmp tmp, ".TB_PREF."emp_pmt_str_tmp str_tmp where str_tmp.emp_pmt_str_id=tmp.emp_pmt_str_id and emp_pmt_tmp_id='".$id."'";
		$ps = db_query($sqlqry);
		while ($mypsrow=db_fetch($ps)) 
		{
			$_POST['emp_pmt_tmp_id']  = $mypsrow["emp_pmt_tmp_id"];
			$_POST['emp_pmt_str_id']  = $mypsrow["emp_pmt_str_id"];
			$_POST['emp_pmt_tmp_amt']  = price_format($mypsrow["emp_pmt_tmp_amt"]);
			hidden('selected_id', $selected_id);
		}
		table_header(array("EDIT","ROW","","","",""));
			start_row();
			text_cells(null, 'emp_pmt_tmp_id',$_POST['emp_pmt_tmp_id'], 10, 10);
			emp_pmt_str_list( NULL, 'emp_pmt_str_id', $_POST['emp_pmt_str_id']);
			text_cells(null, 'emp_pmt_tmp_amt',$_POST['emp_pmt_tmp_amt'], 10, 10);
			echo '<td colspan="2">';
				submit_center('SavePS', _("Save"), true, '', 'default');
			echo '</td>';
		end_row();
	end_table(1);
}


//--------------------------------------------------------------------------

start_form();

	
	if (db_has_salary_scale()) {
	    
		start_table(TABLESTYLE_NOBORDER);
		start_row();
	    
		salaryscale_list_cells(null, 'SalaryScaleId', null, _('Select salary scale'), true, check_value('show_inactive'));
		check_cells(_("Show inactive:"), 'show_inactive', null, true);
	    
		end_row();
		end_table(1);	
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('SalaryScaleId');
			set_focus('SalaryScaleId');
		}

		existing_payslip();
		if($selected_id != -1) 
		{
			if ($Mode == 'Edit') 
			{
				edit_table($selected_id);
			}
			if ($Mode == 'Delete') 
			{
				rashed_delete_payslip_tmp($selected_id);
				display_notification(_('Selected Payslip has been deleted'));
				$Mode = 'RESET';
			}

			if($Mode == 'RESET') 
			{
				$selected_id = -1;
			}
		}
	} 
	else {
		hidden('SalaryScaleId');
		display_note(_('Define Salary Scales first.'));
	}


if(get_post('AddNewPmt')){
	$pay_slip_id = rashed_add_payslip_tmp($_POST['SalaryScaleId'], $_POST['emp_pmt_str_id_new'], $_POST['emp_pmt_amt_new']);
	display_notification(' The Employee Payslip is added #' .$pay_slip_id);
	$Ajax->activate('details');
}
if(get_post('SavePS')){
	$pay_slip_id = rashed_edit_payslip_tmp($_POST['emp_pmt_tmp_id'], $_POST['emp_pmt_str_id'], $_POST['emp_pmt_tmp_amt']);
	display_notification(' The Employee Payslip is edited #' .$pay_slip_id);
	$Ajax->activate('details');
}
end_form();
end_page();
