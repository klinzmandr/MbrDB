<!DOCTYPE html>
<html>
<head>
<title>Send Mail List Sender</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php 
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

echo "<html><head><title>Send Mail Confirmation</title>";

$emarrayin = array();
$emarrayin = $_REQUEST['email'];

// delete senders email address from list
//echo '<pre>emarrayin before '; print_r($emarrayin); echo '</pre>';
$sender = $EmailFROM;			// defined in datautils.inc.php
//echo "from: $from<br />";

//echo '<pre>emarrayin after '; print_r($emarrayin); echo '</pre>';
if (count($emarrayin) == 0) {
  print<<<errorMsg
<h3>ERROR: No addresses passed into the script.</h3>Please correct and resubmit.<br><br>
<a href="remmultiduesnotices.php">RETURN</a>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

errorMsg;
exit;
  }
  
foreach ($emarrayin as $k => $v) {
	if (stripos($v, $from) !== FALSE) {
		//echo "found it at $k<br />";
		unset($emarrayin[$k]);
		}
	}

foreach ($emarrayin as $v) {		// unpack the mcid and email address values into diff arrays
	//echo "v: $v<br />";
	list($mcidv, $emv) = explode(":", $v);
	$emarray[] = $emv;
	$mcidarray[] = $mcidv;
	}
//echo '<pre>emailarray '; print_r($emarray); echo '</pre>';
//echo '<pre>mcid '; print_r($mcidarray); echo '</pre>';

$subject = $_REQUEST['Topic'];
$message = $_REQUEST['Letter'];

$trans = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' '); // remove tabs, cr, lf and double \
$subject = strtr($subject, $trans);
$message  = strtr($message, $trans);    // subject and message should be good to go now

echo "<div class=\"container\">
<h3>Send Email Confirmation&nbsp;&nbsp;<a href=\"remmultiduesnotices.php\" class=\"btn btn-primary\">RETURN</a></h3>";

$tce = count($emarray); $sll = strlen($subject);

//echo "tce: $tce, sl: $sll<br>";
if (($tce <= 0) || ($sll == 0)) {
print<<<errorMsg
<h3>ERROR: No addresses in the "TO" list OR the subject line is empty.</h3>Please correct and resubmit.<br><br>
<a href="remmultiduesnotices.php">RETURN</a>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

errorMsg;
exit;
	}

// in order to forward any responses to the originator of a message we need their MCID
// which MUST be present in their admin registration info since the email address 
// they used to log in with may differ from the one in the membership database.
// The MCID is used as a forwarding filter in the email setup of the mail server

$from = $_SESSION['SessionUser'];
$sql = "SELECT `MCID` FROM `adminusers` WHERE `UserID` = '$from'";
$res = doSQLsubmitted($sql);
$r = $res -> fetch_assoc();
$fromMCID = $r[MCID];
if (strlen($fromMCID) == 0) {
  echo "SessionUser: $from, fromMCID: $r[MCID] length: " . strlen($fromMCID) . "<br>";
  echo '<h3 style="color: red; ">ERROR: Your user registration MUST have an MCID.</h3>
  The sender of any message MUST have their MCID registered into their administion login information.<br><br>
  Update the admin registration info before trying again<br>
  <a class="btn btn-danger btn-xs" href="admin.php">HOME PAGE</a>';
  exit(0);
} 
$_SESSION['SessionUserMCID'] = $fromMCID;

// write info into MailQ for cron sender to process
// first create file names: one for list, one for message
$subject = $subject . '  (' . $fromMCID . ')';
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

// finally we note each email in correspondence log for each mcid
foreach ($mcidarray as $mcid) {
  $corrarray = array();									// add to correspondence log
  $corrarray[MCID] = $mcid;
  $corrarray[CorrespondenceType] = "EmailReminder";
  $corrarray[DateSent] = date('Y-m-d', strtotime(now)); 
  $corrarray[Notes] = "Email Reminder Subject: $subject";
  $corrarray[Reminders] = 'EMailReminder';
  sqlinsert('correspondence', $corrarray);
  $mbrarray = array();									// update member record summary info
  $mbrarray[LastCorrDate] =  $corrarray[DateSent];
  $mbrarray[LastCorrType] = 'EmailReminder';
  sqlupdate('members', $mbrarray, "`MCID` = '$mcid';");
  }

// confirmation output to user
//$timetosend = date('g:00 A', strtotime("now + 60 minutes"));
print<<<pageBody
<h4>The message has been queued for sending. It will be sent at the next scheduled time which is every quarter hour in batches of 50.  A separate log record entry will be created for each batch.</h4>
<p>The message subject and text will be sent to all of email reciepents.  Progress of the send may be reviewed by looking at &apos;Reports->Review Mail Log&apos;.  This report will tell you when a message is being sent and the number of recipients remaining or list the recipients and the message when it has been completed.</p><br>
<a class="btn btn-warning btn-xs" target="_blank" href="rptmaillogviewer.php">Review Mail Log</a>

</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pageBody;
?>