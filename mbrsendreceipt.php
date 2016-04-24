<!DOCTYPE html>
<html>
<head>
<title>Send Receipt</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<div class="container">
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$mcid = $_SESSION['ActiveMCID'];
$yr = isset($_REQUEST['yr']) ? $_REQUEST['yr'] : date('Y', strtotime("now"));

$sqlmcid = "SELECT * FROM `members` WHERE `MCID` = '$mcid';";
$resmcid = doSQLsubmitted($sqlmcid);
$mcidinfo = $resmcid->fetch_assoc();
// echo '<pre> MCID '; print_r($mcidinfo); echo '</pre>';
$startyr = $yr . '-01-01'; $endyr = $yr . '-12-31';
$sqldon = "SELECT * FROM `donations` WHERE `MCID` = '$mcid' 
AND `DonationDate` BETWEEN '$startyr' AND '$endyr' 
ORDER BY `Program` ASC, `DonationDate` ASC;";
// echo "sql: $sqldon<br>";
$resdon = doSQLsubmitted($sqldon);
$donrowcnt = $resdon->num_rows;
while ($rowdon = $resdon->fetch_assoc()) {
	//echo '<pre> Donations '; print_r($rowdon); echo '</pre>';
	$doninfo[$rowdon[DonationID]] = $rowdon;
	$donsum[$rowdon[Program]]['count'] += 1;
	$donsum[$rowdon[Program]]['total'] += $rowdon[TotalAmount];
	}
// echo '<pre> Don Summary '; print_r($donsum); echo '</pre>';
// echo '<pre> Don Info '; print_r($doninfo); echo '</pre>';

if ($action == '') {
if ($donrowcnt == 0) {
	echo '<h3 style="color: RED; ">Send Funding Receipt ERROR</h3>
<p>There are no funding records for '. $mcid . ' for ' . $yr . '</p>';
	}

print <<<scriptsPart
<script type="text/javascript">
// sets select list to pre-selected year value
$(document).ready(function () { 
	$("#yr").val("$yr");
	});
</script>
<script type="text/javascript">
function chkitems() {
	var numberOfChecked = $('input:checkbox:checked').length;
	if (numberOfChecked == 0) {
		alert("Nothing has been selected to include in the receipt.");
		return false;
		}
	return true;
	}
</script>

scriptsPart;

echo '<form action="mbrsendreceipt.php" method="post"  name="selectform";>';
echo '
<h3>Send Funding Receipt</h3>Select Year:<br>
<select name="yr" id="yr" onchange="this.form.submit()">
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
</select>
</form>
';

if ($donrowcnt == 0) { exit(0); }		// no funding for this year - need to choose again

$progarray = array();								// get program names
$progblob = readdblist('Programs');
$progarray = formatdbrec($progblob);
// echo '<pre> Programs '; print_r($progarray); echo '</pre>';

echo '<h3>Creating receipt for member <a href="mbrinfotabbed.php">' . $mcid . '</a></h3>
Please select the funding item(s) to include on the receipt.';
echo '<form action="mbrsendreceipt.php" method="post" name="selectform" onsubmit="return chkitems()">';
echo '<table border=0 class="table-condensed">';
echo '
<tr><td></td><td><b>Payment Item</b></td><td><b>Date</b></td><td><b>Amount</b></td><td><b>Note</b></td></tr>
<tr><td><input type="checkbox" id="chkr" name="chkr" value=""></td><td>Check All/None</td><td></td><td></td></tr>';

foreach ($doninfo as $k=>$v) { 		// send the donaton rec number for each item selected
	$pgm = $progarray[$v[Program]];
	print <<<formPart2
<tr><td><input type="checkbox" name="items[]" id="items[]" value="$k"></td><td>$pgm</td><td>$v[DonationDate]</td><td align="right">$$v[TotalAmount]</td><td>$v[Note]</td></tr>

formPart2;
	}
echo '</table>
<input type="hidden" name="action" value="create">
<input type="hidden" name="total" id="total" value="0">';
echo "<input type=\"hidden\" name=\"yr\" value=\"$yr\">";
echo '<input type="submit" name="submit" value="Select Checked">
</form></div>
';
print <<<scriptPart2
<script>
$("#chkr").click(function() {
if ($("#chkr").prop('checked')) 
	{ $("input").prop("checked", true);	}
else 
	{ $("input").prop("checked", false);	}
});
</script>
</body></html>
scriptPart2;

exit(0);
}

// action == 'create' - Creation of receipt 
// include 'Incls/vardump.inc.php';
//echo "action: $action"; echo ", startyr: $startyr<br>";
$total = isset($_REQUEST['total']) ? $_REQUEST['total'] : 0;
$items = $_REQUEST['items'];
$listitems = '(' . implode(',',$items) . ')';
//echo "items: $items"; echo "listitems: $listitems<br>";

$sqldon = "SELECT * FROM `donations` WHERE `DonationID` IN $listitems 
AND `DonationDate` BETWEEN '$startyr' AND '$endyr' 
ORDER BY `DonationID`;";
// echo "sql: $sqldon<br>";
$resdon = doSQLsubmitted($sqldon);
$donrowcnt = $resdon->num_rows;	
// echo "donrowcnt: $donrowcnt<br>";
$total = 0;
while ($rowdon = $resdon->fetch_assoc()) {
	$total += $rowdon[TotalAmount];
	}
echo '<h2>Receipt data collection complete.</h2><h3>Click to <a class="btn btn-primary" href="mbrsendreceipt.php">RE-DO</a> the selection criteria.<br>';
echo "<h3>A receipt for $donrowcnt item(s) totalling $$total for member $mcid is ready.</h3>
<h4>Continue by choosing to send an email or printed receipt.</h4><br>";

echo '
<table class="table">
<tr><td width="50%">A receipt will be sent to the email address on file for the member. Check the webmail in-box at pacwilica.org/roundcube user \'reminders\' for any reply that might be sent in response.<br><br>
<div align="center">
<form action="mbremailnotice.php" method="post">';
echo "<input type=\"hidden\" name=\"items\" value=\"$items\">";
echo "<input type=\"hidden\" name=\"itemcount\" value=\"$donrowcnt\">";
echo "<input type=\"hidden\" name=\"total\" value=\"$total\">";
echo '<input type="hidden" name="type" value="receipt">';
echo "<input type=\"submit\" name=\"sendemail\" value=\"Send Email Receipt\">";
echo '
</form>
</div>
</td>
<td>A receipt will be created and queued for printing. Use the \'Reminders-> Print Labels and Letters\' menu item to print the hardcopy. Don\'t forget to delete the receipt once the printing has been completed.<br><br>
<div align="center">
<form action="mbrnotice.php" method="post">';
echo "<input type=\"hidden\" name=\"items\" value=\"$items\">";
echo "<input type=\"hidden\" name=\"itemcount\" value=\"$donrowcnt\">";
echo "<input type=\"hidden\" name=\"total\" value=\"$total\">";
echo '<input type="hidden" name="type" value="receipt">';
echo "<input type=\"submit\" name=\"sendemail\" value=\"Print Mail Receipt\">";
echo '</form></div>
<hr></td></tr>
</table><br><br>';

//echo '<pre> MCID '; print_r($mcidinfo); echo '</pre>';
//echo '<pre> ALL Donations '; print_r($doninfo); echo '</pre>';

?>
</div>
</body>
</html>
