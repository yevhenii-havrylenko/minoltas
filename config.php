<?php
//start
//error_reporting(0);
//header ("Content-Type: text/html; charset=utf-8");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
  <li><a href="index.php">Status</a></li>
  <li><a href="send.php">Send report</a></li>
  <li class="active"><a href="config.php">Config</a></li>
</ul>');

$runtime = date("Y-m-d H:i:s");
echo ("<div class=\"page-header\">
  <h2>Configuration<small></small></h2></div>");

$xml = simplexml_load_file('config.xml');




 if (!empty($_POST)) {
 	 if (isset($_POST['from'])) $xml->global->defaultFrom=htmlspecialchars($_POST['from']);
 	 if (isset($_POST['mailto'])) $xml->global->defaultTo=htmlspecialchars($_POST['mailto']);
 	 if (isset($_POST['cc'])) $xml->global->defaultCc=htmlspecialchars($_POST['cc']);
 	 if (isset($_POST['subject'])) $xml->global->defaultSubject=htmlspecialchars($_POST['subject']);
 	
	$xml->asXML('config.xml');
	echo ('<div class="alert alert-success">Well done!</div>');
 } 

$default_from = $xml->global->defaultFrom;
$default_maito = $xml->global->defaultTo;
$default_cc = $xml->global->defaultCc;
$default_subject = $xml->global->defaultSubject;

 	echo('
	<form action="config.php" method="post" class="form-group">


 	 <div class="form-group">
    <label for="from">set default from</label>
    <input type="text" class="form-control" id="from" placeholder="from" name="from" value="');
    echo($default_from);
    echo('">
  	</div>

    <div class="form-group">
    <label for="mailto">set default mailt to</label>
    <input type="text" class="form-control" id="inputEmail" placeholder="email" name="mailto" value="');
    echo($default_maito);
    echo('">
  	</div>

    <div class="form-group">
    <label for="cc">set default Cc:</label>
    <input type="text" class="form-control" id="cc" placeholder="email" name="cc" value="');
    echo($default_cc);
    echo('">
  	</div>

 	 <div class="form-group">
    <label for="subject">set default subject</label>
    <input type="text" class="form-control" id="subject" placeholder="subject" name="subject" value="');
    echo($default_subject);
    echo('">
  	</div>

  	<button type="submit" class="btn btn-default">Save</button>
    </form>
	');


/* ALL POST
 echo "<br>POST<br>";
  foreach($_POST as $key => $value)
  {
     echo "\$_POST[".$key."] = ".$value."<br>";
  } //*/

echo( //end
	'<div>
	</body>
	</html>'
	);
?>