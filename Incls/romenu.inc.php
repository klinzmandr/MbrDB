<?php
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
print <<<menu

<nav class="navbar navbar-default" role="navigation">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
</div>  <!-- navbar-header -->

<div class="collapse navbar-collapse" id="navbar-collapse">		<!-- menu bar for readonly menu -->
<div class="container">
<ul class="nav navbar-nav">
<!-- Menu -->
<ul class="nav navbar-nav">
	<li><a href="index.php" onclick="return chkchg()">Home</a></li>
  <li><a href="mbrsearchlistro.php" onclick="return chkchg()">Member Search</a></li>
  <!-- <li><a href="mbrdonations.php" onclick="return chkchg()">Funding</a></li> -->
  <!-- <li><a href="mbrcorrespondence.php" onclick="return chkchg()">Correspondence</a></li> -->
</ul>

<!-- <li class="dropdown open">  example: to have open on load -->
<li class="dropdown">
<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Reports<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<!-- <li><a href="rptprintmcid.php" target="_blank">Print Active MCID Info</a></li> -->
	<li><a href="rptmemberstatus.php" target="_blank">List All Members by Status</a></li>
	<li><a href="rptFundingPaid.php" target="_blank">MCID Funding</a></li>
	<li><a href="rptdonortopten.php" target="_blank">Donor Top Ten</a></li>
	<li><a href="rptdbsummary.php" target="_blank">Summary Report</a></li>
</ul>
</li>  <!-- class="dropdown" -->
<script>
function setupmcid(theForm)  {
	var fld = theForm.filter.value;
	if (fld == "--none--") { theForm.filter.value = ""; return;}
	// alert("Filter value:" + fld);
	theForm.filter.value = fld.toUpperCase();
	if (fld == "") { theForm.filter.value = "--none--"; return; }
	if (fld.length == 5)  {
		theForm.action = "rptprintmcid.php";		// assume an exact MCID entered
		return true;
		}
	if (fld == "") { theForm.filter.value = ""; } // else search for it
	return true;
	}
<!-- Form change variable must be global -->
var chgFlag = 0;
function chkchg() {
	if (chgFlag == 0) { return true; }
	var r=confirm("All changes made will be lost.\\n\\nConfirm by clicking OK. (" + chgFlag + ")");	
	if (r == true) { chgFlag = 0; return true; }
	return false;
	}
</script>
<form name="filter" action="rptprintmcid.php" target="_blank" method="post" class="navbar-form pull-left" onsubmit="return setupmcid(this)">&nbsp;&nbsp;&nbsp;
  <input autofocus type="text" class="form-control" style="width: 100px;" value="$filter" name="filter" placeholder="MCID">
  <input type="submit" name="submit" value="Lookup" class="btn btn-default" onclick="return chkchg()"/>
</form>
</ul>
</div>  <!--/.nav-collapse -->
</div>  <!-- container -->
</div>  <!-- class = "navbar" -->
</nav>

menu;
?>