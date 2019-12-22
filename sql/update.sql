-- FrontHRM/sql

DROP TABLE IF EXISTS `0_employee`;
CREATE TABLE `0_employee` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_card_no` varchar(30) NULL,
  `emp_first_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_last_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `emp_address` tinytext COLLATE utf8_unicode_ci,
  `emp_mobile` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_birthdate` date NOT NULL,
  `emp_notes` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `emp_hiredate` date DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `salary_scale_id` varchar(20) NOT NULL DEFAULT '0',
  `emp_releasedate` date DEFAULT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  `Designation` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `emp_status` enum('N','R','L','D') NOT NULL,
  `emp_blood` enum('N','A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `UnitID` int(11) NOT NULL,
   PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_department`;
CREATE TABLE IF NOT EXISTS `0_department` (
    `dept_id` int(11) NOT NULL AUTO_INCREMENT,
    `dept_name` tinytext NOT NULL,
    `inactive` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_salaryscale`;
CREATE TABLE IF NOT EXISTS `0_salaryscale` (
    `scale_id` int(11) NOT NULL AUTO_INCREMENT,
    `scale_name` text NOT NULL,
    `inactive` tinyint(1) NOT NULL DEFAULT 0,
    `pay_basis` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = monthly, 1 = daily',
    `gl_debit` VARCHAR(30) NOT NULL,
    `gl_credit` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`scale_id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `0_overtime`;
CREATE TABLE IF NOT EXISTS `0_overtime` ( 
  `overtime_id` int(11) NOT NULL AUTO_INCREMENT, 
  `overtime_name` varchar(100) NOT NULL, 
  `inactive` tinyint(1) NOT NULL DEFAULT 0, 
  PRIMARY KEY (`overtime_id`) 
  ) ENGINE=InnoDB;


DROP TABLE IF EXISTS `0_overtime_template`;
CREATE TABLE IF NOT EXISTS `0_overtime_template` ( 
  `overtime_tmp_id` int(11) NOT NULL AUTO_INCREMENT,
  `overtime_id` int(11) NOT NULL , 
  `overtime_rate` float(5) NOT NULL, `scale_id` varchar(30) NULL, 
  `inactive` tinyint(1) NOT NULL DEFAULT 0, 
  PRIMARY KEY (`overtime_tmp_id`) 
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `0_daily_attendance`;
CREATE TABLE `0_daily_attendance` (
  `emp_id` int(11) NOT NULL,
  `overtime_id` int(11) NULL DEFAULT 1,
  `hours_no` float NOT NULL DEFAULT 0,
  `rate` float NOT NULL DEFAULT 1,
  `att_date` date NOT NULL,
  `EntryTime` time DEFAULT NULL,
  `ExitTime` time DEFAULT NULL,
  `AttdStatus` varchar(10) DEFAULT NULL,
  `proximity_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `0_attendance`;
CREATE TABLE IF NOT EXISTS `0_attendance` (
    `emp_id` int(11) NOT NULL,
    `overtime_id` int(11) NOT NULL,
    `hours_no` float(5) NOT NULL DEFAULT 0,
    `rate` float(5) NOT NULL DEFAULT 1,
    `att_date` date NOT NULL,
    PRIMARY KEY (`emp_id`,`overtime_id`,`att_date`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_payroll_account`;
CREATE TABLE IF NOT EXISTS `0_payroll_account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` int(11) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_payroll_structure`;
CREATE TABLE IF NOT EXISTS `0_payroll_structure` (
  `salary_scale_id` int(11) NOT NULL,
  `payroll_rule` text NOT NULL,
  KEY `salary_scale_id` (`salary_scale_id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_salary_structure`;
CREATE TABLE IF NOT EXISTS `0_salary_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `salary_scale_id` varchar(30) NOT NULL,
  `pay_rule_id` varchar(15) NOT NULL,
  `pay_amount` double NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0 for credit, 1 for debit',
  `is_basic` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_payslip`;
CREATE TABLE IF NOT EXISTS `0_payslip` (
  `payslip_no` int(11) NOT NULL AUTO_INCREMENT,
  `trans_no` int(11) NOT NULL DEFAULT 0,
  `emp_id` int(11) NOT NULL,
  `generated_date` date NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `leaves` int(11) NOT NULL,
  `deductable_leaves` int(11) NOT NULL,
  `payable_amount` double NOT NULL DEFAULT 0,
  `salary_amount` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`payslip_no`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_payslip_details`;
CREATE TABLE IF NOT EXISTS `0_payslip_details` (
  `payslip_no` int(11) NOT NULL AUTO_INCREMENT,
  `detail` int(11) NOT NULL DEFAULT 0,
  `amount` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`payslip_no`, `detail`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_employee_trans`;
CREATE TABLE IF NOT EXISTS `0_employee_trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_no` int(11) NOT NULL DEFAULT 0,
  `payslip_no` int(11) NOT NULL,
  `pay_date` date NOT NULL,
  `to_the_order_of` varchar(255) NOT NULL,
  `pay_amount` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `0_emp_pmt_tmp`;
CREATE TABLE `0_emp_pmt_tmp` (
  `emp_pmt_tmp_id` int(11) NOT NULL,
  `scale_id` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `emp_pmt_str_id` int(11) NOT NULL,
  `emp_pmt_tmp_amt` int(11) NOT NULL
) ENGINE=InnoDB;
ALTER TABLE `0_emp_pmt_tmp`
  ADD PRIMARY KEY (`emp_pmt_tmp_id`);

ALTER TABLE `0_emp_pmt_tmp`
  MODIFY `emp_pmt_tmp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `0_emp_payslip_det`;
CREATE TABLE `0_emp_payslip_det` (
  `emp_payslip_id` int(11) NOT NULL,
  `emp_payslip_No` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `emp_pmt_str_id` int(11) NOT NULL,
  `emp_pmt_amt` int(11) NOT NULL
) ENGINE=InnoDB;
INSERT INTO `0_emp_payslip_det` ( `emp_payslip_id`,`emp_payslip_No`, `emp_id`, `emp_pmt_str_id`, `emp_pmt_amt`) VALUES 
(1,'1', '1', '1', '200000'), 
(2,'1', '1', '2', '35000'),
(3,'1', '1', '3', '80000'), 
(4,'1', '1', '4', '35000'), 
(5,'1', '1', '5', '35000');
ALTER TABLE `0_emp_payslip_det`
  ADD PRIMARY KEY (`emp_payslip_id`);

ALTER TABLE `0_emp_payslip_det`
  MODIFY `emp_payslip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  


DROP TABLE IF EXISTS `0_emp_configutation`;
CREATE TABLE `0_emp_configutation` (
  `emp_conf_id` int(11) NOT NULL,
  `Time_in` time NOT NULL,
  `Time_out` time NOT NULL,
  `Late_Time_in` time NOT NULL,
  `Regular_Hour` int(11) NOT NULL,
  `Min_Late_count` int(11) NOT NULL,
  `Late_Penaly_Rate` int(11) NOT NULL
) ENGINE=InnoDB;

INSERT INTO `0_emp_configutation` (`emp_conf_id`, `Time_in`, `Time_out`, `Late_Time_in`, `Regular_Hour`, `Min_Late_count`, `Late_Penaly_Rate`) VALUES
(1, '10:00:00', '18:00:00', '10:05:00', 8, 3, 33);
ALTER TABLE `0_emp_configutation`
  ADD PRIMARY KEY (`emp_conf_id`);

ALTER TABLE `0_emp_configutation`
  MODIFY `emp_conf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  


DROP TABLE IF EXISTS `0_emp_pmt_str_tmp`;
CREATE TABLE `0_emp_pmt_str_tmp` (
  `emp_pmt_str_id` int(11) NOT NULL,
  `emp_pmt_str_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_pmt_type` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inactive` BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

INSERT INTO `0_emp_pmt_str_tmp` (`emp_pmt_str_id`, `emp_pmt_str_name`, `emp_pmt_type`,`inactive`) VALUES 
(1, 'Basic', 'Add',0), 
(2, 'Medical Allowance', 'Add',0),
(3, 'House Rent Allowance', 'Add',0), 
(4, 'Conveyance Allowance', 'Add',0), 
(5, 'LFA', 'Add',0),
(6, 'Provident Fund', 'Ded',0), 
(7, 'Loan Installment', 'Ded',0);

ALTER TABLE `0_emp_pmt_str_tmp`
  ADD PRIMARY KEY (`emp_pmt_str_id`);
ALTER TABLE `0_emp_pmt_str_tmp`
  MODIFY `emp_pmt_str_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  


DROP TABLE IF EXISTS `0_attendance_status`;
CREATE TABLE `0_attendance_status` (
  `StatusID` int(10) NOT NULL,
  `StatusName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `StatusFullName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `IsLate` tinyint(1) NOT NULL,
  `MaxLateMin` int(10) DEFAULT NULL,
  `Limit` int(10) NULL,
  `FlagStatus` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `0_attendance_status` (`StatusID`, `StatusName`,`StatusFullName`, `IsLate`, `MaxLateMin`,  `Limit`, `FlagStatus`) VALUES
(1,'Pr', 'Present', 0, 0,0, 'Present'),
(2, 'OD','On Duty', 0, 0, 0,'Present'),
(3,'LT', 'Late', 0, 0, 0,'Late'),
(4,'CL', 'Casual Leave', 1, 15,10, 'Leave'),
(5,'SL', 'Sick Leave', 1, 15,14, 'Leave'),
(6,'EL', 'Earned Leave', 1, 15,90, 'Leave'),
(7,'ML', 'Maternity Leave', 1, 15,180, 'Leave');

ALTER TABLE `0_attendance_status`
  ADD PRIMARY KEY (`StatusID`);

ALTER TABLE `0_attendance_status`
  MODIFY `StatusID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  



DROP TABLE IF EXISTS `0_holidays`;
CREATE TABLE `0_holidays` (
  `Holidate` date NOT NULL,
  `OccasionName` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `0_holidays`
  ADD PRIMARY KEY (`Holidate`);


  
DROP TABLE IF EXISTS `0_emp_payslip_no`;
CREATE TABLE `0_emp_payslip_no` (
  `emp_payslip_no` int(30) NOT NULL,
  `emp_id` int(10) NOT NULL,
  `year` int(10) NOT NULL,
  `month` int(11) NOT NULL,
  `payment_status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_of_pay` date NOT NULL
) ENGINE=InnoDB;

INSERT INTO `0_emp_payslip_no` (`emp_payslip_no`, `emp_id`, `year`, `month`, `payment_status`, `date_of_pay`) VALUES
(1, 1, 2018, 7, 'Sent for approval', '2018-07-31'),
(2, 2, 2018, 7, 'Sent for approval', '2018-07-31');

ALTER TABLE `0_emp_payslip_no`
  ADD PRIMARY KEY (`emp_payslip_no`);

ALTER TABLE `0_emp_payslip_no`
  MODIFY `emp_payslip_no` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


  
DROP TABLE IF EXISTS `0_attachments_rashed`;
CREATE TABLE `0_attachments_rashed` (
  `id` int(10) NOT NULL,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(30) NOT NULL DEFAULT '',
  `unique_name` varchar(30) NOT NULL DEFAULT '', 
  `filename` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `filesize` int(11) NOT NULL DEFAULT '0',
  `filetype` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB;

ALTER TABLE `0_attachments_rashed`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `0_attachments_rashed`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  

DROP TABLE IF EXISTS `0_unit`;
CREATE TABLE `0_unit` (
  `UnitID` int(11) NOT NULL,
  `UnitName` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `inactive` BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `0_unit` (`UnitID`, `UnitName`,`inactive`) VALUES
(1, 'Rishal Garments',0),
(2, 'Saroj Garments',0);

ALTER TABLE `0_unit`
  ADD PRIMARY KEY (`UnitID`);

ALTER TABLE `0_unit`
  MODIFY `UnitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

  

DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_deductleave_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_month_work_days';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_overtime_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_payable_act';
DELETE FROM `0_sys_prefs` WHERE `0_sys_prefs`.`name` = 'payroll_work_hours';

INSERT INTO `0_sys_prefs` VALUES ('payroll_deductleave_act', NULL, 'int', NULL, 5410);
INSERT INTO `0_sys_prefs` VALUES ('payroll_month_work_days', NULL, 'float', NULL, 26);
INSERT INTO `0_sys_prefs` VALUES ('payroll_overtime_act', NULL, 'int', NULL, 5420);
INSERT INTO `0_sys_prefs` VALUES ('payroll_payable_act', NULL, 'int', NULL, 2100);
INSERT INTO `0_sys_prefs` VALUES ('payroll_work_hours', NULL, 'float', NULL, 8);