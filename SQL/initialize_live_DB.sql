-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--

-- To ONLY be used to perform initial set up of the 
-- production datbase for MbrDB and VolDB

-- CAUTION:  THIS SCRIPT SHOULD BE DELETED ONCE IT HAS BEEN
-- SUCCESSFULLY RUN AND THE ALL DATABASE TABLES VALIDATED AS
-- FULLY FUNCTIONAL AND AVAILABLE.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`SeqNo`, `UserID`, `Password`, `Role`, `Notes`) VALUES
(1, 'jdoe@setup.com', 'raptor', 'admin', 'user id for admin user');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
