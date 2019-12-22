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

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

page(_($help_context = "Payment Type"));
simple_page_mode(false);
$unit_id = '';
$dept_id = '';
if (isset($_POST['update_aeps']) && (isset($_POST['unit_id']) || isset($_POST['dept_id']))) {
	$unit_id = isset($_POST['unit_id']) ? $_POST['unit_id'] : '';
	$dept_id = isset($_POST['dept_id']) ? $_POST['dept_id'] : '';
	write_allEmployee_paymentString($unit_id,$dept_id);
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	$input_error = 0;
	$selected_id = isset($_POST['selected_id'])? $_POST['selected_id'] : false;
	if (strlen($_POST['emp_pmt_amount']) == 0 || $_POST['emp_pmt_amount'] == '') {
		$input_error = 1;
		display_error(_("The amount field cannot be empty."));
		set_focus('emp_pmt_amount');
	}
    if (!is_numeric($_POST['emp_pmt_amount'])) {
		$input_error = 1;
		display_error(_("Amount must be a number."));
		set_focus('emp_pmt_amount');
	}
	if (strlen($_POST['emp_pmt_str_name']) == 0 || $_POST['emp_pmt_str_name'] == '') {
		$input_error = 1;
		display_error(_("The name field cannot be empty."));
		set_focus('emp_pmt_str_name');
	}
	if ($input_error !=1) {
    	write_payment_string($_POST['selected_id'], $_POST['emp_pmt_str_name'], $_POST['emp_pmt_str_name_bd'], $_POST['emp_pmt_type'], $_POST['emp_pmt_amount_type'],
    	 $_POST['emp_pmt_amount'],$_POST['activity']);
		if($selected_id != '')
			display_notification(_('Selected Payment String has been updated'));
		else
			display_notification(_('New Payment String has been added'));
		$Mode = 'RESET';
	}
}

//--------------------------------------------------------------------------

if ($Mode == 'Delete') {

	if (payment_string_used(!$selected_id))
		display_error(_("This Payment String cannot be deleted."));
	else {
        delete_payment_string($selected_id);
		display_notification(_('Selected Payment String has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET') {
    
	$selected_id = $_POST['selected_id'] = $_POST['emp_pmt_str_name'] = $_POST['emp_pmt_str_name_bd'] = $_POST['emp_pmt_type'] = $_POST['emp_pmt_amount_type'] = $_POST['emp_pmt_amount'] = $_POST['activity'] ='';
}
 //display_notification(get_payment_string(false, check_value('show_inactive')));

//--------------------------------------------------------------------------

$result = db_query(get_payment_string(false, check_value('show_inactive')));

start_form();
start_table(TABLESTYLE);
$th = array(_('Id'), _('Payment String Name'), _('Payment String Name (Bangla)'), _('Payment String Type'),
 _('Payment Amount Type'),_('Payment Amount'),_('Activity'), "", "");

inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);
	$activity = $myrow['activity'] == 0 ? _('Active') : _('Inactive');
	label_cell($myrow["emp_pmt_str_id"]);
	label_cell($myrow["emp_pmt_str_name"]);
	//label_cell($myrow["emp_pmt_str_name_bd"]);
	echo '<td>'.html_specials_encode($myrow["emp_pmt_str_name_bd"]).'</td>';
	label_cell($myrow["emp_pmt_type"]);
	label_cell($myrow["emp_pmt_amount_type"]);
	amount_cell($myrow["emp_pmt_amount"]);
	label_cell($activity);
	inactive_control_cell($myrow["emp_pmt_str_id"], $myrow["inactive"], 'emp_pmt_str_tmp', 'inactive');
 	edit_button_cell("Edit".$myrow["emp_pmt_str_id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["emp_pmt_str_id"], _("Delete"));
    
	end_row();
}

inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if ($selected_id != '') {
	
 	if ($Mode == 'Edit') {
		
		$myrow = get_payment_string($selected_id);
		$_POST['emp_pmt_str_name']  = $myrow["emp_pmt_str_name"];
		$_POST['emp_pmt_str_name_bd']  = $myrow["emp_pmt_str_name_bd"];
		$_POST['emp_pmt_type'] = $myrow["emp_pmt_type"];
		$_POST['emp_pmt_amount_type']  = $myrow["emp_pmt_amount_type"];
		$_POST['emp_pmt_amount']  = $myrow["emp_pmt_amount"];
		$_POST['activity']  = $myrow["activity"];
		hidden('selected_id', $myrow["emp_pmt_str_id"]);
	}
}


text_row(_("Payment String Name:"), 'emp_pmt_str_name', null, 40, 50);
text_row(_("Payment String Name (Bangla):"), 'emp_pmt_str_name_bd', null, 40, 50);
paymentstringtype('Payment String Type', 'emp_pmt_type', $_POST['emp_pmt_type'],'Select');
// paymentstringtype(_('Paymrnt String Type'), 'emp_pmt_type', $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);
// paymentstringamounttype(_('Paymrnt String Amount Type'), 'emp_pmt_amount_type', $id, $selected_id, $all_option=false, $submit_on_change=false, $show_inactive=false, $valfield=null, $namefield=null);
text_row(_("Payment String Amount:"), 'emp_pmt_amount', null, 40, 50);
paymentstringamounttype('Amount Type', 'emp_pmt_amount_type', $_POST['emp_pmt_amount_type'],'Select');
// check_cells(_('Click if inactive').':', 'inactive');
label_row(_('Activity').':', radio(_('Active'), 'activity', 0,1).'&nbsp;&nbsp;'.radio(_('Inactive'), 'activity', 1));

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();

br(3);

start_form();
	start_table();
		start_row();
			input_select_box('Select Unit ','unit_id','unit','UnitID', 'UnitName',$unit_id);
			input_select_box('Department','dept_id','department','dept_id', 'dept_name',$dept_id);	
		end_row();
	end_table();
	br(2);
	btn_submit_select('submit','update_aeps','','','Update All Employee Payment String');
end_form();

end_page();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	tag = document.getElementsByTagName("input");
	setInterval(function(){
		i=0;
		while(tag[i]){
			// if (tag[i].name == 'starting_time' || tag[i].name == 'ending_time') {
			// 	tag[i].type = 'time';
			// }
			i++;
		}
	}, 100);
	
</script>