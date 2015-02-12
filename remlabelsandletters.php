<!DOCTYPE html>
<html>
<head>
<title>Labels and Letters</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<?php
session_start();
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';
include 'Incls/mainmenu.inc';
//include 'Incls/vardump.inc';

$clicker = isset($_REQUEST['clicker']) ? $_REQUEST['clicker'] : '';
$ua = $_SERVER['HTTP_USER_AGENT'];
//echo "clicker: $clicker<br>";
if ($clicker == "Delete Checked") {
	$cba = isset($_REQUEST['cba']) ? $_REQUEST['cba'] : 0;
	if (!is_array($cba)) {
		$cbab[dummy] = 'dummy';
		$cbab[] = $cba;
		}
	else {
		$cbab = $cba;
		}
	//echo "<pre>cbab: "; print_r($cbab); echo "</pre>";
	//unset($cbab[dummy]);
	foreach ($cbab as $v) {
		if ($v == 'dummy') continue;
	//echo "deleting records number: ".$v."<br />";
		$sql = "DELETE FROM `labelsandletters` WHERE `LLID` = '". $v . "'";
		$res = doSQLsubmitted($sql);
		$r = $res->affected_rows;
		}

//echo "<pre>cbab: "; print_r($cbab); echo "</pre>";
$rc = count($cbab);
print <<<confMsg
<div class="container">
<h3>Confiramtion of Deletion</h3>
<h4>Number of label and letter items deleted: $rc<br /></h4>
<br />
<!-- <a class="btn btn-primary" href="index.php">RETURN</a></div> -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div></body></html>
confMsg;
	exit;
	}

$list = isset($_REQUEST['list']) ? $_REQUEST['list'] : "";
if ($list == "") { 
	$sql = 'select * from labelsandletters';
	$res = doSQLsubmitted($sql);
	$nbrrows = $res->num_rows;
	print <<<pagePart1
<div class="container">
<h3>Labels and Letters List</h3>
<h4>There are currently $nbrrows in the Letters and Labels List.</h4>
<p>This list is created and added to when a membership subscription has expired and a letter or post card reminder mailing has been initiated.  The list contains all the information required to print either formatted page(s) of labels and/or the form letter designated from an existing template.</p>
pagePart1;

if (stripos($ua,'Chrome') === FALSE)
print <<<pagePart2
<h4 style="color: red; ">Please note that printing labels is best done when using the Chrome web browser.  Other browers may be used but careful testing must be done BEFORE trying to print on label stock.  Make sure that browser margin settings are correctly set in any case.</h4>
pagePart2;

print <<<pagePart3a
<p>Once the labels and letters have been printed they can (and should be) eliminated form the letters and letters database list so that duplicate mailings will be avoided.</p>
<p>After printing, you may delete any or all of list items by placing a check in the associated box and clicking the 'Delete Items' button.</p>

pagePart3a;
	if ($nbrrows > 0) {
	echo "<a class=\"btn btn-primary\" href=\"remlabelsandletters.php?list=all\">CLICK TO LIST AND DELETE LABELS/LETTERS</a></h4><br /><br />";
print <<<pagePart3b
<table class="table">
<tr>
<td>
<h4>Print Labels</h4>
<p>This facility allows the creation of a page formatted as printing labels based on the criteria selected.  All labels will be sorted by zip code in ascending sequence.</p>
<p>Clicking &apos;SUBMIT&apos; will open a new window with the lables displayed for printing.  Click &apos;CLOSE&apos; to close the window when printing has been completed</p>
<p>Before printing labels, use Chrome&apos;s print function (File -> Print) options to set the <b>top margin of to 0.6 inch and all other print margins to 0 (zero)</b>.</p>
<p>Try printing a test page on plain paper first.  Hold it up to the light behind a sheet of labels to make sure the printed labels line up with the blanks lables.</p>
<form class="form" target="_blank" name="labform" action="remprintlabels.php">
Number of labels to skip on 1st page (max. 29):
<input type="text" name="labelstoskip" value="0" size="2" maxlength="2" /><br />
<input type="submit" value="Submit" name="submit">
</form>
</td>
<td>
<h4>Print Form Letters</h4>
<p>This facility allows the creation of a formatted letter printer ready for letterhead or regular paper.</p>
<p>Clicking &apos;SUBMIT&apos; will open a new window with the all the letters displayed.  Click &apos;CLOSE&apos; to close the window when printing has been completed</p>
<p>Before printing the letters, use your browser&apos;s print preview (File -> Print Preview) options to set the top print margin of to 0.6 inch and all other print margins to 0 (zero).</p>
<p>Try printing a test letter page on plain paper first.  Hold it up to the light behind a sheet of letterhead to make sure the printed letters lines up with the margins.</p>
<form target="_blank" class="form" name="letterform" action="remprintletters.php">
<input type="checkbox" name="header" value="checked" />Include letterhead image in output? <br />
<input type="submit" value="Submit" name="submit">
</form>
</td>
</tr>
</table>

pagePart3b;
	}

print <<<pagePart2
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div></body></html>
pagePart2;
	exit;
	}

// listing all items in labels and letters
$sql = 'select * from labelsandletters';
$res = doSQLsubmitted($sql);
$nbrrows = $res->num_rows;
print <<<formPart1
<div class="container">
<h3>Items in Labels and Letters list:</h3>
<script>
function countchecks(fld) {
var counter = 0;
for (var i=0; i<fld.length; i++) {	
	if (fld[i].checked) { counter += 1; } }
var r=confirm("Confirm deletion(s) by clicking OK: " + counter);
if (r==true) { return true; }
	return false;
	}
function checkAll(chk,fld)  {
for(var i=0; i < fld.length; i++) {
	if(chk.checked ) { fld[i].checked = true; }
	else { fld[i].checked = false ; }
	} 
}
</script>
<form action="remlabelsandletters.php" method="post" onsubmit='return countchecks(document.chkform["cba[]"])' name="chkform"> 

<input type="checkbox" name="chkr" 
onchange='checkAll(document.chkform.chkr,document.chkform["cba[]"])'><b>&nbsp;&nbsp;Check/Uncheck All</b><br>
<table class="table">
<tr><th>Del</th><th>RecNo</th><th>Date</th><th>MCID</th><th>To</th></tr>
formPart1;

while ($r = $res->fetch_assoc()) {
	$recno = $r[LLID];
	echo "<tr><td width=\"5%\">
	<input type=\"checkbox\" name=\"cba[]\" value=\"$recno\"></td>
	<td width=\"5%\">$recno</td><td width=\"20%\">$r[Date]</td><td>$r[MCID]</td><td>$r[NameLabel1stline]</td></tr>";
	//echo "<pre>labelandletter item: "; print_r($r); echo "</pre>";
	}
echo "<tr><td><input type=\"submit\" name=\"clicker\" value=\"Delete Checked\"></td><tr>";
echo "</table>";
//echo "<a class=\"btn btn-primary\" href=\"remlabelsandletters.php\">CANCEL AND RETURN</a>";
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
