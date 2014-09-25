-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: pacificwildlifecare.org
-- Generation Time: Jul 31, 2014 at 08:14 PM
-- Server version: 5.1.65
-- PHP Version: 5.5.9-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mbrtestdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

DROP TABLE IF EXISTS `adminusers`;
CREATE TABLE IF NOT EXISTS `adminusers` (
  `SeqNo` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(50) CHARACTER SET utf8 NOT NULL,
  `Password` varchar(15) CHARACTER SET utf8 NOT NULL,
  `Role` varchar(30) CHARACTER SET utf8 NOT NULL,
  `Notes` varchar(150) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`SeqNo`),
  UNIQUE KEY `UserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`SeqNo`, `UserID`, `Password`, `Role`, `Notes`) VALUES
(1, 'jdoe@mbrdemo.com', 'raptor', 'user', 'user id for regular user in demo mode'),
(2, 'jdoesu@mbrdemo.com', 'raptor', 'admin', 'user id for admin user in demo mode'),
(3, 'jdoeedi@mbrdemo.com', 'raptor', 'devuser', 'userid for user PLUS EDI functions in demo mode'),
(4, 'jdoevoladmin@mbrdemo.com', 'raptor', 'voladmin', 'userid for volunteer admin in demo mode.'),
(5, 'jdoevoluser@mbrdemo.com', 'raptor', 'voluser', 'volunteer db user');

-- --------------------------------------------------------

--
-- Table structure for table `configtable`
--

DROP TABLE IF EXISTS `configtable`;
CREATE TABLE IF NOT EXISTS `configtable` (
  `CFGId` int(6) NOT NULL AUTO_INCREMENT,
  `CfgName` varchar(120) DEFAULT NULL,
  `CfgText` text,
  PRIMARY KEY (`CFGId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `configtable`
--

INSERT INTO `configtable` (`CFGId`, `CfgName`, `CfgText`) VALUES
(1, 'EmailLists', '// List of all distribution list for volunteers\r\nAdm:Office Admin List\r\nBBB:BBB Event Committee\r\nCtr:Center List\r\nMnt:Center Mentor List\r\nObl:Oiled Bird Notify List\r\nOWCN:OWCN\r\nPV:PV List\r\nPVbu:PV Backup List\r\nSat:Home Rehab List\r\nTrn:Transporter List\r\nBBR:Baby Bird Rehabers\r\nWIW:WIW Committee\r\nTst:Test Email List'),
(2, 'MCTypes', '// list of member/contact types.  \n// first number corresponds with member/contact status (mcstatus)\n// the order defined will be order displayed in the form\n// first digit MUST correspond to the related mcstatus for form authorization use\n\n0-Rescuer:0-Rescuer\n0-Proposed:0-Prposed Member\n1-IndivorFamily:1-Individual or Family\n// 1-Benefactor:1-Benefactor\n// 1-Patron:1-Patron\n// 1-Sponsor:1-Sponsor\n// 1-Contributor:1-Contributor\n// 1-Supporter:1-Supporter\n//1-Advocate:1-Advocate\n1-Sr-Student:1-Sr-Student\n1-Lifetime:1-Lifetime\n1-Subscriber:1-Subscriber\n2-Volunteer:2-Volunteer\n2-VolSubscr:2-VolSubscriber\n2-VolLifeTime:2-Vol and Lifetime Mbr\n// 2-Center:2-Center Volunteer\n// 2-Phone:2-Phone Volunteer\n// 2-Transporter:2-Transporter Volunteer\n// 2-Admin:2-Administrative Volunteer\n// 2-Board:2-Board of Directors\n3-Donor:3-Donor\n3-Affiliate:3-Affiliate\n3-Business:3-Business\n3-Company:3-Company\n3-Org:3-Organization\n3-Agency:3-Agency\n3-Vet:3-Veterinarian'),
(3, 'Programs', '// list of donation programs\r\n// programs usually identify why the donor has gifted to PWC\r\n\r\n :---Dues Programs---\r\nDues-AnnualMbr:Annual Membership\r\nDues-Subscr:Subscribing Membership\r\nDues-Other:Other Dues (please note)\r\n :---Donaton Programs---\r\nDon-UnresDonation:Unrestricted Donation\r\nDon-IMO:In Memorandum (please note)\r\nDon-IHO:In Honor Of (please note)\r\nDon-Other:Other Donation (please note)\r\n :---Directed Donation Programs---\r\nDir-DirDonFunding:Directed Donation Funding\r\nDir-Other:Other-Directed Don. (please note)\r\n :---In-kind Donation Programs---\r\nInk-Facility:In-kind (Facility)\r\nInk-Food:In-kind (Food)\r\nInk-Meds:In-kind (Meds)\r\nInk-Other:Other In-kind (please note)\r\n :---Grant Programs---\r\nGra-GrantFunding:Grant Funding\r\nGra-Other:Other Grant (please note)\r\n :---Fund Raising Programs\r\nFun-Event:Event\r\nFun-Donation:Event Donation\r\nFun-Other:Other Fund Raising (please note)\r\n :---Program Income Programs\r\nPrg-Sales:Sales (t-shirts, cat collers, etc)\r\nPrg-EducPres:Education Presentation\r\nPrg-ExclProg:Exclusion Program\r\nPrg-Deposit:Deposit (please note)\r\nPrg-Other:Other Prog. Income (please note)\r\n\r\n'),
(4, 'Purposes', '// list of giving \\"Purposes\\"\r\n// NOTE: option for Dues payment automatically added\r\n\r\nDonation:Donation\r\nDirDon:Directed Donation\r\nInKindDon:In-Kind Doination\r\nGrant:Grant\r\nFundRaising:Fund Raising\r\nPrgIncome:Program Income\r\n'),
(5, 'CorrTypes', '// list of correspondence types\r\n// usually identifies what the correspondence was about\r\n// NOTE: option value=\\\\\\"RenewalReminder\\\\\\" programatically added\r\n// NOTE: sending an MailReminder causes an auto update to corr log\r\n// NOTE: sending an EmailReminder causes an auto update to corr log\r\n\r\nRenewalTY:Renewal Thank You\r\nDonationTY:Donation Thank You\r\nNewMemberTY:New Member Thank You\r\nEmailMsg:Email Messaage\r\nFollowUp:Follow Up Note\r\n :---------Programs-------\r\nEOYTaxRcp:EOYR TaxReceipt\r\nXmasCard:Xmas Card\r\nRecepFollowUp:Reception Follow Up\r\nPVPostcard:PV Postcard\r\nTourInvite:Tour Invitation\r\nPVPostcard:PV Postcard\r\nOther:Other (please note)\r\n'),
(6, 'Campaigns', '// list of campaigns\r\n\r\n : \r\nMailedNotice:Mailed Notice\r\nEmailedNotice:EmailedNotice\r\nNewsletter:Newsletter\r\nEOYAppeal:End of Year Appeal\r\nFundOurVet:FundOurVet\r\nMedia:Radio/TV/News article\r\nT-Shirts:T-shirt Sales\r\nAnonMatch:2014 Anonymous Match\r\nOther:Other(please note)'),
(7, 'VolCategorys', '// volunteer categories\r\n\r\nCtrVol:Center Volunteer\r\nSupervisor:Supervisor\r\nBBRoom:Baby Bird Room\r\nTransporter:Rescue/Transport\r\nHomeRehab:Home Rehabilitator\r\nOfficeAdmin:Office Administration\r\nPhoneVol:Hotline Volunteer\r\nEvent:Event\r\nCommittee:Committee\r\nOther:Other (please note)\r\n\r\n'),
(8, 'Locs', '// Create the city type ahead list \n// format is   nbr:City,state,zip\n// list used to populate the typeahead for CSZ\n\n1:Arroyo Grande,CA,93420\n2:Atascadero,CA,93422\n3:Avila Beach,CA,93424\n4:Cambria,CA,93428\n5:Cayucos,CA,93430\n6:Creston,CA,93432\n7:Grover Beach,CA,93422\n8:Los Osos,CA,93402\n9:Morro Bay,CA,93442\n10:Nipomo,CA,93444\n11:Oceano,CA,93445\n12:Paso Robles,CA,93446\n13:Pismo Beach,CA,93449\n14:Santa Margarita,CA,93453\n15:San Luis Obispo,CA,93401\n16:San Luis Obispo,CA,93405\n17:San Miguel,CA,93451\n18:San Simeon,CA,93428\n19:Shandon,CA,93461\n20:Shell Beach,CA,93449\n21:Templeton,CA,93465');

-- --------------------------------------------------------

--
-- Table structure for table `correspondence`
--

DROP TABLE IF EXISTS `correspondence`;
CREATE TABLE IF NOT EXISTS `correspondence` (
  `CORID` int(6) NOT NULL AUTO_INCREMENT,
  `CorrespondenceType` varchar(25) DEFAULT NULL,
  `DateSent` date DEFAULT NULL,
  `MCID` varchar(6) DEFAULT NULL,
  `SourceofInquiry` varchar(8) DEFAULT NULL,
  `Notes` varchar(128) DEFAULT NULL,
  `Reminders` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`CORID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- Dumping data for table `correspondence`
--

INSERT INTO `correspondence` (`CORID`, `CorrespondenceType`, `DateSent`, `MCID`, `SourceofInquiry`, `Notes`, `Reminders`) VALUES
(1, 'DONATE_THANK', '2009-04-21', 'COL19', NULL, NULL, NULL),
(2, 'RENEW_THANK', '2009-07-12', 'GOV22', NULL, NULL, NULL),
(4, 'Donation_Thank_You', '2009-08-30', 'CHE10', NULL, NULL, NULL),
(5, 'reminder', '2009-08-31', 'BRO56', NULL, NULL, 'Reminder'),
(6, 'NEW_THANK', '2009-10-11', 'CHA18', NULL, NULL, NULL),
(7, 'Lapsed Mem09', '2009-10-16', 'BRO56', NULL, NULL, NULL),
(8, 'RENEW_THANK', '2009-11-05', 'BRO56', NULL, NULL, NULL),
(9, 'RENEW_THANK', '2009-11-12', 'SAL58', NULL, NULL, NULL),
(11, 'reminder', '2009-11-28', 'ZAE48', NULL, NULL, 'Reminder'),
(12, 'NEW_THANK', '2009-12-03', 'HAR14', NULL, NULL, NULL),
(13, 'EOYRTaxRcpt', '2009-12-06', 'CHE10', NULL, NULL, NULL),
(14, 'RENEW_THANK', '2009-12-13', 'ZAE48', NULL, NULL, NULL),
(15, 'DONATE_THANK', '2009-12-16', 'CHA18', NULL, NULL, NULL),
(16, 'Donation_Thank_You', '2009-12-16', 'CHE10', NULL, NULL, NULL),
(17, 'OTHER', '2009-12-16', 'ZAE48', NULL, NULL, NULL),
(18, 'reminder', '2010-01-01', 'HIL21', NULL, NULL, 'Reminder'),
(19, 'RENEW_THANK', '2010-01-07', 'BRO56', NULL, NULL, NULL),
(21, 'NEW_THANK', '2010-02-28', 'PEP21', NULL, NULL, NULL),
(23, 'RENEW_THANK', '2010-03-07', 'HIL21', NULL, NULL, NULL),
(24, 'reminder', '2010-04-06', 'COL19', NULL, NULL, 'Reminder'),
(25, 'DONATE_THANK', '2010-07-18', 'CAR42', NULL, NULL, NULL),
(26, 'reminder', '2010-07-25', 'GOV22', NULL, NULL, 'Reminder'),
(28, 'RENEW_THANK', '2010-08-08', 'GOV22', NULL, NULL, NULL),
(29, 'Tour Invite', '2010-09-13', 'CHE10', NULL, NULL, NULL),
(30, 'reminder', '2010-10-11', 'CHA18', NULL, NULL, 'Reminder'),
(33, 'RENEW_THANK', '2010-10-31', 'AHE22', NULL, NULL, NULL),
(34, 'RENEW_THANK', '2010-10-31', 'SAL58', NULL, NULL, NULL),
(35, 'reminder', '2010-11-01', 'ZAE48', NULL, NULL, 'Reminder'),
(36, 'RENEW_THANK', '2010-11-01', 'CHA18', NULL, NULL, NULL),
(37, 'RENEW_THANK', '2011-01-02', 'ZAE48', NULL, NULL, NULL),
(38, 'EOYRTaxRcpt', '2011-01-11', 'CHE10', NULL, NULL, NULL),
(41, 'DONATE_THANK', '2011-02-08', 'SMO32', NULL, NULL, NULL),
(42, 'reminder', '2011-02-15', 'PEP21', NULL, NULL, 'Reminder'),
(43, 'DONATE_THANK', '2011-02-20', 'BRO56', NULL, NULL, NULL),
(44, 'OTHER', '2011-03-01', 'CAR42', NULL, NULL, NULL),
(45, 'reminder', '2011-04-15', 'COL19', NULL, NULL, 'Reminder'),
(46, 'RENEW_THANK', '2011-05-22', 'PEP21', NULL, NULL, NULL),
(47, 'NEW_THANK', '2011-06-12', 'COS18', NULL, NULL, NULL),
(49, 'OTHER', '2011-08-08', 'CAR42', NULL, NULL, NULL),
(50, 'reminder', '2011-08-10', 'GOV22', NULL, NULL, 'Reminder'),
(51, 'DONATE_THANK', '2011-09-04', 'HIL21', NULL, NULL, NULL),
(52, 'NEW_THANK', '2011-09-04', 'OCO76', NULL, NULL, NULL),
(53, 'DONATE_THANK', '2011-09-11', 'YOU12', NULL, NULL, NULL),
(54, 'reminder', '2011-10-01', 'CHA18', NULL, NULL, 'Reminder'),
(55, 'reminder', '2011-10-30', 'SAL58', NULL, NULL, 'Reminder'),
(57, 'RENEW_THANK', '2011-11-01', 'CHA18', NULL, NULL, NULL),
(58, 'reminder', '2011-11-13', 'ZAE48', NULL, NULL, 'Reminder'),
(59, 'RENEW_THANK', '2011-11-20', 'GOV22', NULL, NULL, NULL),
(60, 'DONATE_THANK', '2011-12-19', 'BRO56', NULL, NULL, NULL),
(61, 'DONATE_THANK', '2011-12-25', 'AHE22', NULL, NULL, NULL),
(62, 'NEW_THANK', '2011-12-30', 'LAN12', NULL, NULL, NULL),
(63, 'EOYRTaxRcpt', '2012-01-02', 'CHE10', NULL, NULL, NULL),
(64, 'reminder', '2012-01-13', 'HIL21', NULL, NULL, 'Reminder'),
(65, 'DONATE_THANK', '2012-01-21', 'HOW98', NULL, NULL, NULL),
(66, 'reminder', '2012-01-21', 'ZAE48', NULL, NULL, 'Reminder'),
(67, 'DONATE_THANK', '2012-01-29', 'SAL58', NULL, NULL, NULL),
(68, 'RENEW_THANK', '2012-01-29', 'COL19', NULL, NULL, NULL),
(69, 'reminder', '2012-02-08', 'BRO56', NULL, NULL, 'Reminder'),
(70, 'RENEW_THANK', '2012-03-10', 'BRO56', NULL, NULL, NULL),
(71, 'RENEW_THANK', '2012-06-03', 'NIC23', NULL, NULL, NULL),
(73, 'reminder', '2012-07-09', 'HIL21', NULL, NULL, 'Reminder'),
(74, 'RENEW_THANK', '2012-07-21', 'SAL58', NULL, NULL, NULL),
(76, 'reminder', '2012-08-28', 'PEP21', NULL, NULL, 'Reminder'),
(77, 'reminder', '2012-08-30', 'CAR62', NULL, NULL, 'Reminder'),
(78, 'reminder', '2012-09-15', 'YOU12', NULL, NULL, 'Reminder'),
(81, 'RENEW_THANK', '2012-09-30', 'COL19', NULL, NULL, NULL),
(82, 'RENEW_THANK', '2012-10-10', 'OCO76', NULL, NULL, NULL),
(83, 'reminder', '2012-11-01', 'GOV22', NULL, NULL, 'Reminder'),
(84, 'Tour Invite', '2012-11-01', 'CHE10', NULL, NULL, NULL),
(85, 'Tour Invite', '2012-11-01', 'LAN12', NULL, NULL, NULL),
(86, 'OTHER', '2012-11-03', 'HAT15', NULL, NULL, NULL),
(87, 'RENEW_THANK', '2012-11-03', 'LAN12', NULL, NULL, NULL),
(88, 'DONATE_THANK', '2012-11-04', 'COL19', NULL, NULL, NULL),
(90, 'RENEW_THANK', '2012-11-04', 'YOU12', NULL, NULL, NULL),
(91, 'reminder', '2012-11-05', 'CHA18', NULL, NULL, 'Reminder'),
(92, 'RENEW_THANK', '2012-11-15', 'CAR62', NULL, NULL, NULL),
(93, 'RENEW_THANK', '2012-11-18', 'GOV22', NULL, NULL, NULL),
(96, 'RENEW_THANK', '2012-12-10', 'CHA18', NULL, NULL, NULL),
(97, 'NEW_THANK', '2012-12-16', 'ABB17', NULL, NULL, NULL),
(98, 'DONATE_THANK', '2012-12-30', 'OCO76', NULL, NULL, NULL),
(99, 'RENEW_THANK', '2012-12-30', 'AHE22', NULL, NULL, NULL),
(100, 'DONATE_THANK', '2012-12-31', 'COL19', NULL, NULL, NULL),
(101, 'RENEW_THANK', '2013-01-05', 'PEP21', NULL, NULL, NULL),
(102, 'reminder', '2013-01-10', 'CHA18', NULL, NULL, 'Reminder'),
(103, 'Donation_Thank_You', '2013-01-13', 'CHE10', NULL, NULL, NULL),
(105, 'reminder', '2013-01-13', 'LAN12', NULL, NULL, 'Reminder'),
(106, 'RenewalPaid', '2013-01-13', 'ZAE48', NULL, NULL, 'RenewalPaid'),
(107, 'RenewalPaid', '2013-02-12', 'CHA18', NULL, NULL, 'RenewalPaid'),
(108, 'RENEW_THANK', '2013-02-15', 'CHA18', NULL, NULL, NULL),
(109, 'RENEW_THANK', '2013-02-17', 'CAR42', NULL, NULL, NULL),
(110, 'RENEW_THANK', '2013-02-25', 'CAR42', NULL, NULL, NULL),
(111, 'RENEW_THANK', '2013-04-07', 'ZAE48', NULL, NULL, NULL),
(112, 'reminder', '2013-06-04', 'MIL73', NULL, NULL, 'Reminder'),
(113, 'reminder', '2013-07-01', 'HAR14', NULL, NULL, 'Reminder'),
(114, 'reminder', '2013-08-04', 'HAR14', NULL, NULL, 'Reminder'),
(117, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(118, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(119, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(120, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(121, 'Other', '2014-02-12', 'ABB17', NULL, 'this is a note\r\n', NULL),
(122, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(123, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(124, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(125, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(126, 'RenewalPaid', '2014-02-12', 'ABB17', NULL, 'auto-added on payment of dues', 'RenewalPaid'),
(128, 'Bulk email send', '2014-02-15', 'BRO56', NULL, 'Subject: initial test message from bulk mail', NULL),
(129, 'Bulk email send', '2014-02-15', 'CAR62', NULL, 'Subject: initial test message from bulk mail', NULL),
(130, 'Bulk email send', '2014-02-15', 'HAR14', NULL, 'Subject: initial test message from bulk mail', NULL),
(131, 'Bulk email send', '2014-02-15', 'HAT15', NULL, 'Subject: initial test message from bulk mail', NULL),
(132, 'Bulk email send', '2014-02-15', NULL, NULL, 'Subject: initial test message from bulk mail', NULL),
(133, 'Bulk email send', '2014-02-15', 'AHE22', NULL, 'Subject: now is the time for all good men to come to the aid of their country.', NULL),
(134, 'Bulk email send', '2014-02-15', 'CAR42', NULL, 'Subject: now is the time for all good men to come to the aid of their country.', NULL),
(136, 'Bulk email send', '2014-02-15', 'OCO76', NULL, 'Subject: now is the time for all good men to come to the aid of their country.', NULL),
(137, 'Bulk email send', '2014-02-15', 'YOU12', NULL, 'Subject: now is the time for all good men to come to the aid of their country.', NULL),
(138, 'Bulk email send', '2014-02-15', 'AHE22', NULL, 'Subject: now is the time for all good men to come to the aid of their country.', NULL),
(139, 'Bulk email send', '2014-02-16', 'AHE22', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(140, 'Bulk email send', '2014-02-16', 'CAR42', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(142, 'Bulk email send', '2014-02-16', 'OCO76', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(143, 'Bulk email send', '2014-02-16', 'YOU12', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(144, 'Bulk email send', '2014-02-16', 'PWC99', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(145, 'Bulk email send', '2014-02-16', 'AHE22', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(146, 'Bulk email send', '2014-02-16', 'CAR42', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(148, 'Bulk email send', '2014-02-16', 'OCO76', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(149, 'Bulk email send', '2014-02-16', 'YOU12', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(150, 'Bulk email send', '2014-02-16', 'HAR14', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(151, 'Bulk email send', '2014-02-16', 'AHE22', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(152, 'Bulk email send', '2014-02-16', 'CAR42', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(154, 'Bulk email send', '2014-02-16', 'OCO76', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(155, 'Bulk email send', '2014-02-16', 'YOU12', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(156, 'Bulk email send', '2014-02-16', 'PWC99', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(157, 'Bulk email send', '2014-02-16', 'AHE22', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(158, 'Bulk email send', '2014-02-16', 'CAR42', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(160, 'Bulk email send', '2014-02-16', 'OCO76', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(161, 'Bulk email send', '2014-02-16', 'YOU12', NULL, 'Subject: last test of bulk mail for mbrdb', NULL),
(162, 'Bulk email send', '2014-02-16', 'PWC99', NULL, 'Subject: last test of bulk mail for mbrdb', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
CREATE TABLE IF NOT EXISTS `donations` (
  `DonationID` int(6) NOT NULL AUTO_INCREMENT,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `MCID` varchar(6) DEFAULT NULL,
  `Purpose` varchar(15) DEFAULT NULL,
  `Program` varchar(25) DEFAULT NULL,
  `Campaign` varchar(25) DEFAULT NULL,
  `DonationDate` date DEFAULT NULL,
  `CheckNumber` varchar(10) DEFAULT NULL,
  `TotalAmount` decimal(7,2) DEFAULT NULL,
  `MembershipDonatedFor` varchar(6) DEFAULT NULL,
  `Note` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`DonationID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=211 ;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`DonationID`, `TimeStamp`, `MCID`, `Purpose`, `Program`, `Campaign`, `DonationDate`, `CheckNumber`, `TotalAmount`, `MembershipDonatedFor`, `Note`) VALUES
(1, '0000-00-00 00:00:00', 'CHE10', 'Donation', 'UnresDonation', NULL, '2007-01-19', NULL, 2000.00, NULL, 'Donor Advised'),
(2, '0000-00-00 00:00:00', 'GOV22', 'Dues', NULL, NULL, '2007-07-13', NULL, 15.00, NULL, NULL),
(3, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2007-09-27', NULL, 20.00, NULL, NULL),
(4, '0000-00-00 00:00:00', 'CAR42', 'Donation', NULL, NULL, '2007-10-23', NULL, 20.00, NULL, NULL),
(5, '0000-00-00 00:00:00', 'SAL58', 'Donation', 'UnresDonation', NULL, '2007-11-08', NULL, 75.00, NULL, NULL),
(6, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2008-01-19', NULL, 25.00, NULL, NULL),
(7, '0000-00-00 00:00:00', 'BRO56', 'Dues', NULL, NULL, '2008-02-28', NULL, 25.00, NULL, NULL),
(8, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2008-02-28', NULL, 25.00, NULL, NULL),
(9, '0000-00-00 00:00:00', 'CHE10', 'Donation', NULL, NULL, '2008-03-17', NULL, 1000.00, NULL, 'abandonment dept.'),
(10, '0000-00-00 00:00:00', 'AHE22', 'Donation', 'UnresDonation', NULL, '2008-04-02', NULL, 20.00, NULL, 'granite dona'),
(11, '0000-00-00 00:00:00', 'GOV22', 'Dues', NULL, NULL, '2008-06-01', NULL, 15.00, NULL, NULL),
(12, '0000-00-00 00:00:00', 'BRO56', 'Dues', NULL, NULL, '2008-08-10', NULL, 25.00, NULL, NULL),
(13, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2008-12-17', NULL, 15.00, NULL, 'PayPal'),
(14, '0000-00-00 00:00:00', 'ZAE48', 'Dues', NULL, NULL, '2008-12-29', '1115', 50.00, NULL, NULL),
(15, '0000-00-00 00:00:00', 'BRO56', 'Donation', 'UnresDonation', NULL, '2008-12-31', NULL, 30.00, NULL, NULL),
(16, '0000-00-00 00:00:00', 'SAL58', 'Donation', 'UnresDonation', NULL, '2009-01-06', '346', 20.00, NULL, NULL),
(17, '0000-00-00 00:00:00', 'NIC23', 'Dues', NULL, NULL, '2009-01-11', NULL, 25.00, NULL, 'double ck this - deleted by accident'),
(18, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2009-01-12', NULL, 100.00, NULL, 'supporting $100'),
(19, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-01-17', NULL, 15.00, NULL, NULL),
(20, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-02-17', NULL, 15.00, NULL, NULL),
(21, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-02-24', NULL, 5.00, NULL, 'subscript cc'),
(22, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-03-17', NULL, 15.00, NULL, NULL),
(23, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-03-28', NULL, 5.00, NULL, NULL),
(24, '0000-00-00 00:00:00', 'COL19', 'Donation', 'UnresDonation', NULL, '2009-04-09', '1525', 15.00, NULL, NULL),
(25, '0000-00-00 00:00:00', 'COL19', 'Dues', NULL, NULL, '2009-04-09', '1525', 25.00, NULL, NULL),
(26, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-04-17', NULL, 15.00, NULL, NULL),
(27, '0000-00-00 00:00:00', 'COS18', 'Donation', 'UnresDonation', NULL, '2009-05-15', '2138930', 250.00, NULL, 'says F&G Ed grant'),
(28, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-05-17', NULL, 15.00, NULL, 't.y eml'),
(29, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-05-28', NULL, 5.00, NULL, NULL),
(30, '0000-00-00 00:00:00', 'MIL73', 'Dues', 'UnresDonation', NULL, '2009-06-04', '12220', 10.00, NULL, NULL),
(31, '0000-00-00 00:00:00', 'COS18', 'Donation', 'UnresDonation', NULL, '2009-06-06', '2140460', 625.00, NULL, 'Co. Elections'),
(32, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-06-17', NULL, 15.00, NULL, NULL),
(33, '0000-00-00 00:00:00', 'COS18', 'Donation', 'UnresDonation', NULL, '2009-06-20', '25410', 500.00, NULL, 'comm. Fndtion grant'),
(34, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-06-28', NULL, 5.00, NULL, NULL),
(35, '0000-00-00 00:00:00', 'YOU12', 'Donation', 'UnresDonation', NULL, '2009-07-01', '1609', 75.00, NULL, NULL),
(36, '0000-00-00 00:00:00', 'YOU12', 'Dues', NULL, NULL, '2009-07-01', '1609', 25.00, NULL, NULL),
(37, '0000-00-00 00:00:00', 'GOV22', 'Dues', NULL, NULL, '2009-07-12', '4378', 15.00, NULL, NULL),
(38, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-07-17', NULL, 15.00, NULL, NULL),
(39, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-07-28', NULL, 5.00, NULL, NULL),
(40, '0000-00-00 00:00:00', 'AHE22', 'Dues', 'In-Kind', NULL, '2009-08-09', NULL, 15.00, NULL, 'transporter'),
(41, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-08-20', NULL, 15.00, NULL, 'note: failed on 8/17 but OK 20th'),
(42, '0000-00-00 00:00:00', 'COS18', 'Donation', 'UnresDonation', NULL, '2009-08-26', '2155059', 2500.00, NULL, 'hotline services'),
(43, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-08-27', NULL, 5.00, NULL, 'TY'),
(44, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-09-17', NULL, 15.00, NULL, NULL),
(45, '0000-00-00 00:00:00', 'CHE10', 'Donation', 'UnresDonation', NULL, '2009-09-18', NULL, 2000.00, NULL, NULL),
(46, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-09-28', NULL, 5.00, NULL, NULL),
(47, '0000-00-00 00:00:00', 'CHA18', 'Dues', NULL, NULL, '2009-10-02', '1821', 25.00, NULL, NULL),
(48, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-10-17', NULL, 15.00, NULL, NULL),
(49, '0000-00-00 00:00:00', 'BRO56', 'Dues', NULL, NULL, '2009-10-23', '5023', 30.00, NULL, NULL),
(50, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-10-28', NULL, 5.00, NULL, NULL),
(51, '0000-00-00 00:00:00', 'MIL73', 'Dues', 'UnresDonation', NULL, '2009-10-29', '12351', 10.00, NULL, NULL),
(52, '0000-00-00 00:00:00', 'SAL58', 'Dues', NULL, NULL, '2009-11-11', '361', 50.00, NULL, NULL),
(53, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-11-17', NULL, 15.00, NULL, 'ty eml'),
(54, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-11-28', NULL, 5.00, NULL, NULL),
(55, '0000-00-00 00:00:00', 'ZAE48', 'Dues', NULL, NULL, '2009-12-01', '1371', 200.00, NULL, NULL),
(56, '0000-00-00 00:00:00', 'CHA18', 'Dues', NULL, NULL, '2009-12-08', '2529', 25.00, NULL, NULL),
(57, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2009-12-17', NULL, 15.00, NULL, NULL),
(58, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2009-12-28', NULL, 5.00, NULL, 'eo yr ty'),
(59, '0000-00-00 00:00:00', 'BRO56', 'Donation', 'ProgIncome', '09Xmascard', '2010-01-05', '5080', 25.00, NULL, NULL),
(60, '0000-00-00 00:00:00', 'NIC23', 'Donation', 'ProgIncome', '09Xmascard', '2010-01-15', '2113', 30.00, NULL, NULL),
(61, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-01-17', NULL, 15.00, NULL, NULL),
(62, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-01-28', NULL, 5.00, NULL, NULL),
(63, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2010-02-06', NULL, 25.00, NULL, 'donated instead of WiW tick'),
(64, '0000-00-00 00:00:00', 'HAR14', 'Donation', NULL, NULL, '2010-02-17', 'ca', 62.00, NULL, NULL),
(65, '0000-00-00 00:00:00', 'HAR14', 'Sales', 'T-Shirts', NULL, '2010-02-17', 'ca', 38.00, NULL, NULL),
(66, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-02-17', NULL, 15.00, NULL, NULL),
(67, '0000-00-00 00:00:00', 'COS18', 'Donation', NULL, NULL, '2010-02-19', '2179700', 1000.00, NULL, 'Educ. Awareness etc.'),
(68, '0000-00-00 00:00:00', 'PEP21', 'Dues', NULL, NULL, '2010-02-23', '3677', 50.00, NULL, NULL),
(69, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-02-28', NULL, 5.00, NULL, '6 mo TY'),
(70, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-03-17', NULL, 15.00, NULL, NULL),
(71, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-03-28', NULL, 5.00, NULL, NULL),
(72, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-04-17', NULL, 15.00, NULL, NULL),
(73, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-04-28', NULL, 5.00, NULL, NULL),
(74, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-05-17', NULL, 15.00, NULL, NULL),
(75, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-05-28', NULL, 5.00, NULL, NULL),
(76, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2010-05-31', '1066', 10.00, NULL, NULL),
(77, '0000-00-00 00:00:00', 'COS18', 'Donation', NULL, NULL, '2010-06-02', '4992695', 50.00, NULL, 'Ed. Presenta'),
(78, '0000-00-00 00:00:00', 'LAN12', 'Donation', 'UnresDonation', NULL, '2010-06-02', NULL, 1000.00, NULL, NULL),
(79, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-06-17', NULL, 15.00, NULL, NULL),
(80, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-06-28', NULL, 5.00, NULL, NULL),
(81, '0000-00-00 00:00:00', 'COS18', 'Donation', NULL, NULL, '2010-07-09', NULL, 625.00, NULL, 'Co. Election'),
(82, '0000-00-00 00:00:00', 'CAR42', 'Dues', NULL, NULL, '2010-07-14', '8679', 20.00, NULL, NULL),
(83, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-07-17', NULL, 15.00, NULL, NULL),
(84, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-07-28', NULL, 5.00, NULL, NULL),
(85, '0000-00-00 00:00:00', 'GOV22', 'Dues', NULL, NULL, '2010-07-29', '7892', 25.00, NULL, NULL),
(86, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-08-17', NULL, 15.00, NULL, NULL),
(87, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-08-28', NULL, 5.00, NULL, NULL),
(88, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-09-17', NULL, 15.00, NULL, NULL),
(89, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-09-28', NULL, 5.00, NULL, NULL),
(90, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-10-17', NULL, 15.00, NULL, NULL),
(91, '0000-00-00 00:00:00', 'AHE22', 'Dues', NULL, NULL, '2010-10-21', '2349', 25.00, NULL, NULL),
(92, '0000-00-00 00:00:00', 'SAL58', 'Dues', NULL, NULL, '2010-10-21', '446', 50.00, NULL, NULL),
(93, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-10-28', NULL, 5.00, NULL, NULL),
(94, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2010-11-08', '1216', 10.00, NULL, NULL),
(95, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-11-17', NULL, 15.00, NULL, NULL),
(96, '0000-00-00 00:00:00', 'COS18', 'Donation', NULL, NULL, '2010-11-19', '2222649', 605.00, NULL, NULL),
(97, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-11-28', NULL, 5.00, NULL, NULL),
(98, '0000-00-00 00:00:00', 'HIL21', 'Donation', 'UnresDonation', NULL, '2010-12-08', '7101', 15.00, NULL, 'MB eoyr'),
(99, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2010-12-17', NULL, 15.00, NULL, NULL),
(100, '0000-00-00 00:00:00', 'ZAE48', 'Dues', NULL, NULL, '2010-12-26', '1662', 50.00, NULL, NULL),
(101, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2010-12-28', NULL, 5.00, NULL, NULL),
(102, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-01-17', NULL, 15.00, NULL, NULL),
(103, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2011-01-24', '2329', 25.00, NULL, NULL),
(104, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-01-28', NULL, 5.00, NULL, NULL),
(105, '0000-00-00 00:00:00', 'SMO32', 'Donation', NULL, NULL, '2011-02-07', '6947', 10.00, NULL, NULL),
(106, '0000-00-00 00:00:00', 'BRO56', 'Dues', NULL, NULL, '2011-02-14', '5312', 25.00, NULL, NULL),
(107, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-02-17', NULL, 15.00, NULL, NULL),
(108, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-02-28', NULL, 5.00, NULL, NULL),
(109, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-03-17', NULL, 15.00, NULL, NULL),
(110, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-03-28', NULL, 5.00, NULL, NULL),
(111, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-04-17', NULL, 15.00, NULL, NULL),
(112, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-04-28', NULL, 5.00, NULL, NULL),
(113, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-05-17', NULL, 15.00, NULL, NULL),
(114, '0000-00-00 00:00:00', 'PEP21', 'Dues', NULL, NULL, '2011-05-18', '3980', 50.00, NULL, '"rescuer"'),
(115, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2011-05-26', '1368', 10.00, NULL, NULL),
(116, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-05-28', NULL, 5.00, NULL, NULL),
(117, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-06-17', NULL, 15.00, NULL, NULL),
(118, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-06-28', NULL, 5.00, NULL, NULL),
(119, '0000-00-00 00:00:00', 'CHE10', 'Donation', NULL, NULL, '2011-07-05', '0026459527', 2000.00, NULL, 'Gift'),
(120, '0000-00-00 00:00:00', 'CAR42', 'Dues', NULL, NULL, '2011-07-07', 'cc', 20.00, NULL, 'subscribing - Aug. CANCELED'),
(121, '0000-00-00 00:00:00', 'CAR62', 'Deposit', 'Deposit', NULL, '2011-07-16', '335', 10.00, NULL, NULL),
(122, '0000-00-00 00:00:00', 'CAR62', 'Dues', NULL, NULL, '2011-07-16', '335', 25.00, NULL, NULL),
(123, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-07-17', NULL, 15.00, NULL, NULL),
(124, '0000-00-00 00:00:00', 'COL20', 'Dues', 'Subscr', NULL, '2011-07-28', NULL, 5.00, NULL, 'failed - needs to wait til next yr.'),
(125, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-08-17', NULL, 15.00, NULL, '9/11 failed - emld her'),
(126, '0000-00-00 00:00:00', 'HIL21', 'Donation', 'UnresDonation', 'Rad-TV-New', '2011-09-01', 'cc', 100.00, NULL, NULL),
(127, '0000-00-00 00:00:00', 'OCO76', 'Dues', NULL, NULL, '2011-09-03', 'cc', 25.00, NULL, NULL),
(128, '0000-00-00 00:00:00', 'YOU12', 'Dues', NULL, NULL, '2011-09-07', 'cc', 100.00, NULL, NULL),
(129, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-09-20', NULL, 15.00, NULL, NULL),
(130, '0000-00-00 00:00:00', 'ABB17', 'Donation', 'Food/Med', NULL, '2011-10-09', NULL, 21.23, 'PEA972', NULL),
(131, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-10-20', NULL, 15.00, NULL, NULL),
(132, '0000-00-00 00:00:00', 'GOV22', 'Donation', 'UnresDonation', 'EOYrNL', '2011-11-09', '7982', 25.00, NULL, NULL),
(133, '0000-00-00 00:00:00', 'LAN12', 'Donation', 'UnresDonation', NULL, '2011-11-15', NULL, 500.00, NULL, NULL),
(134, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-11-20', NULL, 15.00, NULL, NULL),
(135, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2011-11-22', '1518', 10.00, NULL, NULL),
(136, '0000-00-00 00:00:00', 'BRO56', 'Donation', 'UnresDonation', 'Dec11', '2011-12-16', '5485', 50.00, NULL, NULL),
(137, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2011-12-20', NULL, 15.00, NULL, 'eoyr tax rcpt'),
(138, '0000-00-00 00:00:00', 'YOU12', 'Donation', 'UnresDonation', 'Dec11', '2011-12-22', '2859', 100.00, NULL, 'Supporter'),
(139, '0000-00-00 00:00:00', 'AHE22', 'Dues', NULL, NULL, '2011-12-23', 'cc', 30.00, NULL, NULL),
(140, '0000-00-00 00:00:00', 'HOW98', 'Donation', 'UnresDonation', 'EOYrNL', '2012-01-19', '1719', 100.00, NULL, 'supporter'),
(141, '0000-00-00 00:00:00', 'ZAE48', 'Donation', 'UnresDonation', 'EOYrNL', '2012-01-19', '1909', 100.00, NULL, 'supporter'),
(142, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-01-20', NULL, 15.00, NULL, NULL),
(143, '0000-00-00 00:00:00', 'COL19', 'Dues', NULL, NULL, '2012-01-23', '885', 50.00, NULL, NULL),
(144, '0000-00-00 00:00:00', 'SAL58', 'Dues', NULL, NULL, '2012-01-29', 'ca', 40.00, NULL, NULL),
(145, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-02-20', NULL, 15.00, NULL, NULL),
(146, '0000-00-00 00:00:00', 'HIL21', 'Donation', 'UnresDonation', 'Dec11', '2012-02-26', 'cc', 25.00, NULL, 'Auth'),
(147, '0000-00-00 00:00:00', 'BRO56', 'Dues', NULL, NULL, '2012-03-01', '5507', 30.00, NULL, NULL),
(148, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-03-17', NULL, 15.00, NULL, NULL),
(149, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-04-17', NULL, 15.00, NULL, NULL),
(150, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-05-17', NULL, 15.00, NULL, NULL),
(151, '0000-00-00 00:00:00', 'NIC23', 'Dues', NULL, NULL, '2012-05-25', 'cc', 25.00, NULL, 'Auth'),
(152, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2012-05-31', '1682', 10.00, NULL, NULL),
(153, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-06-17', NULL, 15.00, NULL, NULL),
(154, '0000-00-00 00:00:00', 'COS18', 'Donation', NULL, NULL, '2012-07-05', '2307432', 493.00, NULL, 'Election Coverage-County Recorder'),
(155, '0000-00-00 00:00:00', 'SAL58', 'Dues', NULL, NULL, '2012-07-15', '396', 50.00, NULL, NULL),
(156, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-07-17', NULL, 15.00, NULL, NULL),
(157, '0000-00-00 00:00:00', 'HIL21', 'Donation', 'UnresDonation', '6-12', '2012-07-26', 'cc', 25.00, NULL, 'Pelican'),
(158, '0000-00-00 00:00:00', 'LAN12', 'Donation', NULL, NULL, '2012-08-10', NULL, 250.00, NULL, NULL),
(159, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-08-17', NULL, 15.00, NULL, NULL),
(160, '0000-00-00 00:00:00', 'SAL58', 'Donation', NULL, NULL, '2012-09-12', 'ca', 40.00, NULL, NULL),
(161, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-09-17', NULL, 15.00, NULL, NULL),
(162, '0000-00-00 00:00:00', 'COS18', 'Grant', 'GrantFunding', NULL, '2012-09-20', '2317623', 1000.00, NULL, 'General care'),
(163, '0000-00-00 00:00:00', 'COL19', 'Dues', NULL, NULL, '2012-09-30', '1018', 50.00, NULL, NULL),
(164, '0000-00-00 00:00:00', 'OCO76', 'Dues', NULL, NULL, '2012-10-06', NULL, 50.00, NULL, NULL),
(165, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-10-17', NULL, 15.00, NULL, NULL),
(166, '0000-00-00 00:00:00', 'YOU12', 'Dues', NULL, NULL, '2012-10-18', 'cc', 50.00, NULL, NULL),
(167, '0000-00-00 00:00:00', 'LAN12', 'Dues', NULL, NULL, '2012-10-22', NULL, 100.00, NULL, NULL),
(168, '0000-00-00 00:00:00', 'HAT15', 'Dues', NULL, NULL, '2012-10-23', 'cc', 25.00, 'HAT11', NULL),
(169, '0000-00-00 00:00:00', 'COL19', 'Dues', NULL, NULL, '2012-10-29', '1028', 50.00, NULL, NULL),
(170, '0000-00-00 00:00:00', 'CAR62', 'Dues', NULL, NULL, '2012-11-01', '385', 25.00, NULL, NULL),
(171, '0000-00-00 00:00:00', 'HIL21', 'Donation', NULL, NULL, '2012-11-03', 'cc', 25.00, NULL, NULL),
(172, '0000-00-00 00:00:00', 'ABB17', 'FundRaising', 'Fun-Donation', NULL, '2012-11-07', NULL, 25.00, NULL, NULL),
(173, '0000-00-00 00:00:00', 'HAT15', 'PrgIncome', 'Prg-Deposit', NULL, '2012-11-10', 'ca', 15.00, NULL, 'Transporters Handbook'),
(174, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2012-11-11', '7336', 50.00, NULL, NULL),
(175, '0000-00-00 00:00:00', 'CHE10', 'Donation', NULL, NULL, '2012-11-14', '024319798', 1000.00, NULL, NULL),
(176, '0000-00-00 00:00:00', 'GOV22', 'Dues', NULL, NULL, '2012-11-15', '8052', 25.00, NULL, NULL),
(177, '0000-00-00 00:00:00', 'MIL73', 'Dues', NULL, NULL, '2012-11-15', '1851', 10.00, NULL, NULL),
(178, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-11-17', NULL, 15.00, NULL, NULL),
(179, '0000-00-00 00:00:00', 'HAR14', 'Dues', NULL, NULL, '2012-11-27', 'ca', 25.00, NULL, NULL),
(180, '0000-00-00 00:00:00', 'AHE22', 'Donation', 'UnresDonation', '11-12', '2012-11-29', '0052907611', 25.00, NULL, 'Money order'),
(181, '0000-00-00 00:00:00', 'ABB17', 'Donation', 'UnresDonation', '11-12', '2012-12-13', '6512', 200.00, NULL, NULL),
(182, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2012-12-17', NULL, 15.00, NULL, '$180 eoyr'),
(183, '0000-00-00 00:00:00', 'LAN12', 'Donation', 'UnresDonation', 'Dec11', '2012-12-22', '3894', 50.00, NULL, NULL),
(184, '0000-00-00 00:00:00', 'COL19', 'Donation', NULL, NULL, '2012-12-25', '1057', 50.00, NULL, 'Tot. ''12: $200'),
(185, '0000-00-00 00:00:00', 'OCO76', 'Donation', NULL, NULL, '2012-12-28', 'cc', 1000.00, NULL, 'PayPal'),
(186, '0000-00-00 00:00:00', 'ZAE48', 'Dues', NULL, NULL, '2013-01-04', '2276', 100.00, NULL, 'supporter'),
(187, '0000-00-00 00:00:00', 'NIC23', 'Dues', NULL, NULL, '2013-01-13', NULL, 25.00, NULL, NULL),
(188, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-01-17', NULL, 15.00, NULL, NULL),
(189, '0000-00-00 00:00:00', 'CHA18', 'Dues', NULL, NULL, '2013-02-12', 'cc', 25.00, NULL, NULL),
(190, '0000-00-00 00:00:00', 'CAR42', 'Dues', NULL, NULL, '2013-02-16', 'pp', 25.00, NULL, NULL),
(191, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-02-17', NULL, 15.00, NULL, NULL),
(192, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-03-17', NULL, 15.00, NULL, NULL),
(193, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-04-17', NULL, 15.00, NULL, NULL),
(194, '0000-00-00 00:00:00', 'COS18', 'EducProg', 'EducPres', 'Ed Talks', '2013-04-18', '2347928', 500.00, NULL, 'Edu. Talk'),
(195, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-05-17', NULL, 15.00, NULL, NULL),
(196, '0000-00-00 00:00:00', 'MIL73', 'Dues', 'UnresDonation', '11-12', '2013-05-23', '2039', 10.00, NULL, NULL),
(197, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-06-17', NULL, 15.00, NULL, NULL),
(198, '0000-00-00 00:00:00', 'YOU12', 'Dues', NULL, NULL, '2013-07-10', 'cc', 100.00, NULL, 'PayPal'),
(199, '0000-00-00 00:00:00', 'HOL12', 'Dues', 'Subscr', NULL, '2013-07-17', NULL, 15.00, NULL, NULL),
(200, '0000-00-00 00:00:00', 'HIL21', 'Dues', NULL, NULL, '2013-07-25', '1144', 25.00, NULL, NULL),
(201, '0000-00-00 00:00:00', 'PEP21', 'Donation', 'UnresDonation', '11-12', '2013-12-27', '4974', 100.00, NULL, NULL),
(202, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 0.00, NULL, NULL),
(203, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 0.00, NULL, NULL),
(204, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 1.00, NULL, NULL),
(205, '0000-00-00 00:00:00', 'ABB17', 'Donation', 'Don-UnresDonation', NULL, '2014-02-12', NULL, 10.00, NULL, NULL),
(206, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 0.00, NULL, 'this is a explanatory note.'),
(207, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 100.00, NULL, NULL),
(208, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 10.00, NULL, NULL),
(209, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 0.00, NULL, 'this is a note'),
(210, '0000-00-00 00:00:00', 'ABB17', 'Dues', 'Dues-AnnualMbr', NULL, '2014-02-12', NULL, 0.00, NULL, 'this is another note');

-- --------------------------------------------------------

--
-- Table structure for table `extradonorinfo`
--

DROP TABLE IF EXISTS `extradonorinfo`;
CREATE TABLE IF NOT EXISTS `extradonorinfo` (
  `RecID` int(6) NOT NULL AUTO_INCREMENT,
  `MCID` varchar(6) NOT NULL,
  `NameLabel1stline` varchar(50) DEFAULT NULL,
  `personal` text,
  `education` text,
  `business` text,
  `other` text,
  `wealth` text,
  `research` text,
  `DateEntered` date NOT NULL,
  `LastUpdated` date NOT NULL,
  `LastUpdater` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`RecID`),
  UNIQUE KEY `MCID` (`MCID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labelsandletters`
--

DROP TABLE IF EXISTS `labelsandletters`;
CREATE TABLE IF NOT EXISTS `labelsandletters` (
  `LLID` int(6) NOT NULL AUTO_INCREMENT,
  `MCID` varchar(6) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Organization` varchar(30) DEFAULT NULL,
  `NameLabel1stline` varchar(50) DEFAULT NULL,
  `AddressLine` varchar(30) DEFAULT NULL,
  `City` varchar(20) DEFAULT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `CorrSal` varchar(30) DEFAULT NULL,
  `Letter` text,
  PRIMARY KEY (`LLID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `User` varchar(50) DEFAULT NULL,
  `SecLevel` varchar(15) DEFAULT NULL,
  `Page` varchar(50) DEFAULT NULL,
  `SQL` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`LogID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `maillog`
--

DROP TABLE IF EXISTS `maillog`;
CREATE TABLE IF NOT EXISTS `maillog` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `User` varchar(50) DEFAULT NULL,
  `SecLevel` varchar(15) DEFAULT NULL,
  `MailText` text,
  PRIMARY KEY (`LogID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `MbrID` int(11) NOT NULL AUTO_INCREMENT,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `MCID` varchar(6) DEFAULT NULL,
  `Source` varchar(6) DEFAULT NULL,
  `MemStatus` int(1) DEFAULT NULL,
  `MemDate` date DEFAULT NULL,
  `Account` int(6) DEFAULT NULL,
  `Member` varchar(5) DEFAULT NULL,
  `E_Mail` varchar(5) DEFAULT NULL,
  `Inactive` varchar(5) DEFAULT NULL,
  `Inactivedate` date DEFAULT NULL,
  `Mail` varchar(5) DEFAULT NULL,
  `MCtype` varchar(15) DEFAULT NULL,
  `LName` varchar(20) DEFAULT NULL,
  `FName` varchar(20) DEFAULT NULL,
  `NameLabel1stline` varchar(50) DEFAULT NULL,
  `Organization` varchar(30) DEFAULT NULL,
  `AddressLine` varchar(30) DEFAULT NULL,
  `City` varchar(20) DEFAULT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `PrimaryPhone` varchar(15) DEFAULT NULL,
  `EmailAddress` varchar(50) DEFAULT NULL,
  `PaidMemberYear` varchar(5) DEFAULT NULL,
  `Notes` text,
  `MasterMemberID` varchar(6) DEFAULT NULL,
  `CorrSal` varchar(40) DEFAULT NULL,
  `Lists` varchar(128) DEFAULT NULL,
  `LastDonDate` date DEFAULT NULL,
  `LastDonPurpose` varchar(15) DEFAULT NULL,
  `LastDonAmount` decimal(12,2) DEFAULT NULL,
  `LastDuesDate` date DEFAULT NULL,
  `LastDuesAmount` decimal(12,2) DEFAULT NULL,
  `LastCorrDate` date DEFAULT NULL,
  `LastCorrType` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`MbrID`),
  UNIQUE KEY `MCID` (`MCID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`MbrID`, `TimeStamp`, `MCID`, `Source`, `MemStatus`, `MemDate`, `Account`, `Member`, `E_Mail`, `Inactive`, `Inactivedate`, `Mail`, `MCtype`, `LName`, `FName`, `NameLabel1stline`, `Organization`, `AddressLine`, `City`, `State`, `ZipCode`, `PrimaryPhone`, `EmailAddress`, `PaidMemberYear`, `Notes`, `MasterMemberID`, `CorrSal`, `Lists`, `LastDonDate`, `LastDonPurpose`, `LastDonAmount`, `LastDuesDate`, `LastDuesAmount`, `LastCorrDate`, `LastCorrType`) VALUES
(1, '2014-02-07 19:26:40', 'ABB17', 'MbrDB', 1, '2012-12-13', NULL, 'TRUE', 'FALSE', 'TRUE', '2011-07-13', 'FALSE', '1-Advocate', 'Abbott', 'Jillian', 'Jillian Abbott', NULL, 'P.O. Box 17912', 'Avila Beach', 'CA', '93402', '(805) 555-1212', NULL, NULL, 'found in MB St. Park', 'CUM95', 'Jillian', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '2014-02-14 21:30:25', 'AHE22', 'MbrDB', 1, '2011-12-23', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'TRUE', '1-IndivorFamily', 'Aherne', 'Donna', 'Donna Aherne', NULL, '2296 Falconridge Ln', 'San Luis Obispo', 'CA', '93403', '(805) 555-1211', 'someone@anemail.com', NULL, NULL, NULL, 'Donna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '2014-02-14 02:14:52', 'BRO56', 'MbrDB', 2, '2008-02-28', 41, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '2-Volunteer', 'Brown', 'Erin', 'Erin Brown', NULL, '5644 W. Newport Street', 'San Luis Obispo', 'CA', '93404', '(805) 555-1210', 'someone@anemail.com', '2004', '''07: $25 SOWings - 1/2010 ck: 215 Lucas Ln, Avila', NULL, 'Erin', 'Adm,BBB,Obl', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '2014-01-30 23:15:05', 'CAR42', 'MbrDB', 1, '2010-07-14', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '1-Sr-Student', 'Cardoza', 'Cynthia', 'Cynthia Cardoza', NULL, '42 Moore Ln.', 'Los Osos', 'CA', '93405', '(805) 555-1209', 'someone@anemail.com', NULL, NULL, NULL, 'Cynthia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '2014-02-14 02:15:23', 'CAR62', 'MbrDB', 2, '2011-07-17', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '2-Volunteer', 'Cardinali', 'Roxanne', 'Roxanne Cardinali', NULL, 'P.O. Box 62643', 'Morro Bay', 'CA', '93406', '(805) 555-1208', 'someone@anemail.com', NULL, NULL, NULL, 'Roxanne', 'Ctr,Obl,OWCN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '2014-05-20 14:39:21', 'CHA18', 'MbrDB', 1, '2009-10-02', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '1-Sr-Student', 'Chambers', 'Erin', 'Erin Chambers', NULL, '1843 El Camino Real #129', 'Arroyo Grande', 'CA', '93407', '(805) 555-1207', NULL, '2009', '''11 req. uniberibe: rchambers-k9@charter.net; ''13 an. Rescuer ''13 funmem4two@charter.net bounced', NULL, 'Erin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '2014-01-30 23:15:05', 'CHE10', 'MbrDB', 3, '2007-03-01', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '3-AFFILIATE', 'Cherbinski', 'Carlene', 'Carlene Cherbinski', 'Atascadero Pet Center', '1015 Glenn St.', 'San Luis Obispo', 'CA', '93408', '(805) 555-1206', NULL, '2007', NULL, NULL, 'Carlene', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '2014-01-30 23:15:05', 'COL19', 'MbrDB', 1, '2009-04-09', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '1-Advocate', 'Collie', 'Brian', 'Brian Collie', NULL, '195 Center Ct.', 'Atascadero', 'CA', '93409', '(805) 555-1205', NULL, '2009', NULL, NULL, 'Brian', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, '2014-05-20 14:48:56', 'COL20', 'MbrDB', 2, '2008-09-10', NULL, 'FALSE', 'FALSE', 'FALSE', NULL, 'TRUE', '2-Volunteer', 'Collins', 'Laura', 'Laura Collins', NULL, '2090 San Fernando Rd', 'Los Osos', 'CA', '93410', '(805) 555-1204', NULL, NULL, NULL, NULL, 'Laura', 'PV,PVbu,BBR', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '2014-01-30 23:15:05', 'COS18', 'MbrDB', 3, '2011-06-11', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '3-AGENCY', 'of San Luis Obispo', 'County', 'County of San Luis Obispo', 'County of San Luis Obispo', '1858 Brookline Ln', 'Morro Bay', 'CA', '93411', '(805) 555-1203', 'someone@anemail.com', NULL, 'County grants and funding sources', NULL, 'County', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, '2014-05-20 14:46:39', 'GOV22', 'MbrDB', 1, '2007-07-13', 866, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '1-Sr-Student', 'Govednik', 'Heather', 'Heather Govednik', NULL, '2250 Branch Rd', 'Atascadero', 'CA', '93412', '(805) 555-1202', NULL, '2006', NULL, NULL, 'Heather', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, '2014-01-30 23:15:05', 'HAR14', 'MbrDB', 2, '2009-11-27', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '2-Phone', 'Harmon', 'John', 'John Harmon', NULL, '1450 San Luis Bay Dr.', 'San Luis Obispo', 'CA', '93413', '(805) 555-1201', 'someone@anemail.com', '2009', 'Cuesta Program Vol', NULL, 'John', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, '2014-01-30 23:15:05', 'HAT15', 'MbrDB', 2, '2012-10-23', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '2-Transporter', 'Hathaway', 'Donald', 'Donald Hathaway', NULL, '153 Ramona Ave. #103', 'Paso Robles', 'CA', '93414', '(805) 555-1200', 'someone@anemail.com', NULL, 'IHO for Robert', NULL, 'Donald', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '2014-01-30 23:15:05', 'HIL21', 'MbrDB', 1, '2009-01-12', 1392, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '1-Sr-Student', 'Hill', 'Treanna', 'Treanna Hill', NULL, '2148 Los Osos Valley Rd.', 'San Luis Obispo', 'CA', '93415', '(805) 555-1199', 'someone@anemail.com', NULL, NULL, NULL, 'Treanna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, '2014-02-14 02:16:19', 'HOL12', 'MbrDB', 2, '2012-09-05', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'FALSE', '2-Volunteer', 'Holzer', 'Sharon', 'Sharon Holzer', NULL, '1261 S. Bent Tree Dr.', 'Los Osos', 'CA', '93416', '(805) 555-1198', NULL, NULL, NULL, NULL, 'Sharon', 'Ctr,Mnt,Obl', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, '2014-01-30 23:15:05', 'HOW98', 'MbrDB', 3, '2012-01-19', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '3-Donor', 'Howland', 'Celia', 'Celia Howland', NULL, 'P.O. Box 562', 'Los Osos', 'CA', '93417', '(805) 555-1197', NULL, NULL, 'req "no newsletter"', NULL, 'Celia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, '2014-05-20 14:47:23', 'LAN12', 'MbrDB', 1, '2011-12-22', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '1-IndivorFamily', 'Landis', 'Louise', 'Louise Landis', NULL, '128 Greenwood Dr.', 'Bloomington', 'CA', '93418', '(805) 555-1196', NULL, NULL, NULL, NULL, 'Louise', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '2014-05-20 14:48:24', 'MIL73', 'MbrDB', 1, '2012-06-14', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '1-IndivorFamily', 'Miller', 'Tavy', 'Tavy Miller', NULL, 'P.O. Box 7348', 'Avila Beach', 'CA', '93419', '(805) 555-1195', NULL, NULL, NULL, NULL, 'Tavy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '2014-05-20 14:38:45', 'NIC23', 'MbrDB', 2, '2012-06-03', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '2-Volunteer', 'Nicholson', 'Terrence', 'Terrence Nicholson', NULL, '2310 Mesa Rd.', 'Arroyo Grande', 'CA', '93420', '(805) 555-1194', NULL, NULL, NULL, NULL, 'Terrence', 'PV,Trn', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, '2014-01-30 23:15:05', 'OCO76', 'MbrDB', 1, '2011-09-03', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '1-Advocate', 'O''Connor', 'Nancy', 'Nancy O''Connor', NULL, '9575 El Camino Real', 'Los Osos', 'CA', '93421', '(805) 555-1193', 'someone@anemail.com', NULL, 'AGM: Bob Isenberg', NULL, 'Nancy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, '2014-05-20 14:49:23', 'PEP21', 'MbrDB', 2, '2010-02-23', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, 'TRUE', '2-Volunteer', 'Pepple', 'Debby', 'Debby Pepple', NULL, '2166 Wilding Ln', 'Paso Robles', 'CA', '93422', '(805) 555-1192', NULL, '2010', NULL, NULL, 'Debby', 'Ctr,Mnt', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, '2014-01-30 23:15:05', 'SAL58', 'MbrDB', 3, '2009-11-11', 1077, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '3-COMPANY', 'Salosbirey', 'Karen', 'Karen Salosbirey', 'Salisbury Ltd', '200 Cliff Street', 'Atascadero', 'CA', '93423', '(805) 555-1191', 'someone@anemail.com', '2009', NULL, NULL, 'Karen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, '2014-05-20 14:49:58', 'SMO32', 'MbrDB', 0, '2011-11-01', NULL, 'FALSE', 'FALSE', 'FALSE', NULL, 'TRUE', '0-Proposed', 'Smoot', 'Alvin', 'Alvin Smoot', 'Smoots Oak Shadow Vineyard', '1025 Cortez Ct.', 'San Luis Obispo', 'CA', '93424', '(805) 555-1190', NULL, NULL, NULL, NULL, 'Alvin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, '2014-01-30 23:15:05', 'YOU12', 'MbrDB', 1, '2011-09-07', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, 'FALSE', '1-Supporter', 'Young', 'Christine', 'Christine Young', NULL, 'P.O. Box 1294', 'Shell Beach', 'CA', '93425', '(805) 555-1189', 'someone@anemail.com', NULL, 'auth. Says phone is 595-2888 ?', NULL, 'Christine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, '2014-05-20 14:37:55', 'ZAE48', 'MbrDB', 2, '2008-12-29', 1371, 'TRUE', 'TRUE', 'FALSE', NULL, 'TRUE', '2-Volunteer', 'Zaentz', 'Karen', 'Karen Zaentz', NULL, '4867 Capistrano Ct. #A', 'San Luis Obispo', 'CA', '93426', NULL, 'karen@somewhere.com', '2008', '12/09 sent raccoon pkt', NULL, 'Karen', 'Ctr', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `TID` int(5) NOT NULL AUTO_INCREMENT,
  `Type` varchar(6) DEFAULT NULL,
  `Name` varchar(128) DEFAULT NULL,
  `Body` text,
  PRIMARY KEY (`TID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`TID`, `Type`, `Name`, `Body`) VALUES
(1, 'mail', 'Label Only', 'label only<br>'),
(2, 'email', 'Renewal Reminder for Pacific Wildlife Care', '\r\n\r\n	\r\n	\r\n	\r\n	<style type="text/css">P { margin-bottom: 0.08in; }A:link {  }</style>\r\n\r\n\r\n<p><b>Greetings Pacific Wildlife Care supporter!</b></p>\r\n<p>Thank you for your past support of Pacific Wildlife Care. That is\r\nhow the rehabilitation of San Luis Obispo County''s orphaned and\r\ninjured wildlife is even possible!&nbsp; Did you hear that we saw\r\nover 2,800 cases last year?! Needless to say, our budget is being\r\nstretched with our monthly expenses nearing $16,000.00 per month. We\r\nrely on your annual giving since we are not funded by any government\r\nagencies!</p>\r\n<p><font size="4"><b>Your Annual Membership has expired.</b></font></p>\r\n<p><b>There are two Membership types available as a renewal option:</b></p>\r\n<p><u>Subscribing Member</u></p>\r\n<p>This membership type allows you to provide a monthly dues payment\r\nusing your Mastercard, Visa or Discover cards OR using your PayPal\r\naccount. It is very easy to sign up. <a href="http://www.pacificwildlifecare.org/supportus_membership.html">Merely\r\ngo to our website and register.</a> Your dues payment will be monthly\r\nand deposited directly to Pacific Wildlife Care.</p>\r\n<p><u>Annual Membership Levels:</u></p>\r\n<p>Choose from one of the following and mail your dues payment to the\r\naddress provided.</p>\r\n<ul><li><p style="margin-bottom: 0in">Benefactor $2500 - includes all\r\n	below and two tickets to Windows into Wildlife Annual Benefit.</p>\r\n	</li><li><p style="margin-bottom: 0in">Patron $1000 - includes all listed\r\n	below and an invite to a Wildlife Release.</p>\r\n	</li><li><p style="margin-bottom: 0in">Sponsor $500 - includes all listed\r\n	below and invite to an Educational Talk.</p>\r\n	</li><li><p style="margin-bottom: 0in">Contributor $250 - includes all\r\n	listed below and invite to Tour of the Rehab Center.</p>\r\n	</li><li><p style="margin-bottom: 0in">Supporter $100 - Newsletter and\r\n	Wildlife Fact Card.</p>\r\n	</li><li><p style="margin-bottom: 0in">Advocate $50 – tri-annual\r\n	Newsletters.</p>\r\n	</li><li><p style="margin-bottom: 0in">Full-time Student/Senior or\r\n	current PWC Volunteer $25 – tri-annual Newsletters.</p>\r\n</li></ul>\r\n<p>You can pay on-line by using <a href="http://www.pacificwildlifecare.org/supportus_membership.html">THIS\r\nLINK</a> then choosing either the subscription payment option or the\r\nsingle annual dues payment option,</p>\r\n<p>OR, you can mail a check to PWC, P.O. Box 1134, Morro Bay, CA&nbsp;\r\n93443-1134 (please note on the check that the payment is for ''Annual\r\nDues''),</p>\r\n<p>OR, drop off a check with the donation/membership form on the back\r\nof your last newsletter at the Rehab Center in Morro Bay.</p>\r\n<p>Thank you in advance for your support.</p>\r\n<p>Marcelle Bakula, Membership Chair</p>\r\n<p><br><br>\r\n</p>\r\n\r\n'),
(3, 'email', 'Renewal Reminder for Pacific Wildlife Care – Second Notification', '\r\n\r\n\r\n	\r\n	\r\n	\r\n	<style type="text/css">P { margin-bottom: 0.08in; }A:link {  }</style>\r\n\r\n\r\n<p><b>Greetings from Pacific Wildlife Care.</b></p>\r\n<p>Your past support has been extremely valuable and we are hoping\r\nthat you are able to continue it with either a Subscribing or Annual\r\nmembership commitment to Pacific Wildlife Care. Our ability to\r\ncontinue to provide wildlife rescue, rehabilitation and release\r\nservices to the wildlife of San Luis Obispo County is directly\r\ndependent on your support.</p>\r\n<p><font size="4"><b>This is a second reminder that your Annual\r\nMembership to Pacific Wildlife Care has expired.</b></font></p>\r\n<p><b>There are two Membership types available as a renewal option:</b></p>\r\n<p><u>Subscribing Member</u></p>\r\n<p>This membership type allows you to provide a monthly dues payment\r\nusing your Mastercard, Visa or Discover cards OR using your PayPal\r\naccount. It is very easy to sign up. <a href="http://www.pacificwildlifecare.org/supportus_membership.html">Merely\r\ngo to our website and register.</a> Your dues payment will be monthly\r\nand deposited directly to Pacific Wildlife Care.</p>\r\n<p><u>Annual Membership Levels:</u></p>\r\n<p>Choose from one of the following and mail your dues payment to the\r\naddress provided.</p>\r\n<ul><li><p style="margin-bottom: 0in">Benefactor $2500 - includes all\r\n	below and two tickets to Windows into Wildlife Annual Benefit.</p>\r\n	</li><li><p style="margin-bottom: 0in">Patron $1000 - includes all listed\r\n	below and an invite to a Wildlife Release.</p>\r\n	</li><li><p style="margin-bottom: 0in">Sponsor $500 - includes all listed\r\n	below and invite to an Educational Talk.</p>\r\n	</li><li><p style="margin-bottom: 0in">Contributor $250 - includes all\r\n	listed below and invite to Tour of the Rehab Center.</p>\r\n	</li><li><p style="margin-bottom: 0in">Supporter $100 - Newsletter and\r\n	Wildlife Fact Card.</p>\r\n	</li><li><p style="margin-bottom: 0in">Advocate $50 – tri-annual\r\n	Newsletters.</p>\r\n	</li><li><p style="margin-bottom: 0in">Full-time Student/Senior or\r\n	current PWC Volunteer $25 – tri-annual Newsletters.</p>\r\n</li></ul>\r\n<p>You can pay on-line by using <a href="http://www.pacificwildlifecare.org/supportus_membership.html">THIS\r\nLINK</a> then choosing either the subscription payment option or the\r\nsingle annual dues payment option,</p>\r\n<p>OR, you can mail a check to PWC, P.O. Box 1134, Morro Bay, CA&nbsp;\r\n93443-1134 (please note on the check that the payment is for ''Annual\r\nDues''),</p>\r\n<p>OR, drop off a check with the donation/membership form on the back\r\nof your last newsletter at the Rehab Center in Morro Bay.</p>\r\n<p>Thank you in advance for your support.</p>\r\n<p>Marcelle Bakula, Membership Chair</p>\r\n<p style="margin-bottom: 0in"><br>\r\n</p>\r\n\r\n'),
(4, 'email', 'Renewal Reminder for Pacific Wildlife Care – Final Notification', '\n\n\n	\n	\n	\n	<style type="text/css">P { margin-bottom: 0.08in; }A:link {  }</style>\n\n\n<p><b>Greetings from Pacific Wildlife Care.</b></p>\n<p>Your past support has been extremely valuable and we are hoping\nthat you are able to continue to provide it by choosing either a\nSubscribing or Annual membership to Pacific Wildlife Care. Our\nability to continue to provide wildlife rescue, rehabilitation and\nrelease services to the wildlife of San Luis Obispo County is\ndirectly dependent on your support.</p>\n<p><font size="4"><b>This is a FINAL reminder that your Annual\nMembership to Pacific Wildlife Care has expired.</b></font></p>\n<p>If we do not hear from you we will not bother you with further\nnotifications. Hopefully, in the future you will find an opportunity\nto support us. As always, our latest news, events and notices are\nposted on the <a href="http://www.pacificwildlifecare.org/">PWC web\nsite</a> and the <a href="http://www.facebook.com/PacificWildlifeCare">PWC\nFacebook</a> accounts. We hope you will continue to watch for\nupdates.</p>\n<p><b>However, if you choose to do so, there are two Membership types\navailable as a renewal option:</b></p>\n<p><u>Subscribing Member</u></p>\n<p>This membership type allows you to provide a monthly dues payment\nusing your Mastercard, Visa or Discover cards OR using your PayPal\naccount. It is very easy to sign up. <a href="http://www.pacificwildlifecare.org/supportus_membership.html">Merely\ngo to our website and register.</a> Your dues payment will be monthly\nand deposited directly to Pacific Wildlife Care.</p>\n<p><u>Annual Membership Levels:</u></p>\n<p>Choose from one of the following and mail your dues payment to the\naddress provided.</p>\n<ul><li><p style="margin-bottom: 0in">Benefactor $2500 - includes all\n	below and two tickets to Windows into Wildlife Annual Benefit.</p>\n	</li><li><p style="margin-bottom: 0in">Patron $1000 - includes all listed\n	below and an invite to a Wildlife Release.</p>\n	</li><li><p style="margin-bottom: 0in">Sponsor $500 - includes all listed\n	below and invite to an Educational Talk.</p>\n	</li><li><p style="margin-bottom: 0in">Contributor $250 - includes all\n	listed below and invite to Tour of the Rehab Center.</p>\n	</li><li><p style="margin-bottom: 0in">Supporter $100 - Newsletter and\n	Wildlife Fact Card.</p>\n	</li><li><p style="margin-bottom: 0in">Advocate $50 – tri-annual\n	Newsletters.</p>\n	</li><li><p style="margin-bottom: 0in">Full-time Student/Senior or\n	current PWC Volunteer $25 – tri-annual Newsletters.</p>\n</li></ul>\n<p>You can pay on-line by using <a href="http://www.pacificwildlifecare.org/supportus_membership.html">THIS\nLINK</a> then choosing either the subscription payment option or the\nsingle annual dues payment option,</p>\n<p>OR, you can mail a check to PWC, P.O. Box 1134, Morro Bay, CA&nbsp;\n93443-1134 (please note on the check that the payment is for ''Annual\nDues''),</p>\n<p>OR, drop off a check with the donation/membership form on the back\nof your last newsletter at the Rehab Center in Morro Bay.</p>\n<p>Thank you in advance for your support.</p>\n<p>Marcelle Bakula, Membership Chair</p>\n<p><br><br>\n</p>\n\n'),
(5, 'mail', 'Renewal Reminder Letter', '\r\n	\r\n	\r\n	\r\n	\r\n\r\n\r\n<p><b>Greetings from Pacific Wildlife Care.</b></p>\r\n<p>Your past support has been extremely valuable and we are hoping\r\nthat you are able to continue to provide it by choosing either a\r\nSubscribing or Annual membership to Pacific Wildlife Care. Our\r\nability to continue to provide wildlife rescue, rehabilitation and\r\nrelease services to the wildlife of San Luis Obispo County is\r\ndirectly dependent on your support.</p>\r\n<p><font size="4"><b>This is a FINAL reminder that your Annual\r\nMembership to Pacific Wildlife Care has expired.</b></font></p>\r\n<p>If we do not hear from you we will not bother you with further\r\nnotifications. Hopefully, in the future you will find an opportunity\r\nto support us. As always, our latest news, events and notices are\r\nposted on the <a href="http://www.pacificwildlifecare.org/">PWC web\r\nsite</a> and the <a href="http://www.facebook.com/PacificWildlifeCare">PWC\r\nFacebook</a> accounts. We hope you will continue to watch for\r\nupdates.</p>\r\n<p><b>However, if you choose to do so, there are two Membership types\r\navailable as a renewal option:</b></p>\r\n<p><u>Subscribing Member</u><br></p><p>This membership type allows you to provide a monthly dues payment\r\nusing your Mastercard, Visa or Discover cards OR using your PayPal\r\naccount. It is very easy to sign up. <a href="http://www.pacificwildlifecare.org/supportus_membership.html">Merely\r\ngo to our website and register.</a> Your dues payment will be monthly\r\nand deposited directly to Pacific Wildlife Care.</p><p><u>Annual Membership Levels:</u></p>\r\n<p style="margin-bottom: 0in">Choose from one of the following and mail your dues payment to the\r\naddress provided.Benefactor $2500 - includes all\r\n	below and two tickets to Windows into Wildlife Annual Benefit.</p><ul><li>Patron $1000 - includes all listed\r\n	below and an invite to a Wildlife Release.</li><li>Sponsor $500 - includes all listed\r\n	below and invite to an Educational Talk.</li><li>Contributor $250 - includes all\r\n	listed below and invite to Tour of the Rehab Center.</li><li>Supporter $100 - Newsletter and\r\n	Wildlife Fact Card.</li><li>Advocate $50 – tri-annual\r\n	Newsletters.</li></ul>\r\n	Full-time Student/Senior or\r\n	current PWC Volunteer $25 – tri-annual Newsletters.<br>\r\n<p>You can pay on-line by using <a href="http://www.pacificwildlifecare.org/supportus_membership.html">THIS\r\nLINK</a> then choosing either the subscription payment option or the\r\nsingle annual dues payment option,<br></p><p>OR, you can mail a check to PWC, P.O. Box 1134, Morro Bay, CA&nbsp;\r\n93443-1134 (please note on the check that the payment is for ''Annual\r\nDues''),</p><p>OR, drop off a check with the donation/membership form on the back\r\nof your last newsletter at the Rehab Center in Morro Bay.</p>\r\n<p>Thank you in advance for your support.</p>\r\n<p>Marcelle Bakula, Membership Chair</p>\r\n\r\n\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `voltime`
--

DROP TABLE IF EXISTS `voltime`;
CREATE TABLE IF NOT EXISTS `voltime` (
  `VTID` int(6) NOT NULL AUTO_INCREMENT,
  `VTDT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `MCID` varchar(6) DEFAULT NULL,
  `VolDate` date DEFAULT NULL,
  `VolTime` decimal(5,2) DEFAULT NULL,
  `VolMileage` int(5) DEFAULT NULL,
  `VolCategory` varchar(20) DEFAULT NULL,
  `VolNotes` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`VTID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `voltime`
--

INSERT INTO `voltime` (`VTID`, `VTDT`, `MCID`, `VolDate`, `VolTime`, `VolMileage`, `VolCategory`, `VolNotes`) VALUES
(1, '2014-05-22 14:32:06', 'HAR14', '2014-05-20', 1.00, 1, 'CtrVol', NULL),
(2, '2014-05-22 14:33:11', 'HAR14', '2014-05-20', 2.00, NULL, 'CtrVol', NULL),
(3, '2014-05-22 14:34:55', 'HAR14', '2014-05-21', 3.00, 3, 'CtrVol', NULL),
(4, '2014-05-22 14:36:34', 'HAR14', '2014-05-22', 4.00, 4, 'CtrVol', 'this is a note'),
(5, '2014-05-22 14:38:10', 'HAR14', '2014-05-22', 5.00, 5, 'CtrVol', 'note 1'),
(6, '2014-05-22 14:38:39', 'HAR14', '2014-05-22', 6.00, 6, 'CtrVol', 'note 2'),
(7, '2014-05-22 14:40:12', 'HOL12', '2014-05-22', 1.00, 1, 'CtrVol', NULL),
(8, '2014-05-22 14:42:32', 'COL20', '2014-05-22', 3.00, 3, 'BBRoom', NULL),
(9, '2014-05-22 14:50:56', 'CAR62', '2014-05-22', 1.00, 1, NULL, NULL),
(10, '2014-05-22 14:52:15', 'CAR62', '2014-05-22', 1.00, 1, 'CtrVol', NULL),
(11, '2014-05-22 14:52:15', 'CAR62', '2014-05-22', 2.00, 2, 'CtrVol', NULL),
(12, '2014-05-22 14:53:18', 'CAR62', '2014-05-22', 1.00, 1, 'CtrVol', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
