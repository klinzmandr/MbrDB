<!DOCTYPE html>
<html>
<head>
<title>Funding by Type</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>

<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {   // set up form and field validatons
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
//include 'Incls/vardump.inc.php';
	print <<<pagePart1

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

<script>
$(document).ready(function () {

// set/clear check box groups
$("[id$=main]").change(function() { 
    var id = this.id;
    var fullid = "#" + id;
    var partid = "[id=" + id.substring(0,3) + "]";
    var x = $(fullid).prop("checked");
    // console.log("fullid: "+fullid+", partid: " + partid);
    $(partid).prop("checked", x);
    });     

// validate selection form
$("#selectionform").submit(function (event) {
	var errmsg = ""; var chks = 0; var chkcnt = 0;
	var chks = $('[name="cbox[]"]:checked').length; // count checked programs
	
	if (chks == 0) {
		errmsg += "No Member Types(s) have been selected\\n";
		chkcnt += 1;
		}

	if ($("#sdchk").is(':checked')) {
		chkcnt += 1;
		if (($("#sd").val().length == 0) && ($("#ed").val().length == 0))  {
			errmsg += "Date search but no date(s) have been entered\\n";
			}
		}
	if ((($("#sd").val().length > 0) || ($("#ed").val().length > 0)) && 
	   ($("#sdchk").is(':checked') === false)) {
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
	event.preventDefault();
//	return false;
	});

}); // end document.ready function

</script>

<h3>Funding by Member Type Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report lists all records that would be used for by the &apos;Print Labels on Criteria&apos; based on the criteria selected.  The output is sorted by Total amount in descending sequence.</p>
<h4>Select one or more of the following criteria:</h4>

<form action="rptFundingPaidbytype.php" id="selectionform" method="post"  class="form">
<ul>

pagePart1;

echo 'Member Type(s)<br>
<table width="90%" class="table-condensed" border=0>
<tr><td valign=top><input type=checkbox id="cb0main"> Contacts:<ul>';
foreach ($mctype0 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=cb0 name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb1main"> Members:<ul>
';
foreach ($mctype1 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=cb1 name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb2main"> Volunteers:<ul>';
foreach ($mctype2 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=cb2 name=cbox[] value=\"$k\"> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb3main"> Supporters:<ul>';
foreach ($mctype3 as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id=cb3 name=cbox[] value=\"$k\"> - $v<br>
";
	}

echo '</ul></td></tr></table>';

print <<<pagePart2
AND<br />
<input type="checkbox" name="noemail" value="noemail"> - Exclude those with email addresses<br>
<input type="checkbox" id=sdchk name="daterangechk" value="daterange" size="1"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value="" autocomplete="off"> and/or before: 
<input type="text" name="ed" id="ed" value="" autocomplete="off"><br />
<input type="checkbox" id=vchk name="valrangechk" value="valrange" size="1"> - Total Funding Range :
<input placeholder="Low Amount" id=vl type="text" name="vrangelo" value=""> and/or : 
<input placeholder="High Amount" id=vh type="text" name="vrangehi" value=""><br /><br />
<input type="hidden" name="action" value="search"><br />
<input type="submit" name="submit" value="submit">
</form>
</ul>
pagePart2;

exit;
	}

// ------------------ start ----------------
// use input parameters to select records
// include 'Incls/vardump.inc.php';

// NOTE!  This query is performed by a stored procedure on the database.  Its 
//        results are TOTALLY dependent on the accuracy of the stored procedure!
//        The sp does a select of all campaigns in the list storing the results in
//        a temporary table which s then selected based on the date range of the
//        dates provided in the call as input from the form.
//
//        The result rows are totalled by MCID and campaign based on the 
//        value range provided in the input form

$cbox = isset($_REQUEST['cbox']) ? $_REQUEST['cbox'] : array();

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
//echo '<pre> cbox '; print_r($cbox); echo '</pre>';

if (count($cbox) > 0) 
  $typelist = '"\'' . implode("','",$cbox) . '\'"';
//echo "proglist: $proglist<br>";

$rptprogs = 'MbrTypes(s): '.$typelist;
//	echo "$rptmbr<br />";

if (isset($_REQUEST['daterangechk'])) { // do form date range checks
	$rptdate = 'Date Range ';	
	$rptdate .= 'greater than: ' . $drangelo . ', ';
	$rptdate .= 'less than: '. $drangehi . ', ';
	//echo "$rptdate<br />";
	}

$rptrng = '';
if (isset($_REQUEST['valrangechk'])) { // do form value range checks
  $rptrng = "Value Range ";
  if ($vrangelo != 0) $rptrng .= "greater than $vrangelo "; 
  if ($vrangehi != 1000000) $rptrng .= "less than $vrangehi";
  }
  
// now ready to do db search for list by criteria using the sp
$sql = "CALL SummarizeMbrTypes('$drangelo','$drangehi', $typelist)";
//echo "SQL: $sql<br>";

$res = $mysqli->query($sql);
$nbr_rows = $res->num_rows;
//echo "rows returned: $nbr_rows<br />";

if ($mysqli->errno != 0) {
  echo "Query Failed: (" . $mysqli->errno . ") - " . $mysqli->query_error;
  echo "<br>Failing Query string: $sql <br><br>";
  exit;
	}

//$rc = 1;
//while ($row = $res->fetch_assoc()) {
//  echo "<pre> Row $rc "; print_r($row); echo '</pre>';
//  $rc++;
//  }

// check result rows for value check
//echo "values - vrangehlo: $vrangelo, vrangehi: $vrangehi<br />";
while ($row = $res->fetch_assoc()) {  // read results and do value range check
	$mcid = $row[MCID];
	if ($mcid == 'OTD00') continue; 
//	echo '<pre> row returned '; print_r($row); echo '</pre>';
	if ($row[Inactive] == 'TRUE') {    // ignore if record marked inactive
    $inactcnt += 1;
    continue;
   }

// add into results arrays
  $key = $row[MCID] . $row[MCtype];
  $results[$key] = $row;
  $grandtotal += $row[TotalAmount];
  $mcidtot[$key] += $row[TotalAmount];
  $mcidtotcnt[$key] += 1;
  }
// delete any TOTAL mcid/campaign amounts not in the value range
//  echo "vrangelo: $vrangelo, val: $mcidtot[$key], vrangehi: $vrangehi<br>";
foreach ($mcidtot as $key => $tot) {
  if (($vrangelo > $tot) OR ($tot > $vrangehi)) {
//    echo "removing $key<br>";
    $grandtotal -= $tot;
    unset($mcidtot[$key]);
    unset($results[$key]);
    $mcidtotcnt[$key] -= 1;    
    }
  }

//echo "rows: $nbr_rows, unique MCIDs: " . count($mcidtot) . '<br>';
//echo '<pre> mcidtotcnt '; print_r($mcidtotcnt); echo '</pre>';
//echo '<pre> mcidtot '; print_r($mcidtot); echo '</pre>';

if ($nbr_rows == 0) {   // output error page
	print <<<nothingReturned
<h4>No MCID&apos;s meet the criteria supplied</h4>
Criteria: $rptmbr $rptcpg $rptdate $rptrng<br>
<!-- SQL: $sql<br> -->
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
</body>
</html>
nothingReturned;
	exit;
	}

// create html document
echo '<h3>Member Type Funding Report&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "Criteria: $rptmbr $rptcpg $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Rows extracted: $nbr_rows, Unique MCIDs: " . count($mcidtot);
echo " - Grand total for member type(s): $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaidByMbrType.csv\" download=\"FundingPaidByMbrType.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

if (count($results) > 0) {
$csv[] =
"MCID;MemType;Total;Cnt;Fname;Lname;Label1stLine;Salutation;Phone;EMail?;Email;Mail?;Address;City;St;Zip;Notes\n";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>MbrType</th><th>Total(Cnt)</th><th>Name</th><th>Phone</th>
<th>EMail?<th>Email</th><th>Mail?</th><th>Address</th><th>City/St/Zip</th><th>Notes</th></tr>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
foreach ($results as $k => $r) {
	$note = strtr($r[Notes], $translate);
	if ($r[E_Mail] == 'TRUE') $r[E_Mail] = 'Yes'; else $r[E_Mail] = 'No';
	if ($r[Mail] == 'TRUE') $r[Mail] = 'Yes'; else $r[Mail] = 'No'; 
	$mcid = $r[MCID]; $key = $r[MCID].$r[MCtype]; $cmpcnt = $mcidtotcnt[$key];
	$csv[] = "\"$mcid\";\"$r[MCtype]\";$mcidtot[$key];$cmpcnt;\"$r[FName]\";\"$r[LName]\";\"$r[NameLabel1stline]\";\"$r[CorrSal]\";$r[PrimaryPhone];$r[E_Mail];$r[EmailAddress];$r[Mail];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
	echo "<tr><td>$mcid</td><td>$r[MCtype]</td><td>$$mcidtot[$key](x$cmpcnt)</td><td>$r[NameLabel1stline]</td><td>$r[PrimaryPhone]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[City], $r[State], $r[ZipCode]</td><td>$note</td></tr>";
	//echo "<pre>"; echo "key: $k "; print_r($r); echo "</pre>";	
	}
echo "</table>";
file_put_contents('downloads/FundingPaidByMbrType.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';

?>

</body>
</html>
