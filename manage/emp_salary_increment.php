<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   daily_attendance.php                                 |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';
$proot  = __DIR__;

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");
create_access_ontime($proot);
//--------------------------------------------------------------------------

$year = isset($_POST['year']) ? $_POST['year'] : date('Y');
$mon = date('n') == 1 ? 12 : date('n');
$month = isset($_POST['month']) ? $_POST['month'] : $mon;
$card = isset($_POST['card_no']) ? $_POST['card_no'] : '';
$Increment = isset($_POST['inc_amt']) ? $_POST['inc_amt'] : '';


if (isset($_POST['add'])) {
	
	write_increment_salary($card,$month,$year,$Increment);
		
	


}

if ($Mode == 'Delete') {

	var_dump('working');
}
// $EmpAttds=db_query($sql);



//--------------------------------------------------------------------------

page(_($help_context = "Emp Salary Increment"), false, false, "", $js);
simple_page_mode(false);

//-------------------Searching form-------------------------


start_form();
start_table(TABLESTYLE_NOBORDER);

kv_hrm_year_list( _("Year:"), 'year', $year);
kv_hrm_months_list( _("Month:"), 'month', $month);
input_text_td('Card NO','card_no');
input_text_td('Increment Amount','inc_amt');
hidden('id',$selected_id);

end_table(1);

start_table(TABLESTYLE_NOBORDER);

start_row();
btn_submit_search('submit','add','','','Submit');
end_row();

end_table(1);

end_form();


start_table(TABLESTYLE);
end_table(1);
//--------------------Attendance Table Code-----------------------
if($card){
$increment = get_pre_increment_list($month,$year,$card);

$Emp_Presal = db_query($increment);

start_form();
start_table(TABLESTYLE2, "",'0','5');

$th = array(_("SL"), _("Name"), _("Card NO"), _("Month"), _("Year"),_("Amount"));
// inactive_control_column($th);
table_header($th);

$k = 0;
$i = 1;
while ($myrow = db_fetch($Emp_Presal)) {
	alt_table_row_color($k);
		hidden('emp_ids[]',$myrow["emp_id"]);
		label_cell($i);
		$name = explode(' ', $myrow["name"]);
		label_cell("$name[0] $name[1]");
		label_cell($myrow["emp_card_no"]);
		label_cell($myrow["month"]);
		label_cell($myrow["year"]);
		label_cell($myrow["amount"]);
		

	end_row();

	$i++;
	$k++;
	$selected_id = $myrow['id'];
}

end_table(1);

if ($totalrows > 0) {
start_table();
start_row();
hidden('page',$PAGENO);
if (isset($_POST['card_no'])) {
	hidden('card_no',$_POST['card_no']);
}
hidden('month',$month);
hidden('year',$year);
hidden('dept_id',$dept_id );
hidden('unit_id',$unit_id );
hidden('id',$selected_id);
btn_submit_Add('submit','update','','','Update');
end_row();
end_table();
}

end_form();

}










//--------------------Attendance Table-----------------------



//-------------------------------------------------Pagignation---------------------------


end_page();




//---------------------------------------------------


?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	tag = document.getElementsByTagName("input");
	setInterval(function(){
		i=0;
		while(tag[i]){
			if (tag[i].name == 'starting_time' || tag[i].name == 'ending_time') {
				tag[i].type = 'time';
			}
			i++;
		}
	}, 100);
	
</script>