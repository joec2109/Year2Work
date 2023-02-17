-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 26, 2022 at 08:50 PM
-- Server version: 8.0.23
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moodle`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `assignmentId` int NOT NULL,
  `courseId` int NOT NULL,
  `assignmentDetails` varchar(50) DEFAULT NULL,
  `dueDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assignmentgrade`
--

CREATE TABLE `assignmentgrade` (
  `assignmentId` int NOT NULL,
  `fileId` int NOT NULL,
  `grade` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `courseId` int NOT NULL,
  `courseName` varchar(50) NOT NULL,
  `courseLeader` varchar(50) NOT NULL,
  `courseProgramme` varchar(100) NOT NULL,
  `courseFee` double DEFAULT NULL,
  `courseSalary` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`courseId`, `courseName`, `courseLeader`, `courseProgramme`, `courseFee`, `courseSalary`) VALUES
(1, 'Python Beginnner', 'SOmeone', 'Programme', 1200, 45000),
(2, 'HTML and CSS', 'SOmeone', 'course programme', 1300, 45000),
(3, 'C# Beginner', 'Lovely Kapil', 'Big programmer', 1400, 50000);

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `fileId` int NOT NULL,
  `fileName` varchar(50) NOT NULL,
  `authorId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`fileId`, `fileName`, `authorId`) VALUES
(1, '/opt/lampp/temp/phpTLHhW2', 20220001),
(2, '/opt/lampp/temp/phpxYcFBZ', 20220001),
(3, '/opt/lampp/temp/phpzUB2sB', 20220001),
(4, '/opt/lampp/temp/phphwZXIz', 20220001);

-- --------------------------------------------------------

--
-- Table structure for table `lecture`
--

CREATE TABLE `lecture` (
  `lectureId` int NOT NULL,
  `courseId` int NOT NULL,
  `lectureDescription` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lecture`
--

INSERT INTO `lecture` (`lectureId`, `courseId`, `lectureDescription`) VALUES
(1, 3, 'First C# lecture'),
(2, 3, 'second lecture'),
(3, 3, 'third lecture'),
(4, 3, '4th'),
(5, 1, 'CHUJ CI NA PIZDDE'),
(6, 1, 'CHUJ CI NA PIZDDE');

-- --------------------------------------------------------

--
-- Table structure for table `lectureresource`
--

CREATE TABLE `lectureresource` (
  `lectureId` int NOT NULL,
  `fileId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lectureresource`
--

INSERT INTO `lectureresource` (`lectureId`, `fileId`) VALUES
(2, 2),
(3, 1),
(4, 2),
(5, 3),
(6, 4);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentId` int NOT NULL,
  `fees` int DEFAULT NULL,
  `personalTutorId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentId`, `fees`, `personalTutorId`) VALUES
(20220022, 3900, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studentcourse`
--

CREATE TABLE `studentcourse` (
  `studentCourseId` int NOT NULL,
  `studentId` int NOT NULL,
  `courseId` int NOT NULL,
  `courseApproved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `studentcourse`
--

INSERT INTO `studentcourse` (`studentCourseId`, `studentId`, `courseId`, `courseApproved`) VALUES
(9, 20220022, 1, 1),
(11, 20220022, 2, 1),
(12, 20220022, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tutor`
--

CREATE TABLE `tutor` (
  `tutorId` int NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `salary` int DEFAULT NULL,
  `contractType` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Dumping data for table `tutor`
--

INSERT INTO `tutor` (`tutorId`, `department`, `salary`, `contractType`) VALUES
(20220024, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tutorcourse`
--

CREATE TABLE `tutorcourse` (
  `tutorcourseId` int NOT NULL,
  `tutorId` int DEFAULT NULL,
  `courseId` int DEFAULT NULL,
  `courseApproved` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(15) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(15) NOT NULL,
  `address` varchar(60) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `userType` varchar(20) NOT NULL DEFAULT 'student',
  `userAuthorised` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `surname`, `phoneNumber`, `email`, `password`, `address`, `dateOfBirth`, `userType`, `userAuthorised`) VALUES
(20220001, 'Diego', 'Caseroles', 'asd', 'ads', '1234', 'asd', '2022-01-12', 'admin', 1),
(20220022, 'Noyal Babu', 'Babu', '07737048460', 'noyalbabu02@gmail.com', '147', 'Room 4, 58 Carrisbrooke Road', '2002-01-11', 'student', 1),
(20220024, 'Betty ', 'Joseph', '07737048460', 'noyalbabu02@gmail.com', '158', 'Room 4, 58 Carrisbrooke Road', '1677-11-25', 'tutor', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`courseId`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`fileId`);

--
-- Indexes for table `lecture`
--
ALTER TABLE `lecture`
  ADD PRIMARY KEY (`lectureId`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentId`);

--
-- Indexes for table `studentcourse`
--
ALTER TABLE `studentcourse`
  ADD PRIMARY KEY (`studentCourseId`),
  ADD KEY `student_id` (`studentId`),
  ADD KEY `course_id` (`courseId`);

--
-- Indexes for table `tutor`
--
ALTER TABLE `tutor`
  ADD PRIMARY KEY (`tutorId`);

--
-- Indexes for table `tutorcourse`
--
ALTER TABLE `tutorcourse`
  ADD PRIMARY KEY (`tutorcourseId`),
  ADD KEY `tutorId` (`tutorId`),
  ADD KEY `courseId` (`courseId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `courseId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `fileId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecture`
--
ALTER TABLE `lecture`
  MODIFY `lectureId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `studentcourse`
--
ALTER TABLE `studentcourse`
  MODIFY `studentCourseId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tutorcourse`
--
ALTER TABLE `tutorcourse`
  MODIFY `tutorcourseId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20220025;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tutorcourse`
--
ALTER TABLE `tutorcourse`
  ADD CONSTRAINT `tutorcourse_ibfk_1` FOREIGN KEY (`tutorId`) REFERENCES `tutor` (`tutorId`),
  ADD CONSTRAINT `tutorcourse_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
