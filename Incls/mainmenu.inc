<?php
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
print <<<menupart1
<style>
body { padding-top: 50px; }      <!-- add padding to top of each page for fixed navbar -->
</style>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<!-- <div class="container"> -->
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">

    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li><a onclick="return chkchg()" href="index.php"><b>Home</b></a></li>
      <li><a onclick="return chkchg()" href="mbraddition.php">Add New Mbr</a></li>
      <li><a onclick="return chkchg()" href="mbrinfotabbed.php">Mbr Info</a></li>
menupart1;

// include EDI and Solicit menu options for special users
if (($_SESSION['SecLevel'] == "devuser") OR ($_SESSION['SecLevel'] == "admin")) {
print<<<menupart2
<script>
function confirmAdd() {
	var r=confirm("This action will add a new EDI Record for Active MCID.\\n\\nConfirm by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
function confirmDelete() {
	var r=confirm("This action will DELETE the EDI Record and all associated photos for the Active MCID.\\n\\nNOTE: THIS ACTION CAN NOT BE REVERSED!!\\n\\nConfirm by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
</script>
<script>
function adminchk() {
	//alert("checking admin password");
	var r=prompt("Enter the Admin Password.");	
		if (r == "butterfly") { return true; }
		return false;
	}
</script>

<!-- Menu dropdown for Extended Donor Info pages -->	
  <li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">EDInfo<b class="caret"></b></a>
  	<ul class="dropdown-menu" aria-labelledby="drop2" role="menu">
  	  <!-- <li><a href="mbrinfotabbed.php">Active MCID Info</a></li> -->
  		<li><a onclick="return chkchg()" href="ediaddupdate.php">EDI for Active MCID</a></li>
  		<li><a onclick="return confirmAdd()" href="ediaddupdate.php?action=addnew">Add EDI for Active MCID</a></li>
  		<li><a onclick="return confirmDelete()" href="ediaddupdate.php?action=delete">Delete EDI for Active MCID</a></li>
  		<li><a onclick="return chkchg()" href="edilistall.php">List All MCIDs with EDI</a></li>
  	</ul>   <!-- ul dropdown-menu -->
  </li>  <!-- li dropdown -->

<!-- Menu for Solictation Functions pages -->  
  <li><a onclick="return chkchg()" href="devscripts.php" target='_blank'>Solict</a></li>
menupart2;
}
/*
<!-- Menu dropdown for Solictation Functions pages -->	
  <li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Solicit<b class="caret"></b></a>
  	<ul class="dropdown-menu" aria-labelledby="drop2" role="menu">
  		<li><a onclick="return chkchg()" href="devscripts.php" target="_blank">Calling Scripts</a></li>
  		<li><a onclick="return chkchg()" href="devcreditcardpayment.php">Credit Card Payment</a></li>
  		<li><a onclick="return chkchg()" href="devpaypalpayment.php">PayPal Payment</a></li>
  		<li><a onclick="return chkchg()" href="devpaymentconfirmation.php">Payment Confirmation</a></li>
  		<!-- <li><a onclick="return chkchg()" href="#">Mark MCID as Paid</a></li> -->
  	</ul>   <!-- ul dropdown-menu -->
  </li>  <!-- li dropdown --> 
*/
print <<<menupart3
  <!-- <li><a href="mbrdonations.php" onclick="return chkchg()">Funding</a></li> -->
  <!-- <li><a href="mbrcorrespondence.php" onclick="return chkchg()">Corr.</a></li> -->

<!-- <li class="dropdown open">  example: to have open on load -->
<li class="dropdown">
<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Reminders<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<!-- <li><a onclick="return chkchg()" href="remduesowed.php">Display Expired</a></li> -->
	<li><a  onclick="return chkchg()" href="remmultiduesnotices.php">Display Expired</a></li>
	<!-- <li><a onclick="return chkchg()" href="remlists.php">List In-Progress Reminders</a></li> -->
	<!-- <li><a onclick="return chkchg()" href="rememailnotice.php">Send Email Notice to MCID</a></li> -->
	<!-- <li><a onclick="return chkchg()" href="remnotice.php">Send Letter Notice to MCID</a></li> -->
	<li><a onclick="return chkchg()" href="remlabelsandletters.php">Print Labels and Letters</a></li>
	<li><a href="reminprogressreminderlist.php" target="_blank">In-Progress Reminders</a></li>
	<li><a onclick="return chkchg()" href="remindersexplained.php">Reminders Explained</a></li>
	<!-- <li><a href="#">?</a></li> -->
</ul>
</li>  <!-- class="dropdown" -->
<!-- <li class="dropdown open">  example: to have open on load -->
<li class="dropdown">
<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Reports<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<!-- <li><a href="rptFundingPaid.php" target="_blank">Funding Paid Report</a></li> -->
	<li><a href="rptFundingPaidbytype.php" target="_blank">Funding Paid Report by Mbr Type</a></li>
	<!-- <li><a href="rptprintlabels.php" target="_blank">Print Labels on Criteria</a></li> -->
	<li><a href="rptprintlabelsbytype.php" target="_blank">Print Labels on Criteria by Mbr Type</a></li>
	<li><a href="rptmembersummary.php" target="_blank">Membership Drill Down</a></li>
	<li><a href="rptfundingdrilldown.php" target="_blank">Funding Drill Down</a></li>
	<li><a href="rptnewsupporters.php" target="_blank">New Supporters by Date Range</a></li>
	<li><a href="rptmemberexceptions.php" target="_blank">Membership Exception Report</a></li>
	<li><a href="rptsubscribers.php" target="_blank">Subscribing Members Report</a></li>
	<li><a href="rptcorrdrilldown.php" target="_blank">Correspondence Drill Down</a></li>
	<li><a href="rpttransactionlog.php" target="_blank">Transaction Log Report</a></li>
	<li><a href="rptinactiveMCIDs.php" target="_blank">Inactive MCID Report</a></li>
	<li><a href="rptmaillogviewer.php" target="_blank">Mail Log Viewer</a></li>	
	<li><a href="rptlybunty.php" target="_blank">LYBUNTY Report</a></li>
	<li><a href="rptsybunty.php" target="_blank">SYBUNTY Report</a></li>
	<li><a href="rptmonthlyreport.php" target="_blank">Monthly Report</a></li>
	<li><a href="rptfollowups.php" target="_blank">Follow Up Forms</a></li>
	<li><a href="#myModal" data-toggle="modal" data-keyboard="true">About MbrDB</a></li>
</ul>
</li>  <!-- class="dropdown" -->
<script>
function setupmcid(theForm)  {
	var fld = theForm.filter.value;
	if (fld == "--none--") { theForm.filter.value = ""; return; }
	//alert("Filter value:" + fld);
	fld = theForm.filter.value = fld.toUpperCase();  // format MCID to upper case
	if (fld.length == 5)  {
		theForm.action = "mbrinfotabbed.php";		// assume an exact MCID entered
		return true;
		}
	//if (fld == "") { theForm.filter.value = "--none--"; } 
	if (fld == "") { theForm.action = "mbrsearchlist.php"; } // else search for it
	return true;
	}

<!-- Form change variable must be global -->
var chgFlag = 0;
function chkchg() {
	if (chgFlag <= 0) { return true; }
	var r=confirm("All changes made (" + chgFlag + ") will be LOST.\\n\\nConfirm by clicking OK.");	
	if (r == true) { chgFlag = 0; return true; }
		return false;
	}

function flagChange() {
	chgFlag += 1;
	return true;
	}

<!-- ignore any change to the filter input field -->
function ignorefilter() {
	chgFlag -= 1;
	return true;
	}
	
</script>
<!-- lookup input field -->
<form name="filter" action="mbrfilterlist.php" method="post" class="navbar-form pull-left" onsubmit="return setupmcid(this)">&nbsp;&nbsp;&nbsp;
  <input autofocus autocomplete="off" type="text" class="form-control" style="width: 100px;" value="$filter" name="filter" onChange="ignorefilter()" placeholder="MCID">
  <input type="submit" name="submit" value="Lookup" class="btn btn-default" onClick="return chkchg()">
</form>
menupart3;

print <<<theRest
</ul>		<!-- nav navbar-nav  *the menu bar* -->
</div>  <!--/.nav-collapse -->
<!-- </div>  container -->
</nav>  <!-- class = "navbar" -->
<!-- End mainmenu.inc -->

theRest;

print <<<theModal
 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h4 class="modal-title" id="myModalLabel">About MbrDB</h4>
</div>  <!-- modal header -->
<div class="modal-body">
<p>Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</P
<p>MbrDB is a membership management system designed for use by non-profit organizations needing a database solution to organize and optimize their supporter community.</p>
<p>This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.</p>
<p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.</p>
<p>A copy of this license is available at: <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/</a>.</p>
<p><b>Documentation</b><br /><ul>
<a href="docs/MbrDB_Release_Info.html" target="_blank">Version Information</a><br /><br />
<a href="docs/MbrDB%20Documentation.pdf" target="_blank">User Documentation</a><br />
<a href="docs/MbrDB%20Admin%20Documentation.pdf" target="_blank">Administrator Guide</a><br />
<a href="docs/DataDictionary.pdf" target="_blank">Data Dictionary</a>
</ul></p>
</div>  <!-- modal body -->
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>  <!-- modal-footer -->
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end of modal -->
theModal;
?>