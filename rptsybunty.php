<!DOCTYPE html>
<html>
<head>
<title>SYBUNTY Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$yr = isset($_REQUEST['yr']) ? $_REQUEST['yr'] : date('Y', strtotime("now"));

echo '<h3><font color="red">S</font>ome <font color="red">Y</font>ear <font color="red">BU</font>t <font color="red">N</font>ot <font color="red">T</font>his <font color="red">Y</font>ear(SYBUNTY) Report&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>';
if ($action == 'rpt') {
	$sql = "CALL YBUNTYqry()";
	$res = $mysqli->query($sql);
	$rc = $res->num_rows;
//	echo "rc: $rc<br>";
	$YRarray = array(); $ADRarray = array(); $CSVarray = array(); $csv = array();
	$grtot = 0; $lgamt = 0;
	$pyr = $yr - 1;
//	echo "yr: $yr<br>"; echo "pyr: $pyr<br>";
	echo '<table class="table table-condensed">
	<tr><th>MCID</th><th>CummTot</th><th>LastGiftAmt</th><th>LastGiftDate</th><th>MemSt</th><th>Label Name</th><th>First</th><th>Last</th><th>Organization</th><th>Address</th><th>City</th><th>St</th><th>Zip</th><th>Phone</th><th>Email</th></tr>';
	$csv[] =  'MCID;CummTot;LastGiftAmt;LastGiftDate;MemSt;Label Name;First;Last;Organization;Address;City;St;Zip;Phone;Email\n';
	while ($r = $res->fetch_assoc()) {
		//echo '<pre> year '; print_r($r); echo '</pre>';
		if ($r[DonYr] >= $yr) {		// ignore MCID if donated for given year
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		// ignore MCID if NOTHING donated
		if (($r[YrlyDon] == 0) OR ($r[YrlyDon] < 100)) {		
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		// ignore MCID if Inactive
		if ($r[Inactive] == 'TRUE') {		
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		$YRarray[$r['MCID']] += $r['YrlyDon'];		// remember MCID if NOT donated for year
		$grtot += $r[YrlyDon];
		$em = $r[EmailAddress];
		if ($r[E_Mail] == 'FALSE') $em = '';
		$lgamt = $r[LastDonAmount];
		$lgdate = $r[LastDonDate];
		if (strtotime($r[LastDonDate]) < strtotime($r[LastDuesDate])) {
			$lgamt = $r[LastDuesAmount];
			$lgdate = $r[LastDuesDate];
			}
		// ignore MCID if yearly funding less than last dues or donation amt
		if (($r[YrlyDon] < $lgamt)) {
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		$flgamt = number_format($lgamt,0);
		$ADRarray[$r[MCID]] = "<td align=\"right\">$$flgamt</td><td>$lgdate</td><td>$r[MemStatus]</td><td>$r[NameLabel1stline]</td><td>$r[FName]</td><td>$r[LName]</td><td>$r[Organization]</td><td>$r[AddressLine]</td><td>$r[City]</td><td>$r[State]</td><td>$r[ZipCode]</td><td>$r[PrimaryPhone]</td><td>$em</td>";
		$CSVarray[$r[MCID]] = "$$lgamt;$lgdate;$r[MemStatus];\"$r[NameLabel1stline]\";\"$r[FName]\";\"$r[LName]\";\"$r[Organization]\";\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];$r[PrimaryPhone];$em";				
		}
	// echo "grtot: $grtot<br>";
	$fgrtot = number_format($grtot);
	echo "Supporters with a total of at least $100 or more prior to Jan 1 $yr. Count: ".count($YRarray)."&nbsp;&nbsp;";
	// echo '<pre> YR '; print_r($YRarray); echo '</pre>';
	// echo '<pre> ADR '; print_r($ADRarray); echo '</pre>';
	echo "<a href=\"downloads/sybunty.csv\" download=\"sybunty.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
	
	arsort($YRarray);
//	echo '<pre>YRarray '; print_r($YRarray); echo '</pre>';
	foreach ($YRarray as $m => $v) {
		//echo "m: $m, v: $v<br>";
		$fv = number_format($v,0);
		echo "<tr><td>$m</td><td align=\"right\">$$fv</td>" . $ADRarray[$m] . "</tr>";
		$csv[] = "$m;$$v;$CSVarray[$m]\n";
		}
	echo '</table>';
	echo '======= END OF REPORT ===========';
	file_put_contents('downloads/sybunty.csv',$csv);
	exit;
	}
	

if ($action == '') {
print <<<pagePart1
<div class="container">
<p>This report lists all members that have provided financial support for the years years prior to but <b>NONE</b> for the selected year.</p>
<br>
Select the Year: <br>
<form action="rptsybunty.php">
<input type="hidden" name="action" value="rpt">
<select name=yr onchange="javascript: this.form.submit();">
<option value=""></option>
<option value=2011>2011</option>
<option value=2012>2012</option>
<option value=2013>2013</option>
<option value=2014>2014</option>
<option value=2015>2015</option>
<option value=2016>2016</option>
<option value=2017>2017</option>
<option value=2018>2018</option>
<option value=2019>2019</option>
<option value=2020>2020</option>
</select>
</form>
<h3>Report Explaination</h3>
<ol>
	<li>Selection of a year from the drop down list will indicate the 'target' year.</li>
	<li>All funding before January 1 of the target year is included in the report.</li>
	<li>All funding for dates after January 1 of the target year are ignored.</li>
	<li>All accumulated funding less than $100 is ignored.</li>
  <li>The count indicates the number of unique MCIDs included in the report</li>
  <li>In the report:
  <ol>
    <li>The 'CummTot' column indicates the total accumulated value of all funding (dues, donations, etc.) paid by that supporter.</li>
    <li>The LastGiftAmt column indicates the amount of the last funding provided by the supporter.</li>
    <li>The LastGiftDate column is the date of the previous column.</li>
    </ol>
  <li>The download spreadsheet has the same column names and definitions.</li>  
  </li>
</ol>
</p>
</div>
pagePart1;

}

?>

</body>
</html>
