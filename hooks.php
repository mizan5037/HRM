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
define ('SS_HRM', 251<<8);

class FrontHrm_app extends application {
	
    function __construct() {
        global $path_to_root;
        
        parent::__construct("FrontHrm", _($this->help_context = "Human Resource"));
        
        $this->add_module(_("Transactions"));
		//$this->add_lapp_function(0, _('Attendance'), $path_to_root.'/modules/FrontHrm/manage/attendance.php', 'SA_EMPL', MENU_TRANSACTION);
        //$this->add_lapp_function(0, _('Payslip Entry'), $path_to_root.'/modules/FrontHrm/manage/payslip.php?NewPayslip=Yes', 'SA_EMPL', MENU_TRANSACTION);
        //$this->add_rapp_function(0, _('Payment Advice'), $path_to_root.'/modules/FrontHrm/manage/pay_advice.php?NewPaymentAdvice=Yes', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Daily Attendance'), $path_to_root.'/modules/FrontHrm/manage/daily_attendance.php', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Monthly Attendance'), $path_to_root.'/modules/FrontHrm/manage/emp_daily_attendance.php', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Salary Increment after gadget'), $path_to_root.'/modules/FrontHrm/manage/increment_process.php', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Salary Increment by hiredate'), $path_to_root.'/modules/FrontHrm/manage/increment_hiredate.php', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_rapp_function(0, _('Last Date Entry'), $path_to_root.'/modules/FrontHrm/manage/last_date_entry.php', 'SA_EMPL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Import HRM'), $path_to_root.'/modules/FrontHrm/manage/import_hrm.php', 'SA_HRSETUP', MENU_TRANSACTION);

         $this->add_rapp_function(0, _('Create New Employees'), $path_to_root.'/modules/FrontHrm/manage/create_new_emp_list.php', 'SA_EMPL', MENU_TRANSACTION);
         $this->add_rapp_function(0, _('Pre Salery Sheet Process'), $path_to_root.'/modules/FrontHrm/manage/pre_salery_sheet.php', 'SA_EMPL', MENU_TRANSACTION);

         $this->add_rapp_function(0, _('Employees Pre Salery Sheet'), $path_to_root.'/modules/FrontHrm/manage/emp_pre_salarysheet.php', 'SA_EMPL', MENU_TRANSACTION);
          $this->add_rapp_function(0, _('Employees Salary Increment'), $path_to_root.'/modules/FrontHrm/manage/emp_salary_increment.php', 'SA_EMPL', MENU_TRANSACTION);
         $this->add_rapp_function(0, _('Temporary Pre Salery Sheet'), $path_to_root.'/modules/FrontHrm/manage/emp_pre_salarysheet_temp.php', 'SA_EMPL', MENU_TRANSACTION);

        // $this->add_lapp_function(0, _('Detail Payslip'), $path_to_root.'/modules/FrontHrm/manage/detail_payslip.php', 'SA_EMPL', MENU_TRANSACTION);
        // $this->add_rapp_function(0, _('Attachments'), $path_to_root.'/modules/FrontHrm/manage/attachments_rashed.php', 'SA_EMPL', MENU_TRANSACTION);



        
        $this->add_module(_("Reports"));
		// $this->add_lapp_function(1, _('Timesheet'), $path_to_root.'/modules/FrontHrm/inquiry/time_sheet.php', 'SA_EMPL', MENU_INQUIRY);
        // $this->add_lapp_function(1, _('Timesheet'), $path_to_root.'/modules/FrontHrm/inquiry/time_sheet.php', 'SA_EMPL', MENU_INQUIRY);
	    // $this->add_lapp_function(1, _('Employee Transaction Inquiry'), $path_to_root.'/modules/FrontHrm/inquiry/emp_inquiry.php?', 'SA_EMPL', MENU_INQUIRY);
        //$this->add_lapp_function(1, _('Reports'), $path_to_root.'/modules/FrontHrm/reporting/reports_main.php', 'SA_EMPL', MENU_INQUIRY);
        // $this->add_rapp_function(1, _("Employee &Reports"),
            // "reporting/reports_main.php?Class=8", 'SA_EMPL', MENU_REPORT);

        $this->add_lapp_function(1, _('&Reports'), '../Reports', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(''), '', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(''), '', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _('Absent Employees'), '../Reports/index.php?page=absent_list', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(' Error List '), '../Reports/index.php?page=errorlist', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(' Daily Manpower '), '../Reports/index.php?page=daily_manpower', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(' Leave Ledger List '), '../Reports/index.php?page=leave', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(' Insurance List '), '../Reports/index.php?page=insurance_list', 'SA_EMPL', MENU_REPORT);
         $this->add_lapp_function(1, _('Release Employee'), '../Reports/index.php?page=release', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _('Late Employees'), '../Reports/index.php?page=late_employee', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _('Increment List Of Employees'), '../Reports/index.php?page=after_increment_list', 'SA_EMPL', MENU_REPORT);
        $this->add_lapp_function(1, _(' ID Card '), '../Reports/index.php?page=id_card', 'SA_EMPL', MENU_REPORT);
         $this->add_lapp_function(1, _('Embroidary Buyer'), '../Reports/index.php?page=embroidary_buyer_shift_job_card', 'SA_EMPL', MENU_REPORT);

         $this->add_lapp_function(1, _('Lefty And Resign Employees'), '../Reports/index.php?page=lefty', 'SA_EMPL', MENU_REPORT);
        
       

        $this->add_rapp_function(1, _('Payslip'), '../Reports/index.php?page=payslip', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Overtime Wages'), '../Reports/index.php?page=overtime_wages', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Job Card'), '../Reports/index.php?page=job_card', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Job Card (Buyer)'), '../Reports/index.php?page=job_card_buyer', 'SA_EMPL', MENU_REPORT);
         $this->add_rapp_function(1, _('ATH Sheet'), '../Reports/index.php?page=ATH_sheet', 'SA_EMPL', MENU_REPORT);
         $this->add_rapp_function(1, _('Embroidary Actual'), '../Reports/index.php?page=ebroidary_actual_shift_job_card', 'SA_EMPL', MENU_REPORT);
         
        $this->add_rapp_function(1, _('Job Report'), '../Reports/index.php?page=job_report', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Salary Summery'), '../Reports/index.php?page=summery', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Salary Summery'), '../Reports/index.php?page=buyer_salary_summery', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('EL Sheet'), '../Reports/index.php?page=el_sheet', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('New Employees'), '../Reports/index.php?page=new_employee', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Employees'), '../Reports/index.php?page=employee_list', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Overtime'), '../Reports/index.php?page=ot_list', 'SA_EMPL', MENU_REPORT);
        $this->add_rapp_function(1, _('Eid Bonus'), '../Reports/index.php?page=eid_bonus', 'SA_EMPL', MENU_REPORT);



        
        $this->add_module(_("Maintenance"));
		$this->add_lapp_function(2, _('Employees'), $path_to_root.'/modules/FrontHrm/manage/employee.php', 'SA_EMPL', MENU_ENTRY);
		$this->add_lapp_function(2, _('Departments'), $path_to_root.'/modules/FrontHrm/manage/department.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_lapp_function(2, _('Sections'), $path_to_root.'/modules/FrontHrm/manage/section.php', 'SA_HRSETUP', MENU_MAINTENANCE);
		// $this->add_lapp_function(2, _('Manage Overtime'), $path_to_root.'/modules/FrontHrm/manage/overtime.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        // $this->add_lapp_function(2, _('Manage Overtime Template'), $path_to_root.'/modules/FrontHrm/manage/overtime_tmp.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        // $this->add_lapp_function(2, _('Default Settings'), $path_to_root.'/modules/FrontHrm/manage/default_setup.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        //$this->add_lapp_function(2, _('Salary Scales'), $path_to_root.'/modules/FrontHrm/manage/salaryscale.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        // $this->add_lapp_function(2, _('Salary Template'), $path_to_root.'/modules/FrontHrm/manage/salary_template.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_lapp_function(2, _('Payment String'), $path_to_root.'/modules/FrontHrm/manage/payment_string.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_lapp_function(2, _('Salary Template'), $path_to_root.'/modules/FrontHrm/manage/template_salary.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        // $this->add_lapp_function(2, _('Salary Template Head'), $path_to_root.'/modules/FrontHrm/manage/sal_temp_head.php', 'SA_HRSETUP', MENU_MAINTENANCE);


		// $this->add_rapp_function(2, _('Allowance and Deduction Account'), $path_to_root.'/modules/FrontHrm/manage/accounts.php', 'SA_HRSETUP', MENU_MAINTENANCE);
		// $this->add_rapp_function(2, _('Allowance and Deduction Rules'), $path_to_root.'/modules/FrontHrm/manage/payroll_rules.php', 'SA_HRSETUP', MENU_MAINTENANCE);
       // $this->add_rapp_function(2, _('Salary Structure'), $path_to_root.'/modules/FrontHrm/manage/salary_structure.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        
        $this->add_rapp_function(2, _('Attendance Status'), $path_to_root.'/modules/FrontHrm/manage/attendance_status.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_rapp_function(2, _('Leave Ledger'), $path_to_root.'/modules/FrontHrm/manage/leave_ledger.php', 'SA_HRSETUP', MENU_MAINTENANCE);

        $this->add_rapp_function(2, _('Unit'), $path_to_root.'/modules/FrontHrm/manage/unit.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_rapp_function(2, _('Holiday'), $path_to_root.'/modules/FrontHrm/manage/holiday.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_rapp_function(2, _('Shift'), $path_to_root.'/modules/FrontHrm/manage/shift.php', 'SA_HRSETUP', MENU_MAINTENANCE);
        $this->add_lapp_function(2, _('Shift Wise Employee'), $path_to_root.'/modules/FrontHrm/manage/emp_shift_list.php', 'SA_HRSETUP', MENU_MAINTENANCE);

		$this->add_extensions();
    }
}

class hooks_FrontHrm extends hooks {
    function __construct() {
 		$this->module_name = 'FrontHrm';
 	}
    
    function install_tabs($app) {
        $app->add_application(new FrontHrm_app);
    }
    
    function install_access() {
        $security_sections[SS_HRM] =  _("Human Resource");
        $security_areas['SA_EMPL'] = array(SS_HRM|1, _("Hrm entry"));
        $security_areas['SA_HRSETUP'] = array(SS_HRM|1, _("Hrm setup"));
        return array($security_areas, $security_sections);
    }
    
    function activate_extension($company, $check_only=true) {
        global $db_connections;
        
        $updates = array( 'update.sql' => array('fronthrm'));
 
        return $this->update_databases($company, $updates, $check_only);
    }
	
    function deactivate_extension($company, $check_only=true) {
        global $db_connections;

        $updates = array('remove.sql' => array('fronthrm'));

        return $this->update_databases($company, $updates, $check_only);
    }
}
