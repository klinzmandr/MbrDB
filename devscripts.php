<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Solicition Scripts</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : '';

// following is where the definition and loading of the tab's happens - 
// use ediaddupdate.php as example of tab'ed layout
print <<<tabPage
<div class="container">
<h3>Solicitation Information    <a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#usage" data-toggle="tab">Usage</a></li>
  <li class=""><a href="#tab1" data-toggle="tab">Org Info</a></li>
  <li class=""><a href="#tab2" data-toggle="tab">Opening Comments</a></li>
  <li class=""><a href="#tab3" data-toggle="tab">Funding Options</a></li>
  <li class=""><a href="#tab4" data-toggle="tab">Payment Options</a></li>
  <li class=""><a href="#tab5" data-toggle="tab">Decline Counters</a></li>  
  <li class=""><a href="#tab6" data-toggle="tab">Last Words</a></li>
 </ul>

<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="usage">
<p>This page is to provide all available scripts developed for use by the phone soliciation person to be used during the course of a conversation with the active MCID.  Use the &apos;MCID Lookup&apos; function of the main menu to estalish an &apos;active&apos; MCID.  Contact information about the &apos;active&apos; MCID is available on the Info tab of the main menu.  Funding and Correspondence tabs provide historical funding support and member contact information.</p>
<p>Various scripts are developed, usually, by professional Fund Development staff.  Once approved, they are they loaded into the system and become available on the various tabs of this page.  Techniques on phone protocols, questions, tone of voice and other non-visual indicators will largely determine the success or failure of phone solicitation efforts.  These scripts must be developed with an understanding of these factors.</p>
<p>In general, each tab is free form in nature.  Loading of information for each tab is done after it has been developed and trial tested within the organzation.  Special system administration functions are provided to allow these scripts to be loaded when they are have been developed or updated.
<p>This page opens up as a separate tab or window.  All scripts are a part of the page downloaded so that navigation between them is very fast.  Click the appropriate tab to begin.</p>
<p>Please enter all donor funding activity on the 'Funding' tab and all contacts made on the 'Correspondence' tab.</p>
</div>
<div class="tab-pane fade" id="tab1">
<h4>Information like:</h4>
<ul>
<p>Mission Statement:</p>
<p>Organizational Goals:</p>
<p>Accomplishments:</p>
</ul>
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="tab2">
<h4>Greeting and opening comments</h4>
<ul>
<li><p>Note and thank you for past support</p>
<li><p>Noticed that the membership dues has expired.</p>
<li><p>Are you interested in continuing to support the organization?</p>
<li><p>Would it be possible to increase you membership amount to the next level?</p>
<li><p>Can I assist in setting up a continuing, subscription membership with you?	</p>
</ul>
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="tab3">
<h4><p>Description of various ways that funding support can be provided:</p></h4>
<ul>
<li><p>Annual membership dues</p>
<li><p>Annual or periodic donations</p>
<li><p>Scheduled or subscription dues or donations</p>
<li><p>Directed donations for specific projects or use</p>
<li><p>In-kind donations for equipment, food or consumable goods on the &apos;wish&apos; list</p>
<li><p>Volunteering of time on a assignment or scheduled basis</p>
<li><p>Participating in orginaztional committees (Board, Events, Membership, Fund Raising, Volunteer, etc.</p>
</ul>
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="tab4">
<h4>Payment options include:</h4>
<ul>
<li>Immediate support by providing payment information right now.</li>
<li>On-line membership payment or donation on our web site or through PayPal</li>
<li>Setting up an automatic payment with your bank (would you like more iformation?)
<li>Sending a check or money order directly (would you like the address or have me send a self addressed return remittence envelope?)
</ul>
<h4>Immediate Credit Card and PayPal payment options</h4>
<ul>
<li>Credit card information should be considered as sensitive and never recorded or written down.</li>
<li>The member should ALWAYS be offered the option of:</li>
<ul><li>being sent an email with the web site link and entering the payment information themselves.-OR-</li>
<li>being sent a regular mail letter with a return remittance envelope.</li></ul>
</ul>
<a href="http://dev.pacwilica.org/CCard/auth.php?desc='Solicitation One-time Donation' " class="btn btn-primary" target="_blank">Credit Card One<br>Time Donation</a>&nbsp;&nbsp;
<a href="http://dev.pacwilica.org/ARBill/arbcreate.php" class="btn btn-primary" target="_blank">Credit Card Monthly<br>Recurring Donation</a>&nbsp;&nbsp;
<a href="http://dev.pacwilica.org/PPal/pp.php?enteramt&ddown" class="btn btn-primary" target="_blank">PayPal Payment<br>Options</a>
<h4>Payment Confirmation</h4>
<p>Use the mail and/or email facilities in the &apos;Summary&apos; tab of a supporter&apos;s information page to send appropriate follow up messages.</p>
<!-- <a href='#' class="btn btn-primary" target='_blank'>Send Confirmation Email</a> -->
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="tab5">
<p>Counters to declinations:</p>
<ul>
<li>Would you like to continue to receive periodic information and mailers from the organization?</li>
<li>Would it be OK for us to contact you in the future?</li>
<li>Are you interested in learning about our special educational or fund raising events?</li>
<li></li>
</ul>
</div>  <!-- tab-pane -->

<div class="tab-pane fade" id="tab6">
<h3>Final Words</h3>
<p>Suggestions on how to close the call:</p>
<ul><li>Thank you for your continued support.</li>
<li></li>
<li></li>
</div>  <!-- tab-pane -->
</div>  <!-- tab-content -->
</div>  <!-- container -->
tabPage;

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
