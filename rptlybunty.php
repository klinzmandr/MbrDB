<!DOCTYPE html>
<html>
<head>
<title>LYBUNTY Report</title>
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

echo '<h3><font color="red">L</font>ast <font color="red">Y</font>ear <font color="red">BU</font>t <font color="red">N</font>ot <font color="red">T</font>his <font color="red">Y</font>ear(LYBUNTY) Report&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>';

if ($action == 'rpt') {
	$sql = "SELECT `donations`.`MCID`, 
		SUM( `donations`.`TotalAmount` ) AS `YrlyDon`, 
		YEAR( `donations`.`DonationDate` ) AS `DonYr`, 
		`members`.`NameLabel1stline`, `MemStatus`,`FName`, `LName`, 
		`Organization`,`AddressLine`,`City`,`State`,`ZipCode`,`PrimaryPhone`,
		`EmailAddress`, `E_Mail`, `LastDonAmount`,`LastDonDate`,`LastDuesAmount`,`LastDuesDate`
	FROM `donations`, `members` 
	WHERE `donations`.`MCID` = `members`.`MCID` 
	GROUP BY `donations`.`MCID`, YEAR( `donations`.`DonationDate` ) 
	ORDER BY `donations`.`MCID` ASC, `DonYr` ASC";
	$res = doSQLsubmitted($sql);
	$rc = $res->num_rows;
	$YRarray = array(); $ADRarray = array(); $CSVarray = array(); $csv = array();
	$grtot = 0;
	$lgamt = 0;
	$pyr = $yr - 1;
//	echo "yr: $yr<br>"; echo "pyr: $pyr<br>";
	echo '<table class="table table-condensed">
	<tr><th>MCID</th><th>'.$pyr.'Tot</th><th>LastGift</th><th>LastGiftDate</h><th>MemSt</th><th>Label Name</th><th>First</th><th>Last</th><th>Organization</th><th>Address</th><th>City</th><th>
St</th><th>Zip</th><th>Phone</th><th>Email</th></tr>';
	$csv[] =  'MCID;'.$pyr."Tot;LastGiftAmt;LastGiftDate;MemSt;Label Name;First;Last;Organization;Address;City;St;Zip;Phone;Email\n";
	while ($r = $res->fetch_assoc()) {
//		echo '<pre> year '; print_r($r); echo '</pre>';
		if ($r[DonYr] >= $yr) {		// ignore MCID if donated for given year
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		if ($r[YrlyDon] == 0) {		// ignore MCID if nothing giv
			unset($YRarray[$r[MCID]]);
			unset($ADRarray[$r[MCID]]);
			continue;
			}
		if ($r[DonYr] == $yr - 1 ) {
			$YRarray[$r[MCID]] += $r[YrlyDon];		// remember MCID if NOT donated for year
			$grtot += $r[YrlyDon];
			$em = $r[EmailAddress];
			if ($r[E_Mail] == 'FALSE') $em = '';
			$lgamt = $r[LastDonAmount];
			$lgdate = $r[LastDonDate];
			if (strtotime($r[LastDonDate]) < strtotime($r[LastDuesDate])) {
				$lgamt = $r[LastDuesAmount];
				$lgdate = $r[LastDuesDate];
				}
			$flgamt = number_format($lgamt,0);
//		echo "lgdate: $lgdate, lgamt: $lgamt<br>";
			$ADRarray[$r[MCID]] = "<td align=right>$$flgamt</td><td>$lgdate</td><td>$r[MemStatus]</td><td>$r[NameLabel1stline]</td><td>$r[FName]</td><td>$r[LName]</td><td>$r[Organization]</td><td>$r[AddressLine]</td><td>$r[City]</td><td>$r[State]</td><td>$r[ZipCode]</td><td>$r[PrimaryPhone]</td><td>$em</td>";
			$CSVarray[$r[MCID]] = "$$lgamt;$lgdate;$r[MemStatus];\"$r[NameLabel1stline]\";\"$r[FName]\";\"$r[LName]\";\"$r[Organization]\";\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];$r[PrimaryPhone];$em";
			}		
		}
	$fgrtot = number_format($grtot);
	echo "Supporters for $pyr with NO funding for $yr: ".count($YRarray)." for a total of $$fgrtot.&nbsp;&nbsp;";
//	echo '<pre> YR '; print_r($YRarray); echo '</pre>';
//	echo '<pre> ADR '; print_r($ADRarray); echo '</pre>';
	echo "<a href=\"downloads/lybunty.csv\" download=\"lybunty.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
	
	arsort($YRarray);
//	echo '<pre> YRarray '; print_r($YRarray); echo '</pre>';
	foreach ($YRarray as $m => $v) {
		$fv = number_format($v,0); $ld = $v;
//		echo "fv: $fv, ADRarray: "; print_r($ADRarray[$m]); echo '<br>';
		echo "<tr><td>$m</td><td align=\"right\">$$fv</td>" . $ADRarray[$m] . "</tr>";
		$csv[] = "$m;$$v;$CSVarray[$m]\n";
		}
	echo '</table>';
	echo '======= END OF REPORT ===========';
	file_put_contents('downloads/lybunty.csv',$csv);
	exit;
	}
	

if ($action == '') {
print <<<pagePart1

<p>This report lists all member ids that have provided financial support for the previous year but have not provided <b>ANY</b> financial support for the given year.</p>
<br>
Select the Year: <br>
<form action="rptlybunty.php">
<input type="hidden" name="action" value="rpt">
<select name=yr>
<option value=2020>2020</option>
<option value=2019>2019</option>
<option value=2018>2018</option>
<option value=2017>2017</option>
<option value=2016 selected>2016</option>
<option value=2015>2015</option>
<option value=2014>2014</option>
<option value=2013>2013</option>
<option value=2012>2012</option>
<option value=2011>2011</option>
</select>
<input type="submit" name="submit" value="CONTINUE">
</form>
pagePart1;

}

?>

</body>
</html>
