<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>EDI DB Photo Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onChange="flagChange(); setUpd();">
<script>
function setUpd() {
	document.getElementById("hdr3").style.color="Red";
	}
</script>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : "";
$field = isset($_REQUEST['field']) ? $_REQUEST['field'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$notes = isset($_REQUEST['Notes']) ? $_REQUEST['Notes'] : "";
$title = isset($_REQUEST['Title']) ? $_REQUEST['Title'] : "";

$filename = $mcid . '-' . date('ymdHis', strtotime("now"));
// echo "filename: $filename<br>";

// upload and save to db
echo "<div class=\"container\">";
if ($action == "APPLY") {
// add new photo to the database
	if (strlen($_FILES['file']['name']) == 0) { 
		echo "No name specified.  Upload failed!<br>";
		}
	if ($_FILES["file"]["size"] == 0) {
		echo "File error. Empty file, nothing uploaded<br>";
		$_FILES["file"]["error"] = 99;
		}
//	echo "store and process uploaded file<br>";
	if ($_FILES["file"]["size"] < 3000000)  {
  	if ($_FILES["file"]["error"] > 0) {
  		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
  		}
  	else	{					// confirm upload info
//   		echo "Uploaded: " . $_FILES["file"]["name"] . "<br />";
//   		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br /><br>";
			$ext = pathinfo($_FILES["file"]["name"],  PATHINFO_EXTENSION);
   		move_uploaded_file($_FILES["file"]["tmp_name"], "../mbrdbphotos/" . $filename . '.' . $ext);
			}
		}
		else {
			echo "File upload file exceeds maximum of 3 MB in size - upload failed!<br>";
			}  // end of upload processing
			
//	$ext = $extparts['extension'];
	$flds = array();
	$flds[MCID] = $mcid;
	$flds[Title] = $title;
	$flds[Notes] = $notes;
	$flds[PathInfo] = "../mbrdbphotos/" . $filename . '.' . $ext;

	if ($_FILES["file"]["size"] > 0) {
		
		echo "<p style=\"color: red; \"><br><b>New photo added for MCID: $mcid</b></p>";
		}
		
//	echo '<pre> insert flds '; print_r($flds); echo '</pre>';
	
	sqlinsert('photos', $flds);
	
	}
echo "</div>";

// create input form for upload info
print<<<scriptBody
<script>
function loadProgress() {
	// alert("loadProgress entered");	
	// ProgressImage = document.getElementById('progress_image');
	//document.getElementById("progress").style.visibility = "visible";
	// setTimeout("ProgressImage.src = ProgressImage.src",100);
	return true;
    } 
</script>
<script>
function checktitle() {
	var ll = document.getElementById("PT").value.length;
	if (ll == 0) {
		alert("Photo title must be supplied");
		return false;
		}
	document.getElementById("progress").style.visibility = "visible";
	return true;
	}	
</script>
</head>
scriptBody;

if ($field == 'photos') $field = 'Pictures and Documents';
print <<<formPage
<div class="container">
<h3 id="hdr3">EDI Photo/Document Update</h3>
<h4>MCID: $mcid</h4>
<h4>Extra Donor Info Section: $field</h4>
<div class="well">
<form action="edidbphotoupd.php?action=APPLY" onsubmit="return checktitle()" method="post" enctype="multipart/form-data">
Picture/Document Title: <input id="PT" type="text" size=80 name=Title value=""><br>
Additional Note: <input type="text" size=80 name=Notes value=""><br>
<label for="file">Filename to upload:</label>
<input size=25 type="file" name="file" id="file" />
<input type="submit" name="submit" value="Upload" onclick="loadProgress()"><br>
<img src="config/verifying01.gif" height="30" alt="ProgressBar" id="progress" style="visibility: hidden; " /><br />
</form>
<br />
<a class="btn btn-primary" href="ediaddupdate.php" onclick="return chkchg()">RETURN</a>
</div>  <!-- container -->
formPage;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
