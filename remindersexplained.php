<html>
<head>
<title>Reminders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="Incls/letter_print_css.inc" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();

include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';

?>
<a name="top"></a><div class="container">
<h2>Reminders Explained&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="remmultiduesnotices.php">RETURN</a></h2>
<a href="#workflow">Recommended Workfow</a>
<h3>Overview</h3>
<p>Reminders and notifications are utilized to maintain the enrolled membership.  Membership provides an essential source of funding to support the ongoing fulfilment of the organization's mission. This function is provided to allow members to be advised when their annual membership is due.</p>
<p>Reminders revolve around the expired membership list that is created from the Reminders menu. The expired list is comprised of 'active' member records which show that no payment marked as "DUES"(or a 'Donation' in the case of a 'Donor) has been entered within the last 11 months. This would indicate that renewal reminders may need to be initiated.</p><p>It should be noted that an 'active' member, volunteer or donor is a record with a member status of '1-Member', '2-Volunteer' or '3-Donor' with the 'Mbr Inactive' flag as 'NO'.</p>
<p>The reminder process begins with listing all those active members for whom dues payments are delinquent (e.g. nothing paid in the last 11 months).  This listing is produced by using the 'Reminders &#8594; Display Expired' menu item. The listing produced shows all member records by default.  Volunteer and Donor records are list when that option is selected from the report page drop down selection. </p>
<p>The listing has several columns one of which is the MCID which is a link to the member information page. Other columns contain check boxes under the headings of 'Email' and'Mail' with submit buttons under their respective columns at the end of the listing.  These check boxes are 'active' or 'disabled' depending on whether or not the member's information indicates 'EmailOK?' and/or 'Mail OK?' options are enabled.  If a member has indicated that they do not wish to receive email from PWC, for example, then the 'Email OK? flag is set to 'NO' and the check box on the reminder listing will be disabled.  The same applies to the 'MailOK?' flag.</p>
<p>Individual members are selected based on the need to send a specific reminder message to one or more in the listing.  The message is chosen from list of message templates created specifically for this purpose.  Email messages are sent immediately and mail messages are queued for printing.  In either case, a notation is entered into the individual members 'correspondence' log that this communication has taken place.</p>
<p>Maintenance of the message templates is an administrative function reserved for those with the authority to develop them.</p>
<p>All MCID's with in-progress "renewal reminders" can be listed to provide the ability to manage membership enrolment using the 'Reports &#8594; List In-progress Reminders' report. Reminders are listed for an individual MCIDs from their correspondence logs along with the current count and date of the last reminder notice sent and are included regardless of when the reminder was sent.</p>
<p>A member's MCID is no longer included in the "In-Progress Reminder" list when any 1 of 3 things happens: </p>
<ul>
<li>a payment is submitted which is entered as a <b>new</b> Funding record as "DUES",</li>
<li>the MCID has been re-designated to a non-Member status (Member Status of 0), or</li>
<li>the MCID has been designated as"Inactive" (Inactive flag = "YES").</li></ul>
<p>Once a reminder message has been initiated to one or more members, they will no longer appear on the 'Expired' listing for a period of 10 days.  This provides, hopefully,enough time for the member to respond.  After 10 days, failing any further update, the member will again be listed on the expired report with along with the number of reminders that have been sent and the date of the last one.</p>
<p>A final check box column labelled 'Inactive?' is included and is active for all member's to whom reminders have been sent.  This check box is to provide the ability to set one or more of these members as 'Inactive' using today's date. This option is used when the 'final' notice has been sent to the member and it is determined that it is appropriate to drop the member from the database entirely.  By checking one or more check boxes in the 'Inactive?' column and clicking the corresponding button at the bottom of the report will result in all those member records being marked as 'Inactive' using today's date.</p>
<p>Setting a member 'Inactive' does not,in and of itself, drop the members record.  The date that the 'Inactive' status has been set is used to determine a time period of 90 days, after which the DB Janitor process will permanently delete those 'expired' ember records and all their associated funding,correspondence, and volunteer time entries (if any.)  Any payment or donation made during that 90 day period will automatically set the member record to 'Inactive: NO' thus making the record 'active' once again.</p>
</p>
<br />
<a name=workflow></a>
<div class="page-break"></div>
<H4>Recommended Reminders Work Flow</H4>
<P>To maximize the time required to maintain expired memberships the
following steps are recommended:</P>
<OL>
	<LI>Select 'Reminders -> Print Labels and Letters' and, if
	present, click the 'List and Delete Items' button to delete all
	existing entires in the labels and letters print queue.</li>
	<LI>Select 'Reminders -> Display Expired' to list all expired
	members.</li>
	<LI>Review the resulting list to make sure that either or both of
	the check boxes for Email or Mail are available. Update the member
	record(s) by clicking the MCID link to correct the email and/or
	mailing information to allow reminders to be sent. Mark the member
	record inactive if there is no obvious method (i.e. no mailing
	address information or email address) to communicate with them.</li>
	<LI>Review the expired listing and check off all records that are
	candidates for marking as 'Inactive' and click the 'Make Inactive'
	button at the bottom of the column. </li>
	<LI>Review the resulting listing and check all those that are
	candidates for sending an email reminder number one (usually those
	without any prior reminders.) Click the 'Send Email' button at the
	bottom of the column, select the appropriate email template and
	complete the sending process. A copy of the email and a list of all
	those is was sent to is available for review using 'Reports -> Mail
	Log Viewer'. Also, each members correspondence log is updated with
	the date, time and subject line of the email notice that was sent.</li>
	<LI>Perform the previous step for each of the other for email
	reminders number 2 and Final using the appropriate email templates
	for each.</li>
	<LI>Those remaining on the expired list should now (usually) be
	those that are to be mailed either a postcard or a reminder form
	letter. </li>
	<LI>Check off all those to be sent a post card (usually those
	without any prior reminder) and click the 'Send Mail' button at the
	bottom of the column. </li>
	<LI>Select the 'Reminders -> Print Labels and Letters' menu item
	and print the labels just produced. Enter the number of 'blank'
	labels for a partially used first page and print these labels.
	Delete the printed labels when finished. Each members correspondence
	log is updated with the date, time and note that the notice was
	printed.</li>
	<LI>Select 'Reminders -> Display Expired' to list the remaining
	records. Check off all those that need to be send the reminder form
	letter (hopefully all those that remain) and click the 'Send Mail'
	button at the bottom of the column and select the appropriate
	template for the form letter.</li>
	<LI>Select the 'Reminders -> Print Labels and Letters' menu item
	to list all the queued items to be printed. Select the ''Include
	letterhead image in output' if the logo is to be printed.</li>
	<LI>Finally, delete the items just printed from the 'Labels and
	Letters' print queue.</li>
</OL>
<P>This process should be done periodically - usually at least
weekly - to keep the database up to date. The same process should
be applied to volunteers and donors as well.</P>
<P>Here are some import time periods to remember:</P>
<OL>
	<LI>Expiration of Inactive members for deletion from the
	database: 90 days.</li>
	<LI>Expiration of Active membership period: 11 months</li>
	<LI>Display of member on expired listing: 10 days</li>
</OL>
<P>If a member has been marked as 'Inactive' through the reminders
process, they will automatically become 'Active' again if any
donation or dues payment is received prior to their deletion
deadline.</P>

<a href=#top>TOP</a>
<h4></h4>
<!-- <a class="btn btn-large btn-primary" href="index.php">HOME</a> -->
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
