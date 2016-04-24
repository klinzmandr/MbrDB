<html>
<head>
</head>
<body>
<?php
//include 'Incls/vardump.inc.php';
$em = $_REQUEST['unsubscribe'];
$mcid = $_REQUEST['MCID'];

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {
	$ema = urlencode($em);
print <<<pageOut
<a href="http://pacwilica.org"><img border="0" src="config/logo_bg.jpg"></a>
<h2>PWC Unsubscribe Confirmation</h2>
<p>Please confirm your request to unsubscribe from future mailings by clicking the "Unsubscribe" button below.</p>
<p>You may cancel this action and return to the <a href="https://pacwilica.org">HOME</a> web page.</p>
<FORM method=POST action="unsubscribenew.php">
<input type=hidden name=unsubscribe value=$em>
<input type=hidden name=mcid value=$mcid>
<input type=hidden name=action value=send>
<input type=submit name=submit value="Unsubscribe">
</FORM>
</body></html>
pageOut;
	exit;
	}

// action == 'send'
if ($em == '') {
	echo "<h4>No email address supplied</h4>";
	exit;
	}

$ema = htmlentities($em, ENT_COMPAT,'ISO-8859-1', true);

$to = $EmailTO;
//$to = 'dave.klinzman@yahoo.com';
$subj = 'PWCUnsub: Unsubscribe Request';
$msg = "<p>Please remove the following email address from future email distributions: $ema</p>
<p>MCID: $mcid</p>";

mail($to, $subj, $msg);

echo "<a href=\"$HomeURL\">
<img border=\"0\" src=\"./config/logo_bg.jpg\"></a>
<h2>PWC Unsubscribe Confirmation</h2>";
echo "<p>Email address to be removed: $em</p>";
echo "<p>Thank you for your past support.</p>
<p>Your request has been forwarded for processing.  You may now close this window or click the following link to return to the <a href=\"https://pacwilica.org\">HOME PAGE</a> of the web site.</p>
</body>
</html>";
?>
