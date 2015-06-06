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
	echo '<h4>Explanation of report</h4>
	<p>The classifications of the records for Mbrdb is:</p>
	<ol>
	<li>Members - records with a member status of &apos;1-Member&apos; with at least 1 payment record marked as &apos;Dues&apos; paid within the expiration period.</li>
	<li>Volunteers - a member (see above) that is donating time as well as being a dues paying member.</li>
	<li>Donors - non-members that provide funding support.  Usually entities that are not a person like a company, estate, trust, business, affiliated organization, etc.  Usually this entity will be registered with a contact person acting as a representative or interal contact for the entity.</li>
	<li>Contacts - None of the above.  This represents the &apos;pool&apos; of candidates for recruitment of both financial and volunteer time support.</li>
	</ol>
	<p>It should be the goal is to classify all those who provide financial support and/or volunteer time into as a member, volunteer or donor.  Those that do not qualify should be re-classified as a &apos;0-Contact&apos;</p>
	<p>The expiration date used is 11 months from the current month and is listed with each report section.</p>
	<h4>Report Sections</h4>
	<p><b>0-Contact That Paid Dues or Made A Donation</b> - lists all records classified as Member Status of 0 (Contacts) that have current funding records paid within the expiration period</p>
	<p><b>1-Members or 2-Volunteers With NO Dues Payment Record</b> - list of all records classified as 1-Member or 2-Volunteer with NO funding records paid within the expiration period.</p>
	<p><b>3-Donors with NO Donations</b> - list of all records classified as 3-Donor with NO donation or dues funding records paid within the expiration period.</p>
	<p><b>Invalid Mail or Email Flag Settings</b> - list of those records that have inconsistent flag settings on the Mail and/or Email flags indicating that they want mail and/or email but there is no information provided to do so.</p>
	<p><b>Supporter records with NO funding records</b> - list of supporter&apos;s that have no associated funding records.  These should be examined to determine if they should be made inactive.</p>
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
echo "<div class=\"container\"><h4>0-Contact That Paid Dues or Made A Donation</a></h4>";
if ($rowcnt > 0) {
echo "<p>There were $rowcnt records classified as &apos;0-Contacts&apos; that made a payment marked as either &apos;Dues&apos; or as a &apos;Donation&apos; of some kind.  Those listed were paid <b>within</b> the expiration period. These member records MAY qualify as members, volunteers or donors.  
<p>Those in this list should probably be re-classified as members or donors.</p>
<p>The expiration date used is $expdate</p>";

	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>MCType</th><th>Name</th><th>Last Dues Date</th><th>Last Dues Amt</th><th>Last Don. Date</th><th>Last Don. Amt</th></tr>';
	while ($r = $res->fetch_assoc()) {
		//echo'<pre> contacts '; print_r($r); echo '</pre>';
		echo "<tr><td>$r[MCID]</td><td>$r[MCtype]</td><td>$r[NameLabel1stline]</td><td>$r[LastDuesDate]</td><td>$r[LastDuesAmount]</td><td>$r[LastDonDate]</td><td>$r[LastDonAmount]</td></tr>";

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
echo "<h4>1-Members or 2-Volunteers With NO Dues Payments Recorded</a></h4>";
if ($rowcount > 0) {
echo "<p>There were $rowcount records classified as &apos;1-Members&apos; or &apos;2-Volunteer&apos;; without ANY funding records marked as &apos;Dues&apos; associated with them.  These member records should be reviewed and reclassified if warranted.</p>
<p>If the record is to be retained as a member or volunteer, a $0 dues payment could be entered to remove the record from this list and allow review of this status in 11 months.</p>
<p>The expiration date used is $expdate</p>";
	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>Name</th><th>MCType</th><th>Last Dues Date</th><th>Last Dues Amount</th></tr>';
	while ($r = $res->fetch_assoc()) {			
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[LastDuesDate]</td><td>$r[LastDuesAmount]</td></tr>";
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
echo "<h4>3-Donors with NO Donations</a></h4>";
if ($rowcount > 0) {
echo "<p>There were $rowcount records classified as &apos;3-Donor&apos; that have NO non-Dues payment logged within the expriation period.  These member records should be reviewed and reclassified if warranted.</p>

<p>The expiration date used is $expdate</p>";
	echo '<table border="0" class="table table-condensed">
<tr><th>MCID</th><th>Name</th><th>MCtype</th><th>Last Don. Date</th><th>Last Don. Amount</th></tr>';
	while ($r = $res->fetch_assoc()) {			
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[LastDonDate]</td><td>$r[LastDonAmount]</td></tr>";
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
echo "<h4>Invalid Mail or Email Flag Settings</h4>";
if ($rowcount > 0) {
echo "<p>There are $rowcount ACTIVE member, volunteer or donor records that have EITHER the &apos;Mail OK?&apos; flag set to YES/TRUE with no information in the Address Line OR the &apos;Email OK?&apos; set to YES/TRUE with no email address provided.  All these records should reviewed and corrected.</p>";
	echo '<table class="table-condensed">
	<tr><th>MCID</th><th>Name</th><th>MC Type</th><th>MailOK?</th><th>AddressLine</th><th>EmailOK?</th><th>EmailAddress<th></th></tr>';
	while ($r = $res->fetch_assoc()) {
//		echo "Mail: $r[Mail], E_Mail: $r[E_Mail]<br>";
		$mf = ($r[Mail] == 'TRUE') ? 'YES' : 'NO';
		$ef = ($r[E_Mail] == 'TRUE') ? 'YES' : 'NO';
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$mf</td><td>$r[AddressLine]</td><td>$ef</td><td>$r[EmailAddress]</td></tr>";
		//echo '<pre> mail with no address '; print_r($r); echo '</pre>';	
		}
	}
echo '</table>----- END OF LIST -----<br>';

$sql = "SELECT `members`.`MCID`, `members`.`MemStatus`,  `members`.`MCtype`, `members`.`MemDate`, `members`.`Inactive`, `members`.`FName`, `members`.`LName` 
FROM { OJ `pwcmbrdb`.`members` AS `members` LEFT OUTER JOIN `pwcmbrdb`.`donations` AS `donations` 
ON `members`.`MCID` = `donations`.`MCID` } 
WHERE `donations`.`MCID` IS NULL 
	AND `members`.`Inactive` = FALSE 
	ORDER BY `members`.`MCID` ASC";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
//$rowcount = $res->num_rows;
//echo "SQL: $sql<br />";
echo "<h4>List of supporters with NO associated funding records</h4>";
if ($rowcount > 0) {
echo "<p>There are $rowcount active supporters that have NO funding records associated with them.  All these records should reviewed and probably set inactive.</p>";
	echo '<table class="table-condensed">
	<tr><th>MCID</th><th>FName</th><th>LName</th><th>MCType</th><th>MemDate</th><th>Inactive</th></tr>';
	while ($r = $res->fetch_assoc()) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td>$r[MCtype]</td><td>$r[MemDate]</td><td>$r[Inactive]</td></tr>";
		}
	}
echo '</table>----- END OF LIST -----<br>';

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
