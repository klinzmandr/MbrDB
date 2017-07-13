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
//include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime('-12 months'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime('-1 month'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

/*
if ($action == '') {
print <<<pagePart1
<h3>Reminder Conversion Rates</h3>
<p>The report scans the membership database and reports on the success/failure of sending email or mail notices to expired members.</p>
<a class="btn btn-primary" href="remconversions.php?action=go">Continue</a>
</body>
</html>

pagePart1;
exit;

}
*/
echo '<h3>Reminder Conversion Report&nbsp;&nbsp;
<a class="hidden-print btn btn-primary" onclick="javascript:window.close() ">CLOSE</a></h3>
<h4>For the 12 month period from '.$sd.' to '.$ed.'</h4>
<div class="well">';

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

/* create results array for all paid reminders in given time period by:
**  1. looking for the date of the LAST mail or email reminder
**  2. ignoring any reminder that has a subsequent renewal payment
**  3. ignore any renewal payment from a subscriber
**  4. ignore any renewal payment with no mail or email notice sent
*/

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

  if ($r[Reminders] == 'RenewalPaid')   {
    $resarray[$r[MCID]][paid] = $r[DateSent];
    $resarray[$r[MCID]][paidcnt] += 1; 
    continue; }
  if ($r[Reminders] == 'EMailReminder') {
    $resarray[$r[MCID]][emsent] = $r[DateSent];
    $resarray[$r[MCID]][emsentcnt] += 1;
    unset($resarray[$r[MCID]][paid]);
    unset($resarray[$r[MCID]][paidcnt]);
    continue; }
  if ($r[Reminders] == 'MailReminder')  {
    $resarray[$r[MCID]][msent] = $r[DateSent];
    $resarray[$r[MCID]][msentcnt] += 1;
    unset($resarray[$r[MCID]][paid]);
    unset($resarray[$r[MCID]][paidcnt]);
    continue; }
  }
//echo "res array size: " . count($resarray) . '<br>';
//echo '<pre> resarray ';  print_r($resarray); echo '</pre>';

// now delete from the resarray any paid entries with no sent date
// and eliminate any payments for reminders that were sent prior 
// to the start of the reporting period.
// and eliminate any payment recorded with no reminder recorded
// and eliminate any payment recorded before any reminder recorded 

$remdist = array(); $pddist = array();

foreach ($resarray as $k => $v) {
  if ((count($v) == 2) AND (isset($v[paid]))) 
    unset($resarray[$k]);       // this eliminates any paid with no reminder
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
echo '<ul>';
foreach ($remdist as $k => $v) {
  $p = isset($pddist[$k]) ? $pddist[$k] : 0;
  echo "$k: $v/$p<br>";
  }
echo '</ul></div>';

echo '<div class="well">';
echo "<h4>Summary</h4><ul>Email reminders sent: $emcount<br>";
echo "Email reminders paid: $empcount (".number_format(($empcount/$emcount)*100,2)."%)<br>";
echo "Mail reminders sent: $mcount<br>";
echo "Mail reminders paid: $mpcount (".number_format(($mpcount/$mcount)*100,2)."%)<br>";
echo "Total number reminders sent: " . count($resarray) . '<br>';
echo "Total number paid: $pdcount (" . number_format(($pdcount/($mcount+$emcount))*100, 2) . '%)</ul>';

?>
</div>
</body>
</html>
