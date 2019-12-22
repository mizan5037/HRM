<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-07-2017                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_HRSETUP';
$path_to_root  = '../../..';
$proot = __DIR__;
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

page(_($help_context = "Leave Ledger"));
simple_page_mode(false);

if (isset($_POST['create_ledger_employee'])) {
       
	if(strlen($_POST['leave_from']) == 0 && $_POST['leave_from'] == '' && strlen($_POST['leave_to']) == 0 && $_POST['leave_to'] == '' && strlen($_POST['approved_from']) == 0 && $_POST['approved_from'] == '' && $_POST['pay_method'] == 'p') {
		display_error( _("Field cannot be empty."));
		set_focus('leave_to');
		set_focus('leave_from');
	}
	else{
   	  if ($_POST['pay_method'] == 'wp') {

   	     $leave = date_create($_POST['leave_from']);
   	     $leave_to = date_create($_POST['leave_to']);
         
   	     if (isset($leave) && isset($leave_to)) {

   	     	$diff  = date_diff($leave,$leave_to);
			$diff  = $diff->format("%a");
			$difference = $diff+1;
			$status = 1;
			$leave_to = $_POST['leave_to'];
			wp_payment($_POST['employee_id'],$_POST['pay_method'],$_POST['leave_from'],$leave_to,$_POST['approved_from'],$_POST['approved_to'],$status,$difference);

   	     	
   	     }
   	     if ($leave) {

   	     	$diff  = date_diff($leave,$leave);
			$diff  = $diff->format("%a");
			$difference = $diff+1;
			$status = 0;
			$leave_to = '0000-00-00';
			//wp_payment($_POST['employee_id'],$_POST['pay_method'],$_POST['leave_from'],$leave_to,$status,$difference);
   	     }
   	     display_notification(_(' employee with pay day has been added'));
   	  }
   	  else{
	 	$date1 = date_create($_POST['approved_from']);
        
		$date2 = date_create($_POST['approved_to']);
		$diff  = date_diff($date1,$date2);
		$diff  = $diff->format("%a");
		if (isset($_POST['approved_from'])) {
			if($_POST['approved_to']) {
				$status = 1;
				$difference = $diff+1;
			}
		}
		else{
			$status = 0;
		}

		 $cal = leave_calculation($_POST['leave_type'],$_POST['employee_id']);
		 $result = db_fetch($cal);		 
		 $total_leave = total_leave($_POST['leave_type']);
		 $t = db_fetch($total_leave);
		$leave_name = get_leave_type($_POST['leave_type']);
		$total = db_fetch($leave_name);
		if ($result['l']<=$t['t']) {
		$status = write_ledger($_POST['employee_id'],$_POST['leave_type'],$_POST['pay_method'],$_POST['leave_from'],$_POST['leave_to'],$_POST['approved_from'],$_POST['approved_to'],$status,$_POST['approved_by'],$difference,$_POST['designation'],$_POST['reason'],$_POST['reliever']);
		display_notification(_('New employee ledger has been added'));
		$t['t'] = '';
		$_POST['employee_id'] = '';
		}

		else
		{
			display_notification(_('Total number of '.$total['StatusFullName'].'  have used by this employee'));
		}
	 }
}
		
	
}

if ($Mode=='UPDATE_ITEM') {

		$date1 = date_create($_POST['leave_approved_from']);
		$date2 = date_create($_POST['leave_approved_to']);
		$diff  = date_diff($date1,$date2);
		$diff  = $diff->format("%a");

		if (isset($_POST['leave_approved_from'])) {
			if ($_POST['leave_approved_to']) {
				$status = 1;
				$difference = $diff+1;
			}
		}
		else{
			$status = 0;
		}
      
		update_ledger($selected_id,$_POST['leave_type'],$_POST['pay_method'],$_POST['leave_requested_from'], $_POST['leave_requested_to'], $_POST['leave_approved_from'], $_POST['leave_approved_to'],$status, $_POST['approved_by'], $_POST['proximity_id'], $difference, $_POST['designation'], $_POST['reason'],$_POST['reliever']);
	
		
			display_notification(_('Selected employee ledger query has been updated'));
    		
		$Mode = 'RESET';

		

	
}

if ($Mode == 'Delete') {

	
		delete_ledger($selected_id);
		display_notification(_('Selected ledger has been deleted'));

	
	$Mode = 'RESET';


}
if($Mode == 'RESET')
{
	$selected_id=$_POST['emp_id']= $_POST['name'] =$_POST['leave_type'] =$_POST['leave_from']  = $_POST['leave_to'] = $_POST['approved_from']=$_POST['approved_to']  = $_POST['approval_status'] = $_POST['approved_by']=$_POST['proximity_id']=$_POST['designation'] = $_POST['reason']=$_POST['reliever']=$_POST['total_day']=$difference='';
}

	
start_form();

start_table(TABLESTYLE2);

text_row_ex(_("Enter Card Number:"), 'name', 50, 60);

end_table(1);
// submit_add_or_update_center($selected_id == '', 'Search', 'both');
btn_submit_search('submit','search','','','Search');
// submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_form();





if (isset($_POST['name'])){


	start_table(TABLESTYLE);
	$th = array(_("Id"), _("Name"), _("Card Number"), _("Designation"), "");
	inactive_control_column($th);

	table_header($th);
	$result = get_emp($_POST['name'], $_POST['employee_id'], check_value('show_inactive'));
	$k = 0;
	while ($myrow = db_fetch($result)) {
		start_form();
		alt_table_row_color($k);
		label_cell($myrow["emp_id"]);
		label_cell($myrow["emp_first_name"]);
		label_cell($myrow['emp_card_no']);
		label_cell($myrow["Designation"]);
		hidden('employee_id', $myrow["emp_id"]);
		//hidden('emp_proximity_id', $myrow["emp_proximity_id"]);
		btn_submit_select('submit','search','','','Select');
		end_row();
		end_form();
	}


	end_table(1);

}

start_form();
if(strlen($_POST['name'])) {
	start_table(TABLESTYLE);
	$th = array(_("Employee Name"),_("Leave Type"),_("Requested Leave From"),_("Requested Leave To"),_("Approved From"),_("Approved To"),_("Approved Status"),_("Approved By"),_("Proximity Id"), _("Total Day"), _("Designation"), _("Reason"), _("Reliever"),"","");
	inactive_control_column($th);
	table_header($th);

	$employee_n = get_employee($_POST['name']);

	$employee_name = db_fetch($employee_n);
 	
	$result = employee_ledger($_POST['name']);
	$k = 0;
	while ($myrow = db_fetch($result)) {
		 alt_table_row_color($k);
		    $name = $myrow["emp_id"];
		    $nam = get_employee_information($name);
			$nam = db_fetch($nam);
			label_cell($employee_name["emp_first_name"]);
			
			$type = $myrow['leave_type'];

			$t = get_leave_type($type);
			$t = db_fetch($t);
			 
			label_cell($t['StatusFullName']);
			label_cell($myrow['leave_requested_from']);
			label_cell($myrow['leave_requested_to']);
			label_cell($myrow['leave_approved_from']);
			label_cell($myrow['leave_approved_to']);
				if ($myrow['approval_status']==1) {
				label_cell('Approved');
			}

			else{
				label_cell('Not Approved');
			}
			
			label_cell($myrow['approved_by']);
			label_cell($myrow['proximity_id']);
			label_cell($myrow['total_day']);
			label_cell($myrow['designation']);
			label_cell($myrow['reason']);
			label_cell($myrow['reliever']);
			edit_button_cell("Edit".$myrow["id"], _("Edit"));
			delete_button_cell("Delete".$myrow["id"], _("Delete"));
			end_row();
	}
	//inactive_control_row($th);
	end_table(1);

	end_form();
}
//var_dump($selected_id);
if($selected_id != '') {
	
 	if ($Mode == 'Edit') {
		$myrow = get_ledger($selected_id);
  		$_POST['employee_id']  = '';
        $_POST['id']  = $myrow["id"];
		$_POST['emp_id']  = $myrow["emp_id"];
		$_POST['leave_type']  = $myrow["leave_type"];
		$_POST['pay_method']  = $myrow["pay_method"];
		$_POST['leave_requested_from']  = $myrow["leave_requested_from"];
		$_POST['leave_requested_to']  = $myrow["leave_requested_to"];
		$_POST['leave_approved_from']  = $myrow["leave_approved_from"];
		$_POST['leave_approved_to']  = $myrow["leave_approved_to"];
		$_POST['approval_status']  = $myrow["approval_status"];
		$_POST['approved_by']  = $myrow["approved_by"];
		$_POST['proximity_id']  = $myrow["proximity_id"];
		$_POST['total_day']  = $myrow["total_day"];
		$_POST['designation']  = $myrow["designation"];
		$_POST['reason']  = $myrow["reason"];
		$_POST['reliever']  = $myrow["reliever"];
		hidden('selected_id', $myrow['id']);
 	}
}

if (isset($_POST['id'])) {
	$id = $_POST['id'];
	
     $employee_id=$_POST['proximity_id'];

     $employee_n = get_employee_info($employee_id);

	$employee_name = db_fetch($employee_n);
	//var_dump($r);

	if (strlen($_POST['proximity_id']>0)) {

		start_form();
		start_table(TABLESTYLE2);
		     label_row('Employee',$employee_name['fullName']);
		     label_row('Proximity Id',$employee_name['emp_proximity_id']);
		   
		    
			
			leavetype('Leave Type', 'leave_type', $_POST['leave_type'],'Select');
		    payment_method('Payment Method Type', 'pay_method', $_POST['pay_method'],'Select');
			

			text_requested_leave_from('leave_requested_from');
			text_requested_leave_to('leave_requested_to');
			text_approved_from('leave_approved_from');
			text_approved_to('leave_approved_to');

			
			text_row_ex(_("Approved By:"), 'approved_by', 50, 60);
			
			text_row_ex(_("Designation :"), 'designation', 50, 60);
			text_row_ex(_("Reason :"), 'reason', 50, 60);
			text_row_ex(_("Reliever :"), 'reliever', 50, 60);

		hidden('id',$selected_id);
		//var_dump($selected_id);
		end_table(1);
		submit_add_or_update_center($selected_id == '', '', 'both');
		end_form();
	}
		

	
	if(update_emp_ext_ledger($id) != null){
		$results = update_emp_ext_ledger($id);
		$emp_str = array();
		

		
		start_form();
		
		start_table(TABLESTYLE);
		$th = array(_("Employee Name"),_("Leave Type"),_(" Requested Leave From"),_(" Requested Leave To"),_("Leave Approved From"),_("Leave Approved To"),_("Approval Status"),_("Approved By"),_("Proximity Id"), _("Total Day"), _("Designation"), _("Reason"), _("Reliever"), "", "");
		
		table_header($th);

		while ($r = db_fetch($results)){
		 alt_table_row_color($k);
		 	 $name = $r["emp_id"];
		    $nam = get_employee_information($name);
			$nam = db_fetch($nam);
			label_cell($nam["fullName"]);
			$type = $r['leave_type'];

			 $t = get_leave_type($type);
			  $t = db_fetch($t);
			 
			label_cell($t['StatusFullName']);

			
			label_cell($r['leave_requested_from']);
			label_cell($r['leave_requested_to']);
			label_cell($r['leave_approved_from']);
			label_cell($r['leave_approved_to']);
				if ($r['approval_status']==1) {
				label_cell('Approved');
			}

			else{
				label_cell('Not Approved');
			}
			
			label_cell($r['approved_by']);
			label_cell($r['proximity_id']);
			label_cell($r['total_day']);
			label_cell($r['designation']);
			label_cell($r['reason']);
			label_cell($r['reliever']);
			edit_button_cell("Edit".$r["id"], _("Edit"));
			delete_button_cell("Delete".$r["id"], _("Delete"));
			end_row();
		}
		
end_table(1);


end_form();


	}
}

$_POST['leave_from'] = '';
$_POST['leave_to'] = '';

if(strlen($_POST['employee_id'])>0) {
	$empId = $_POST['employee_id'];

	$employee_n = get_employee_information($empId);
	$employee_info = db_fetch($employee_n);
	start_form();
	start_table(TABLESTYLE2);
	label_row('Employee',$employee_info['fullName']);
	label_row('Proximity Id',$employee_info['emp_proximity_id']);
	leavetype('Leave Type', 'leave_type', $_POST['leave_type'],'Select');
	payment_method('Payment Method Type', 'pay_method', $_POST['pay_method'],'Select');
	
	requested_leave_from();
	requested_leave_to();
	approved_from();
	approved_to();
	text_row_ex(_("Approved By:"), 'approved_by', 50, 60);
	text_row_ex(_("Designation :"), 'designation', 50, 60);
	text_row_ex(_("Reason :"), 'reason', 50, 60);
	text_row_ex(_("Reliever :"), 'reliever', 50, 60);
	hidden('employee_id',$empId);
	end_table(1);
	btn_submit_Add($type='submit',"create_ledger_employee",$id,$value,$title='Add');
	// btn_submit_select("submit","create_salary_temp",$id,$value,'Select');
	end_form();
	//end total salary form
	
		if(emp_ext_ledger($empId) != null){
		$results = emp_ext_ledger($empId);

		

		
		start_form();

		start_table(TABLESTYLE);
		$th = array(_("Employee Name"),_("Leave Type"),_(" Leave From"),_("Requested Leave To"),_("Approved From"),_("Approved To"),_("Approved Status"),_("Approved By"),_("Proximity Id"), _("Total Day"), _("Designation"), _("Reason"), _("Reliever"), "", "");
		inactive_control_column($th);
		table_header($th);
		
		while ($r = db_fetch($results)){
		 alt_table_row_color($k);
		 	label_cell($employee_info['fullName']);
			 
			 $type = $r['leave_type'];

			 $t = get_leave_type($type);
			 $t = db_fetch($t);
			 
			label_cell($t['StatusFullName']);
			
			label_cell($r['leave_requested_from']);
			label_cell($r['leave_requested_to']);
			label_cell($r['leave_approved_from']);
			label_cell($r['leave_approved_to']);
			if ($r['approval_status']==1) {
				label_cell('Approved');
			}

			else{
				label_cell('Not Approved');
			}
			
			label_cell($r['approved_by']);
			label_cell($r['proximity_id']);
			label_cell($r['total_day']);
			label_cell($r['designation']);
			label_cell($r['reason']);
			label_cell($r['reliever']);
		    //inactive_control_cell($r["emp_id"], $r["inactive"], 'department', 'emp_id');
			edit_button_cell("Edit".$r["id"], _("Edit"));
			delete_button_cell("Delete".$r["id"], _("Delete"));
			end_row();
		}
	
end_table(1);


end_form();


	}

}
end_page();
