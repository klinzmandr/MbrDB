<?php
print <<<menubar
<script>
<!-- Form change variable must be global -->
var chgFlag = 0;
function chkchg() {
	if (chgFlag <= 0) { return true; }
	var r=confirm("All changes made (" + chgFlag + ") will be lost.\\n\\nConfirm by clicking OK.");	
	if (r == true) { chgFlag = 0; return true; }
		return false;
	}

function flagChange() {
	chgFlag += 1;
	return true;
	}
</script>
<script>
function adminchk() {
	//alert("checking admin password");
	var r=prompt("Enter the DB Admin Password.");	
		if (r == "butterfly") { return true; }
		return false;
	}
</script>
<!-- Admin menu start -->
<nav class="navbar navbar-default" role="navigation">
<!-- <nav class="nav nav-tabs" role="navigation"> -->
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-2">
						<span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
</div>  <!-- navbar-header -->
<div class="collapse navbar-collapse" id="navbar-collapse-2">

<ul class="nav navbar-nav">
<!-- Menu -->
	<li><a href="index.php">Home Page</a></li>
	<li><a href="admaddnewuser.php">Admin Users</a></li>
	<li><a href="admcorrbulkloader.php">CorrBulkUpd</a></li>
<!-- Maintain Lists dropdown -->
  <li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Maint. Lists<b class="caret"></b></a>
  	<ul class="dropdown-menu" aria-labelledby="drop2" role="menu">      
			<!-- <li><a href="index.php" onclick="return chkchg()">Return</a></li> -->
		  <li><a href="admlistmaint.php?file=MCTypes" onclick="return chkchg()">Mbr Types</a></li>
  		<li><a href="admlistmaint.php?file=Purposes" onclick="return chkchg()">Funding Purposes</a></li>
  		<li><a href="admlistmaint.php?file=Programs" onclick="return chkchg()">Fund Programs</a></li>	
  		<li><a href="admlistmaint.php?file=Campaigns" onclick="return chkchg()">Fund Campaigns</a></li>
			<li><a href="admlistmaint.php?file=CorrTypes" onclick="return chkchg()">Corr Types</a></li>
			<li><a href="admlistmaint.php?file=Locs" onclick="return chkchg()">Locations</a></li>
			<li><a href="admemaillists.php" onclick="return chkchg()">Vol Email Lists</a></li>
			<li><a href="admvolcategories.php" onclick="return chkchg()">Vol Categories</a></li>
		</ul>  <!-- dropdown-menu -->
	</li>  <!-- dropdown -->  
  <li><a href="admtemplatemaint.php">Reminder Templates</a></li>
<!-- Export dropdown -->
  <li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Export<b class="caret"></b></a>
  	<ul class="dropdown-menu" aria-labelledby="drop2" role="menu">      
			<li><a href="admmbrcsvexport.php" onclick="return chkchg()">Export Members</a></li>
		  <li><a href="admdoncsvexport.php" onclick="return chkchg()">Export Funding</a></li>
		  <li><a href="admcorrcsvexport.php" onclick="return chkchg()">Export Correspondence</a></li>
  	</ul>  <!-- dropdown-menu -->
  </li>  <!-- dropdown -->

<!-- Report dropdown -->
  <li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Reports<b class="caret"></b></a>
  	<ul class="dropdown-menu" aria-labelledby="drop2" role="menu">      
	  <li><a href="admlogviewer.php" target="_blank">Database Activity Log</a></li>
		<li><a href="rptnewsubscribers.php" target="_blank">New Subscribing Members Report</a></li>
		<li><a href="rptcorrfollowup.php" target="_blank">Correspondence Followup Report</a></li>
		<li><a href="maillogchecker.php" target="_blank">Server Log Viewer</a></li>
	<li><a href="rptfundingdrilldown.php" target="_blank">Funding Drill Down Report</a></li>
	<li><a href="rptdonortopten.php" target="_blank">Donor Top Ten Report</a></li>
	<li><a href="rptmemberstatus.php" target="_blank">List All Members by Status</a></li>
	<li><a href="rpttransactionsummary.php" target="_blank">Transactions Summary Report</a></li>
		<li><a href="rptdbsummary.php" target="_blank">DB Summary Report</a></li>
  	</ul>  <!-- dropdown-menu -->
  </li>  <!-- dropdown -->

  <li><a href="admDBJanitor.php" target="_blank" onclick="return adminchk()">DB Janitor</a></li>
  <!-- <li><a href="#" onclick="return chkchg()">?</a></li> -->

</ul>  <!-- nav navbar-nav -->
</div>  <!--collapse nav-collapse -->
</nav>  <!-- class = "navbar" -->

menubar;
?>