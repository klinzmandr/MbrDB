<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Follow Up Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/datepicker3.css" rel="stylesheet">

</head>
<body>
<?php
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
$blanks = isset($_REQUEST['blanklabels']) ? $_REQUEST['blanklabels'] : 0;	
$mstat0 = isset($_REQUEST['mstat0']) ? $_REQUEST['mstat0'] : '';
$mstat1 = isset($_REQUEST['mstat1']) ? $_REQUEST['mstat1'] : '';
$mstat2 = isset($_REQUEST['mstat2']) ? $_REQUEST['mstat2'] : '';
$mstat3 = isset($_REQUEST['mstat3']) ? $_REQUEST['mstat3'] : '';
$daterangechk = isset($_REQUEST['daterangechk']) ? $_REQUEST['daterangechk'] : ''; 
$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
$valrangechk = isset($_REQUEST['valrangechk']) ? $_REQUEST['valrangechk'] : '';
$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
$subscr = isset($_REQUEST['subscr']) ? $_REQUEST['subscr'] : '';
$noemail = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : '';

$memstat = isset($_REQUEST['memstat']) ? $_REQUEST['memstat'] : '';
$ua = $_SERVER['HTTP_USER_AGENT'];

echo '
<div class="container">
<h3>Follow Up Report</h3>
<div class="hidden-print"><a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></div>
';

//include 'Incls/vardump.inc.php';
print <<<formPart

<h4>Select one or more of the following criteria:</h4>
<script>
window.onload = function() {
//	alert("setting form values");
	var ms0 = '$mstat0'; var ms1 = '$mstat1'; var ms2 = '$mstat2'; var ms3 = '$mstat3';
	var drc = '$daterangechk'; var emc = '$noemail'; var vrc = '$valrangechk';
	var sub = '$subscr';
	if (ms0 != '') document.getElementById("CB0").checked = true;  
	if (ms1 != '') document.getElementById("CB1").checked = true;  
	if (ms2 != '') document.getElementById("CB2").checked = true;  
	if (ms3 != '') document.getElementById("CB3").checked = true;  
	if (drc != '') document.getElementById("DRC").checked = true;
	if (emc != '') document.getElementById("EMC").checked = true;
	if (vrc != '') document.getElementById("VRC").checked = true;
	if (sub != '') document.getElementById("SUB").checked = true;
return true;
}
</script>

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
<form action="rptfollowups.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>
<input type="checkbox" name="mstat0" id="CB0" value="0" /> - 0-Contacts, or<br />
<input type="checkbox" name="mstat1" id="CB1" value="1" /> - 1-Members, or<br />
<input type="checkbox" name="mstat2" id="CB2" value="2" /> - 2-Volunteers, or<br />
<input type="checkbox" name="mstat3" id="CB3" value="3" /> - 3-Donors<br />

AND<br />
<script>
function chkdrc() {
	if (document.getElementById("DRC").checked == false) {
		document.getElementById("sd").value = '';
		document.getElementById("ed").value = '';
		}
	return true;
	}
</script>
<input type="checkbox" onchange="return chkdrc()" name="daterangechk" value="daterange" id="DRC" size="1"> - Member Date Range is from:
<input type="text" name="sd" id="sd" value="$sd" autocomplete="off"> and/or before: 
<input type="text" name="ed" id="ed" value="$ed" autocomplete="off"><br />
<script>
function chkvrc() {
	if (document.getElementById("VRC").checked == false) {
		document.getElementById("VRL").value = '';
		document.getElementById("VRH").value = '';
		}
	return true;
	}
</script>
<input type="checkbox" onchange="return chkvrc()" name="valrangechk" value="valrange" id="VRC" size="1"> - Total Funding Range :
<input placeholder="Low Amount" type="text" name="vrangelo" id="VRL" value="$vrangelo" autocomplete="off"> and/or : 
<input placeholder="High Amount" type="text" name="vrangehi" id="VRH" value="$vrangehi" autocomplete="off"><br />
<script>
function toggle1() {
	if (document.getElementById("EMC").checked) document.getElementById("SUB").checked = false;
	return true;
	}
function toggle2() {
	if (document.getElementById("SUB").checked) document.getElementById("EMC").checked = false;
	return true;
	}
</script>
Choose one:
<ul><input type="checkbox" onchange="return toggle1()" name="noemail" value="noemail" id="EMC"> - Exclude those with email addresses, OR<br>
<input type="checkbox" onchange="return toggle2()" name="subscr" value="subscr" id="SUB"> - Include only subscribing members</ul>
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value-"submit">
</form>
</ul>
formPart;

// set up intro page	
print <<<pagePart1
<p>This report is intended to be printed and used for telephone contact with the supporters listed.  The date and any comments/notes regarding the contact should be written for later entry into the membership database.</p>
<p>New supporters are selected by comparing the &apos;Date Joined&apos; of each supporter record to the specified dates specified. If the &apos;Date Joined&apos; is greater than the &apos;from&apos; date or within the specified date range, it is included in this listing.</p>
<p>Please note that the &apos;Date Joined&apos; of the supporter record is automatically set on introduction of the supporter into the database.  It can not be changed once established.  This date may be after the date of the &apos;Last Dues Payed&apos; date since the payment date usually the check date which may be dated prior to member entry into the database.</p>
<p>The report is initiated when the &apos;Submit&apos; button is clicked.  The values entered into the search criteria fields is used to select supporter records to be included in the final report.</p>
<p>The &apos;Funding Range&apos; is compaired to the TOTAL funding by supporter qualifying for selection using other criteria.  If the total funding for a member exceeds the minimum value or falls within the range entered the supporter record is included in the listing output.</p>
<p>Print margins should be defined as 'None' or set to the minimum values allowed for proper page layouts.</p>

pagePart1;

if (stripos($ua,'Chrome') === FALSE)
print <<<pagePart2
<h4 style="color: red; ">Please note that printing is best done when using the Chrome web browser.  Other browsers may be used but careful testing must be done BEFORE trying to print.  Make sure that browser margin settings are correctly set in any case.</h4>
pagePart2;

// ------------------ start ----------------
// use input parameters to select records
if ($action == 'search') {
	//include 'Incls/vardump.inc.php';
	
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
		$rptdate = ', Mbr Date Range ';
		if ($drangelo == '') $drangelo = '2001-01-01';
		if ($drangelo != '') {
			$extwhere .= "`members`.`MemDate` >= '$drangelo' AND ";
			$rptdate .= 'greater than: ' . $drangelo . ', ';
			}
		if ($drangehi != '') {
			$extwhere .= "`members`.`MemDate` <= '$drangehi' AND ";
			$rptdate .= 'less than: '. $drangehi . ', ';
			}
		$extwhere = rtrim($extwhere, ' AND ');
		$rptdate = rtrim($rptdate, ', ');
		//echo "$rptdate<br />";
		//echo "Date(s): $extwhere<br />";
		}
	
	$having = '';
	if (isset($_REQUEST['valrangechk'])) {
		$rptrng = ', Funding Range ';
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
//	echo "SQL: $sql<br>";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
//	echo "rows returned: $nbr_rows<br />";
	
	if (isset($_REQUEST['valrangechk'])) {
		if ($vrangelo == '') $vrangelo = 0;
		if ($vrangehi == '') $vrangehi = 10000000;
		}
//echo "vrangelo: $vrangelo, vrangehi: $vrangehi<br />";
	$valcount = 0; $noaddr = 0; $nomail = 0; $withemail = 0;
	while ($row = $res->fetch_assoc()) {
		$mcid = $row['MCID'];
		//echo '<pre> row returned '; print_r($row); echo '</pre>';
		
		if (($subscr != '') AND (stripos($row['MCtype'], 'subscr') === false)) {
			$rptsub = ", subscribers only"; 
			continue;											// keep subscr rows only 
			}
	
		if (($noemail != '') AND ($row['EmailAddress'] != '')) { 
			$withemail++;									// bypass those with email addresses
			$rptemail = ", without email addresses"; 
			continue; 
			}
	
		if (($row['AddressLine'] == "") AND ($row['Mail'] == 'TRUE')) {
			$noaddr++;										// no address info
			//continue;
			} 
		if ($row['Mail'] == 'FALSE') {
			$nomail++; 
			//continue;											// member does not want mail
			}
		$results[$mcid] = $row;
		$grandtotal += $row['Total'];
		}
	}		// action == search
// --------------------- end -----------------------------
//echo "falsecount: $falsecount<br />";
//echo "valcount: $valcount<br />";
//echo "results count: " . count($results) . '<br />';
if (($nbr_rows == 0) OR (count($results) == 0)) {
print <<<nothingReturned
<h4>No search criteria supplied OR no MCIDs meet the criteria supplied</h4>
Criteria: $rptmbr$rptdate $rptrng$rptsub$rptemail<br>
<!-- SQL: $sql<br> -->
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>
nothingReturned;
	exit;
	}

echo "
Criteria: $rptmbr$rptdate $rptrng$rptsub$rptemail<br>
<b>MCIDs meeting criteria: " . count($results) . "</b><br><br>";
include 'Incls/followup_print_css.inc.php';

// create html document
// include in CSS to format label printing
//include 'Incls/label_print_css.inc.php';	
// leave empty labels empty
$sheetcount = 0;

// echo "$rptmbr $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
// echo "Rows extracted: $nbr_rows, No mail: $nomail, Missing address: $noaddr, Email Excluded: $withemail, Records selected: " . count($results) . ", Grand Total: $" . $grandtotal . '<br>';

$sheetcount = 0;	
echo "<div class=\"page-break\"></div>";
foreach ($results as $k => $r) {
	$ld = "Last Dues Paid: $r['LastDuesDate']";
	if ($r['MemStatus'] == 3)
		$ld = "Last Donation Made: $r['LastDonDate']";
echo "<div class=mbrsec>
";
echo "<table width='100%' border=0>
<tr><td valign='top' width='25%'>$r[MCID]</td><td width='30%'>Mem Type:<br>$r[MCtype]</td><td>Member Since: $r[MemDate]<br>$ld</td></tr>
<tr><td>$r[NameLabel1stline]</td><td>$r[EmailAddress]</td><td>$r[PrimaryPhone]</td></tr>
<tr><td colspan=3 height='20%'>Contact Date:<br>Contact Notes:</td></tr>
</table></div>
";

$sheetcount += 1;
	if ($sheetcount >= 5) {
		echo "<div class=\"page-break\"></div>
		";
		$sheetcount = 0;
		}
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
