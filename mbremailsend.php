<!DOCTYPE html>
<html>
<head>
<title>Email Conformantion</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();

include 'Incls/datautils.inc.php';
//include 'Incls/vardump.inc.php';

$mcid = $_SESSION['ActiveMCID'];

$emarrayin = array();
$to = $_REQUEST['to'];
$sender = $EmailFROM;				// defined in datautils.inc.php

list($mcid, $emaddr) = explode(':',$to);
//echo "emaddr: $emaddr<br>";
$emh = htmlentities($emaddr);
$emarrayin[] = $mcid . ': ' . $emaddr;
//echo "emh: $emh<br>";

$sufrom = $_SESSION['SessionUser'];
$sql = "SELECT `MCID` FROM `adminusers` WHERE `UserID` = '$sufrom'";
$res = doSQLsubmitted($sql);
$r = $res -> fetch_assoc();
$fromMCID = '  ('.$r['MCID'].')';
//$echo "sufrom: $sufrom, fromMCID: $fromMCID<br>";

$subject = $_REQUEST['subject'];
$body = $_REQUEST['body'];

$trans = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ');
$subject = strtr($subject, $trans);
$message  = strtr($body, $trans);

echo '<div class="container">
<h3>Email Send Confirmation</h3>
<a class="btn btn-primary" href="mbrinfotabbed.php">RETURN</a>
<br><br><strong>To: </strong>'.$emh.'<br>
<strong>From: </strong>'.$sender.'<br>
<strong>Subject:</strong><br>' . $subject . '<br>
<strong>Message:</strong><br>' . $message . '<br>';
//echo $trmsg;

// format email message and write to queue
$tce = 1;
$subject = $subject . $fromMCID;
$prefix = date('YmdHis');
$listname = "../MailQ/$prefix.$tce.LIST";
$msgname  = "../MailQ/$prefix.$tce.MSG";
//create message string for output
$msgarray[] = $sender; $msgarray[] = $subject; $msgarray[] = $message;
$listval = "Original list size: $tce";
//echo "list: $listname, msg: $msgname, lock: $lockname<br>";
sort($emarrayin);
file_put_contents($listname, implode("\n", $emarrayin));
file_put_contents($msgname, implode("\n", $msgarray));

$log = "MbrEmailSend sent a msg to $mcid with subject: $subject";
addlogentry($log);

echo 'server: ' . $_SERVER['SERVER_NAME'] .'<br>';
echo 'Message written to the send queue.<br>';
if ($_SERVER['SERVER_NAME'] != 'localhost') {
  echo '<br>Starting sender program at ' . date('r') . '<br>';
  // kick the mailsender routine on its way 
  // cron will automatically schedule every hour, but this gets it start right now
  // output of command will be in mailsenderlog.txt
  $cmd = '/home/pacwilica/bin/mailsender';
  exec($cmd . " > /home/pacwilica/public_html_apps/mailsenderlog.txt &");
  }

// finally add new correspondence record noting send of this email
$fields['CorrespondenceType'] = 'Email Message';
$fields['DateSent'] = date('Y-m-d');
$fields['MCID'] = $mcid;
$fields['CorrespondenceType'] = 'EmailMsg';
$fields['Notes'] = "Subject: $subject";
sqlinsert('correspondence', $fields);
$mbrarray = array();									// update member record summary info
$mbrarray['LastCorrDate'] =  $fields['DateSent'];
$mbrarray['LastCorrType'] = 'EmailMessage';
sqlupdate('members', $mbrarray, "`MCID` = '$mcid';");

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
