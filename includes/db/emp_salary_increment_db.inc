<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|   daily_attendance_db.inc                              |
\=======================================================*/

//-----------------------------------------------------------------
function write_increment_salary($card = false,$month=false,$year=false, $increment=0)
{
	if ($increment==0) {
		$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
		$emp_id = db_fetch(db_query($sql));

		$sql = "SELECT * FROM 0_emp_salary_increment where emp_id=".$emp_id['emp_id']." AND year=".$year." AND month=".$month."";
		$inc = db_fetch(db_query($sql));

		$sql = "UPDATE 0_employee SET emp_salary = (emp_salary-".$inc['amount'].") where emp_card_no=".$card;
		db_query($sql);

		$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
		$update = db_fetch(db_query($sql));

		write_template_salary($emp_id['emp_id'],$update['emp_salary']);

		$sql = "DELETE FROM 0_emp_salary_increment where id=".$inc['id'];
		$inc = db_query($sql);
		display_notification("Succesfully Deleted!!");

	}
	else{

	$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
	$salary = db_fetch(db_query($sql));
	//display_notification($sql);

	$sql = "SELECT count(*) as t FROM 0_emp_salary_increment where emp_id=".$salary['emp_id']." AND year=".$year." AND month=".$month."";

	$count = db_fetch(db_query($sql));
	if ($count['t']>0) {

		$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
		$emp_id = db_fetch(db_query($sql));
		$sql = "SELECT * FROM 0_emp_salary_increment where emp_id=".$emp_id['emp_id']." AND year=".$year." AND month=".$month."";
		$inc = db_fetch(db_query($sql));
		$faltu = $emp_id['emp_salary']-$inc['amount']+$increment;
		$sql = "UPDATE 0_employee SET emp_salary = ".$faltu." where emp_card_no=".$card;
		db_query($sql);

		$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
		$update = db_fetch(db_query($sql));

		write_template_salary($emp_id['emp_id'],$update['emp_salary']);

		$sql = "UPDATE 0_emp_salary_increment SET amount = ".$increment." where emp_id=".$salary['emp_id']." AND year=".$year." AND month=".$month."";
		db_query($sql);

		display_notification("Updated Successfully");	
	}
	else
	{


	$sql = "UPDATE 0_employee SET emp_salary = (emp_salary+".$increment.") where emp_card_no=".$card;
	db_query($sql);
    

		$sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount, year, month) VALUES (".$salary['emp_id']." , ".$increment." , ".$year." , ".$month.")";

		db_query($sql);

	 	$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
		$update = db_fetch(db_query($sql));

		write_template_salary($update['emp_id'],$update['emp_salary']);


	 	display_notification('Successfully Added..!');
	}



}

}

function get_pre_increment_list($month,$year,$card)
{
	$sql = "SELECT * FROM 0_employee where emp_card_no=".$card;
	$emp_id = db_fetch(db_query($sql));

	$sql = "SELECT i.*,e.emp_card_no,concat(e.emp_first_name,'',IFNULL(e.emp_last_name,'')) name FROM 0_employee e LEFT JOIN 0_emp_salary_increment i on i.emp_id = e.emp_id WHERE e.emp_id=".$emp_id['emp_id']." AND i.month=".$month." and i.year=".$year." ";
	
	return $sql;


}



