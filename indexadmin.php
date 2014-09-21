<!DOCTYPE html>
<html>
<head>
<title>Admin Home Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc';
include 'Incls/adminmenu.inc';


print <<<pagePart1
<div class="container">
<h3>Administrator Home Page</h3>
<p>This page provide the starting point for the administrative functions provided for MbrDB.</p>
<p>To have access to this page you must be registered with a user role of 'admin'.  Further authorization is required to use the DBJanitor.</p>
<p>Each page on the menu is individually documented to ensure each function is fully explained before being performed.</p>

</div>
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
