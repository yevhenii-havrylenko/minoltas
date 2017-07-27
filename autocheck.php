<?php
error_reporting(0);
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
date_default_timezone_set("Europe/Kiev"); 
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

$runtime = date("Y-m-d H:i:s");

$xml = simplexml_load_file('/usr/local/www/apache24/data/minoltas/config.xml');
   // print_r($xml);

$status=TRUE;
foreach($xml->printers->printer as $printer) {
  $fp = fsockopen ("$printer->ip", 80, $errno, $errstr, 15);
  if (!$fp) { $status=FALSE; }
}

foreach($xml->printers->printer as $printer) {

	$ip = $printer->ip;

	if ($status) {
		$info =  (int) snmpget($ip, "private", ".1.3.6.1.4.1.18334.1.1.1.5.7.2.1.1.0");
		$printer->last=$info;	
		$printer->lasttime=$runtime;
		//$printer->lastControlCount=$info;
		//$printer->lastControlTime=$runtime;
	}
};

$xml->asXML('/usr/local/www/apache24/data/minoltas/config.xml');
?>
