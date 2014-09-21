<!DOCTYPE html>
<html>
<head>
<title>Set Member Dates</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {

print <<<pagePart1
<h3>Set Member Dates</h3>
<p>This page <b>ONE TIME FIX</b> that will search the membership database for all records that have a NULL value in the MemDate field and replace it with the earliest funding record date found from the donations table.</p>
<p>Those member records changed will have their Source field marked with &apos;Accexx&apos; in it.</p>
<p>If no member records are found that have a NULL MemDate, then the results will return 0 rows affected with nothing changed.</p>
<p>SQL used: SELECT `donations`.`MCID`, MIN( `donations`.`DonationDate` ) AS `DonDate`, `members`.`MemDate`, `members`.`Source` FROM `pwcmbrdb`.`donations` AS `donations`, `pwcmbrdb`.`members` AS `members` WHERE `donations`.`MCID` = `members`.`MCID` AND `members`.`MemDate` IS NULL GROUP BY `donations`.`MCID` ORDER BY `donations`.`MCID` ASC</p>

pagePart1;

if ($action == '') 
$sql = "SELECT `donations`.`MCID`, MIN( `donations`.`DonationDate` ) AS `DonDate`, `members`.`MemDate`, `members`.`Source` 
	FROM `pwcmbrdb`.`donations` AS `donations`, `pwcmbrdb`.`members` AS `members` 
	WHERE `donations`.`MCID` = `members`.`MCID` 
		AND `members`.`MemDate` IS NULL 
	GROUP BY `donations`.`MCID` 
	ORDER BY `donations`.`MCID` ASC";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo "Records with NULL MemDates: $rc<br><br>
<a class=\"btn btn-success\" href=\"admsetmemdate.php?action=continue\">CONTINUE</a><br>
<a class=\"btn btn-danger\" href=\"admDBJanitor.php\">CANCEL & RETURN</a></body></html>";
exit;
}

$sql = "SELECT `donations`.`MCID`, MIN( `donations`.`DonationDate` ) AS `DonDate`, `members`.`MemDate`, `members`.`Source` 
	FROM `pwcmbrdb`.`donations` AS `donations`, `pwcmbrdb`.`members` AS `members` 
	WHERE `donations`.`MCID` = `members`.`MCID` 
		AND `members`.`MemDate` IS NULL 
	GROUP BY `donations`.`MCID` 
	ORDER BY `donations`.`MCID` ASC";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo "Records with NULL MemDates: $rc<br><br>";

while ($r = $res->fetch_assoc()) {
	echo "MCID: $r[MCID], Source: $r[Source], DonDate: $r[DonDate], MemDate: $r[MemDate], <br>";
	$updarray[MemDate] = $r[DonDate];
	$updarray[Source] = substr($r[Source],0,4) . 'xx';				// just to mark those changed.
	echo '<pre> update '; print_r($updarray); echo '</pre>'; 
	sqlupdate('members', $updarray, "`MCID` = '$r[MCID]'");
	}
echo "---Update Complete ---<br><br>";
?>
<a class="btn btn-success" href="admDBJanitor.php">RETURN</a>
</body>
</html>
