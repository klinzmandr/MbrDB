<!DOCTYPE html>
<html>
<head>
<title>Funding Paid Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

<?php
session_start();

include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$systemlists = readdblist('Programs');
$progtypes = formatdbrec($systemlists);
$campaignlists = readdblist('Campaigns');
$camptypes = formatdbrec($campaignlists);
// echo '<pre> progtypes '; print_r($progtypes); echo '</pre>';
// echo '<pre> camptypes '; print_r($camptypes); echo '</pre>';

// load program arrays for creating checkboxes
foreach ($progtypes as $k => $v) {
  if ($k == ' ') continue;
	list($val,$desc) = explode('-',$k);
	switch ($val) {
		case 'Dues': $dues[$desc] = $k; break;
		case 'Don': $don[$desc] = $k; break;
		case 'Dir': $dir[$desc] = $k; break;
		case 'Ink': $ink[$desc] = $k; break;
		case 'Gra': $gra[$desc] = $k; break;
		case 'Fun': $fun[$desc] = $k; break;
		case 'Prg': $prg[$desc] = $k; break;
		default : $other[$desc] = $k; break;
		}
	}

//echo "action: $action<br>";
if ($action == '') {
	//include 'Incls/vardump.inc.php';
	print <<<pagePart1
<h3>Funding Paid Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report lists the total funding provided for each MCID based on the funding type selected.  The output is sorted by Total amount in descending sequence.</p>
<h4>Select one or more of the following criteria:</h4>

<script>
$(document).ready(function () {
  $("[id$=main]").change(function() { // all/none section box
    var id = this.id;
    var fullid = "#" + id;
    var partid = "[id=" + id.substring(0,3) + "]";
    var x = $(fullid).prop("checked");
    // console.log("fullid: "+fullid+", partid: " + partid);
    $(partid).prop("checked", x);
    });     
});
</script>

<script>
function chkvals(form) {
	//alert("check values entered");
	var errmsg = ""; var chks = 0; var chkcnt = 0;
	var chks = $('[id="cpg"]:checked').length;         // count checked campaigns
	chks = chks + $('[name="cbox[]"]:checked').length; // count checked programs
	if (chks == 0) {
		errmsg += "No Funding/Campaign Type(s) have been selected\\n";
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

<form action="rptFundingPaidbyfund.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>

pagePart1;

echo '<b>Funding Type(s)</b><br>
<table width="95%" class="table-condensed" border=1>
<tr><td valign=top><input type=checkbox id="cb0main"> Dues:<ul>';
foreach ($dues as $k => $v) {
//	echo "fund type key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb0\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb1main"> Donations:<ul>
';
foreach ($don as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb1\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb2main"> Directed Donations:<ul>';
foreach ($dir as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb2\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb3main"> In-kind Donations:<ul>';
foreach ($ink as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb3\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td></tr><tr><td valign=top><input type=checkbox id="cb4main"> Grants:<ul>';
foreach ($gra as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb4\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb5main"> Fund Raising & Events:<ul>';
foreach ($fun as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb5\" name=cbox[] value=\"$v\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb6main"> Prog. Income:<ul>';
foreach ($prg as $k => $v) {
//	echo "type key: $k, value:$v<br>";
	echo "
<input type=checkbox id=\"cb6\"  name=cbox[] value=\"$v\"> - $v<br>
";
	}
// echo '<pre> camptypes '; print_r($camptypes); echo '</pre>';	
if (count($camptypes) > 0) {
  echo '</ul></td><td valign=top><input type=checkbox id="cpgmain"> <b>Campaigns</b>:<ul>';
  foreach ($camptypes as $k => $v) {
    if ($v == '') continue;
  // echo "type key: $k, value:$v<br>";
	echo "  
  <input id=\"cpg\" type=checkbox name=cpg[] value=\"$k\"> - $v<br>
  ";
	}
}

echo '</ul></td></tr></table>';

// <input type="checkbox" name="mstat0" value="0" /> - 0-Contacts, or<br />

print <<<pagePart2
<b>AND</b><br />
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
// include 'Incls/vardump.inc.php';
if ($action == 'search') {
	$cbox = isset($_REQUEST['cbox']) ? $_REQUEST['cbox'] : array();
	$cpgbox = isset($_REQUEST['cpg']) ? $_REQUEST['cpg'] : array();

	$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
	$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
	$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
	$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
	$noemail = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : '';

// echo '<pre> cbox'; print_r($cbox); echo '</pre>';
	if (count($cbox) > 0) $cblist = "('" . implode("','",$cbox) . "')";
	if (count($cpgbox) > 0) $cpglist = "('" . implode("','",$cpgbox) . "')";
  // echo "cblist: $cblist<br>";
  // echo "cpglist: $cpglist<br>";
  
  $cpgwhere = "`donations`.`Campaign` IN $cpglist";
	$mbrwhere = "`donations`.`Program` in $cblist ";
	$rptmbr = 'In list: '. $cblist;
	$rptcpg = 'In campaign: '.$cpglist;
//	echo "$rptmbr<br />";
	
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
	
	if (count($cbox) > 0) $where = " $mbrwhere AND";
	if (count($cpgbox) > 0) $where .= "$cpgwhere AND"; 
	if ($extwhere != '') $where .= "( $extwhere ) AND";
	$where = rtrim($where,' AND');
	
// echo "where: $where<br />";

// must be uncommented for DW server version, 
$sql = "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
//echo "sql: $sql<br>";
$status = doSQLsubmitted($sql);
//echo "set global status: $status<br>";

// now ready to do db search for list by criteria
	$sql = "SELECT `donations`.`MCID`, SUM( `donations`.`TotalAmount` ) AS `Total`, `members`.* 
FROM { OJ `members` LEFT OUTER JOIN `donations` ON `members`.`MCID` = `donations`.`MCID` } 
WHERE $where 
GROUP BY `donations`.`MCID` 
$having 
ORDER BY `Total` DESC;";
	if (($extwhere == '') AND ($having == '')) {
		$sql = "SELECT * FROM `members` WHERE $where";
		}
  // echo "SQL: $sql<br>";

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
		if ($mcid == 'OTD00') continue; 
		//echo '<pre> row returned '; print_r($row); echo '</pre>';
		if ($row[Inactive] == 'TRUE') {    // ignore if record marked inactive
      $inactcnt += 1;
      continue;
      }

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
//	echo "Inactive count: $inactcnt<br>";
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
Criteria: $rptmbr $rptcpg $rptdate $rptrng<br>
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

echo '<h3>Funding Type Report Results&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "Criteria: $rptmbr $rptcpg $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Rows extracted: $nbr_rows, No mail: $nomail, Missing address: $noaddr, Email Excluded: $withemail, Records selected: " . count($results) . ", Grand Total: $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaidByFund.csv\" download=\"FundingPaidByType.csv\">DOWNLOAD CSV FILE</a>";
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
file_put_contents('downloads/FundingPaidByFund.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';
}
?>
	
</body>
</html>
