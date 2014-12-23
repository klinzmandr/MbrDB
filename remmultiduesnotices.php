<!DOCTYPE html>
<html>
<head>
<title>Dues Owed List</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onload="initAllFields(MemStat)">
<?php
// ====================================
//
//This page is a rework of the reminders system to list those needing reminders with 
// checkboxes.  The intention is to allow selection of one or more needing a mail or email
// reminder sent and send them at the same time as well as set one or more members inactive 
// when multiple reminders have had no response.
//
// Associated pages are:
//   remmultiemail.php -> remmultiemailupd.php   	to send email message
//   remmultimail.php -> remmultimailupd.php			to print mail message
//	 remmultimakeinactive.php											to set members inactive
//
//These 5 pages all have to work in sync to make this work out.
//
//=====================================
session_start();
																// To allow dues paid for by another and low end donors all who
																// need to get newsletters as members do.
$duesthreshold = 0;						  // NOTE: this is the threshold for sending a reminder
																// if less than or equal to the threshold, no reminder is listed
																//
																// number of days to elapse before members receiving reminders
$listingthreshold = 30;					// are listed again in this report
																//
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

// select info from database.  rows returned will be sorted by MCID then DonatedDate
$rnseq = isset($_REQUEST['rnseq']) ? $_REQUEST['rnseq'] : $_SESSION['rnseq'];
if ($rnseq == "") $rnseq = 'SORT_DESC';
$_SESSION['rnseq'] = $rnseq;

$rptmemstatus = isset($_REQUEST['rptmemstatus']) ? $_REQUEST['rptmemstatus'] : $_SESSION['rptmemstatus'];
if ($rptmemstatus == "") $rptmemstatus = 1;
$_SESSION['rptmemstatus'] = $rptmemstatus;

if (($rptmemstatus == 0) OR ($rptmemstatus == 3)) 
	$rpthaving = "`donations`.`Purpose` = 'dues' OR `donations`.`Purpose` LIKE '%don%'";
else
	$rpthaving = "`donations`.`Purpose` = 'dues'";

$expdate = calcexpirationdate();									// this is the expiration period

$sql = "SELECT `donations`.`MCID`, `donations`.`Purpose`, `donations`.`DonationDate`, 
	`donations`.`TotalAmount`, 
  MAX( `donations`.`DonationDate` ) as MaxDate, 
  `members`.*
FROM `donations`, `members` 
WHERE `donations`.`MCID` = `members`.`MCID` 
	AND `members`.`Inactive` = 'FALSE' 
	AND `members`.`MemStatus` = $rptmemstatus 
GROUP BY `donations`.`MCID`, `donations`.`Purpose` 
 HAVING ( $rpthaving );";

//echo "rptmemstatus: $rptmemstatus<br />";
//echo "expdate: $expdate<br />";
//echo "sql: $sql<br>";
$results = doSQLsubmitted($sql);

// parse out those rows to just show the latest payment made
$nbr_rows = $results->num_rows;
//echo "rows returned from sql: $nbr_rows<br>";
$count = 0;
while ($row = $results->fetch_assoc()) {
	// ignore payments within expiration period
	if (strtotime($row['MaxDate']) < strtotime($expdate)) $resarray[] = $row;
	else {
		//echo '<pre>expdate reject '; print_r($row); echo '</pre>';
		}
	}
//ksort($resarray);
//echo '<pre>Selected rows: '; print_r($resarray); echo '</pre>';
$rowcount = count($resarray);
//echo "rowcount after expire date filter: $rowcount<br />";
if ($rowcount == 0) {
	$_SESSION['rptmemstatus'] = '';
	print <<<noExp
<div class="container">
<h4>There are no expired memberships to report.</h4>
<b>Please note:</b><br />
To be included in this list the MCID must:
<ol>
	<li>be active (e.g. member &apos;Inactive&apos; flag = &apos;FALSE&apos;,</li>
	<li>a payment marked as &apos;Dues&apos; has not been entered in the last 11 months, and</li>
	<li>the last notice has not been sent within the last 10 days.</li>
</ol>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
</div></body></html>
noExp;
	exit;
	}

// find mcid's with in-progress reminders, load date of last one and count of each mcid's into sep arrays
$sql = "SELECT `correspondence`.`MCID`, `correspondence`.`DateSent`, `correspondence`.`Reminders`, `members`.`MemStatus`, `members`.`Inactive` 
FROM `correspondence`, `members`
WHERE `members`.`MCID` = `correspondence`.`MCID` 
  AND `correspondence`.`Reminders` IS NOT NULL 
  AND `members`.`Inactive` = 'FALSE' 
ORDER BY `correspondence`.`MCID` ASC, `correspondence`.`DateSent` ASC";
$results = doSQLsubmitted($sql);
$dr = array();			// array of mcid's with date of last reminder sent
$ar = array();			// array of mcid's with count of reminders sent
// parse result rows to find those reminders with renewal notices
while ($r = $results->fetch_assoc()) {
	$mcidid = $r['MCID'];
	if (stripos($r['Reminders'],"remind") !== FALSE) {  // count the reminder notices sent and latest date
		$ar[$mcidid] += 1;
		if (strtotime($dr[$mcidid]) <= strtotime($r[DateSent])) {
			$dr[$mcidid] = $r[DateSent];
			//echo 'mcid: '.$mcidid.', corr time: ' . $dr[$mcidid] . '<br />';
			}
		}
	// sort order of returned rows means last row for an mcid is the last thing done: reminder vs renewal
	if (stripos($r['Reminders'],"renew") !== FALSE) {		// forget it all since a renewal was done last
		unset($ar[$mcidid]); unset($dr[$mcidid]);
		//echo "unset mcid: $mcidid<br />";
		}
	}
//echo "<pre>active MCID's"; print_r($ar); echo "</pre>";
print <<<formPart1
<script>
function initAllFields(form) {
// Initialize all form controls
  with (form) {
		initSelect(rptmemstatus,"$rptmemstatus");
		initSelect(rnseq,"$rnseq");
  	}
	}
	
function initSelect(control,value) {
// Initialize a selection list (single valued)
	if (value == "") return;
	for (var i = 0; i < control.length; i++) {
		if (control.options[i].value == value) {
			control.options[i].selected = true;
			break;
			}
		}
	}

</script>

<div class="container">
<form class="form-inline" name="MemStat">
<select onchange="this.form.submit()" name="rptmemstatus" size="1">
<option value="">Select New Status</option>
<option value="0">0-Contact</option>
<option value="1">1-Member</option>
<option value="2">2-Volunteer</option>
<option value="3">3-Donor</option>
</select>
<select onchange="this.form.submit()" name="rnseq" size="1">
<option value="SORT_DESC">Latest First</option>
<option value="SORT_ASC">Oldest First</option>
</select>
</form>

<script>
function redirector(button) {
	var btn = button.value;
	//alert("redirect button action: " + btn);
	if (btn == "SendEmail") {
		var l = EmailAddr.length;
		if (l == 0) {
			alert("No FROM email address configured.\\nContact the system administrator.");
			return false;
			}
		document.boxform.action= "remmultiemail.php";
		}
	if (btn == "SendMail") document.boxform.action = "remmultimail.php";
	if (btn == "MakeInactive") document.boxform.action = "remmultimakeinactive.php";
	return true;
	}
</script>

formPart1;

// create array to use to sort
//echo "resarray count before sort: " . count($resarray) . '<br>';
foreach ($resarray as $row) {
	$key = $row['MaxDate'] . $row[MCID];			// to create a unique key
	$dondate[$key] = $row;		// sorting by donation date + MCID
	//echo "<pre> key: $key "; print_r($row); echo "</pre>";
	}
//echo "resarray count after sort: " . count($dondate) . '<br>';
// sort result array returned from sqli
if ($rnseq == 'SORT_ASC') {
	ksort($dondate); }
else {
	krsort($dondate); }
// echo "<pre> sorted "; print_r($dondate); echo "</pre>";
//echo "count in sorted array: " . count($dondate) . '<br />';

// create the output listing as a form
$delay = "today -$listingthreshold days +24 hours";
$notedate = strtotime($delay);

// NOTE: form action set based on button selection
echo "<form name=\"boxform\" action=\"\" method=\"post\">";
foreach ($dondate as $key => $row) {
	//echo '<pre> resarray '; print_r($row); echo '</pre>';
	if (stripos($row[MCtype], 'lifetime') !== FALSE) continue;	// NO REMINDER FOR LIFETIME MEMBERS
	if ($row[TotalAmount] <= $duesthreshold) continue;					// NO REMINDER IF < THAN THRESHOLD VALUE
	//echo "notedate: $notedate, dondate: ".strtotime($dr[$row[MCID]]).'<br />';
	if ($notedate <= strtotime($dr[$row[MCID]])) continue; 			// NO REMINDER IF < DELAY DAYS
	$finalarray[$key] = $row;
	//echo '<pre> final array '; print_r($row); echo '</pre>';
	}

if (count($finalarray) == 0) {
	print <<<emptyList
<div class="container">
<h4>There are no expired memberships to report.</h4>
<b>Please note:</b><br />
To be included in this list the MCID must:
<ol>
	<li>be active (e.g. member &apos;Inactive&apos; flag = &apos;FALSE&apos;,</li>
	<li>a payment marked as &apos;Dues&apos; has not been entered in the last 11 months, and</li>
	<li>the last reminder has not been sent within the last $listingthreshold days.</li>
</ol>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
</div></body></html>
emptyList;
	exit;
	}

else {
$rowcount = count($finalarray);
$rpttitle = "List of $rowcount Members with expired memberships";
if ($rptmemstatus == 2) $rpttitle = "List of $rowcount Volunteers with expired memberships";
elseif ($rptmemstatus == 3) $rpttitle = "List of $rowcount Donors without recent funding support";
elseif ($rptmemstatus == 0) $rpttitle = "List of $rowcount Contacts without recent funding support";
echo "<font size=\"+3\">$rpttitle</font><br />";
echo "Notices sent since " . date('Y-m-d H:i:s',$notedate) .' not listed.<br />';
echo "<table border=\"0\" class=\"table table-condensed\">
<tr><th>MCID</th><th>Name</th><th align=\"center\">EMail?</th><th align=\"center\">Mail?</th><th>Last Paid</th><th>Amount</th><th>Purpose</th><th align=\"center\">Inactive?</th><th>Rem Cnt.</th><th>LastReminder</th><th>RemType</th></tr>";

foreach ($finalarray as $key=>$row) {
	$mcid=rtrim($row['MCID']); $dondate=$key; 
	$amount=rtrim($row['TotalAmount']);	$labelname=rtrim($row['NameLabel1stline']); 
	$purpose=$row['Purpose']; $email=$row[E_Mail]; $mail=$row[Mail];
	$maxdate=$row[MaxDate]; $emailaddr = $row['EmailAddress'];
	$lastduesamount = $row['LastDuesAmount'];
	$lastdonamount = $row['LastDonAmount'];
	$remdate = $dr[$mcid];
	$remcnt = $ar[$mcid];
	$remtype = '';
	if ($remcnt > 0) $remtype = $row[LastCorrType];
	$emcode = $mcid . ':' . $emailaddr;

	//$mok = "<input type=\"checkbox\" name=\"mail[]\" value=\"$emcode\" disabled>";
	$mok = '';
	if ($row[Mail] == 'TRUE') {
		$mok = "<input type=\"checkbox\" name=\"mail[]\" value=\"$emcode\">";
		}

	//$emok = "<input type=\"checkbox\" name=\"email[]\" value=\"$emcode\" disabled>";
	$emok = '';
	if ($row[E_Mail] == 'TRUE') {
		$emok = "<input type=\"checkbox\" name=\"email[]\" value=\"$emcode\">";
		}
		
	$inact = "<input type=\"checkbox\" name=\"inact[]\" value=\"$emcode\">";
	if ($purpose == 'Dues') $amt = $lastduesamount;
	else $amt = $lastdonamount;
print <<<bulletForm
<tr><td><a href="MbrInfotabbed.php?filter=$mcid">$mcid</a></td><td>$labelname</td><td align="center">$emok</td><td align="center">$mok</td><td>$maxdate</td><td align="right">$$amt</td><td>$purpose</td><td align="center">$inact</td><td>$remcnt</td><td>$remdate</td><td>$remtype</td></tr>
</div>  <!-- container -->

bulletForm;
	}
}
echo "<tr><td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type=\"submit\" value=\"SendEmail\" onclick=\"return redirector(this)\"></td>
<td><input type=\"submit\" name=\"submit\" value=\"SendMail\" onclick=\"return redirector(this)\"></td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td><input type=\"submit\" name=\"submit\" value=\"MakeInactive\" onclick=\"return redirector(this)\"></td>
<td>&nbsp;</td><td>&nbsp;</td>
</tr></form></table>";

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>