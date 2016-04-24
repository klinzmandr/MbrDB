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

function clickable($string){
  // if anchors already exist - don't translate
  if (stripos($string,'</a>') !== FALSE) return($string); 
  // make sure there is an http:// on all URLs
  $string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
  // make all URLs links
  $string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A target=\"_blank\" href=\"$1\">$1</A>",$string);
  // make all emails hot links
  $string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$string);
  return $string;
	}

echo "<html><head><title>Send Mail Confirmation</title>";

$emarrayin = array();
$emarrayin = $_REQUEST['email'];

// delete senders email address from list
//echo '<pre>emarrayin before '; print_r($emarrayin); echo '</pre>';
$from = $EmailFROM;			// defined in datautils.inc.php
echo "from: $from<br />";
foreach ($emarrayin as $k => $v) {
	if (stripos($v, $from) !== FALSE) {
		//echo "found it at $k<br />";
		unset($emarrayin[$k]);
		}
	}
//echo '<pre>emarrayin after '; print_r($emarrayin); echo '</pre>';

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

echo "<div class=\"container\">
<h3>Bulk Email Confirmation&nbsp;&nbsp;<a href=\"remmultiduesnotices.php\" class=\"btn btn-primary\">RETURN</a></h3>";

$tce = count($emarray); $sll = strlen($subject);
$sl = isset($_REQUEST['Topic'])? $sll : 0;

//echo "tce: $tce, sl: $sl<br>";
if (($tce <= 0) || ($sl == 0)) {
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
$logmsg = '';
$starttime = date('r', strtotime(now));
$logmsg .= "<br>\n<br>\n***Send Message Processing Started at $starttime***<br>\n";

$logmsg .= "<br><strong>To:</strong><br>\n";
foreach ($emarray as $addr) {
	$emaddr = htmlentities($addr, ENT_COMPAT,'ISO-8859-1', true);
	$logmsg .= "&nbsp;&nbsp;$emaddr<br>\n";
	}

$logmsg .= "<br><strong>From: </strong>$from<br>\n";

$logmsg .=  "<br><b>Header:</b><br>\n";
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $from . "\r\n";
$headers .= "Return-Path: " . $from . "\r\n";   // these two to set reply address
$logmsg .= $headers . "<br>\n";

$foption = "-f" . $from;												// notify of undeliverable mail to sender
$logmsg .= '<br><b>f-option</b><br>' . $foption . "<br>\n";

$trmsg = clickable($message); 							// turn url's into links

$trans = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ');
$trsub = strtr($subject, $trans);
$trmsg  = strtr($trmsg, $trans);

$logmsg .= "<br><strong>Subject:</strong><br>\n";
$logmsg .=  $trsub . "\n";
$logmsg .=  "<br><strong>Message:</strong><br>\n";
$logmsg .=  $trmsg . "<br>\n";

// format email message
$subject = "PWC: " . $trsub;

$cnt = count($emarray);

$errcnt = 0;
//foreach ($em as $addr) {
for ($i = 0; $i < count($emarray); $i++) {
	$addr = $emarray[$i];
	$mcid = $mcidarray[$i];
	if ($addr == "") { continue; }
	$to = $addr;
	$finmsg = "";
	$finmsg = $trmsg;
	$tag = "<br><br><font size=1><center>
	<a href=".$HomeURL."/unsubscribenew.php?unsubscribe=";		// HomeURL defined in datautils.inc.php
	$tag .= urlencode($to);
	$tag .= "&MCID=$mcid>Click to unsubscribe from future emails from PWC.</a></center></font>";
	$finmsg .= $tag;
	$finmsg = wordwrap($finmsg);

	$mresp = TRUE; 
	if (isset($_SESSION[TEST_MODE])) {
			echo "Test mode on - mail not sent to " . htmlentities($to) . "<br />";
			$logmsg .= "Test mode on - mail not sent to ".htmlentities($to)."<br>\n";
			//$mresp = mail($to, $subject, $finmsg, $headers, $foption);
			}
		else {
			//echo "Test mode on - mail not sent to " . htmlentities($to) . "<br />";
			//$logmsg .= "Test mode on - mail not sent to ".htmlentities($to)."<br>\n";
		  $mresp = mail($to, $subject, $finmsg, $headers, $foption);
			}
		
	 if ($mresp == FALSE) {
	 	$toaddr = htmlentities($to, ENT_COMPAT,'ISO-8859-1', true);
		$logmsg .= "**ERROR: mail function failed on " . $toaddr . "<br>\n";
		echo "**ERROR: mail function failed on " . $toaddr . "<br>";
		}
	// note each email in correspondence log for each mcid
	$corrarray = array();									// add to correspondence log
	$corrarray[MCID] = $mcid;
	$corrarray[CorrespondenceType] = "EmailReminder";
	$corrarray[DateSent] = date('Y-m-d', strtotime(now)); 
	$corrarray[Notes] = "Email Reminder Subject: $trsub";
	$corrarray[Reminders] = 'EMailReminder';
	sqlinsert('correspondence', $corrarray);
	$mbrarray = array();									// update member record summary info
	$mbrarray[LastCorrDate] =  $corrarray[DateSent];
	$mbrarray[LastCorrType] = 'EmailReminder';
	sqlupdate('members', $mbrarray, "`MCID` = '$mcid';");

	usleep(250000); // wait for 1/4 of a second and send next
	}

echo "<br><h4>***Bulk Email Processing Complete***</h4><br>";
echo '<h5>Please refer to the Mail Log Viewer to review the results</h5>';
//echo '<a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a>';
$logmsg .= "Last end tag used: $tag<br />\n";
$endtime = date('r', strtotime(now));
$logmsg .= "<br>\n<br>\n***Send Message Processing Complete at $endtime***<br>\n";
addmaillogentry($logmsg);

print<<<pageBody
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pageBody;
?>