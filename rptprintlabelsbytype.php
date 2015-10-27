<?php
session_start();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {
	print <<<pagePart1
<!DOCTYPE html>
<html>
<head>
<title>Print Labels</title><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<div class="container">

<h3>Print Labels  <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>

pagePart1;
//include 'Incls/vardump.inc';

include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$systemlists = readdblist('MCTypes');
$mctypes = formatdbrec($systemlists);
// echo '<pre> syslistsarray '; print_r($mctypes); echo '</pre>';	

$ua = $_SERVER['HTTP_USER_AGENT'];

foreach ($mctypes as $k => $v) {
	$val = substr($k,0,1);
	switch ($val) {
		case 0: $mctype0[$k] = $v; break;
		case 1: $mctype1[$k] = $v; break;
		case 2: $mctype2[$k] = $v; break;
		case 3: $mctype3[$k] = $v; break;
		}
	}

if (stripos($ua,'Chrome') === FALSE) {
print <<<pagePart2
<h3 style="color: red; ">Please note that printing labels can only be done when using the Chrome web browser.</h3>

pagePart2;
// exit;
}

print <<<pagePart3
<p>This facility allows the creation of a page formatted as printing labels based on the criteria selected.  All labels will be sorted by zip code in ascending sequence.</p>
<p>Before printing labels, use Chrome&apos;s print function (File -> Print -> More Settings) to define the custom margin settings to <b>top margin to 0.5 inch and all other print margins to 0 (zero)</b>.</p>
<p>PLEASE NOTE: for the labels to print properly, the default font setting for Chorme MUST be set to 'LARGE'.  For Chrome, this setting is at 'Settings -> Show Advanced Settings -> Web Content'.</p>
<p>Before printing multiple pages on label stock try printing a single test page on plain paper first.  Hold it up to the light behind a sheet of labels to make sure the printed labels line up with the lables on the page.  This will ensure that all settings are in effect.</p>
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

<form action="rptprintlabelsbytype.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>
pagePart3;

echo 'Member Type(s)<br>
<table class="table table-condensed" border=0>
<tr><td valign="top"><input type=checkbox id="cb0" onchange="chckr(0);"> Contacts:<ul>';
foreach ($mctype0 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb1" onchange="chckr(1);"> Members:<ul>';
foreach ($mctype1 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb2" onchange="chckr(2);"> Volunteers:<ul>';
foreach ($mctype2 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb3" onchange="chckr(3);"> Supporters:<ul>';
foreach ($mctype3 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td></tr></table>';

// <input type="checkbox" name="mstat0" value="0" /> - 0-Contacts, or<br />

print <<<pagePart4
<h4>AND</h4>
<input type="checkbox" name="noemail" value="TRUE"> - Exclude those WITH email addresses<br>
<input type="checkbox" name="email" value="TRUE"> - Exclude those WITHOUT email addresses<br>
<input type="checkbox" name="daterangechk" value="daterange" size="1"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value=""> and/or before: 
<input type="text" name="ed" id="ed" value=""><br />
<input type="checkbox" name="valrangechk" value="valrange" size="1"> - Total Funding Range is greater than or equal to:
<input placeholder="Low Amount" type="text" name="vrangelo" value=""> and/or less than or equal to: 
<input placeholder="High Amount" type="text" name="vrangehi" value=""><br /><br />
Number of labels to skip on 1st page (max. 29): 
<input type="text" name="blanklabels" value="0" size="2" maxlength="2" /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value="submit">
</form>
</ul>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
pagePart4;
	exit;
	}

// ------------------ start ----------------
// use input parameters to select records
if ($action == 'search') {
	
// create html document for labels
print <<<labelPart1
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MbrDB Label Output</title>
</head>
<body>

labelPart1;


//	include 'Incls/vardump.inc';
	include 'Incls/seccheck.inc';
	include 'Incls/datautils.inc';
	
	$blanks = isset($_REQUEST['blanklabels']) ? $_REQUEST['blanklabels'] : 0;	
	$cbox = $_REQUEST['cbox'];
	$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
	$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
	$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
	$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
	$noemail = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : 'FALSE';
	$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : 'FALSE';

// echo '<pre> cbox'; print_r($cbox); echo '</pre>';
	$cblist = "('" . implode("','",$cbox) . "')";
// echo "cblist: $cblist<br>";

	$mbrwhere = "`MCtype` in $cblist ";

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
		$where = " $mbrwhere AND ( $extwhere ) ";
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
	$valcount = 0; $noaddr = 0; $nomail = 0; $withemail = 0; $withoutemail = 0;
	while ($row = $res->fetch_assoc()) {
		$mcid = $row[MCID];
		//echo '<pre> row returned '; print_r($row); echo '</pre>';

		if (($row[E_Mail] == 'TRUE') AND ($noemail == 'TRUE')) { 		// member has a valid email so ignore it
			$withemail++; 
//			echo "E_Mail: $row[E_Mail], noemail: $noemail<br>";
			continue; 							 
			}
		if ((($row[E_Mail] == 'FALSE') OR ($row[E_Mail] == '')) AND ($email == 'TRUE')) {			// member does NOT has a valid email so ignore it
			$withoutemail++;
//			echo "E_Mail: $row[E_Mail], email: $email<br>";
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
<h3>No supporter records meet the criteria supplied</h3>
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
</body>
</html>
nothingReturned;
	exit;
	}

// include in CSS to format label printing
include 'Incls/label_print_css.inc';
		
// leave empty labels empty
$sheetcount = 0;
$cnt = count($results);
if ($blanks > 0) $blanks -= 1;
echo "<div class=\"label\">";
echo "
Ext/Prt: $nbr_rows/$cnt<br />
No mail/no addr: $nomail/$noaddr<br />
Excl w/wo Eml: $withemail/$withoutemail<br>
";
echo '<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>/
<a href="rptprintlabelscorradder.php?count=' . $cnt . '" class="btn btn-primary">CONTINUE</a>
</div>';
$sheetcount += 1;
for ($i=0;$i<$blanks;$i++) {
	echo "<div class=\"label\"></div>";
	$sheetcount += 1;
	}
$corrarray = array(); $corrarray[] = "MCID,Name\n";
if (count($results) != 0) {
foreach ($results as $k => $r) {
	$mcid = $r[MCID]; $zipcode = $r[ZipCode]; $org = substr($r[Organization],0,24);
	$name = substr($r[NameLabel1stline],0,24); $addr = $r[AddressLine]; $city = $r[City]; $state = $r[State];
	$corrarray[] = $r[MCID] . ',' .  $r[NameLabel1stline] . "\n";
	if ($org == '')
		echo "<div class=\"label\">
$name<br>
$addr<br>
$city, $state  $zipcode
<div style=\"text-align: right; \"><mcid>$mcid</mcid></div>
</div>\n";
	else {
		$name = 'Attn: ' . substr($name,0,19);
		echo "<div class=\"label\">
$org<br>
$name<br>
$addr<br>
$city, $state  $zipcode
<div style=\"text-align: right; \"><mcid>$mcid</mcid></div>
</div>\n";
	}
	//echo "<pre>"; print_r($r); echo "</pre>";
	$sheetcount += 1;
	if ($sheetcount >= 30) {
		echo "<div class=\"page-break\"></div>";
		$sheetcount = 0;
		}
	}
}
file_put_contents('uploads/corraddarray.csv', $corrarray); // for corr adder page
print <<<labelPart2
</body>
</html>
labelPart2;

exit;
?>
