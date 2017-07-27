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
echo('
<div class=\"page-header\">
<h1>minolta bizhub 222 control<small></small></h1></div>
<ul class="nav nav-pills">
  <li><a href="index.php">Status</a></li>
  <li class="active"><a href="send.php">Send report</a></li>
  <li><a href="config.php">Config</a></li>
</ul>
');
$runtime = date("Y-m-d H:i:s");
echo ("<div class=\"page-header\">
  <h2>Sending report<small></small></h2></div>");

$xml = simplexml_load_file('config.xml');


$default_from = $xml->global->defaultFrom;
$default_maito = $xml->global->defaultTo;
$default_cc = $xml->global->defaultCc;
$default_subject = $xml->global->defaultSubject;;
$default_message = "Состояние счетчиков печати на ПАО \"ЕВРАЗ Днепродзержинский КХЗ\" \r\n";

$status=TRUE;
foreach($xml->printers->printer as $printer) {
  $fp = fsockopen ("$printer->ip", 80, $errno, $errstr, 15);
  if (!$fp) { $status=FALSE; }
}


$info = [];
$lasttime=[];
foreach($xml->printers->printer as $printer) {
	$ip = $printer->ip;
	$serial = $printer->serial;
	$model = $printer->model;
  if ($status) {
	$info["$serial"] =  (int) snmpget($ip, "private", ".1.3.6.1.4.1.18334.1.1.1.5.7.2.1.1.0");
  $lasttime["$serial"] = $runtime;
  } else {
  $info["$serial"] = $printer->last;
  $lasttime["$serial"] = $printer->lasttime;
  }
	$default_message .= $model.'   serial: '.$serial.'   current counter: '.$info["$serial"].'     control date: '.$lasttime["$serial"]."\r\n";
}

/*
$default_from = "e.gavrilenko@dkhz.com.ua";
$default_maito = "e.gavrilenko@dkhz.com.ua";
$default_cc = "e.gavrilenko@dkhz.com.ua";
$default_subject = "Minoltas counters report from DKHZ";
$default_message = "message";*/




 if (isset($_POST['mailto'])) {
 	
 	//$headers = 'From: '.htmlspecialchars($_POST['from'])."\r\n"."Cc: ".htmlspecialchars($_POST['cc'])."\r\n"."Content-type: text/plain; charset=UTF-8 \r\n".'X-Mailer: PHP/'.phpversion();
  $from = htmlspecialchars($_POST['from']);
 	$to = htmlspecialchars($_POST['mailto']);
  $cc = htmlspecialchars($_POST['cc']);
 	$subject = htmlspecialchars($_POST['subject']);
  $message = $_POST['massage'];

	
	
	/*echo($to);
	echo($headers);
	echo($subject);
	echo($message);*/

  //new mailer begin

  require_once './phpmailer/PHPMailerAutoload.php';

  //Create a new PHPMailer instance
  $mail = new PHPMailer;
  //Tell PHPMailer to use SMTP
  $mail->isSMTP();
  //Enable SMTP debugging
  // 0 = off (for production use)
  // 1 = client messages
  // 2 = client and server messages
  $mail->SMTPDebug = 0;
  //Ask for HTML-friendly debug output
  //$mail->Debugoutput = 'html';
  //Set the hostname of the mail server
  $mail->Host = "mail.dkhz.com.ua";
  //Set the SMTP port number - likely to be 25, 465 or 587
  $mail->Port = 25;
  //Whether to use SMTP authentication
  $mail->SMTPAuth = false;
  //Set who the message is to be sent from

  $mail->CharSet="UTF-8";
  $mail->ContentType = 'text/plain'; 
  $mail->IsHTML(false);

  $mail->setFrom($from);
  //Set an alternative reply-to address
  //$mail->addReplyTo('replyto@dkhz.com.ua', 'First Last');
  //Set who the message is to be sent to
  $mail->addAddress($to);
  $mail->AddCC($cc);
  //Set the subject line
  $mail->Subject = $subject;
  $mail->Body = ($message);
  //Read an HTML message body from an external file, convert referenced images to embedded,
  //convert HTML into a basic plain-text alternative body
  //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
  //Replace the plain text body with one created manually
  //$mail->AltBody = 'This is a plain-text message body';
  //Attach an image file
  //$mail->addAttachment('images/phpmailer_mini.png');

  //send the message, check for errors
  if (!$mail->send()) {
      echo ('<div class="alert alert-danger">Error:  '.$mail->ErrorInfo.'</div>');
  } else {
      echo ('<div class="alert alert-success">Well done! You successfully sent mail.</div>');
  }

  //new mailer end
 

/* old mailer
	if (mail($to, $subject, $message, $headers)) {
	echo ('<div class="alert alert-success">Well done! You successfully sent mail.</div>');
	} else {
	echo ('<div class="alert alert-danger">Error!</div>');	
	}
*/
	$save = htmlspecialchars($_POST['save']);
	
	
	//* save control counters
	if ($save == "on") {
		foreach($xml->printers->printer as $printer) {
				$printer->lastControlCount=$info["$printer->serial"];
				$printer->lastControlTime=$lasttime["$printer->serial"];
		}
	$xml->asXML('config.xml');
	} // */

	 /*
	$to      = 'e.gavrilenko@dkhz.com.ua';
	$subject = 'the subject';
	$message = 'hello';
	$headers = 'From: noreply@example.com'."\r\n".'Reply-To: webmaster@example.com'."\r\n".'X-Mailer: PHP/'.phpversion();

	mail($to, $subject, $message, $headers);

	*/
 } else {

 	echo('
	<form action="send.php" method="post" class="form-group">


 	 <div class="form-group">
    <label for="from">from</label>
    <input type="text" class="form-control" id="from" placeholder="from" name="from" value="');
    echo($default_from);
    echo('">
  	</div>

    <div class="form-group">
    <label for="mailto">mailt to</label>
    <input type="text" class="form-control" id="inputEmail" placeholder="email" name="mailto" value="');
    echo($default_maito);
    echo('">
  	</div>

    <div class="form-group">
    <label for="cc">copy</label>
    <input type="text" class="form-control" id="cc" placeholder="email" name="cc" value="');
    echo($default_cc);
    echo('">
  	</div>

 	 <div class="form-group">
    <label for="subject">subject</label>
    <input type="text" class="form-control" id="subject" placeholder="subject" name="subject" value="');
    echo($default_subject);
    echo('">
  	</div>

	<div class="form-group">
	<label for="Message">Message</label>
	<textarea name="massage" class="form-control" id="Message" rows="3">');
	echo($default_message);
	echo('</textarea>
	</div>

  	<div class="checkbox">
    <label>
      <input type="checkbox" name="save"checked>Save date and counters to config as control</input>
    </label>
  	</div>

  	<button type="submit" class="btn btn-default">Submit</button>
    </form>
	');
	}

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