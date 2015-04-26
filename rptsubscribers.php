<!DOCTYPE html>
<html>
<head>
<title>Subscribers Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<div class="container">
<?php
session_start();
include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<h3>Subscribing Members Report&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>';
if ($action == '') {
print <<<pagePart1
<p>This report provides a reconciliation point for members that have been indentified as &apos;subscribing&apos; members where a subscribing member is defined as having a Member Status of &apos;1-Member&apos; with a Member Type of &apos;1-Subscriber&apos; or a Member Status of &apos;2-Volunteer&apos; with a Member Type of &apos;2-VolSubsciber&apos;.  
<p>This report has 5 potential sections:</p>
<ol>
	<li>Listing of all subscription members whose last DUES payments was marked as a &apos;non-subscription&apos; payment but their membership status and type indicate them as a &apos;subscribing&apos; member.  These MCID&apos;s should be examined and either the payment changed to a &apos;subscription&apos; payment OR the member&apos;s record be updated to make them a &apos;regular&apos; member or volunteer.</li>
	<li>Listing of all regular members whose last DUES payments was marked as a &apos;subscription&apos; payment but their membership status and type do NOT indicate them as a &apos;subscribing&apos; member.  These MCID&apos;s should be examined and either the payment changed to a &apos;regular&apos; Purpose OR the member&apos;s record be updated to make them a &apos;subscribing&apos; member or volunteer.</li>

	<li>Listing of all &apos;subscribing&apos; members that do not have a &apos;subscription&apos; DUES payment within the last 120 days.  These MCID&apos;s may need to be reclassified making them &apos;non-subscribers&apos; or the DUES payment deleted and re-entered as a subscription payment.</li>

	<li>List of scribing members with current payments made within the last 120 days with a payment that would appear to be incorrectly categoriezed.  This payment(s) may need to be reclassified by deleteing the DUES payment and re-entering it as a subscription payment.</li>
	
	<li>Listing of all MCID&apos;s that are registered as &apos;subscribing&apos; members that have a DUES payment designated as a &apos;subscription&apos; payment within the last 120 days.</li>

</ol>
<p>NOTE: the first three sections will not appear unless there is a qualifying MCID to be listed.</p>

<a class="btn btn-success" href="rptsubscribers.php?action=report">CONTINUE</a><br /><br />
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

pagePart1;
exit;
}

// create the report output
if ($action == 'report') {
// =================================================	
// Section 1: create subscriber member report that have last dues payment as non-subscription
	echo '<h4>1. Subscribing Members with Last Payment marked as Dues is a &apos;non-subscription&apos; Payment</h4>';
	$p = array(); $inarray = array(); $sqlstr = ''; $instr = '';
	$sql = "SELECT `f`.`MCID`, `f`.`Program`, `f`.`TotalAmount`, `f`.`DonationDate` 
	FROM ( 
		SELECT `MCID`, MAX( `DonationDate` ) AS `MaxDate` 
		FROM `pwcmbrdb`.`donations` 
		GROUP BY `MCID` ) AS `x` 
	INNER JOIN `donations` AS `f` ON `f`.`MCID` = `x`.`MCID` 
		AND `f`.`DonationDate` = `x`.`MaxDate` 
	WHERE `f`.`Program` LIKE 'dues%'
		AND `f`.`Program` NOT LIKE '%subscr%';";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
//	echo "Rows returned: $rowcount<br>";
	while ($r = $res->fetch_assoc()) {
//		echo "<pre>subscription "; print_r($r); echo '</pre>';
		$inarray[] = $r[MCID];
		$p[$r[MCID]] = $r;
		}
//	echo "sqlstr: $sqlstr<br>";
//	echo '<pre>payments '; print_r($p); echo '</pre>';
	$instr = implode("','", $inarray);
	$sqlstr = "SELECT * FROM `members` 
	WHERE `MCID` IN ('" . $instr . "')
		AND `Inactive` = 'FALSE'
		AND `MCtype` LIKE '%subscr%';";
// now get the corresonding members and check them
	$res = doSQLsubmitted($sqlstr);
	$rowcount = $res->num_rows;
//	echo "Rows returned: $rowcount<br>";

	if ($rowcount > 0) {	
		echo 'Subscription members having their last Dues payment marked as a &apos;non-subsciption&apos; payment.  Their status should be reveiwed and either the payment&apos;s Program or the member&apos; status changed appropriately.<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';
		while ($r = $res->fetch_assoc()) {
//			echo '<pre>Exceptions '; print_r($r); echo '</pre>';
			$mcid = $r[MCID];
			$program = $p[$mcid][Program];
			$lastpay = $p[$mcid][DonationDate];
			echo "<tr><td>$r[MCID]</td><td>$program</td><td>$lastpay</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
			}
		}
	echo '</table>----- END OF LIST -----<br>';

// ======================================================
// Section 2: create non-subscribers report - first get array of last subscriptions
	$p = array(); $inarray = array(); $sqlstr = ''; $instr = '';
	$sql = "SELECT `f`.`MCID`, `f`.`Program`, `f`.`TotalAmount`, `f`.`DonationDate` 
	FROM ( 
		SELECT `MCID`, MAX( `DonationDate` ) AS `MaxDate` 
		FROM `pwcmbrdb`.`donations` 
		GROUP BY `MCID` ) AS `x` 
	INNER JOIN `donations` AS `f` ON `f`.`MCID` = `x`.`MCID` 
		AND `f`.`DonationDate` = `x`.`MaxDate` 
	WHERE `f`.`Program` LIKE '%subscr%';";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
//	echo "Rows returned: $rowcount<br>";
	while ($r = $res->fetch_assoc()) {
//		echo "<pre>subscription "; print_r($r); echo '</pre>';
		$inarray[] = $r[MCID];
		$p[$r[MCID]] = $r;
		}
	$instr = implode("','", $inarray);
	$sqlstr = "SELECT * FROM `members` 
	WHERE `MCID` IN ('" . $instr . "')
	AND `Inactive` = 'FALSE';";
//	echo "sqlstr: $sqlstr<br>";
//	echo '<pre>payments '; print_r($p); echo '</pre>';

// now get the corresonding members and check them
	$res = doSQLsubmitted($sqlstr);
	$rowcount = $res->num_rows;
//	echo "Rows returned: $rowcount<br>";
	echo '<h4>2. Non-Subscribing Members with Last Payment marked as Dues is a &apos;subscription&apos; Payment</h4>';
	if ($rowcount > 0) {	
		echo 'Members having their last Dues payment marked as a &apos;subsciption&apos; payment but are not noted as a subscribing member or subscribing volunteer.<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';
		while ($r = $res->fetch_assoc()) {
			$mcid = $r[MCID];
			$program = $p[$mcid][Program];
			$lastpay = $p[$mcid][DonationDate];
			if (($ret = stripos($r[MCtype],'subscr')) !== FALSE) continue;
			echo "<tr><td>$r[MCID]</td><td>$program</td><td>$lastpay</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			} 
		//echo '<pre>Exceptions '; print_r($r); echo '</pre>';
		}
	echo '</table>----- END OF LIST -----<br>';


// get all those subscribers with last payment as a subscription
	//echo "Subscribers<br />";
	$sql = "
	SELECT `donations`.`MCID`, 
		`donations`.`Purpose`, 
		MAX( `donations`.`Program` ) AS `LastProg`, 
		MAX( `donations`.`DonationDate`) AS `LastDate`, 
		`donations`.`TotalAmount`, 
		`members`.`NameLabel1stline`, 
		`members`.`MCtype`,
		`members`.`PrimaryPhone`,
		`members`.`EmailAddress`,
		`members`.`LastDuesAmount`,
		`members`.`Notes`
	FROM { OJ `donations` 
		LEFT OUTER JOIN `members` ON `donations`.`MCID` = `members`.`MCID` } 
	WHERE `donations`.`Purpose` = 'dues' 
		AND `members`.`MCtype` LIKE '%subscr%' 
	GROUP BY `donations`.`MCID` 
	HAVING ( ( ( `LastProg` ) IS NULL ) 
		OR ( ( `LastProg` ) LIKE '%dues-subscr%' ) )
	";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	//echo "rowcount: $rowcount<br />";
	
	$subarray = array();		// for current subscribers
	$delarray = array();		// for delinquent subscribers
	$payarray = array();		// for incorrectly classified subscription payments (Program == NULL)
	
	$expdate = strtotime('-120 days'); $totamt = 0;
	$totamt = 0; $subamt = 0;
	
	// this will classify the payment information
	while ($r = $res->fetch_assoc()) {
		//echo '<pre>Subscribers '; print_r($r); echo '</pre>';
		if (strtotime($r[LastDate]) <= $expdate)	{		
			$delarray[$r[MCID]] = $r;									// subscription payment out of expiration period
			continue;
			}
		if ($r[LastProg] == '') { 
			$payarray[$r[MCID]] = $r;									// payment missing a Program classification
			continue;
			}

		$subarray[$r[MCID]] = $r;										// all info good 
		$totamt += $r[TotalAmount];
		$subamt += $r[LastDuesAmount]; 
		}

// =================================================		
// Section 3: list all those delinquent and/or improperly classified
	echo '<h4>3. Delinquent Subscribing Members</h4>';
	if (count($delarray) > 0) {
		echo 'Subscribing members with no subscription payment in last 120 days.  <br>(NOTE: some may also have their last payments improperly classified.)<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';
		ksort($delarray);
		foreach ($delarray as $r) {
			echo "<tr><td>$r[MCID]</td><td>$r[LastProg]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			}
		echo '</table>----- END OF LIST -----';
		}

// =================================================
// Section 4: now list all those with unclassified payment program
	echo '<h4>4. Subscribing Members with Unclassified Payment</h4>';
	if (count($payarray) > 0) {
		echo 'Subscribing members with a subscription payment without a payment Program designated.<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';
		ksort($payarray);
		foreach ($payarray as $r) {
			echo "<tr><td>$r[MCID]</td><td>$r[LastProg]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			}
		}
	echo '</table>----- END OF LIST -----';

// =================================================
// Section 5:listing of all current subscribers with proper payment info
	$subamt = number_format($subamt,2);
	echo '<h4> 5. Current Subscribing Members ('.count($subarray).')</h4>
	Subscribing members with current, up to date subscription payments.<br>';
	echo "Total value of all subscriptions: $$subamt<br>";
	echo '<table class="table-condensed">
	<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>LastDues</th><th>Name</th><th>MemType</th><th>Phone</th><th>Email Address</th></tr>';
	ksort($subarray);
	foreach ($subarray as $r) {
		echo "<tr><td>$r[MCID]</td><td>$r[LastProg]</td><td>$r[LastDate]</td><td>$r[LastDuesAmount]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td></tr>";
		//echo '<pre>Subscribers '; print_r($subarray); echo '</pre>';	
		}
	echo '</table>----- END OF LIST -----';
	
}


?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
