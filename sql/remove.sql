-- FrontHRM/sql
DROP TABLE IF EXISTS `0_employee`;
DROP TABLE IF EXISTS `0_department`;
DROP TABLE IF EXISTS `0_salaryscale`;
DROP TABLE IF EXISTS `0_overtime`;
DROP TABLE IF EXISTS `0_daily_attendance`;
DROP TABLE IF EXISTS `0_attendance`;
DROP TABLE IF EXISTS `0_payroll_account`;
DROP TABLE IF EXISTS `0_payroll_structure`;
DROP TABLE IF EXISTS `0_salary_structure`;
DROP TABLE IF EXISTS `0_payslip`;
DROP TABLE IF EXISTS `0_payslip_details`;
DROP TABLE IF EXISTS `0_employee_trans`;
DROP TABLE IF EXISTS `0_emp_pmt_tmp`;
DROP TABLE IF EXISTS `0_emp_payslip_det`;
DROP TABLE IF EXISTS `0_emp_configutation`;
DROP TABLE IF EXISTS `0_emp_pmt_str_tmp`;
DROP TABLE IF EXISTS `0_attendance_status`;
DROP TABLE IF EXISTS `0_holidays`;
DROP TABLE IF EXISTS `0_emp_payslip_no`;
DROP TABLE IF EXISTS `0_attachments_rashed`;

DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_deductleave_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_month_work_days';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_overtime_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_payable_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_work_hours';
