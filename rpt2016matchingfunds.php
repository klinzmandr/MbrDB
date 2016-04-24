<!DOCTYPE html>
<html>
<head>
<title>2016 Matching Funds</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

<?php
// temp report to track matching funds for 2016 program
session_start();
//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<h2>2015 vs 2016 Matching Fund Report</h2>';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d', strtotime("2016-03-01"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("2016-12-31"));

if ($action == '') {

print <<<pagePart1
<div class="container">
<h3>Report Explaination</h3>
<p>This report will summarizes all dues and donations paid during 2015 for comparison to the funding provided by each in 2016.</p>
<p>The Purpose field of each funding record for 2015 and 2016 is examined and those containing either 'Dues' or 'Donations' are selected. All these selected funding records are then totalled for each supporter by year. Those 2016 supporters who have provided total funding in excess of their 2015 are listed in the report along with their 2015 and 2016 totals plus the qualifying match amount, the difference between the totals for each year. New supporters are assumed to be those that have no funding at all during 2015 so they will show as having $0 in those respective columns</p>
<p>Assumptions used for development of this report:</p>
<ol>
	<li>Period for the program is from March 1, 2016 to December 31, 2016</li>
	<li>The program is cumulative in that ongoing funding payments made throughout the program period will qualify.</li>
	<li>Only funding records marked with a 'Purpose' of 'Dues' or 'Donations' are eligible. For example, funding for directed donations, fund raiser tickets/auctions, and in-kind donations are not eligible for this program.</li>
	<li>To qualifying for matching funds:</li>
	  <ol>
	  <li>A payment record with a 'Purpose' of 'Dues' or 'Donation' must be dated within the program period.</li>
	  <li>New supporter funding paid within the program period is automatically fully qualified.</li>
	  <li>A supporter is considered 'new' if there was no funding paid in the program period for 2015.</li>
    <li>Existing supporter funding within the period must be in excess of total funding for the total dues and donations paid in 2015.</li>
    </ol>
  <li>The default report start date is Jan 1, 2016 and can not be changed.</li>
	<li>The default report end date is December 31, 2016.  This can be optionally be changed if needed.</li>
	<li></li>
	<li></li>
</ol>
<a class="btn btn-success" href="rpt2016matchingfunds.php?sd=$sd&ed=$ed&action=continue">CONTINUE</a>
</div>

pagePart1;
exit;
}

$sql = "SELECT * FROM `donations` 
WHERE `DonationDate` BETWEEN '2015-01-01' AND '2015-12-31'
AND (`Purpose` = 'dues' 
 OR `Purpose` = 'donation' )
ORDER BY `donations`.`DonationDate` ASC";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
//echo "2015 rc: $rc<br>";

$d15 = array(); $d15dues = array(); $d15don = array(); $d15fr = array();
while ($r = $res->fetch_assoc()) {
  $d15[$r[MCID]] += $r[TotalAmount];
  if (stripos($r[Purpose],'ues')) $d15dues[$r[MCID]] += $r[TotalAmount];
  if (stripos($r[Purpose],'onation')) $d15don[$r[MCID]] += $r[TotalAmount];
  }

//echo '<pre> d15 '; print_r($d15); echo '</pre>';
//echo '<pre> d15dues '; print_r($d15dues); echo '</pre>';
//echo '<pre> d15don '; print_r($d15don); echo '</pre>';
//echo '<pre> d15fr '; print_r($d15fr); echo '</pre>';

$sql = "SELECT `donations`.`DonationID`, `donations`.`MCID`, `donations`.`Purpose`, `donations`.`Program`, `donations`.`Campaign`, `donations`.`DonationDate`, `donations`.`TotalAmount`, `members`.* 
FROM `pacwilic_mbrdb`.`donations` AS `donations`, `pacwilic_mbrdb`.`members` AS `members` 
WHERE `donations`.`MCID` = `members`.`MCID` 
	AND `donations`.`DonationDate` BETWEEN '$sd' AND '$ed'
	AND ( `donations`.`Purpose` = 'dues' 
	 OR `donations`.`Purpose` = 'donation' )
ORDER BY `donations`.`DonationDate` ASC";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
//echo "2016 rc: $rc<br>";

$d16 = array(); $d16dues = array(); $d16don = array(); $d16fr = array();
$d16dueslp = array(); $d16donlp = array();
while ($r = $res->fetch_assoc()) {
  $d16[$r[MCID]] += $r[TotalAmount];
  if (stripos($r[Purpose],'ues')) {
    $d16dues[$r[MCID]] += $r[TotalAmount];
    if (strtotime($d16dueslp[$r[MCID]]) < strtotime($r[DonationDate])) 
      $d16dueslp[$r[MCID]] = $r[DonationDate];
      }
  if (stripos($r[Purpose],'onation')) {
    $d16don[$r[MCID]] += $r[TotalAmount];
    if (strtotime($d16donlp[$r[MCID]]) < strtotime($r[DonationDate])) 
      $d16donlp[$r[MCID]] = $r[DonationDate];
      }
  $d16rows[$r[MCID]] = $r;
  }
//echo '<pre> d16 '; print_r($d16); echo '</pre>';  
//echo '<pre> d16dues '; print_r($d16dues); echo '</pre>';  
//echo '<pre> d16don '; print_r($d16don); echo '</pre>';  
//echo '<pre> d16donlp '; print_r($d16donlp); echo '</pre>';  

// echo '<pre> d16 '; print_r($d16); echo '</pre>';
$ss = "Dues15;Dues15;Don16;DuesLP;Don16;DonLP;QualTot;MCID;FName;Lname\n";

$tbl = '<table class="table table-striped table-condensed" border=0>';

$tbl .= '<tr>
<th>Dues<br>2015</th>
<th>Donations<br>2015</th>
<th>Dues<br>2016</th>
<th>Dues<br>LastPd</th>
<th>Donations<br>2016</th>
<th>Donations<br>LastPd</th>
<th>Qualifying<br>Total</th>
<th>MCID</th><th>First Name</th><th>Last Name</th></tr>';
ksort($d16);
foreach ($d16 as $k => $v) {
  if ($k == 'OTD00') continue;
  $vald15 = $d15[$k]; $vald16 = $d16[$k]; $diff = $vald16 - $vald15;
  if ((!array_key_exists($k, $d15)) AND ($v > 0)) {   // check if this is new for 2016
    list($do15,$du15) = chk15($k);
    list($do16,$du16,$do16lp,$du16lp) = chk16($k);
    $dodiff = $do16 - $d015; $dudiff = $du16 - $du15;
    $newcount += 1;
    $newtot += $vald16;
    $totqual += $diff;
    $fn = $d16rows[$k][FName]; $ln = $d16rows[$k][LName];
//    echo "$du15/$du16 $do15/$do16 $fr15/$fr16 $diff $k $fn $ln<br>";
    $tbl .= "<tr>
    <td width=\"75px\" align=\"right\">$$du15</td>
    <td width=\"75px\" align=\"right\">$$do15</td>
    <td width=\"75px\" align=\"right\">$$du16</td>
    <td width=\"75px\" align=\"right\">$du16lp</td>
    <td width=\"75px\" align=\"right\">$$do16</td>
    <td width=\"75px\" align=\"right\">$do16lp</td>    
    <td width=\"75px\" align=\"right\">$$diff</td>
    <td>$k</td><td>$fn</td><td>$ln</td></tr>";
    $ss .= "$$du15;$$do15;$$du16;$du16lp;$$do16;$do16lp;$$diff;$k;\"$fn\";\"$ln\"\n";
    continue;
    }
  if ($vald16 > $vald15) {
    list($do15,$du15) = chk15($k);
    list($do16,$du16,$do16lp,$du16lp) = chk16($k);
    $dodiff = $do16 - $do15; $dudiff = $du16 - $du15;
    $curcount += 1;
    $currtot += $diff;
    $totqual += $diff;
    $fn = $d16rows[$k][FName]; $ln = $d16rows[$k][LName];
//    echo "<b>$du15/$du16 $do15/$do16 $fr15/$fr16 $diff $k $fn $ln</b><br>";
    $tbl .= "<tr>
    <td width=\"75px\" align=\"right\">$$du15</td>
    <td width=\"75px\" align=\"right\">$$do15</td>
    <td width=\"75px\" align=\"right\">$$du16</td>
    <td width=\"75px\" align=\"right\">$du16lp</td>
    <td width=\"75px\" align=\"right\">$$do16</td>
    <td width=\"75px\" align=\"right\">$do16lp</td>    
    <td width=\"75px\" align=\"right\">$$diff</td>
    <td>$k</td><td>$fn</td><td>$ln</td></tr>";
    $ss .= "$$du15;$$do15;$$du16;$du16lp;$$do16;$do16lp;$$diff;$k;\"$fn\";\"$ln\"\n";
    }
  }
$tbl .= "</table>";

print <<<pageForm
<form action="rpt2016matchingfunds.php" method="post">
From:
<input type="text" name="sd" id="sd" value="$sd">
&nbsp;&nbsp;
To:<input type="text" name="ed" id="ed" value="$ed">
<input type="hidden" name="action" value="continue">
<input type="submit" name="submit" value="Generate Report">
</form>

pageForm;

echo 'SUMMARY: TOTAL QUALIFIED MATCH: $' . number_format($totqual) . '<br>
2016 New Supporters: ' . $newcount . ', New Supporters Qualified Total Amount: $' . number_format($newtot) . '<br>' .
'Existing supporter count: ' . number_format($curcount) . ', Existing Supporter Qualified Amount: $' . number_format($currtot) . '<br>'; 

echo "<a href=\"downloads/2016MatchingFunds.csv\" download=\"2016MatchingFunds.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

echo $tbl . '<br>';
echo "===END===<br>";
file_put_contents('downloads/2016MatchingFunds.csv',$ss);
//echo "spreadsheet<br><pre>$ss</pre>";

function chk15($key) {
  global $d15dues, $d15don, $d15fr;
  $dox15 = array_key_exists($key, $d15don) ? $d15don[$key] : 0;
  $dux15 = array_key_exists($key, $d15dues) ? $d15dues[$key] : 0;
  return array($dox15, $dux15);
  }
function chk16($key) {
  global $d16dues, $d16don, $d16fr, $d16dueslp, $d16donlp;
  $dox16 = array_key_exists($key, $d16don) ? $d16don[$key] : 0;
  $dux16 = array_key_exists($key, $d16dues) ? $d16dues[$key] : 0;
  $dox16lp = array_key_exists($key, $d16donlp) ? $d16donlp[$key] : '-';
  $dux16lp = array_key_exists($key, $d16dueslp) ? $d16dueslp[$key] : '-';
  return array($dox16, $dux16, $dox16lp, $dux16lp);
  }
?>

</body>
</html>
