<!DOCTYPE html>
<html>
<head>
<title>Email Send Confermantion</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();

include 'Incls/datautils.inc';
//include 'Incls/vardump.inc';

$mcid = $_SESSION['ActiveMCID'];

function clickable($string){
  // if anchors already exist - don't translate
  if (stripos($string,'<a ') !== FALSE) return($string); 
  // make sure there is an http:// on all URLs
  $string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
  //echo "string: $string<br>";
  // make all URLs links
  $string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A target=\"_blank\" href=\"$1\">$1</A>",$string);
  // make all emails hot links
  $string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$string);
  return $string;
	}

$to = $_REQUEST['to'];
$from = $_REQUEST['from'];
$subject = $_REQUEST['subject'];
$body = $_REQUEST['body'];

$body = clickable($body); // turn url's into links

$trans = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ');
$trsub = strtr($subject, $trans);
$trmsg  = strtr($body, $trans);


echo "<div class=\"container\">";
echo "<h3>Email Send Confirmation</h3>";
echo "<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\">RETURN</a>";

echo "<br><br><strong>To: </strong>$to<br>";
echo "<strong>From: </strong>$from<br>";
echo "<strong>Subject:</strong> ";
echo $trsub;
echo "<br><br><strong>Message:</strong><br>";
echo $trmsg;

// format email message
$subject = "PWCMbr: " . $trsub;

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $from . "\r\n";
$headers .= "Return-Path: " . $from . "\r\n";   // these two to set reply address
$foption = "-f" . $from;												// notify of undeliverable mail to sender

$finmsg = "";
$finmsg = $trmsg;
$finmsg .= "<br><br><font size=1><center><a href=".$HomeURL."/unsubscribenew.php?unsubscribe=";
$finmsg .= $to;
$finmsg .= ">Unsubscribe from further PWC e-mail.</a></center></font>";
$finmsg = wordwrap($finmsg);
echo date("r") . ": Sent To: $to<br>";
$tolist .= $to . "\n";
$mresp = mail($to, $subject, $finmsg, $headers, $foption);
if ($mresp == FALSE) {
	echo "ERROR: an error was returned when sending the email message<br />";
	}
//echo "<h2>TEST MODE ENABLED - reminder noted - but NO email sent<br /></h2>";

// finally add new correspondence record noting send of this email
$fields[CorrespondenceType] = 'EmailNotice';
$fields[DateSent] = date('Y-m-d');
$fields[MCID] = $mcid;
$fields[Reminders] = 'EMailNotice';
$fields[Notes] = "Subject: $trsub";
sqlinsert('correspondence', $fields);

// update member summary info
$mbrflds[LastCorrType] = $fields[CorrespondenceType];  // update member summary info
$mbrflds[LastCorrDate] = $fields[DateSent];
sqlupdate('members', $mbrflds, "`MCID` = '$mcid';");

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
