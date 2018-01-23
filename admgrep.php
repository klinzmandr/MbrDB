<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Keyword Search</title>
<script src="jquery.js" type="text/javascript"></script>
</head>
<body>

<h3>USAGE</h3>
<p>Utility to search all php files in the current dir and its sub-direcotries.  Search string is a REGEX string used in a LINUX grep command.</p>

<?php
$str = isset($_REQUEST['str']) ? $_REQUEST['str'] : '';
$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';

$dirlist = `find . -type d -print`;
$dirlistarray = array();
$dirlistarray = preg_split("/\\n/",$dirlist,"-1",1);

//echo '<pre>dirlist '; print_r($dirlist); echo '</pre>';
//echo '<pre>dirs '; print_r($dirlistarray); echo '</pre>';

echo '
<script>
$(document).ready(function() {
  val d = "'.$dir.'";
  $("#dir").val("d");
});
</script>';
echo "<b>Current Dir: " . getcwd() . '</b><br>';
echo '
<form action="admgrep.php" method="post">
<select id="dir" name="dir">';

foreach ($dirlistarray as $d) {
  if (preg_match("/boot|nic/i",$d)) continue;
  if ($d == '.') { echo "<option value=\".\">(Current Dir)</option>"; }
  else { echo "<option value=\"$d\">$d</option>"; }
  }
echo '
</select><br>
Search String: <input type="text" name="str" value="'.$str.'">
<input type="submit" name="cont" value="Apply">
</form>';

echo '<br><a href="admgrep.php"><b>RESET AND RESTART</b></a><br><br>';
//$cmd = "find $dir -maxdepth 0 -type f -exec grep -n -H $str {} \; -print";
$cmd = "grep -i -n -H \"$str\" $dir/*.php";
//$cmd = 'find . -type d -exec ls {} \; -print';
echo "cmd: $cmd<br>";
$results   = `$cmd`;
$results   = htmlentities($results);

$currdir = getcwd() . '/';
echo "currdir: $dir<br><br>";
echo 'RESULTS <pre>'; print_r($results); echo '</pre>
<h3 style="color: red; ">Completion successful.</h3>';

?>
</body>
</html>