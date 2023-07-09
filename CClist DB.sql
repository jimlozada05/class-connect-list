-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: localhost    Database: list
-- ------------------------------------------------------
-- Server version	8.0.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `archive_class`
--

DROP TABLE IF EXISTS `archive_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive_class` (
  `archive_class_id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`archive_class_id`),
  KEY `FK_archive_class_user_idx` (`user_id`),
  KEY `FK_archive_class_class_idx` (`class_id`),
  CONSTRAINT `FK_archive_class_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_archive_class_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_note`
--

DROP TABLE IF EXISTS `archive_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive_note` (
  `archive_note_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `member_id` int NOT NULL,
  PRIMARY KEY (`archive_note_id`),
  KEY `FK_archive_note_note_idx` (`note_id`),
  KEY `FK_archive_note_member_idx` (`member_id`),
  CONSTRAINT `FK_archive_note_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_archive_note_note` FOREIGN KEY (`note_id`) REFERENCES `note` (`note_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_user_note`
--

DROP TABLE IF EXISTS `archive_user_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive_user_note` (
  `archive_user_note_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`archive_user_note_id`),
  KEY `FK_archive_user_note_user_note_idx` (`note_id`),
  KEY `FK_archive_user_note_user_idx` (`user_id`),
  CONSTRAINT `FK_archive_user_note_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_archive_user_note_user_note` FOREIGN KEY (`note_id`) REFERENCES `user_note` (`note_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `event_title` varchar(500) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `class_id` int NOT NULL,
  `subject_id` int DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `FK_class_calendar_idx` (`class_id`),
  CONSTRAINT `FK_calendar_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `class_id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(250) NOT NULL,
  `class_code` varchar(15) NOT NULL,
  `creator_id` int DEFAULT NULL,
  `school_year` date NOT NULL,
  PRIMARY KEY (`class_id`),
  KEY `FK_class_user_idx` (`creator_id`),
  CONSTRAINT `FK_class_user` FOREIGN KEY (`creator_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member` (
  `member_id` int NOT NULL AUTO_INCREMENT,
  `member_type` int NOT NULL,
  `class_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`member_id`),
  KEY `FK_member_user_idx` (`user_id`),
  KEY `FK_member_class_idx` (`class_id`),
  CONSTRAINT `FK_member_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_member_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `note` (
  `note_id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `subject_id` int DEFAULT NULL,
  `post_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `note_title` varchar(500) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `link` varchar(2083) DEFAULT NULL,
  PRIMARY KEY (`note_id`),
  KEY `FK_note_class_idx` (`class_id`),
  CONSTRAINT `FK_note_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `note_calendar`
--

DROP TABLE IF EXISTS `note_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `note_calendar` (
  `note_calendar_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  PRIMARY KEY (`note_calendar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `note_history`
--

DROP TABLE IF EXISTS `note_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `note_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `pending_note_id` int NOT NULL,
  `prev_subject_id` int DEFAULT NULL,
  `prev_due_date` date DEFAULT NULL,
  `prev_due_time` time DEFAULT NULL,
  `prev_note_title` varchar(500) NOT NULL,
  `prev_description` varchar(5000) DEFAULT NULL,
  `change_date` datetime NOT NULL,
  `prev_link` varchar(2083) DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `FK_note_history_note_idx` (`note_id`),
  CONSTRAINT `FK_note_history_note` FOREIGN KEY (`note_id`) REFERENCES `note` (`note_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pending_note`
--

DROP TABLE IF EXISTS `pending_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pending_note` (
  `pending_note_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `post_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `note_title` varchar(500) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `link` varchar(2083) DEFAULT NULL,
  `pending_date` date NOT NULL,
  `status` int NOT NULL,
  `member_id` int NOT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`pending_note_id`),
  KEY `FK_pending_note_note_idx` (`note_id`),
  CONSTRAINT `FK_pending_note_note` FOREIGN KEY (`note_id`) REFERENCES `note` (`note_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `subject_id` int NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(150) NOT NULL,
  `subject_details` varchar(2000) DEFAULT NULL,
  `professor` varchar(200) DEFAULT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `FK_subject_class_idx` (`class_id`),
  CONSTRAINT `FK_subject_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subject_calendar`
--

DROP TABLE IF EXISTS `subject_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_calendar` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `event_title` varchar(500) NOT NULL,
  `event_details` varchar(5000) DEFAULT NULL,
  `event_from_date` datetime NOT NULL,
  `event_to_date` datetime NOT NULL,
  `subject_id` int NOT NULL,
  `subject_schedule_id` int NOT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `FK_subject_calendar_class_idx` (`class_id`),
  KEY `FK_subject_calendar_subject_idx` (`subject_id`),
  CONSTRAINT `FK_subject_calendar_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_subject_calendar_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subject_schedule`
--

DROP TABLE IF EXISTS `subject_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_schedule` (
  `subject_schedule_id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `start_date` date NOT NULL,
  `occurrence` int DEFAULT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`subject_schedule_id`),
  KEY `FK_subject_schedule_subject_idx` (`subject_id`),
  KEY `FK_subject_schedule_class_idx` (`class_id`),
  CONSTRAINT `FK_subject_schedule_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_subject_schedule_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unenroll`
--

DROP TABLE IF EXISTS `unenroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unenroll` (
  `unenroll_id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `subject_id` int NOT NULL,
  PRIMARY KEY (`unenroll_id`),
  KEY `class_subjects_idx` (`subject_id`),
  KEY `FK_unenroll_member_idx` (`member_id`),
  CONSTRAINT `FK_unenroll_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_unenroll_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `contact_no` varchar(12) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `m_name` varchar(50) DEFAULT NULL,
  `l_name` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_calendar`
--

DROP TABLE IF EXISTS `user_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_calendar` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `event_title` varchar(500) NOT NULL,
  `event_details` varchar(5000) DEFAULT NULL,
  `event_from_date` datetime NOT NULL,
  `event_to_date` datetime NOT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `FK_user_calendar_user_idx` (`user_id`),
  CONSTRAINT `FK_user_calendar_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_class_note`
--

DROP TABLE IF EXISTS `user_class_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_class_note` (
  `user_class_note_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`user_class_note_id`),
  KEY `FK_user_class_note_user_idx` (`user_id`),
  CONSTRAINT `FK_user_class_note_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_note`
--

DROP TABLE IF EXISTS `user_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_note` (
  `note_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `post_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `note_title` varchar(500) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`note_id`),
  KEY `FK_user_note_user_idx` (`user_id`),
  CONSTRAINT `FK_user_note_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_note_calendar`
--

DROP TABLE IF EXISTS `user_note_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_note_calendar` (
  `note_calendar_id` int NOT NULL AUTO_INCREMENT,
  `note_id` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  PRIMARY KEY (`note_calendar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-02-12  8:02:48
