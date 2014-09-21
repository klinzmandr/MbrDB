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
<p>This report has 3 potential sections:</p>
<ol>
	<li>Listing of all members whose last DUES payments was marked as a &apos;subscription&apos; payment but their membership status and type do not indicate them as a &apos;subscribing&apos; member.  These MCID&apos;s should be examined and either the payment changed to a &apos;regular&apos; Purpose OR the member&apos;s record be updated to make them a &apos;subscribing&apos; member or volunteer.</li>

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

// create report - first list those that are exceptions
if ($action == 'report') {
	$sql = "SELECT `donations`.`MCID`, `donations`.`Program`, MAX( `donations`.`DonationDate` ) AS `LastPay`, `members`.`NameLabel1stline`, `members`.`MemStatus`, `members`.`MCtype` 
	FROM `donations`, `members` 
	WHERE `donations`.`MCID` = `members`.`MCID` 
		AND `donations`.`Program` LIKE '%subscr%' 
		AND `members`.`MCtype` NOT LIKE '%subscr%' 
	GROUP BY `donations`.`MCID` 
	ORDER BY `donations`.`MCID` ASC;";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	if ($rowcount > 0) {
		echo '<h4>List of exceptions ('.$rowcount.')</h4>Members having their last Dues payment marked as a &apos;subsciption&apos; payment but are not noted as a subscribing member or subscribing volunteer.<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemStatus</th><th>MemType</th></tr>';
		while ($r = $res->fetch_assoc()) {
			echo "<tr><td>$r[MCID]</td><td>$r[Program]</td><td>$r[LastPay]</td><td>$r[NameLabel1stline]</td><td>$r[MemStatus]</td><td>$r[MCtype]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			} 
		echo '</table>----- END OF LIST -----<br>';
		//echo '<pre>Exceptions '; print_r($r); echo '</pre>';
		}
	// now get all those subscribers with last payment as a subscription
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
		`members`.`LastDuesAmount`
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
			$payarray[$r[MCID]] = $r;									// payment classified incorrectly
			continue;
			}
		$subarray[$r[MCID]] = $r;										// all info good 
		$totamt += $r[TotalAmount];
		$subamt += $r[LastDuesAmount]; 
		}

		//echo "<br>subarray count: ".count($subarray).", delarray count: ".count($delarray).', payarray count: '.count($payarray).'<br />';
		
	// now list all those delinquent and/or improperly classified
	if (count($delarray) > 0) {
		echo '<h4>Delinquent Subscribing Members ('.count($delarray).')</h4>
		Subscribing members with no subscription payment in last 120 days.  <br>(NOTE: some may also have their last payments improperly classified.)<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Phone</th><th>Email Address</th></tr>';
		ksort($delarray);
		foreach ($delarray as $r) {
			echo "<tr><td>$r[MCID]</td><td>$r[LastProg]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			}
		echo '</table>----- END OF LIST -----';
		}

// now list all those improperly classified
	if (count($payarray) > 0) {
		echo '<h4>Subscribing Members with Incorrectly Classified Payment ('.count($payarray).')</h4>
		Subscribing members with a subscription payment within last 120 days that apprears to be incorrrectly classified.<br>';
		echo '<table class="table-condensed">
		<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Phone</th><th>Email Address</th></tr>';
		ksort($payarray);
		foreach ($payarray as $r) {
			echo "<tr><td>$r[MCID]</td><td>$r[LastProg]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td></tr>";
		//echo '<pre>Delinquents '; print_r($delarray); echo '</pre>';
			}
		echo '</table>----- END OF LIST -----';
		}

$subamt = number_format($subamt,2);
	// listing of all current subscribers with proper payment info
	echo '<h4>Current Subscribing Members ('.count($subarray).')</h4>
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
