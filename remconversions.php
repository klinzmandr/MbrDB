<!DOCTYPE html>
<html>
<head>
<title>Reminder Conversion Rpt</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime('-12 months'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime('-1 month'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
?>
<div class="container">
<div id="help">
<h3>Reminder Conversion Rates</h3>
<p>The report scans the membership database and reports on the success/failure of sending email or mail notices to expired members.</p>
<h4>Results are determined by:</h4>
<ul>
1. Look for the date of the LAST mail or email reminder sent and remember it.<br>
2. Count any reminder that has a subsequent renewal payment.<br>
3. Ignore any renewal payment from a subscriber.<br>
4. Ignore any renewal payment with no mail or email notice sent.<br>
</ul>
<p>Results output show how many payments were probably prompted by receiving either an email or mail reminder.</p>

</div>  <!-- help -->
<h3>Reminder Conversion Report&nbsp;&nbsp;
<span id="helpbtn" title="Help" class="glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span></h3>
<h4>For the 12 month period from <?=$sd?> to <?=$ed?></h4>

<?php
// create array of subscribers to ignore
$sql = 'SELECT `MCID` from `members` WHERE `MCtype` LIKE "%subscr%" ORDER by `MCID`';
$res = doSQLsubmitted($sql);
$nbr_subscribers = $res->num_rows;
//echo "nbr_subscribers: $nbr_subscribers<br>";
$subscribers = array();
while ($r = $res->fetch_assoc()) {
  $subscribers[] = $r[MCID];
}
//echo '<pre> subscribers ';  print_r($subscribers); echo '</pre>';

$sql = 'SELECT * from `correspondence` 
WHERE `DateSent` BETWEEN "'.$sd.'" and "'.$ed.'"
ORDER BY `DateSent` ASC';  
//echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$nbr_rows = $res->num_rows;
//echo "nbr_rows: $nbr_rows<br>";
$resarray = array();
while ($r = $res->fetch_assoc()) {
  if (in_array($r[MCID], $subscribers)) continue;  // ignore subscribers
  if ($r[Reminders] == '') continue;               // ignore empty
// returned rows are in date sent sequence
// remember payments - but forget them if reminder sent after getting it
  if ($r[Reminders] == 'RenewalPaid')   {
    $resarray[$r[MCID]][paid] = $r[DateSent];
    $resarray[$r[MCID]][paidcnt] += 1; 
    continue; }
// email sent after payment made noted
  if (($r[Reminders] == 'EMailReminder') OR ($r[Reminders] == 'RenewalReminder')) {
    $resarray[$r[MCID]][emsent] = $r[DateSent];
    $resarray[$r[MCID]][emsentcnt] += 1;
    unset($resarray[$r[MCID]][paid]);
    unset($resarray[$r[MCID]][paidcnt]);
    continue; }
// mail sent after payment made noted
  if ($r[Reminders] == 'MailReminder')  {
    $resarray[$r[MCID]][msent] = $r[DateSent];
    $resarray[$r[MCID]][msentcnt] += 1;
    unset($resarray[$r[MCID]][paid]);
    unset($resarray[$r[MCID]][paidcnt]);
    continue; }
  $somethingelse[$r[MCID]] = $r;
  }
// echo "res array size: " . count($resarray) . '<br>';
// echo '<pre> resarray ';  print_r($resarray); echo '</pre>';

// now delete from the resarray any paid entries with no sent date
// and eliminate any payments for reminders that were sent prior 
// to the start of the reporting period.
// and eliminate any payment recorded with no reminder recorded
// and eliminate any payment recorded before any reminder recorded 

$remdist = array(); $pddist = array();

foreach ($resarray as $k => $v) {
  if ((count($v) == 2) AND (isset($v[paid]))) {
    unset($resarray[$k]);       // this eliminates any paid with no reminder
    // echo '<pre> paid/no reminder '; print_r($v); echo '</pre>';
    }
  else {                        // otherwise create summary arrays
    if (isset($v[emsent])) {
      $yrmo = substr($v[emsent],0,7);
      $remdist[$yrmo] += 1;
      }
    if (isset($v[msent])) {
      $yrmo = substr($v[msent],0,7);
      $remdist[$yrmo] += 1;
      }
    if (isset($v[paid])) {
      $yrmo = substr($v[paid],0,7);
      $pddist[$yrmo] += 1;
      }
    }
  }
//echo "xresarray size: " . count($resarray) . '<br>';
//echo '<pre> xresarray ';  print_r($resarray); echo '</pre>';

// summarize the final results
foreach ($resarray as $k => $v) {
  if (isset($v[emsent])) $emcount += 1;
  if (isset($v[emsent]) AND (isset($v[paid]))) $empcount += 1;
  if (isset($v[msent])) $mcount += 1;
  if (isset($v[msent]) AND (isset($v[paid]))) $mpcount += 1;
  if (isset($v[paid])) $pdcount += 1;
  }
echo "<h4>Reminders sent/returned distribution by month: </h4>";
ksort($remdist);
//echo '<pre> remdist ';  print_r($remdist); echo '</pre>';
// output distributon array
echo '
<style>
table { border-spacing: 2px; }  /* cellspacing */
th,td { padding: 5px; }         /* cellpadding */
</style>

<ul><table>
<tr><th>Yr/Mo</th><th>Sent</th><th>Paid</th></tr>';
foreach ($remdist as $k => $v) {
  $p = isset($pddist[$k]) ? $pddist[$k] : 0;
  echo "<tr><td>$k</td><td align='right'>$v</td><td align='right'>$p</td></tr>";
  }
echo '</table></ul>';

echo "<ul>
<table><tr><th>Period Summary</th><th>Sent</th><th>Paid</th><th>Rate</th></tr>
<tr><td>Email reminders: </td><td align='right'>$emcount</td><td align='right'>$empcount</td><td align='right'>".number_format(($empcount/$emcount)*100)."%</td><tr>";
echo "<tr><td>Mail reminders sent: </td><td align='right'>$mcount</td><td align='right'>$mpcount</td><td align='right'>".number_format(($mpcount/$mcount)*100)."%</td></tr>";
echo "<tr><td>Total number reminders sent: </td><td align='right'>" . ($mcount+$emcount) . "</td><td align='right'>$pdcount</td><td align='right'>" . number_format(($pdcount/($mcount+$emcount))*100) . '%</td><tr></table></ul>
<br><br><br>===== END REPORT =====<br>';

// echo '<pre>bad reminder notes '; print_r($somethingelse); echo '</pre>';
?>
</div>
</body>
</html>
