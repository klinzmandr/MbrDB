<?php
session_start();

include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$ua = $_SERVER['HTTP_USER_AGENT'];
if ($action == '') {
	//include 'Incls/vardump.inc';
	print <<<pagePart1
<!DOCTYPE html><html><head><title>Print Labels</title><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head><body><div class="container">
<h3>Print Labels  <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
pagePart1;

if (stripos($ua,'Chrome') === FALSE)
print <<<pagePart2
<h4 style="color: red; ">Please note that printing labels is best done when using the Chrome web browser.  Other browers may be used but careful testing must be done BEFORE trying to print on label stock.  Make sure that browser margin settings are correctly set in any case.</h4>
pagePart2;

print <<<pagePart3
<p>This facility allows the creation of a page formatted as printing labels based on the criteria selected.  All labels will be sorted by zip code in ascending sequence.</p>
<p>Before printing labels, use your browser&apos;s print preview (File -> Print Preview) options to set the top print margin of to 1/2 (0.5) inch and all other print margins to 0 (zero).</p>
<p>Try printing a test page on plain paper first.  Hold it up to the light behind a sheet of labels to make sure the printed labels line up with the stickers.</p>
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
	if (chkcnt == 0) {
		errmsg += "Nothing entered as criteria for label printing.\\n";
		}
	if (form.blanklabels.value > 29) {
		errmsg += "Labels to skip on first sheet is more than 29.\\n";
		}
	if (errmsg == "") return true;
	alert(errmsg);
	return false;
	}

</script>
<form action="rptprintlabels.php" method="post"  class="form" onsubmit="return chkvals(this)">
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
<input type="checkbox" name="valrangechk" value="valrange" size="1"> - Total Funding Range is greater than or equal to:
<input placeholder="Low Amount" type="text" name="vrangelo" value=""> and/or less than or equal to: 
<input placeholder="High Amount" type="text" name="vrangehi" value=""><br /><br />
Number of labels to skip on 1st page (max. 29): 
<input type="text" name="blanklabels" value="0" size="2" maxlength="2" /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value-"submit">
</form>
</ul>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
pagePart3;
	exit;
	}

// ------------------ start ----------------
// use input parameters to select records
if ($action == 'search') {
	//include 'Incls/vardump.inc';
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
	if ($mstat0 != '') $mbrwhere .= "`MemStatus` = '$mstat0' OR ";
	if ($mstat1 != '') $mbrwhere .= "`MemStatus` = '$mstat1' OR ";
	if ($mstat2 != '') $mbrwhere .= "`MemStatus` = '$mstat2' OR ";
	if ($mstat3 != '') $mbrwhere .= "`MemStatus` = '$mstat3' OR ";
	$mbrwhere = rtrim($mbrwhere, ' OR ');
	if (isset($_REQUEST['daterangechk'])) {
		if ($drangelo == '') $drangelo = '2001-01-01';
		if ($drangelo != '') $extwhere .= "`donations`.DonationDate >= '$drangelo' AND ";
		if ($drangehi != '') $extwhere .= "`donations`.DonationDate <= '$drangehi' AND ";
		$extwhere = rtrim($extwhere, ' AND ');
		}
	
	$having = '';
	if (isset($_REQUEST['valrangechk'])) {
		if ($vrangelo != '') $having .= "`Total` >= '$vrangelo' AND ";
		if ($vrangehi != '') $having .= "`Total` <= '$vrangehi' AND ";
		$having = 'HAVING ( ' . rtrim($having, ' AND ') . ')';
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
			$withemail++; 							// member has a valid email so ignore it 
			continue; 
			}
		
		if (($row[AddressLine] == "") AND ($row[Mail] == 'TRUE')) {
			$noaddr++;									// bad address 
			//echo '<pre> bad address '; print_r($row); echo '</pre>';
			continue;
			} 
		if ($row[Mail] == 'FALSE') {
			$nomail++; 									// member does not want mail
			//echo '<pre> no mail '; print_r($row); echo '</pre>';
			continue;		
			}
		$results[$mcid] = $row;				
		}
	}		// action == search
// --------------------- end -----------------------------
//echo "falsecount: $falsecount<br />";
//echo "valcount: $valcount<br />";
//echo "results count: " . count($results) . '<br />';
if ($nbr_rows == 0) {
	print <<<nothingReturned
<!DOCTYPE html>
<html><head><title>Print Labels-Nothing</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="css/bootstrap.min.css" rel="stylesheet" media="screen"></head><body>
<div class="container">
<h4>No MCID&apos;s meet the criteria supplied</h4>
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
nothingReturned;
	exit;
	}
// create html document for labels
print <<<labelPart1
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MbrDB Label Output</title>
</head>
<body>
labelPart1;
// include in CSS to format label printing
include 'Incls/label_print_css.inc';		
// leave empty labels empty
$sheetcount = 0;
if ($blanks > 0) $blanks -= 1;
echo "<div class=\"label\">";
echo "Rows extracted: $nbr_rows<br />No mail/no addr: $nomail/$noaddr<br />Excl Email: $withemail<br>Labels printed: " . count($results) . '<br />';
echo "<a href=\"javascript:self.close();\" class=\"btn btn-primary\">CLOSE</a></div>";
$sheetcount += 1;
for ($i=0;$i<$blanks;$i++) {
	echo "<div class=\"label\"></div>";
	$sheetcount += 1;
	}

if (count($results) != 0) {
foreach ($results as $k => $r) {
	$mcid = $r[MCID]; $zipcode = $r[ZipCode]; $org = $r[Organization];
	$name = $r[NameLabel1stline]; $addr = $r[AddressLine]; $city = $r[City]; $state = $r[State];
	if ($org == '')
		echo "<div class=\"label\">$name<br>$addr<br>$city, $state  $zipcode</div>";
	else 
		echo "<div class=\"label\">$org<br>$name<br>$addr<br>$city, $state  $zipcode</div>";
	//echo "<pre>"; print_r($r); echo "</pre>";
	$sheetcount += 1;
	if ($sheetcount >= 30) {
		echo "<div class=\"page-break\"></div>";
		$sheetcount = 0;
		}
	}
}
	print <<<labelPart2
</body>
</html>
<!-- <div class="label"><a href="javascript:self.close();" class="btn btn-primary">CLOSE</a></div> -->
labelPart2;
	exit;
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
