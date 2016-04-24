<!DOCTYPE html>
<html>
<head>
<title>Payment Confirmation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : '';

if ($mcid == "") {
print <<<pagePart1
<div class="container">
<h3>Payment Confirmation</h3>
<p>This page will provide the initation of an immediate email sent to the member and appropriate entries made into the &apos;Correspondence&apos; and &apos;Funding&apos; logs when the payment confirmation has been received.</p>  
<p>An entry will is only made into &apos;Correspondence Log&apos; in the case that the member has chosen to provide a funding payment either by using a web payment or payment by regular mail.  It is expected that an entry to the &apos;Funding&apos; log would be done on actual receipt of the funds. </p>
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
<h4>Display of payment confirmation entry forms.</h4>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
