-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2024 at 07:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aaccupmonitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `accreditationvisits`
--

CREATE TABLE `accreditationvisits` (
  `VisitID` int(11) NOT NULL,
  `ProgramID` int(11) DEFAULT NULL,
  `VisitDate` date DEFAULT NULL,
  `VisitType` enum('Initial Accreditation','Reaccreditation') DEFAULT NULL,
  `AssessmentTeam` varchar(255) DEFAULT NULL,
  `VisitOutcome` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accreditationvisits`
--

INSERT INTO `accreditationvisits` (`VisitID`, `ProgramID`, `VisitDate`, `VisitType`, `AssessmentTeam`, `VisitOutcome`) VALUES
(1, 0, '2024-05-10', 'Reaccreditation', 'Team Enteng', 'Goodie');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `DepartmentID` int(11) NOT NULL,
  `abbreviation` varchar(25) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `UniversityID` int(11) DEFAULT NULL,
  `ContactInformation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`DepartmentID`, `abbreviation`, `Name`, `UniversityID`, `ContactInformation`) VALUES
(11, 'COE', 'College of Engineering', 1, 'coe@jrmsu');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `DocumentID` int(11) NOT NULL,
  `DocumentName` varchar(255) NOT NULL,
  `UploadedBy` int(11) DEFAULT NULL,
  `UploadDate` date DEFAULT NULL,
  `FileURL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`DocumentID`, `DocumentName`, `UploadedBy`, `UploadDate`, `FileURL`) VALUES
(3, 'trial and error', 1, '2024-05-11', 'uploads/Log Report.docx'),
(4, 'rit', 1, '2024-05-12', 'uploads/iyot.docx');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageID` int(11) NOT NULL,
  `senderID` int(255) NOT NULL,
  `receiverID` int(255) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`messageID`, `senderID`, `receiverID`, `message`, `timestamp`) VALUES
(1, 1, 0, 'Hi', '2024-05-16 23:23:28'),
(2, 1, 0, 'Yaoa na ka', '2024-05-16 23:30:51'),
(3, 1, 0, 'Trial napud ni', '2024-05-17 00:26:22'),
(4, 1, 1, 'Hi', '2024-05-17 00:26:40'),
(5, 0, 1, 'Hilom diha', '2024-05-17 00:32:31'),
(6, 0, 0, 'Hoy\r\n', '2024-05-17 00:32:38');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `ProgramID` int(11) NOT NULL,
  `code` varchar(25) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `Level` varchar(50) DEFAULT NULL,
  `AccreditationStatus` enum('Candidate','Accredited') DEFAULT NULL,
  `AccreditationLevel` enum('Level I','Level II','Level III') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`ProgramID`, `code`, `Name`, `DepartmentID`, `Level`, `AccreditationStatus`, `AccreditationLevel`) VALUES
(1, 'BSCpE', 'Bachelor of Science in Computer Engineering', 11, 'I', 'Candidate', 'Level I');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `TaskID` int(11) NOT NULL,
  `ProgramID` int(11) DEFAULT NULL,
  `Description` varchar(255) NOT NULL,
  `Assignee` varchar(255) DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `Status` enum('Pending','In Progress','Completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`TaskID`, `ProgramID`, `Description`, `Assignee`, `DueDate`, `Status`) VALUES
(1, 0, 'Please pagdugang ug gwapo sa inyong program. Akong gipatas-an ug sulay para lantawon tong isa ka feature. Unta mugana kay taas na kaayo ni', 'RHett', '2024-05-16', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `UniversityID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `ContactInformation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`UniversityID`, `Name`, `Location`, `ContactInformation`) VALUES
(1, 'Main Campus', 'Dapitan City', 'main@jrmsu.edu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Administrator','Faculty','Assessor') NOT NULL,
  `Campus` int(11) DEFAULT NULL,
  `programAffiliated` int(11) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `uniqueID` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `firstName`, `lastName`, `email`, `Password`, `Role`, `Campus`, `programAffiliated`, `img`, `uniqueID`) VALUES
(0, 'James', 'Vincent', 'admin', 'admin', 'Administrator', 1, 1, NULL, '24-A-00001'),
(1, 'Rhett Wayne', 'Manumbag', 'faculty', 'faculty', 'Faculty', 1, 1, NULL, '24-F-00002'),
(12, 'Rranian', 'Rulona', 'assessor', 'assessor', 'Assessor', 1, 1, NULL, '24-S-00003'),
(19, 'jamis', 'wow', 'tryadd@user', 'trial', 'Faculty', 1, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accreditationvisits`
--
ALTER TABLE `accreditationvisits`
  ADD PRIMARY KEY (`VisitID`),
  ADD KEY `ProgramID` (`ProgramID`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`DepartmentID`),
  ADD KEY `UniversityID` (`UniversityID`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`DocumentID`),
  ADD KEY `UploadedBy` (`UploadedBy`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageID`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`ProgramID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `ProgramID` (`ProgramID`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`UniversityID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `uniqueID` (`uniqueID`),
  ADD KEY `FK_Campus_UniversityID` (`Campus`),
  ADD KEY `programAffiliated` (`programAffiliated`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accreditationvisits`
--
ALTER TABLE `accreditationvisits`
  MODIFY `VisitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `DocumentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `ProgramID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `UniversityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accreditationvisits`
--
ALTER TABLE `accreditationvisits`
  ADD CONSTRAINT `accreditationvisits_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`UniversityID`) REFERENCES `universities` (`UniversityID`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`UploadedBy`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `departments` (`DepartmentID`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `programs` (`ProgramID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_Campus_UniversityID` FOREIGN KEY (`Campus`) REFERENCES `universities` (`UniversityID`),
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`programAffiliated`) REFERENCES `programs` (`ProgramID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
