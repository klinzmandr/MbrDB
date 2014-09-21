<!DOCTYPE html>
<html>
<head>
<title>Mbr Exceptions</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onload="initAllFields(MemStat)">
<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

echo "<div class=\"container\"><h3>MbrDB Exception Report&nbsp;&nbsp;<a class=\"btn btn-xs btn-primary\" href=\"javascript:self.close();\">(CLOSE)</a></h3>";

if (!isset($_REQUEST['rpt'])) {
	echo '<h4>Explaination of report</h4>
	<p>The classifications of the records for Mbrdb is:</p>
	<ol>
	<li>Members - records with a member status of &apos;1-Member&apos; with at least 1 payment record marked as &apos;Dues&apos; paid within the expiration period.</li>
	<li>Volunteers - a member (see above) that is donating time as well as being a dues paying member.</li>
	<li>Donors - non-members that provide funding support.  Usually entities that are not a person like a company, estate, trust, business, affiliated organization, etc.  Usually this entity will be registered with a contact person acting as a representative or interal contact for the entity.</li>
	<li>Contacts - None of the above.  This represents the &apos;pool&apos; of candidates for recruitment of both financial and volunteer timesupport.</li>
	</ol>
	<p>It should be the goal is to classify all those who provide financial support and/or volunteer time into as a member, volunteer or donor.  Those that do not qualify should be re-classified as a &apos;0-Contact&apos;</p>
	<p>The expiration date used is 11 months from the current month and is listed with each report section.</p>
	<h4>Report Sections</h4>
	<p>NOTE: a section may not appear if there are no records to list.</p>
	<p><b>0-Contact That Paid Dues or Made A Donaton</b> - lists all records classified as Member Status of 0 (Contacts) that have current funding records paid within the expiration period</p>
	<p><b>1-Members or 2-Volunteers With NO Dues Payment Record</b> - list of all records classified as 1-Member or 2-Volunteer with NO funding records paid within the expiration period.</p>
	<p><b>3-Donors with NO Donations</b> - list of all records classified as 3-Donor with NO donation or dues funding records paid within the expiration period.</p>
	<p><b>Invalid Mail or Email Flag Settings</b> - list of those records that have incosistant flag settings on the Mail and/or Email flags indicating that they want mail and/or email but there is no information provided to do so.</p>
	<a class="btn btn-success" href="rptmemberexceptions.php?rpt">CONTINUE</a>';
	exit;
	}
$expdate = calcexpirationdate();
$sql = "SELECT * 
	FROM `members` 
	WHERE `Inactive` = 'FALSE' 
		AND `MemStatus` = '0' 
		AND ( `LastDuesDate` >= '$expdate' OR `LastDonDate` >= '$expdate' ) 
	ORDER BY `MCID` ASC;"; 
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
//echo "SQL: $sql<br />";
//echo "rowcnt: $rowcnt<br />";
if ($rowcnt > 0) {
	echo "<div class=\"container\"><h4>0-Contact That Paid Dues or Made A Donaton</a></h4>
<p>There were $rowcnt records classified as &apos;0-Contacts&apos; that made a payment marked as either &apos;Dues&apos; or as a &apos;Donation&apos; of some kind.  Those listed are were paid <b>within</b> the expiration period. These member records MAY qualify as members, volunteers or donaors.  
<p>Those in this list should probably be re-clasified as members or donors.</p>
<p>The expiration date used is $expdate</p>";

	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>MemStat</th><th>MCType</th><th>Name</th><th>Last Dues Date</th><th>Last Dues Amt</th><th>Last Don. Date</th><th>Last Don. Amt</th></tr>';
	while ($r = $res->fetch_assoc()) {
		//echo'<pre> contacts '; print_r($r); echo '</pre>';
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MemStatus]</td><td>$r[MCtype]</td><td>$r[NameLabel1stline]</td><td>$r[LastDuesDate]</td><td>$r[LastDuesAmount]</td><td>$r[LastDonDate]</td><td>$r[LastDonAmount]</td></tr>";

		}
	echo '</table>----- END OF LIST -----<br />';
	}

$sql = "SELECT * 
	FROM `members` 
	WHERE `Inactive` = 'FALSE' 
		AND ( `MemStatus` = '1' OR `MemStatus` = '2' ) 
	AND ( `LastDuesDate` IS NULL ) 
	ORDER BY `MCID` ASC;";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
//echo "SQL: $sql<br />";
//echo "rowcount: $rowcount<br />";
if ($rowcount > 0) {
	echo "<h4>1-Members or 2-Volunteers With NO Dues Payment Record</a></h4>
<p>There were $rowcount records classified as &apos;1-Members&apos; or &apos;2-Volunteer&apos;; without ANY payment records marked as &apos;Dues&apos; associated them.  These member records should be reviewed and reclassified if warranted.</p>
<p>If the record is to be retained as a member or volunteer, a $0 dues payment should be entered to remove the record from this list and allow review of this status at the end of the next expiration period.</p>
<p>The expiration date used is $expdate</p>";
	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>Name</th><th>MemStatus</th><th>Last Dues Date</th><th>Last Dues Amount</th></tr>';
	while ($r = $res->fetch_assoc()) {			
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MemStatus]</td><td>$r[LastDuesDate]</td><td>$r[LastDuesAmount]</td></tr>";
		}
	}
echo "</table>----- END OF LIST -----<br />";

$sql = "SELECT * 
	FROM `members` 
	WHERE `Inactive` = 'FALSE' 
		AND ( `MemStatus` = '3' ) 
		AND ( `LastDonDate` IS NOT NULL 
		AND `LastDuesDate` >= '$expdate' ) 
	ORDER BY `MCID` ASC;";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
//echo "SQL: $sql<br />";
//echo "rowcount: $rowcount<br />";
if ($rowcount > 0) {
	echo "<h4>3-Donors with NO Donations</a></h4>
<p>There were $rowcount records classified as &apos;3-Donor&apos; that have NO non-Dues payment logged within the expriation period.  These member records should be reviewed and reclassified if warranted.</p>

<p>The expiration date used is $expdate</p>";
	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>Name</th><th>MemStatus</th><th>Last Don. Date</th><th>Last Don. Amount</th></tr>';
	while ($r = $res->fetch_assoc()) {			
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MemStatus]</td><td>$r[LastDonDate]</td><td>$r[LastDonAmount]</td></tr>";
		}
	}
echo "</table>----- END OF LIST -----<br />";

$sql = "SELECT * 
	FROM `members`  
	WHERE ( `Inactive` = 'FALSE' AND 	MemStatus >= 1 ) 
		AND ( ( `Mail` = 'TRUE' AND  `AddressLine` IS NULL )
		OR  ( `E_Mail` = 'TRUE' AND `EmailAddress` IS NULL ) )
	ORDER BY `MCID` ASC;";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
//$rowcount = $res->num_rows;
//echo "SQL: $sql<br />";
if ($rowcount > 0) {
	echo "<h4>Invalid Mail or Email Flag Settings</h4>
	<p>There are $rowcount ACTIVE member, volunteer or donor records that have EITHER the &apos;Mail OK?&apos; flag set to YES/TRUE witih no information in the Address Line OR the &apos;Email OK?&apos; set to YES/TRUE with no email address provided.  All these records should reviewed and corrected.</p>";
	echo '<table class="table-condensed">
	<tr><th>MCID</th><th>Name</th><th>MC Type</th><th>MailFlag</th><th>AddressLine</th><th>EmailFlag</th>EmailAddress<th></th></tr>';
	while ($r = $res->fetch_assoc()) {
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td></tr>";
		//echo '<pre> mail with no address '; print_r($r); echo '</pre>';	
		}
	echo '</table>----- END OF LIST -----<br>';
	}



?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
