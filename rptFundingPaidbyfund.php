<!DOCTYPE html>
<html>
<head>
<title>Summarize Programs</title>
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

// set up form and validations to start
if ($action == '') {
print <<<formPart1
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

<script>
// initial setup of jquery function(s) for page
$(document).ready (function () {
  
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
  //alert("submiss of selection form");
	//alert("check values entered");
	var errmsg = ""; var chks = 0; var chkcnt = 0;
	var chks = $('[name="cbox[]"]:checked').length; // count checked programs
	
	if (chks == 0) {
		errmsg += "No Funding/Campaign Type(s) have been selected\\n";
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
formPart1;

$systemlists = readdblist('Programs');
$progtypes = formatdbrec($systemlists);

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
		default   : $other[$desc] = $k; break;
		}
	}

echo '<h3>Funding Paid Report <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>';

print <<<formPart2
<p>This report lists the total funding provided for each MCID for each funding type selected.</p>
<h4>Select one or more of the following:</h4>

<script>
$(document).ready(function () {
});
</script>

<form action="rptFundingPaidbyfund.php" method="post" class="form" id="selectionform" >
<ul>
<b>Funding Type(s)</b><br>
<table width="95%" class="table-condensed" border=1>
<tr>

formPart2;

echo '<td valign=top><input type=checkbox id="cb0main"> Dues:<ul>
';

foreach ($dues as $k => $v) {
//	echo "fund type key: $k, value:$v<br>";
	echo "
<input type=checkbox id='cb0' name=cbox[] value='$v'> - $v<br>
";
	}

echo '</ul></td><td valign=top><input type=checkbox id="cb1main"> Donations:<ul>
';
foreach ($don as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id='cb1' name=cbox[] value='$v'> - $v<br>
";
	}
echo '</ul></td><td valign=top><input type=checkbox id="cb2main"> Directed Donations:<ul>';
foreach ($dir as $k => $v) {
//	echo "mctype key: $k, value:$v<br>";
	echo "
<input type=checkbox id='cb2' name=cbox[] value='$v'> - $v<br>
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

echo '</ul></td></tr></table>';

echo '
<b>AND</b><br />
<!-- <input type="checkbox" name="noemail" value="noemail"> - Exclude those with email addresses<br> -->
<input type="checkbox" id="sdchk" name="daterangechk" value="daterange" size="1"> - Funding Date Range is from:
<input type="text" name="sd" id="sd" value="" autocomplete="off"> and/or before: 
<input type="text" name="ed" id="ed" value="" autocomplete="off"><br />
<input type="checkbox" id="vchk" name="valrangechk" value="valrange" size="1"> - Total Funding Range :
<input placeholder="Low Amount" id="vl" type="text" name="vrangelo" value=""> and/or : 
<input placeholder="High Amount" id="vh" type="text" name="vrangehi" value=""><br>
<input type="hidden" name="action" value="search"><br>
<input type="submit" name="submit" value="submit">
</form>
</ul>
';
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
  $proglist = '"\'' . implode("','",$cbox) . '\'"';
// echo "proglen: ". strlen($proglist) . ", proglist: $proglist<br>";

$rptprogs = 'Program(s): '.$proglist;
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
$sql = "CALL SummarizePrograms('$drangelo','$drangehi', $proglist)";
// echo "SQL: $sql<br>";

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
echo '<h3>Program Funding Report Results&nbsp;&nbsp;';
echo " <a href=\"javascript:self.close();\" class=\"btn btn-primary btn-xs\">CLOSE</a><br></h3>";
echo "Criteria: $rptmbr $rptcpg $rptdate $rptrng<br />";
$grandtotal = number_format($grandtotal);
echo "Funding rows extracted: $nbr_rows, Inactive recs dropped: $inactcnt, Unique MCIDs: " . count($mcidtot);
echo " - Grand total for program(s): $" . $grandtotal . '<br>';

echo "<a href=\"downloads/FundingPaidByProgram.csv\" download=\"FundingPaidByProgram.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

if (count($results) > 0) {
$csv[] =
"MCID;MemType;Total;Cnt;Fname;Lname;Label1stLine;Salutation;Phone;EMail?;Email;Mail?;Address;City;St;Zip;Notes\n";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>MemType</th><th>Total(Cnt)</th><th>Name</th><th>Phone</th>
<th>EMail?<th>Email</th><th>Mail?</th><th>Address</th><th>City/St/Zip</th><th>Notes</th></tr>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
foreach ($results as $k => $r) {
	$note = strtr($r[Notes], $translate);
	if ($r[E_Mail] == 'TRUE') $r[E_Mail] = 'Yes'; else $r[E_Mail] = 'No';
	if ($r[Mail] == 'TRUE') $r[Mail] = 'Yes'; else $r[Mail] = 'No'; 
	$mcid = $r[MCID]; $key = $r[MCID].$r[Campaign]; $cmpcnt = $mcidtotcnt[$key];
	$csv[] = "\"$mcid\";$r[MCtype];$mcidtot[$key];\"$cmpcnt\";\"$r[FName]\";\"$r[LName]\";\"$r[NameLabel1stline]\";\"$r[CorrSal]\";$r[PrimaryPhone];$r[E_Mail];$r[EmailAddress];$r[Mail];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
	echo "<tr><td>$mcid</td><td>$r[MCtype]</td><td>$$mcidtot[$key](x$cmpcnt)</td><td>$r[NameLabel1stline]</td><td>$r[PrimaryPhone]</td><td>$r[E_Mail]</td><td>$r[EmailAddress]</td><td>$r[Mail]</td><td>$r[AddressLine]</td><td>$r[City], $r[State], $r[ZipCode]</td><td>$note</td></tr>";
	//echo "<pre>"; echo "key: $k "; print_r($r); echo "</pre>";	
	}
echo "</table>";
file_put_contents('downloads/FundingPaidByProgram.csv',$csv);
	}
echo '----- END OF LISTING -----<br />';

?>

</body>
</html>
