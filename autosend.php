<?php
error_reporting(0);
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
date_default_timezone_set("Europe/Kiev"); 
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

$runtime = date("Y-m-d H:i:s");

$xml = simplexml_load_file('/usr/local/www/apache24/data/minoltas/config.xml');
//$xml = simplexml_load_file('config.xml');


$from = $xml->global->defaultFrom;
$to = $xml->global->defaultTo;
$cc = $xml->global->defaultCc;
$subject = $xml->global->defaultSubject;;
$message = "Состояние счетчиков печати на ПАО \"ЕВРАЗ Днепродзержинский КХЗ\" \r\n";

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
	$message .= $model.'   serial: '.$serial.'   current counter: '.$info["$serial"].'     control date: '.$lasttime["$serial"]."\r\n";
}

 //$headers = 'From: '.$from."\r\n"."Cc: ".$cc."\r\n"."Content-type: text/plain; charset=UTF-8 \r\n".'X-Mailer: PHP/'.phpversion();
 
   require_once '/usr/local/www/apache24/data/minoltas/phpmailer/PHPMailerAutoload.php';

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
  $mail->send();

/*
 mail($to, $subject, $message, $headers);
*/

foreach($xml->printers->printer as $printer) {
		$printer->lastControlCount=$info["$printer->serial"];
		$printer->lastControlTime=$lasttime["$printer->serial"];
}

 $xml->asXML('/usr/local/www/apache24/data/minoltas/config.xml');
?>
