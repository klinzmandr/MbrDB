<!DOCTYPE html>
<html>
<head>
<title>2013 Vs 2014 Funding</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
//include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {

print <<<pagePart1
<h3>2013 VS 2014 Funding Levels&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
<br><h1>This is a special version of this program!</h1><br>
<p>This report summarizes all payment records on the database isolating those that qualify for matching grant funding.</p>
<p>The list of payment 'Purposes' that qualify are: </p>
<ol>
	<li>All Dues</li>
	<li>All Donations</li>
</ol>
<p>A comparison is made for each members and donors providing financial support in the form of dues and/or donations for 2014 in comparison with the support that each provided for the whole of 2013. If the total funding by an MCID IN 2014 exceeds that provided in ALL of 2013, the difference between these two levels is eligible for the matching funds grant.</p>
<p>The listing shows the total support for 2013, total support for 2014 and the difference.  The detail shown is for the LAST dues or donation funding received for the MCID.</p>
<h4>If the funding record qualifies and its &apos;Campaign&apos; field is empty, then the value of <b>AnonMatch</b> is updated into the field.</h4>
<p><b><font color="#FF0000">NOTE:</font></b> A zero value for 2013 indicates that this is a <b>NEW</b> member OR <b>NEW</b> donor since Jan 1, 2014.</p>
<a class="btn btn-success" href="rpt2013vs2014updater.php?action=continue">Continue</a>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

pagePart1;
exit;
}

//echo 'start of report generation<br>';
$sql = "SELECT `donations`.`DonationID`, `donations`.`MCID`, `donations`.`Purpose`, `donations`.`Program`, `donations`.`Campaign`, `donations`.`DonationDate`, `donations`.`TotalAmount`, `members`.* 
FROM `pwcmbrdb`.`donations` AS `donations`, `pwcmbrdb`.`members` AS `members` 
WHERE `donations`.`MCID` = `members`.`MCID` 
	AND `donations`.`DonationDate` >= '2013-01-01' 
	AND ( `donations`.`Purpose` = 'dues' OR `donations`.`Purpose` = 'donation' )
ORDER BY `donations`.`DonationDate` ASC";

//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
//echo "rc: $rc<br>";
$res14array = array(); $mbr13 = array();  $mbr14 = array();
$datebreak = strtotime('2014-01-01');
while ($r = $res->fetch_assoc()) {
	//echo '<pre> row '; print_r($r); echo '</pre>';
	if (strtotime($r[DonationDate]) < $datebreak) $mbr13[$r[MCID]] += $r[TotalAmount];
	else {
		$mbr14[$r[MCID]] += $r[TotalAmount];
		if ((isset($res14array[$r[MCID]])) AND ($res14array[Campaign] == ""))
			$r[Purpose] = $r[Purpose] . '**';
		$res14array[$r[MCID]] = $r;
	}
	}
ksort($mbr14);
//echo '<pre> res '; print_r($resarray); echo '</pre>';
//echo '13 count: '.count($mbr13);echo '<pre> 13 '; print_r($mbr13); echo '</pre>';
//echo '14 count: '.count($mbr14);echo '<pre> 14 '; print_r($mbr14); echo '</pre>';
$csv = array();
$csv[] = "Tot13;Tot14;QualDiff;DonID;LastDate;Purpose;Program;Campaign;MCID;FName;LName\n";
$mcidcount = 0; $diff = 0; $totdiff = 0; $updcount = 0;
$updarray[Campaign] = 'AnonMatch14';
foreach ($mbr14 as $k => $v) {
	if ($v > $mbr13[$k]) {
		$mcidcount += 1;
		//echo "HIT: $k, 13: $mbr13[$k], 14: $v<br>";
		$diff = $v - $mbr13[$k];
		$totdiff += $diff;
		$diffv = number_format($diff);
		$donid = $res14array[$k][DonationID];
		$mcid = $res14array[$k][MCID]; 		
		$name = $res14array[$k][NameLabel1stline];
		$purp = $res14array[$k][Purpose];
		$prog = $res14array[$k][Program];
		$camp = $res14array[$k][Campaign];
		$ddate = $res14array[$k][DonationDate];
		$fname = $res14array[$k][FName]; $lname = $res14array[$k][LName];  
		if ($mbr13[$k] == '') $mbr13[$k] = 0;
		// this is the logic that will update the campaign field
		if ($camp != 'AnonMatch14') {		// if not designated campaign
			$updcount++;
			sqlupdate('donations', $updarray, "`DonationID` = '$donid'");
			$camp = 'AnonMatch14';
			//echo "sqlupdate('donations', $updarray, \"`DonationID` = '$donid'\")<br>";
			}
		$table[] .= "<tr><td align=\"right\">$$mbr13[$k]</td><td align=\"right\">$$v</td><td align=\"right\">$$diffv</td><td>$donid</td><td>$ddate</td><td>$purp</td><td>$prog</td><td>$camp</td><td>$mcid</td><td>$fname</td><td>$lname</td></tr>";
		$val13 = $mbr13[$k];
		$csv[] = "$val13;$v;$diff;$donid;$ddate;$purp;$prog;$camp;\"$mcid\";$fname;$lname\n";	
		// update 'Campaign' if blank
		
	}
}
file_put_contents('downloads/QualMatchingFunding.csv',$csv);
echo "<h3>Qualifying Matching Grant Funds - Detailed Listing&nbsp;&nbsp;<a class='btn btn-primary' href='javascript:self.close();'>(CLOSE)</a></h3>";
$totdiff = number_format($totdiff);
echo "Total Member Count: $mcidcount, Total Qualifying Amount: $$totdiff, Total records updated: $updcount<br>";
echo "<a href=\"downloads/QualMatchingFunding.csv\" download=\"QualMatchingFunding.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
echo "<table class=\"table-condensed\">
<tr><th>Tot'13</th><th>Tot'14</th><th>QualDiff</th><th>DonID</th><th>LastDate</th><th>Purpose</th><th>Program</th><th>Campaign</th><th>MCID</th><th>FName</th><th>LName</th></tr>";

foreach ($table as $r) echo $r;
echo '</table>----- END OF LISTING -----<br>';

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
