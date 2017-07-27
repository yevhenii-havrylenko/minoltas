<?php
//start
error_reporting(0);
//header ("Content-Type: text/html; charset=utf-8");
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
date_default_timezone_set("Europe/Kiev"); 
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
//header ("Content-Type: text/html; charset=utf-8");
//mail("e.gavrilenko@dkhz.com.ua", "My Subject", "Line 1\nLine 2\nLine 3");
//phpinfo();
//var $info1, $info2;


echo (	//head
	'<!DOCTYPE html>
	<html lang="en">
  	<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>print</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  	</head>'
	);

echo ( //body
	'<body>
	<div class="container">
    <!-- jQuery (necessary for Bootstraps JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>'
	);

echo( //nav
'<div class=\"page-header\">
<h1>minolta bizhub 222 control<small></small></h1></div>
<ul class="nav nav-pills">
  <li class="active"><a href="index.php">Status</a></li>
  <li><a href="send.php">Send report</a></li>
  <li><a href="config.php">Config</a></li>
</ul>');

$runtime = date("Y-m-d H:i:s");
echo ("<div class=\"page-header\">
  <h2>date: <small>".$runtime."</small></h2></div>");
//echo "type: ".gettype($runtime)."</br>";



$xml = simplexml_load_file('config.xml');
   // print_r($xml);

foreach($xml->printers->printer as $printer) {
	echo('<div class="panel panel-primary">
  	<div class="panel-heading"><span class="glyphicon glyphicon-print"></span>');
	//$last=$printer->last;
	$ip = $printer->ip;
	$serial = $printer->serial;
	echo "  minolta ".$printer->id."</div><div class=\"panel-body\">";
	echo "<div>serial: <span class=\"badge\">".$serial."</span></div>";
	$fp = fsockopen ("$ip", 80, $errno, $errstr, 15);
	if (!$fp) {
		echo ('<div class="alert alert-danger">not available </div>');
	} else {	
		echo "<div>control date: <span class=\"badge\">".$printer->lastControlTime."</span></div>";
		echo "<div>control count: <span class=\"badge\">".$printer->lastControlCount."</span></div>";
		$info =  (int) snmpget($ip, "private", ".1.3.6.1.4.1.18334.1.1.1.5.7.2.1.1.0");
		echo "<div>current count: <span class=\"badge\">".$info."</span></div>";
			//echo "type: ".gettype($info)."</br>";
		$lastdelta=$info-$printer->last;
	//	echo "last delta: ".$lastdelta."</br>";
		$fulldelta=$info-$printer->lastControlCount;
		$status="success";
		if ($fulldelta>7000) {$status="warning"; }
		if ($fulldelta>15000) {$status="danger"; }
		echo "<div>full delta: <span class=\"label label-".$status."\">".$fulldelta."</span></div>";
		$printer->last=$info;	
		//$printer->lastControlCount=$info;
		//$printer->lastControlTime=$runtime;

		$d1= new DateTime($printer->lastControlTime);
		$d2= new DateTime($runtime);
		$interval = $d1->diff($d2)->format('%R%a дней');
		echo "interval: ".$interval."</br>";
		echo("<div>web interface: <a href=\"http://$ip\">http://$ip</a></div>");

		$printer->lasttime=$runtime;
	}
	echo("</div></div>");
};

$xml->asXML('config.xml');

echo('
<div class="panel panel-default">
  <div class="panel-heading"><span class="glyphicon glyphicon-info-sign"></span> info</div>
  <div class="panel-body">
    <p>ОКПО ДКХЗ: 05393085</p>
    <p>Minolta service: <a href="http://service.konicaminolta.ua/">http://service.konicaminolta.ua/</a></p>
    <p>Manager: Liliya Toporets</p>
    <p>Phone: + 38 044 230 10 72</p>
    <p>Mob: +38 067 334 43 58</p>
    <p>eMail: <a href="mailto:Liliya.Toporets@konicaminolta.ua">Liliya.Toporets@konicaminolta.ua</a></p>

  </div>
</div>
	');

echo( //end
	'<div>
	</body>
	</html>'
	);
?>

