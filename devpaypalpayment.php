<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>PayPal Payments</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : '';

if ($mcid == "") {
print <<<pagePart1
<div class="container">
<h3>PayPal Payments</h3>
<p>This page displays the interface provided to allow entry of information to allow and payment or donation from a member over the phone via PayPal.  This is in the instance when they are willing to provide the credit card information necessary.  Successful entry of the transaction will provide a response receipt that can then be sent to the member&apos;s email address (if one is on the membership record.)</p>

<h4>CAUTION</h4>
<ul>
<li>Credit card information should be considered as sensitive and never recorded or written down.</li>
<li>The member should ALWAYS be offered the option of:</li>
<ul><li>being sent an email with the web site link and entering the payment information themselves.-OR-</li>
<li>being sent a regular mail letter with a return remittance envelope.</li></ul>
</ul>
</div>  <!-- container -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>
pagePart1;
exit;
}
?>
<div class="container">
<h4>Display of PayPal credit card interface and data entry page</h4>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
