<?php

function get_increment_process_status($month, $year)
{

    
	$sql = "SELECT * FROM " . TB_PREF . "increment_process_status where month = '" . $month . "' and year = '" . $year . "'";
	// display_notification($sql);
	$result = db_query($sql, _('Could not get Payslip Process Status data.'));
	return $result;
}

function delete_increment($month = false, $year = false)
{
	// echo $month,$year;
	if ($month != false && $year != false) {

	
	}
}

function third_floor($month = false, $year = false)
{
   
  $sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount,year,month) SELECT emp_id,round((emp_salary-1850)*0.05),$year,$month FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=1 and department_id not in (21,22,23)";
  db_query($sql);

    $sql = "UPDATE 0_employee SET emp_salary = (emp_salary+round((emp_salary-1850)*0.05)) WHERE emp_hiredate<'2019-01-01' and UnitID=1  and inactive=0" ;
    $inc = db_query($sql);

    $increment = "SELECT emp_id,emp_salary FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=1 and department_id not in (21,22,23)";
    $increment = db_query($increment);
   while($myrow = db_fetch($increment)){

     write_template_salary($myrow['emp_id'],$myrow['emp_salary']);

     //echo $myrow['emp_id']." ".$myrow['emp_salary']."<br>";


    }

    $sql = "INSERT INTO " . TB_PREF . "increment_process_status (step, status, month, year) VALUES ('third_floor', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));

}

function fifth_floor($month = false, $year = false)
{
    //display_notification($unit);
    //display_notification($year);

    $sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount,year,month) SELECT emp_id,round((emp_salary-1850)*0.05),$year,$month FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=2 and department_id not in (21,22,23)";
    db_query($sql);

    $sql = "UPDATE 0_employee SET emp_salary = (emp_salary+round((emp_salary-1850)*0.05)) WHERE emp_hiredate<'2019-01-01' and UnitID=2  and inactive=0 ORDER BY `0_employee`.`emp_hiredate` DESC ";

    $inc = db_query($sql);


 

   //display_notification($sql);

    $increment = "SELECT emp_id,emp_salary FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=2 and department_id not in (21,22,23)";
    $increment = db_query($increment);
   while($myrow = db_fetch($increment)){

     write_template_salary($myrow['emp_id'],$myrow['emp_salary']);

     //echo $myrow['emp_id']." ".$myrow['emp_salary']."<br>";


    }

    $sql = "INSERT INTO " . TB_PREF . "increment_process_status (step, status, month, year) VALUES ('fifth_floor', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));

}
function six_floor($month = false, $year = false)
{
    //display_notification($unit);
    //display_notification($year);

    $sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount,year,month) SELECT emp_id,round((emp_salary-1850)*0.05),$year,$month FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=3 and department_id not in (21,22,23)";
    db_query($sql);


    $sql = "UPDATE 0_employee SET emp_salary = (emp_salary+round((emp_salary-1850)*0.05)) WHERE emp_hiredate<'2019-01-01' and UnitID=3  and inactive=0 ORDER BY `0_employee`.`emp_hiredate` DESC ";

    $inc = db_query($sql);


   //display_notification($sql);

    $increment = "SELECT emp_id,emp_salary FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=3 and department_id not in (21,22,23)";
    $increment = db_query($increment);
   while($myrow = db_fetch($increment)){

     write_template_salary($myrow['emp_id'],$myrow['emp_salary']);

     //echo $myrow['emp_id']." ".$myrow['emp_salary']."<br>";


    }

    $sql = "INSERT INTO " . TB_PREF . "increment_process_status (step, status, month, year) VALUES ('six_floor', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));

}

function eight_floor($month = false, $year = false)
{
    //display_notification($unit);
    //display_notification($year);

    $sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount,year,month) SELECT emp_id,round((emp_salary-1850)*0.05),$year,$month FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=4 and department_id not in (21,22,23)";
    db_query($sql);

    $sql = "UPDATE 0_employee SET emp_salary = (emp_salary+round((emp_salary-1850)*0.05)) WHERE emp_hiredate<'2019-01-01' and UnitID=4  and inactive=0 ORDER BY `0_employee`.`emp_hiredate` DESC ";

    $inc = db_query($sql);


  

   //display_notification($sql);

    $increment = "SELECT emp_id,emp_salary FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=4 and department_id not in (21,22,23)";
    $increment = db_query($increment);
   while($myrow = db_fetch($increment)){

     write_template_salary($myrow['emp_id'],$myrow['emp_salary']);

     //echo $myrow['emp_id']." ".$myrow['emp_salary']."<br>";


    }

    $sql = "INSERT INTO " . TB_PREF . "increment_process_status (step, status, month, year) VALUES ('eight_floor', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));

}

function ninth_floor($month = false, $year = false)
{
    //display_notification($unit);
    //display_notification($year);
    $sql = "INSERT INTO 0_emp_salary_increment (emp_id,amount,year,month) SELECT emp_id,round((emp_salary-1850)*0.05),$year,$month FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=5 and department_id not in (21,22,23)";
    db_query($sql);


    $sql = "UPDATE 0_employee SET emp_salary = (emp_salary+round((emp_salary-1850)*0.05)) WHERE emp_hiredate<'2019-01-01' and UnitID=5  and inactive=0 ORDER BY `0_employee`.`emp_hiredate` DESC ";

    $inc = db_query($sql);


   
   //display_notification($sql);

    $increment = "SELECT emp_id,emp_salary FROM 0_employee WHERE emp_hiredate < '2019-01-01' and inactive =0  and UnitID=5 and department_id not in (21,22,23)";
    $increment = db_query($increment);
   while($myrow = db_fetch($increment)){

     write_template_salary($myrow['emp_id'],$myrow['emp_salary']);

     //echo $myrow['emp_id']." ".$myrow['emp_salary']."<br>";


    }

    $sql = "INSERT INTO " . TB_PREF . "increment_process_status (step, status, month, year) VALUES ('ninth_floor', 1," . db_escape($month) . ", " . db_escape($year) . ")";
		db_query($sql, _('Sorry to over write data..!'));

}








