<?php
session_start();

include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {
	//include 'Incls/vardump.inc.php';
	print <<<pagePart1
<!DOCTYPE html>
<html>
<head>
<title>Funding Paid Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet"></head><body>
<h3>Funding Paid Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report lists all records that would be used for by the &apos;Print Labels on Criteria&apos; for printing labels based on the criteria selected.  The output is sorted by Total amount in descending sequence.</p>
<h4>Select one or more of the following criteria:</h4>
<script>
function chkvals(form) {
	//alert("check values entered");
	var errmsg = "";
	var chkcnt = 0;
	if (form.mstat0.checked) 	chkcnt += 1;
	if (form.mstat1.checked) 	chkcnt += 1;
	if (form.mstat2.checked) 	chkcnt += 1;
	if (form.mstat3.checked) 	chkcnt += 1;
	if (chkcnt == 0) {
		errmsg += "No Member Status has been selected\\n";
		}

	if (form.daterangechk.checked) {
		chkcnt += 1;
		if ((form.sd.value == "") && (form.ed.value == "")) {
			errmsg += "Date search but no date(s) have been entered\\n";
			}
		}
	if (((form.sd.value != "") || (form.ed.value != "")) && (form.daterangechk.checked == false)) {
		errmsg += "Date range specified but not selected\\n";
		}
	if (form.valrangechk.checked) {
		chkcnt += 1;
		if ((form.vrangelo.value == "") && (form.vrangehi.value == "")) {
			errmsg += "Value range search but no values(s) have been entered.\\n";
			}
		if ((form.vrangelo.value !== "") && (isNaN(form.vrangelo.value))) {
		  errmsg += "Invalid value entered for funding low range.\\n";
			}
		if ((form.vrangehi.value !== "") && (isNaN(form.vrangehi.value))) {
		  errmsg += "Invalid value entered for funding high range.\\n";
			}
		}
	if (((form.vrangelo.value != "") || (form.vrangehi.value != "")) && (form.valrangechk.checked == false)) {
		errmsg += "Value range specified but not selected\\n";
		}
	if (chkcnt == 0) {
		errmsg += "No selection criteria has been selected.\\n";
		}
	if (errmsg == "") return true;
	alert(errmsg);
	return false;
	}

</script>
<form action="rptFundingPaid.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>
<input type="checkbox" name="mstat0" value="0" /> - 0-Contacts, or<br />
<input type="checkbox" name="mstat1" value="1" /> - 1-Members, or<br />
<input type="checkbox" name="mstat2" value="2" /> - 2-Volunteers, or<br />
<input type="checkbox" name="mstat3" value="3" /> - 3-Donors<br />

AND<br />
<input type="checkbox" name="noemail" value="noemail"> - Exclude those with email addresses<br>
<input type="checkbox" name="daterangechk" value="daterange" size="1"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value=""> and/or before: 
<input type="text" name="ed" id="ed" value=""><br />
<input type="checkbox" name="valrangechk" value="valrange" size="1"> - Total Funding Range :
<input placeholder="Low Amount" type="text" name="vrangelo" value=""> and/or : 
<input placeholder="High Amount" type="text" name="vrangehi" value=""><br /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value-"submit">
</form>
</ul>
pagePart1;

	}

else {
// ------------------ start ----------------
// use input parameters to select records
if ($action == 'search') {
	//include 'Incls/vardump.inc.php';
	$blanks = isset($_REQUEST['blanklabels']) ? $_REQUEST['blanklabels'] : 0;	
	$mstat0 = isset($_REQUEST['mstat0']) ? $_REQUEST['mstat0'] : '';
	$mstat1 = isset($_REQUEST['mstat1']) ? $_REQUEST['mstat1'] : '';
	$mstat2 = isset($_REQUEST['mstat2']) ? $_REQUEST['mstat2'] : '';
	$mstat3 = isset($_REQUEST['mstat3']) ? $_REQUEST['mstat3'] : ''; 
	$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
	$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
	$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
	$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
	$noemail = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : '';

	$mbrwhere = "( ";
	$rptmbr = "Mbr Status: ";
	if ($mstat0 != '') {
		$mbrwhere .= "`MemStatus` = '$mstat0' OR ";
		$rptmbr .= $mstat0 . ', ';
		}
	if ($mstat1 != '') {
		$mbrwhere .= "`MemStatus` = '$mstat1' OR ";
		$rptmbr .= $mstat1 . ', ';
		}
	if ($mstat2 != '') {
		$mbrwhere .= "`MemStatus` = '$mstat2' OR ";
		$rptmbr .= $mstat2 . ', ';
		}
	if ($mstat3 != '') {
		$mbrwhere .= "`MemStatus` = '$mstat3' OR ";
		$rptmbr .= $mstat3 . ', ';
		}
	$mbrwhere = rtrim($mbrwhere, ' OR ');
	$rptmbr = rtrim($rptmbr, ', ');
	//echo "$rptmbr<br />";
	if (isset($_REQUEST['daterangechk'])) {
		$rptdate = 'Date Range ';
		if ($drangelo == '') $drangelo = '2001-01-01';
		if ($drangelo != '') {
			$extwhere .= "`donations`.DonationDate >= '$drangelo' AND ";
			$rptdate .= 'greater than: ' . $drangelo . ', ';
			}
		if ($drangehi != '') {
			$extwhere .= "`donations`.DonationDate <= '$drangehi' AND ";
			$rptdate .= 'less than: '. $drangehi . ', ';
			}
		$extwhere = rtrim($extwhere, ' AND ');
		$rptdate = rtrim($rptdate, ', ');
		//echo "$rptdate<br />";
		//echo "Date(s): $extwhere<br />";
		}
	
	$having = '';
	if (isset($_REQUEST['valrangechk'])) {
		$rptrng = ', Value Range ';
		if ($vrangelo != '') {
			$having .= "`Total` >= '$vrangelo' AND ";
			$rptrng .= 'greater than: ' . $vrangelo . ', ';
			}
		if ($vrangehi != '') {
			$having .= "`Total` <= '$vrangehi' AND ";
			$rptrng .= 'less than: ' . $vrangehi . ', ';
			}
		$having = 'HAVING ( ' . rtrim($having, ' AND ') . ')';
		//echo "$rptrng<br />";
		//echo "Value: $having<br />";
		}
	
	if ($extwhere != '') {
		$where = " $mbrwhere ) AND ( $extwhere ) ";
		}
	else {
		$where = " $mbrwhere )";
		}
	
//echo "where: $where<br />";
// now ready to do db search for list by criteria


	if (($extwhere == '') AND ($having == '')) {
		$sql = "SELECT `donations`.`MCID`, SUM( `donations`.`TotalAmount` ) AS `Total`, `members`.* 
		FROM `donations`, `members`
		WHERE `donations`.`MCID` = `members`.`MCID` 
			AND `Inactive` = 'FALSE'  
			AND $where
		GROUP BY `donations`.`MCID`
		ORDER BY `Total` DESC;";
		}
	else {
		$sql = "SELECT `donations`.`MCID`, SUM( `donations`.`TotalAmount` ) AS `Total`, `members`.* 
		FROM `donations`, `members`
		WHERE `donations`.`MCID` = `members`.`MCID` 
			AND `Inactive` = 'FALSE' 
			AND $where 
		GROUP BY `donations`.`MCID` 
		$having
		ORDER BY `Total` DESC;";
		}
	//echo "SQL: $sql<br>";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	//echo "rows returned: $nbr_rows<br />";
	if (isset($_REQUEST['valrangechk'])) {
		if ($vrangelo == '') $vrangelo = 0;
		if ($vrangehi == '') $vrangehi = 10000000;
		}
//echo "vrangelo: $vrangelo, vrangehi: $vrangehi<br />";
	$valcount = 0; $noaddr = 0; $nomail = 0; $withemail = 0;
	while ($row = $res->fetch_assoc()) {
		$mcid = $row[MCID];
		//echo '<pre> row returned '; print_r($row); echo '</pre>';
		
		if (($row[E_Mail] == 'TRUE') AND ($noemail == 'noemail')) { 
			$withemail++;									// bypass those with email addresses 
			continue; 
			}
	
		if (($row[AddressLine] == "") AND ($row[Mail] == 'TRUE')) {
			$noaddr++;										// no address info
			//continue;
			} 
		if ($row[Mail] == 'FALSE') {
			$nomail++; 
			//continue;											// member does not want mail
			}
		$results[$mcid] = $row;
		$grandtotal += $row[Total];
		}
	}		// action == search
// --------------------- end -----------------------------
//echo "falsecount: $falsecount<br />";
//echo "valcount: $valcount<br />";
//echo "results count: " . count($results) . '<br />';
if ($nbr_rows == 0) {
	print <<<nothingReturned
<!DOCTYPE html>
<html><head><title>Funding Report-Nothing</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="css/bootstrap.min.css" rel="stylesheet" media="screen"></head><body>

<h4>No MCID&apos;s meet the criteria supplied</h4>
Criteria: $rptmbr $rptdate $rptrng<br>
<!-- SQL: $sql<br> -->
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
nothingReturned;
	exit;
	}
// create html document
print <<<labelPart1
<!DOCTYPE html><html><head><title>Funding Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen"></head><body>
labelPart1;
// include in CSS to format label printing
//include 'Incls/label_print_css.inc.php';	
// leave empty labels empty
$sheetcount = 0;
if ($blanks > 0) $blanks -= 1;

echo '<h3>Funding Report Results&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "$rptmbr $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Rows extracted: $nbr_rows, No mail: $nomail, Missing address: $noaddr, Email Excluded: $withemail, Records selected: " . count($results) . ", Grand Total: $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaid.csv\" download=\"FundingPaid.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
if (count($results) > 0) {
$csv[] = "MCID;MStat;Total;Fname;Lname;Label1stLine;Salutation;Phone;EMail?;Email;Mail?;Address;City;St;Zip;Notes\n";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>MemStatus</th><th>Total</th><th>Name</th><th>Phone</th>
<th>EMail?<th>Email</th><th>Mail?</th><th>Address</th><th>City/St/Zip</th><th>Notes</th></tr>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
foreach ($results as $k => $r) {
	$note = strtr($r[Notes], $translate);
	if ($r[E_Mail] == 'TRUE') $r[E_Mail] = 'Yes'; else $r[E_Mail] = 'No';
	if ($r[Mail] == 'TRUE') $r[Mail] = 'Yes'; else $r[Mail] = 'No';
	$csv[] = "\"$r[MCID]\";$r[MemStatus];$r[Total];\"$r[FName]\";\"$r[LName]\";\"$r[NameLabel1stline]\";\"$r[CorrSal]\";$r[PrimaryPhone];$r[E_Mail];$r[EmailAddress];$r[Mail];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
	echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Total]</td><td>$r[NameLabel1stline]</td><td>$r[PrimaryPhone]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[City], $r[State], $r[ZipCode]</td><td>$note</td></tr>";
	//echo "<pre>"; echo "key: $k "; print_r($r); echo "</pre>";	
	}
echo "</table>";
file_put_contents('downloads/FundingPaid.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';
}
?>
	
</body>
</html>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>
