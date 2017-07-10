<?php
/* Iletimerkezi SMS Eklentisi
 * whmcsSMS - http://www.whmcssms.com
 */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require_once("smsclass.php");
$class = new iletimerkezi();
$hooks = $class->getHooks();

foreach($hooks as $hook){
   $res = add_hook($hook['hook'], 1, $hook['function'], "");   
}