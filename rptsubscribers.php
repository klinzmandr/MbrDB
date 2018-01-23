<?php 
session_start(); 
//include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Subscribers Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
$("document").ready(function () {
  $("#info").hide();
  $("#infoclk").click(function() {
    $("#info").toggle();
    });
});
</script>

<div class="container"> 
<h3>Subscribing Members Report&nbsp;<span id="infoclk" title="Help" class="hidden-print glyphicon glyphicon-question-sign" style="color: blue; font-size: 25px"></span>
&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>

<div id="info">
<p>This report provides a reconciliation point for members that have been indentified as &apos;subscribing&apos; members where a subscribing member is defined as having a Member Status of &apos;1-Member&apos; with a Member Type of &apos;1-Subscriber&apos; or a Member Status of &apos;2-Volunteer&apos; with a Member Type of &apos;2-VolSubsciber&apos;.  
<p>This report has 5 potential sections:</p>
<ol>
	<li>Listing of all subscription members whose last DUES payments was marked as a &apos;non-subscription&apos; payment but their membership status and type indicate them as a &apos;subscribing&apos; member.  These MCID&apos;s should be examined and either the payment changed to a &apos;subscription&apos; payment OR the member&apos;s record be updated to make them a &apos;regular&apos; member or volunteer.</li>
	<li>Listing of all regular members whose last DUES payments was marked as a &apos;subscription&apos; payment but their membership status and type do NOT indicate them as a &apos;subscribing&apos; member.  These MCID&apos;s should be examined and either the payment changed to a &apos;regular&apos; Purpose OR the member&apos;s record be updated to make them a &apos;subscribing&apos; member or volunteer.</li>
	<li>Listing of all &apos;subscribing&apos; members that do not have a &apos;subscription&apos; DUES payment within the last 120 days.  These MCID&apos;s may need to be reclassified making them &apos;non-subscribers&apos; or the DUES payment deleted and re-entered as a subscription payment.</li>
	<li>Listing of all MCID&apos;s that are registered as &apos;subscribing&apos; members that have a DUES payment designated as a &apos;subscription&apos; payment within the last 120 days.</li>
</ol>
<p>NOTE: the first three sections will not appear unless there is a qualifying MCID to be listed.</p>
</div>  <!-- container -->
</div>   <!-- info -->

<style>
table, th, td {
    border: 0px solid black;
    font-size: 15px;
}
</style>

<?php
// now ready to do db search for list by criteria using the sp
// the sp lists the date of the last funding record joined with the purpose and
//   amount from the donation record plus the member info from the member record for 
//   just ACTIVE members.
$sql = "CALL rptsubscriberssp()";
//echo "SQL: $sql<br>";

addlogentry("SPCalled: rptsubscriberssp");

$res = $mysqli->query($sql);
$resrows = $res->num_rows;
//echo "rows returned: $resrows<br />";

if ($mysqli->errno != 0) {
  echo "Query Failed: (" . $mysqli->errno . ") - " . $mysqli->query_error;
  echo "<br>Failing Query string: $sql <br><br>";
  exit;
	}
// memorize the results returned for repeated use.	
while ($r = $res->fetch_assoc()) {  // read results and do value range check
	$resarray[$r[MCID]] = $r;
  }
//echo '<pre> row returned '; print_r($resarray); echo '</pre>';

// ================================================	
// Section 1: create subscriber member report that have last dues payment as non-subscription
$exp = strtotime("now - 120 days");
$expformatted = date("M d, Y", $exp);
echo '<h4>1. Subscribing Members with Last Dues Payment marked as a &apos;non-subscription&apos; Payment</h4>';

echo '<table class="table table-condensed">';
echo 'Subscription members having their last Dues payment marked as a &apos;non-subsciption&apos; payment.  Their status should be reveiwed and either the payment&apos;s Program or the member&apos; status changed appropriately.<br>';
$l = array();
$l[] = '<table class="table">
<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';
foreach($resarray as $k => $r) {
  if (!preg_match("/dues/i", $r[Program])) continue;
  if ((preg_match("/subscr/i", $r[MCtype])) AND (!preg_match("/subscr/i", $r[Program]))) {
    $l[] = "<tr><td>$r[MCID]</td><td>$r[Program]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
    }
  }
$l[] = '</table>----- END OF LIST -----<br>';
if (count($l) > 2) {
  foreach ($l as $v) echo $v; }
else
  echo '--- NONE ---<br>';


// ======================================================
// Section 2: create non-subscribers report - first get array of last subscriptions
echo '<h4>2. Non-subscriber Payments</h4>';
echo 'Regular supporters having their Dues payment since '.$expformatted.' or within the last 120 days marked as a &apos;subsciption&apos; payment but are not registered as a subscribing member or subscribing volunteer.<br>';

$l = array();
$l[] = '<table class="table table-condensed">
<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';

foreach($resarray as $k => $r) {
  if ((strtotime($r[LastDate])) < (strtotime("now - 120 days"))) continue;
  // if (!preg_match("/dues/i", $r[Program])) continue;
  if ((!preg_match("/subscr/i", $r[MCtype])) AND (preg_match("/subscr/i", $r[Program]))) {
    $l[] = "<tr><td>$r[MCID]</td><td>$r[Program]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
    }
  }
$l[] = '<table>----- END OF LIST -----<br>';
if (count($l) > 2) {
  foreach ($l as $v) echo $v; }
else
  echo '--- NONE ---<br>';
  
// =================================================		
// Section 3: list all those delinquent and/or improperly classified
echo '<h4>3. Delinquent Subscribing Members</h4>';
echo 'Subscribing members with no subscription payment since '.$expformatted.' (or in the last 120 days).<br>(NOTE: some may also have their last payments improperly classified.)<br>';

$l = array();
$l[] = '<table class="table table-condensed">
<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';

foreach($resarray as $k => $r) {
  $lpay = strtotime($r[LastDate]);
  if ($lpay > $exp) { 
    //echo "lpay: $lpay, exp: $exp<br>"; 
    continue; 
    }
  if (preg_match("/subscr/i", $r[MCtype])) {
    $l[] = "<tr><td>$r[MCID]</td><td>$r[Program]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
    }
  }
$l[] = '<table>----- END OF LIST -----<br>';
if (count($l) > 2) {
  foreach ($l as $v) echo $v; }
else
  echo '--- NONE ---<br>';

// =================================================
// Section 4:listing of all current subscribers with proper payment info
$l = array();
$l[] = '<table class="table table-condensed">
<tr><th>MCID</th><th>Program</th><th>LastPay</th><th>Name</th><th>MemType</th><th>Notes</th></tr>';

foreach($resarray as $k => $r) {
  $lpay = strtotime($r[LastDate]);
  if ($lpay < $exp) { 
    //echo "lpay: $lpay, exp: $exp<br>"; 
    continue; 
    }
  if (preg_match("/subscr/i", $r[MCtype])) {
    $l[] = "<tr><td>$r[MCID]</td><td>$r[Program]</td><td>$r[LastDate]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Notes]</td></tr>";
    }
  }
$l[] = '<table>----- END OF LIST -----<br>';

$lc = count($l) -2;
echo '<h4> 4. Current Subscribing Members ('.$lc.')</h4>
	Subscribing members with current with payments within the since '.$expformatted.' (or in the last 120 days).<br>';

if (count($l) > 2) {
  foreach ($l as $v) echo $v; }
else
  echo '--- NONE ---<br>';

	
	
echo '----- END OF LIST -----';

?>
</body>
</html>
