<!DOCTYPE html>
<html>
<head>
<title>New Supporters Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST[action]) ? $_REQUEST[action] : '';
$sd = isset($_REQUEST[sd]) ? $_REQUEST[sd] : date('Y-m-01', strtotime("previous month -2 months"));
$ed = isset($_REQUEST[ed]) ? $_REQUEST[ed] : date('Y-m-t', strtotime("previous month"));

if (($sd == "") OR ($ed == "")) $action = '';

// set up intro page	
print <<<pagePart1
<div class="container">
<h3>New Supporters Report&nbsp;&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
<p>This report is a listing of new members, volunteers or donors that have <b>joined</b> within the following specific date range (default is 3 months).</p>
<p>Listed members are selected by comparing the &apos;Date Joined&apos; of each member record to the specified date range.  If the &apos;Date Joined&apos; is within the specified date range, it is included in this listing.</p>
<p>The &apos;Date Joined&apos; is set on introduction of the member into the database.  It can not be changed once established.</p>

<form action="rptnewsupporters.php" method="post">
Start Date:
<input type="text" name="sd" id="sd" value="$sd">
End Date:<input type="text" name="ed" id="ed" value="$ed" >
<input type="hidden" name="action" value="continue">
<input type="submit" name="submit" value="Submit">
</form>
<!-- <a class="btn btn-primary" href="rptnewsupporters.php?action=continue">Continue</a> -->

pagePart1;

if ($action == 'continue') {
	$sql = "SELECT * 
		FROM `members` 
		WHERE `MemDate` BETWEEN '$sd' AND '$ed' 
 			AND `Inactive` = 'FALSE' 
		ORDER BY `MCID`;";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	if ($rowcnt == 0) {
		echo '<h3>No new supporters found in date range provivded</h3>';
		print<<<endPage
		</div>  <!-- container -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body></html>

endPage;
		exit;
		}
	//echo "SQL: $sql<br />";
	echo "<h5>New supporters in range: $rowcnt</h5>";
	echo "<a href=\"downloads/Supporters.csv\" download=\"supporters.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Downloaded file contains more fields than shown\nFields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
$fa = array();		// array to capture csv file for download
// dump in csv format
	$hdr = 'MCID;MemStatus;MemDate;MCType;Lname;Fname;LabelLine;Org;Address;City;St;Zip;';
	$hdr .= 'Phone;Email;Notes;LastDonDate;LastDonPurpose;LastDonAmt;LastDuesDate;LastDuesAmt;';
	$hdr .= "LastCorrDate;LastCorrType\n";
	$fa[] =  $hdr;
	$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
	while ($r = $res->fetch_assoc()) {
		unset($r[MbrID]);
		unset($r[TimeStamp]);
		unset($r[Source]);
		unset($r[Account]);
		unset($r[E_Mail]);
		unset($r[PaidMemberYear]);
		unset($r[MasterMemberID]);
		unset($r[CorrSal]);
		unset($r[Lists]);
		unset($r[Member]);
		unset($r[Inactive]);
		unset($r[Inactivedate]);
		unset($r[Mail]);
		$r[MCID] = "\"$r[MCID]\"";
		$r[Notes] = strtr($r[Notes], $translate);
		$r[Notes] = "\"$r[Notes]\"";
		$l = implode(';',$r);
		$fa[] = "$l\n";
		//echo '<pre>supporter '; print_r($r); echo '</pre>';
		}
	file_put_contents('downloads/Supporters.csv',$fa);

mysqli_data_seek($res, 0);		// reset results to read from top
// create table report
echo '<table class="table">
	<tr><th>MCID</th><th>MemStatus</th><th>MC Type</th><th>DateJoined</th><th>LastDues</th><th>LastDon</th><th>LabelLine1</th><th>Email Address</th><th>Phone</th><th>Notes</th>';
while ($r = $res->fetch_assoc()) {
		$r[Notes] = strtr($r[Notes], $translate);
		echo "<tr><td>$r[MCID]</td><td>$r[MemStatus]</td><td>$r[MCtype]</td><td>$r[MemDate]</td><td>$r[LastDuesAmount]</td><td>$r[LastDonAmount]</td><td>$r[NameLabel1stline]</td><td>$r[EmailAddress]</td><td>$r[PrimaryPhone]</td><td>$r[Notes]</td></tr>";
		//echo '<pre> New member '; print_r($r); echo '</pre>';
		}
	echo '</table>----- End of Report -----';		
	}

?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
