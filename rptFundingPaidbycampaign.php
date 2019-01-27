<!DOCTYPE html>
<html>
<head>
<title>Campaign Funding Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
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
//include 'Incls/vardump.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$campaignlists = readdblist('Campaigns');
$camptypes = formatdbrec($campaignlists);
// echo '<pre> camptypes '; print_r($camptypes); echo '</pre>';

//echo "action: $action<br>";
if ($action == '') {
	//include 'Incls/vardump.inc.php';
	print <<<pagePart1
<h3>Campaign Funding Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report lists the total funding provided for each MCID based on the Campaign selected.  The output is sorted by Total amount in descending sequence.</p>
<h4>Select one or more from the following list:</h4>

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
	if (chks == 0) {
		errmsg += "No Campaign(s) have been selected\\n";
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

	if ($("#vchk").is(':checked')) {
	  chkcnt += 1;
		if (($("#vl").val().length == 0) && ($("#vh").val().length == 0)) {
			errmsg += "Value range search but no values(s) have been entered.\\n";
			}
    var vall = $("#vl").val(); 
		if ((vall.length > 0) && (!($.isNumeric(vall)))) {
		  errmsg += "Invalid value entered for funding low range.\\n";
			}
    var valh = $("#vh").val();
		if ((valh.length > 0) && (!($.isNumeric(valh)))) {
		  errmsg += "Invalid value entered for funding high range.\\n";
			}
		}

	if ((($("#vl").val() > 0) || ($("#vh").val() > 0)) && ($("#vchk").is(':checked') == false)) {
	  chkcnt += 1;
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

<form action="rptFundingPaidbycampaign.php" method="post"  class="form" onsubmit="return chkvals(this)">
<ul>

pagePart1;

echo '
<table width="95%" class="table-condensed" border=0>';
// echo '<pre> camptypes '; print_r($camptypes); echo '</pre>';	
echo '</ul></td><td valign=top><input type=checkbox id="cpgmain">&nbsp;&nbsp;<b>All Active Campaigns</b>:<ul>';
  foreach ($camptypes as $k => $v) {
    if ($v == '') continue;
  // echo "type key: $k, value:$v<br>";
	echo "  
  <input id=\"cpg\" type=checkbox name=cpg[] value=\"$k\">&nbsp;&nbsp;$v<br>
  ";
	}

echo '</td></tr></table></ul>';

// <input type="checkbox" name="mstat0" value="0" /> - 0-Contacts, or<br />

print <<<pagePart2
<ul><b>AND</b></ul>
<ul><ul>
<!-- <input type="checkbox" name="noemail" value="noemail"> - Exclude those with email addresses<br> -->
<input type="checkbox" name="daterangechk" value="daterange" size="0"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value="" autocomplete="off"> and/or before: 
<input type="text" name="ed" id="ed" value="" autocomplete="off"><br />
<input type="checkbox" id="vchk" name="valrangechk" value="valrange" size="1"> - Total Funding Range :
<input placeholder="Low Amount" id="vh" type="text" name="vrangelo" value=""> and/or : 
<input placeholder="High Amount" id="vl" type="text" name="vrangehi" value=""><br /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value="submit">
</form>
</ul></ul>
pagePart2;

exit;
	}

// ------------------ start ----------------
// use input parameters to select records
// include 'Incls/vardump.inc.php';

// NOTE!  This query is performed by a stored procedure on the database.  Its results
//        are TOTALLY dependent on the accuracy of the stored procedure!
//        The sp does a select of all campaigns in the list storing the results in
//        a temporary table which s then selected based on the date range of the
//        dates provided in the call as input from the form.
//
//        The result rows are totalled by MCID and campaign based on the 
//        value range provided in the input form

$cpgbox = isset($_REQUEST['cpg']) ? $_REQUEST['cpg'] : array();

$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';
$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : '';
$vrangelo = isset($_REQUEST['vrangelo']) ? $_REQUEST['vrangelo'] : '';
$vrangehi	= isset($_REQUEST['vrangehi']) ? $_REQUEST['vrangehi'] : '';
$noemail  = isset($_REQUEST['noemail']) ? $_REQUEST['noemail'] : '';

if ($drangelo == '') $drangelo = '2001-01-01';
if ($drangehi == '') $drangehi = date('Y-m-d',strtotime(now));
if ($vrangelo == '') $vrangelo = 0;
if ($vrangehi == '') $vrangehi = 1000000;

//echo "init drangehlo: $drangelo, drangehi: $drangehi<br />";
//echo "init vrangehlo: $vrangelo, vrangehi: $vrangehi<br />";

// echo '<pre> cbox'; print_r($cbox); echo '</pre>';
if (count($cpgbox) > 0) 
  $cpglist = '"\'' . implode("','",$cpgbox) . '\'"';
//echo "cpglist: $cpglist<br>";

$rptcpg = 'Campaign(s): '.$cpglist;
//	echo "$rptmbr<br />";

if (isset($_REQUEST['daterangechk'])) {
	$rptdate = 'Date Range ';	
	$rptdate .= 'greater than: ' . $drangelo . ', ';
	$rptdate .= 'less than: '. $drangehi . ', ';
	//echo "$rptdate<br />";
	}

$rptrng = '';
if (isset($_REQUEST['valrangechk'])) {
  $rptrng = "Value Range ";
  if ($vrangelo != 0) $rptrng .= "greater than $vrangelo "; 
  if ($vrangehi != 1000000) $rptrng .= "less than $vrangehi";
  }
  
// now ready to do db search for list by criteria using the sp
$sql = "CALL SummarizeCampaigns('$drangelo','$drangehi', $cpglist)";
//echo "SQL: $sql<br>";

//$res = doSQLsubmitted($sql);
$res = $mysqli->query($sql);
if ($mysqli->connect_errno) {
  echo "Query Failed: (" . $mysqli->errno . ") " . $mysqli->query_error;
  echo "<br>Query string: $sql <br><br>";
  exit;
	}
	
//echo "Query string: $sql <br><br>";

$nbr_rows = $res->num_rows;
//echo "rows returned: $nbr_rows<br />";

//$rc = 1;
//while ($row = $res->fetch_assoc()) {
//  echo "<pre> Row $rc "; print_r($row); echo '</pre>';
//  $rc++;
//  }
// exit;

// check result rows for value check
//echo "values - vrangehlo: $vrangelo, vrangehi: $vrangehi<br />";
$valcount = 0; $noaddr = 0; $nomail = 0; $withemail = 0;
$mcidtot = array();
while ($row = $res->fetch_assoc()) {
	$mcid = $row[MCID];
	if ($mcid == 'OTD00') continue; 
//	echo '<pre> row returned '; print_r($row); echo '</pre>';
	if ($row[Inactive] == 'TRUE') {    // ignore if record marked inactive
    $inactcnt += 1;
    continue;
   }

// add into results arrays
  $key = $row[MCID] . $row[Campaign];
  $results[$key] = $row;
  $grandtotal += $row[TotalAmount];
  $mcidtot[$key] += $row[TotalAmount];
  $mcidtotcnt[$key] += 1;
  }
// delete any TOTAL mcid/campaign amounts not in the value range
//  echo "vrangelo: $vrangelo, val: $mcidtot[$key], vrangehi: $vrangehi<br>";
if (count($mcidtot) > 0) {
  foreach ($mcidtot as $key => $tot) {
    if (($vrangelo > $tot) OR ($tot > $vrangehi)) {
  //    echo "removing $key<br>";
      $grandtotal -= $tot;
      unset($mcidtot[$key]);
      unset($results[$key]);
      $mcidtotcnt[$key] -= 1;    
      }
    }
}  
//echo "Inactive count: $inactcnt<br>";

// action == search
// --------------------- end -----------------------------

//echo "falsecount: $falsecount<br />";
//echo "valcount: $valcount<br />";
//echo "Unique MCID count: " . count($mcidtot) . "<br />";
if ($nbr_rows == 0) {
	print <<<nothingReturned

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
echo '<h3>Campaign Funding Report Results&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "Criteria: $rptmbr $rptcpg $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Funding rows extracted: $nbr_rows, Inactive recs dropped: $inactcnt, Unique MCIDs: " . count($mcidtot);
echo " - Grand total for campaign(s): $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaidByCampaign.csv\" download=\"FundingPaidByType.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

if (count($results) > 0) {
$csv[] =
"MCID;MemType;Campaign;Total;Cnt;Fname;Lname;Label1stLine;Salutation;Phone;EMail?;Email;Mail?;Address;City;St;Zip;Notes\n";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>MemType</th><th>Campaign</th><th>Total(Cnt)</th><th>Name</th><th>Phone</th>
<th>EMail?<th>Email</th><th>Mail?</th><th>Address</th><th>City/St/Zip</th><th>Notes</th></tr>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
foreach ($results as $k => $r) {
	$note = strtr($r[Notes], $translate);
	if ($r[E_Mail] == 'TRUE') $r[E_Mail] = 'Yes'; else $r[E_Mail] = 'No';
	if ($r[Mail] == 'TRUE') $r[Mail] = 'Yes'; else $r[Mail] = 'No'; 
	$mcid = $r[MCID]; $key = $r[MCID].$r[Campaign]; $cmpcnt = $mcidtotcnt[$key];
	$csv[] = "\"$mcid\";$r[MCtype];\"$r[Campaign]\";$mcidtot[$key];\"$cmpcnt\";\"$r[FName]\";\"$r[LName]\";\"$r[NameLabel1stline]\";\"$r[CorrSal]\";$r[PrimaryPhone];$r[E_Mail];$r[EmailAddress];$r[Mail];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
	echo "<tr><td>$mcid</td><td>$r[MCtype]</td><td>$r[Campaign]</td><td>$$mcidtot[$key](x$cmpcnt)</td><td>$r[NameLabel1stline]</td><td>$r[PrimaryPhone]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[City], $r[State], $r[ZipCode]</td><td>$note</td></tr>";
	//echo "<pre>"; echo "key: $k "; print_r($r); echo "</pre>";	
	}
echo "</table>";
file_put_contents('downloads/FundingPaidByCampaign.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';

?>
	
</body>
</html>
