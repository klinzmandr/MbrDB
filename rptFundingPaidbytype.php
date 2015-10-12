<!DOCTYPE html>
<html>
<head>
<title>Funding Paid Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet"></head><body>

<?php
session_start();

include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$systemlists = readdblist('MCTypes');
$mctypes = formatdbrec($systemlists);
// echo '<pre> syslistsarray '; print_r($mctypes); echo '</pre>';
foreach ($mctypes as $k => $v) {
	$val = substr($k,0,1);
	switch ($val) {
		case 0: $mctype0[$k] = $v; break;
		case 1: $mctype1[$k] = $v; break;
		case 2: $mctype2[$k] = $v; break;
		case 3: $mctype3[$k] = $v; break;
		}
	}

//echo "action: $action<br>";
if ($action == '') {
	//include 'Incls/vardump.inc';
	print <<<pagePart1
<h3>Funding Paid Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report lists all records that would be used for by the &apos;Print Labels on Criteria&apos; for printing labels based on the criteria selected.  The output is sorted by Total amount in descending sequence.</p>
<h4>Select one or more of the following criteria:</h4>
<script>
function chkvals(form) {
	//alert("check values entered");
	var errmsg = ""; var chks = 0; var chkcnt = 0;
	var elems = document.getElementsByName("cbox[]");
	for (i = 0; i < elems.length; i++) {
		if (elems[i].checked) chks++;
		}
	if (chks == 0) {
		errmsg += "No Member Type(s) have been selected\\n";
		chkcnt += 1;
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
<script>
function chckr(val) {
	var v = val; var cb = false;
	var elems = document.getElementsByName("cbox[]");
	switch (v) {
		case(0): cb = document.getElementById("cb0").checked; break; 
		case(1): cb = document.getElementById("cb1").checked; break;
		case(2): cb = document.getElementById("cb2").checked; break;
		case(3): cb = document.getElementById("cb3").checked; break;
		default: alert("Invalid checkbox id."); return;
	}
	for (i = 0; i < elems.length; i++) {
		var str = elems[i].value;
		if (str.substring(0,1) == v) {
			elems[i].checked = cb;
			}
		}
return false;
}
</script>
<form action="rptFundingPaidbytype.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>

pagePart1;

echo 'Member Type(s)<br>
<table width="90%" class="table-condensed" border=0>
<tr><td valign=top><input type=checkbox id="cb0" onchange="chckr(0);"> Contacts:<ul>';
foreach ($mctype0 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb1" onchange="chckr(1);"> Members:<ul>
';
foreach ($mctype1 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb2" onchange="chckr(2);"> Volunteers:<ul>';
foreach ($mctype2 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb3" onchange="chckr(3);"> Supporters:<ul>';
foreach ($mctype3 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>
";
	}

echo '</ul></td></tr></table>';

// <input type="checkbox" name="mstat0" value="0" /> - 0-Contacts, or<br />

print <<<pagePart2
AND<br />
<input type="checkbox" name="noemail" value="noemail"> - Exclude those with email addresses<br>
<input type="checkbox" name="daterangechk" value="daterange" size="1"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value=""> and/or before: 
<input type="text" name="ed" id="ed" value=""><br />
<input type="checkbox" name="valrangechk" value="valrange" size="1"> - Total Funding Range :
<input placeholder="Low Amount" type="text" name="vrangelo" value=""> and/or : 
<input placeholder="High Amount" type="text" name="vrangehi" value=""><br /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value="submit">
</form>
</ul>
pagePart2;

	}

else {
// ------------------ start ----------------
// use input parameters to select records
if ($action == 'search') {
//	include 'Incls/vardump.inc';
	$blanks = isset($_REQUEST['blanklabels']) ? $_REQUEST['blanklabels'] : 0;	
	$cbox = $_REQUEST['cbox'];
	$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
	$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
	$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
	$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
	$noemail = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : '';

// echo '<pre> cbox'; print_r($cbox); echo '</pre>';
	$cblist = "('" . implode("','",$cbox) . "')";
// echo "cblist: $cblist<br>";

	$mbrwhere = "`MCtype` in $cblist ";

	$rptmbr = 'In list: '. $cblist;
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
		$where = " $mbrwhere  AND ( $extwhere ) ";
		}
	else {
		$where = " $mbrwhere ";
		}
	
//echo "where: $where<br />";
// now ready to do db search for list by criteria
	$sql = "SELECT `donations`.`MCID`, SUM( `donations`.`TotalAmount` ) AS `Total`, `members`.* 
FROM { OJ `members` LEFT OUTER JOIN `donations` ON `members`.`MCID` = `donations`.`MCID` } 
WHERE $where 
GROUP BY `donations`.`MCID` 
$having 
ORDER BY `members`.`ZipCode` ASC;";
	if (($extwhere == '') AND ($having == '')) {
		$sql = "SELECT * FROM `members` WHERE $where";
		}
//	echo "SQL: $sql<br>";
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
//include 'Incls/label_print_css.inc';	
// leave empty labels empty
$sheetcount = 0;
if ($blanks > 0) $blanks -= 1;

echo '<h3>Funding Report Results&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "Criteria: $rptmbr $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Rows extracted: $nbr_rows, No mail: $nomail, Missing address: $noaddr, Email Excluded: $withemail, Records selected: " . count($results) . ", Grand Total: $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaidByType.csv\" download=\"FundingPaidByType.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
if (count($results) > 0) {
$csv[] =
"MCID;MemType;Total;Fname;Lname;Label1stLine;Salutation;Phone;EMail?;Email;Mail?;Address;City;St;Zip;Notes\n";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>MemType</th><th>Total</th><th>Name</th><th>Phone</th>
<th>EMail?<th>Email</th><th>Mail?</th><th>Address</th><th>City/St/Zip</th><th>Notes</th></tr>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
foreach ($results as $k => $r) {
	$note = strtr($r[Notes], $translate);
	if ($r[E_Mail] == 'TRUE') $r[E_Mail] = 'Yes'; else $r[E_Mail] = 'No';
	if ($r[Mail] == 'TRUE') $r[Mail] = 'Yes'; else $r[Mail] = 'No';
	$csv[] = "\"$r[MCID]\";$r[MCtype];$r[Total];\"$r[FName]\";\"$r[LName]\";\"$r[NameLabel1stline]\";\"$r[CorrSal]\";$r[PrimaryPhone];$r[E_Mail];$r[EmailAddress];$r[Mail];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
	echo "<tr><td>$r[MCID]</td><td>$r[MCtype]</td><td>$r[Total]</td><td>$r[NameLabel1stline]</td><td>$r[PrimaryPhone]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[City], $r[State], $r[ZipCode]</td><td>$note</td></tr>";
	//echo "<pre>"; echo "key: $k "; print_r($r); echo "</pre>";	
	}
echo "</table>";
file_put_contents('downloads/FundingPaidByType.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';
}
?>
	
</body>
</html>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
