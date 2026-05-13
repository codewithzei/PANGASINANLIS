-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 11, 2026 at 05:06 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pangasinan-lis`
--

-- --------------------------------------------------------

--
-- Table structure for table `agendas`
--

CREATE TABLE `agendas` (
  `agenda_id` int NOT NULL,
  `document_id` int NOT NULL,
  `committee_id` int NOT NULL,
  `agenda_number` varchar(100) NOT NULL,
  `agenda_type` varchar(100) NOT NULL,
  `chairperson` varchar(150) NOT NULL,
  `agenda_date` date NOT NULL,
  `agenda_time` time NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `log_type` varchar(20) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `log_type`, `module`, `action`, `record_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'CREATE', NULL, 'Created a new role named: Sample Audit User', '::1', '2026-04-23 11:36:10'),
(2, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 8, 'Deleted role with ID: 8', '::1', '2026-04-23 11:54:41'),
(3, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 7, 'Deleted role: Unknown Role', '::1', '2026-04-23 12:00:21'),
(4, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 6, 'Deleted role: Unknown Role', '::1', '2026-04-23 12:24:17'),
(5, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 5, 'Deleted role: Unknown Role', '::1', '2026-04-23 12:28:34'),
(6, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 4, 'Deleted role: Plenary', '::1', '2026-04-23 12:40:32'),
(7, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'UPDATE', 3, 'Updated role: Committees', '::1', '2026-04-23 12:44:51'),
(8, 1, 'DATA_CHANGE', 'USER_MGMT', 'UPDATE', 2, 'Updated user: admin (Zyron Manangan)', '::1', '2026-04-23 12:46:01'),
(9, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 3, 'Updated document type: File3', '::1', '2026-04-23 12:47:13'),
(10, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 3, 'Updated document type: File34', '::1', '2026-04-23 12:58:02'),
(11, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 2, 'Updated municipality/city: Malasiquis', '::1', '2026-04-23 12:59:05'),
(12, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'DELETE', 2, 'Deleted municipality/city: ID #2', '::1', '2026-04-23 12:59:11'),
(13, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 2, 'Updated document status: Approveds', '::1', '2026-04-23 12:59:52'),
(14, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'DELETE', 2, 'Deleted document status: ID #2', '::1', '2026-04-23 12:59:55'),
(15, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'DELETE', 8, 'Deleted role: Sample Audit User', '::1', '2026-04-23 13:00:28'),
(16, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'UPDATE', 2, 'Updated external office: Office123', '::1', '2026-04-23 13:01:23'),
(17, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'DELETE', 2, 'Deleted external office: Office123', '::1', '2026-04-23 13:01:24'),
(18, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'UPDATE', 2, 'Updated hospital: Hospitals', '::1', '2026-04-23 13:01:43'),
(19, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'DELETE', 2, 'Deleted hospital: Hospitals', '::1', '2026-04-23 13:01:44'),
(20, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'UPDATE', 5, 'Updated source type: Clients', '::1', '2026-04-23 13:02:00'),
(21, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'DELETE', 5, 'Deleted source type: Clients', '::1', '2026-04-23 13:02:02'),
(22, 2, 'DATA_CHANGE', 'ROUTING_OPTION_MGMT', 'UPDATE', 2, 'Updated routing option: Plenarys', '::1', '2026-04-23 13:02:16'),
(23, 2, 'DATA_CHANGE', 'ROUTING_OPTION_MGMT', 'DELETE', 2, 'Deleted routing option: Plenarys', '::1', '2026-04-23 13:02:17'),
(24, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'UPDATE', 2, 'Updated communication category: Records Servicess', '::1', '2026-04-23 13:02:43'),
(25, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'DELETE', 2, 'Deleted communication category: Records Servicess', '::1', '2026-04-23 13:02:44'),
(26, 2, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 3, 'Updated requirement: Test123', '::1', '2026-04-23 13:03:08'),
(27, 2, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'DELETE', 3, 'Deleted requirement: ID #3', '::1', '2026-04-23 13:03:10'),
(28, 2, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Okay', '::1', '2026-04-23 13:03:45'),
(29, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: 1', '::1', '2026-04-23 13:04:02'),
(30, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: 1', '::1', '2026-04-23 13:04:07'),
(31, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'CREATE', NULL, 'Created source type: 1', '::1', '2026-04-23 13:04:12'),
(32, 2, 'DATA_CHANGE', 'ROUTING_OPTION_MGMT', 'CREATE', NULL, 'Created routing option: 1', '::1', '2026-04-23 13:04:18'),
(33, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'CREATE', NULL, 'Created communication category: 1', '::1', '2026-04-23 13:04:22'),
(34, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: 1', '::1', '2026-04-23 13:04:33'),
(35, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'CREATE', NULL, 'Created municipality/city: 1', '::1', '2026-04-23 13:04:37'),
(36, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: 1', '::1', '2026-04-23 13:04:41'),
(37, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'CREATE', 9, 'Created new role: 1', '::1', '2026-04-23 13:04:45'),
(38, 1, 'DATA_CHANGE', 'USER_MGMT', 'CREATE', 3, 'Created new user: client (Client User)', '::1', '2026-04-23 13:06:18'),
(39, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'DELETE', 3, 'Deleted document status: 1', '::1', '2026-04-23 13:12:00'),
(40, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'DELETE', 3, 'Deleted municipality/city: 1', '::1', '2026-04-23 13:12:07'),
(41, 2, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'DELETE', 4, 'Deleted requirement: Okay', '::1', '2026-04-23 13:12:20'),
(42, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'UPDATE', 9, 'Updated role: 2', '::1', '2026-04-23 13:21:15'),
(43, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Approved', '::1', '2026-04-28 12:07:12'),
(44, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Withdrawn', '::1', '2026-04-28 12:12:20'),
(45, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Deferred', '::1', '2026-04-28 12:12:29'),
(46, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Noted', '::1', '2026-04-28 12:12:37'),
(47, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Under Processing', '::1', '2026-04-28 12:12:49'),
(48, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Lay on the Table', '::1', '2026-04-28 12:12:59'),
(49, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Referred', '::1', '2026-04-28 12:13:19'),
(50, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Remanded', '::1', '2026-04-28 12:13:25'),
(51, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: Returned to Plenary', '::1', '2026-04-28 12:13:36'),
(52, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: For Committee Report', '::1', '2026-04-28 12:14:06'),
(53, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'UPDATE', 5, 'Updated source type: Client', '::1', '2026-04-28 13:13:49'),
(54, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'DELETE', 6, 'Deleted source type: 1', '::1', '2026-04-28 13:13:52'),
(55, 2, NULL, 'Documents', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00001', NULL, '2026-04-28 13:32:59'),
(56, 2, 'INFO', 'Documents', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00002', '::1', '2026-04-28 13:41:46'),
(57, 2, 'DATA_CHANGE', 'ROUTING_OPTION_MGMT', 'CREATE', NULL, 'Created routing option: Client', '::1', '2026-04-28 14:48:04'),
(58, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'UPDATE', 2, 'Updated communication category: Personnel Services', '::1', '2026-04-28 14:50:28'),
(59, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'UPDATE', 3, 'Updated communication category: Posting', '::1', '2026-04-28 14:50:38'),
(60, 2, 'DATA_CHANGE', 'COMM_CATEGORY_MGMT', 'CREATE', NULL, 'Created communication category: Records Services', '::1', '2026-04-28 14:50:49'),
(61, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 4, 'Updated document status: Approvedsss', '::1', '2026-04-28 14:55:16'),
(62, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 2, 'Updated document status: Approved', '::1', '2026-04-28 14:55:19'),
(63, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 5, 'Updated document status: Withdrawns', '::1', '2026-04-28 14:55:25'),
(64, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 3, 'Updated document status: Withdrawn', '::1', '2026-04-28 14:55:38'),
(65, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 6, 'Updated document status: Deferreds', '::1', '2026-04-28 14:55:50'),
(66, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 4, 'Updated document status: Deferred', '::1', '2026-04-28 14:55:59'),
(67, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 7, 'Updated document status: Noteds', '::1', '2026-04-28 14:56:06'),
(68, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 5, 'Updated document status: Noted', '::1', '2026-04-28 14:56:11'),
(69, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 6, 'Updated document status: Under Processings', '::1', '2026-04-28 14:56:26'),
(70, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 9, 'Updated document status: Lay on the Tables', '::1', '2026-04-28 14:56:55'),
(71, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 7, 'Updated document status: Lay on the Table', '::1', '2026-04-28 14:57:03'),
(72, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 10, 'Updated document status: Referreds', '::1', '2026-04-28 14:57:09'),
(73, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 8, 'Updated document status: Referred', '::1', '2026-04-28 14:57:20'),
(74, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'DELETE', 10, 'Deleted document status: Referreds', '::1', '2026-04-28 14:57:24'),
(75, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 9, 'Updated document status: Remandeds', '::1', '2026-04-28 14:58:13'),
(76, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 10, 'Updated document status: Return to Plenarys', '::1', '2026-04-28 14:58:27'),
(77, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 10, 'Updated document status: Returned to Plenarys', '::1', '2026-04-28 14:58:35'),
(78, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 11, 'Updated document status: For Committee Reports', '::1', '2026-04-28 14:58:49'),
(79, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'DELETE', 13, 'Deleted document status: For Committee Report', '::1', '2026-04-28 14:58:57'),
(80, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'DELETE', 12, 'Deleted document status: Returned to Plenary', '::1', '2026-04-28 14:59:01'),
(81, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 6, 'Updated document status: Under Processing', '::1', '2026-04-28 14:59:07'),
(82, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 9, 'Updated document status: Remanded', '::1', '2026-04-28 14:59:11'),
(83, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 11, 'Updated document status: For Committee Report', '::1', '2026-04-28 14:59:44'),
(84, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'UPDATE', 10, 'Updated document status: Returned to Plenary', '::1', '2026-04-28 14:59:48'),
(85, 1, 'DATA_CHANGE', 'ROLE_MGMT', 'UPDATE', 3, 'Updated role: Committee', '::1', '2026-04-28 15:00:30'),
(86, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'CREATE', NULL, 'Created municipality/city: Agno', '::1', '2026-04-29 05:00:34'),
(87, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 4, 'Updated municipality/city: Anda', '::1', '2026-04-29 05:01:16'),
(88, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 1, 'Updated municipality/city: Agno', '::1', '2026-04-29 05:01:21'),
(89, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 2, 'Updated municipality/city: Aguilar', '::1', '2026-04-29 05:01:33'),
(90, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 3, 'Updated municipality/city: Alcala', '::1', '2026-04-29 05:01:44'),
(91, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 1, 'Updated document type: Provincial Tax Ordinance', '::1', '2026-04-29 05:40:16'),
(92, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 2, 'Updated document type: Provincial Resolution', '::1', '2026-04-29 05:42:06'),
(93, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 3, 'Updated document type: Provincial Appropriation Ordinance for Supplemental Budget', '::1', '2026-04-29 05:43:05'),
(94, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'UPDATE', 4, 'Updated document type: Provincial Appropriation Ordinance for Annual Budget', '::1', '2026-04-29 05:44:02'),
(95, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Provincial Ordinance', '::1', '2026-04-29 05:44:17'),
(96, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Municipal/City Ordinance', '::1', '2026-04-29 05:44:25'),
(97, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Municipal/City Resolution', '::1', '2026-04-29 05:44:36'),
(98, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Municipal/City Appropriation Ordinance for Supplemental Budget', '::1', '2026-04-29 05:44:46'),
(99, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Municipal/City Appropriation Ordinance for Annual Budget', '::1', '2026-04-29 05:44:56'),
(100, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Communication', '::1', '2026-04-29 05:45:06'),
(101, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Administrative Cases', '::1', '2026-04-29 05:45:17'),
(102, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Complaint', '::1', '2026-04-29 05:45:25'),
(103, 1, 'DATA_CHANGE', 'DOC_TYPE_MGMT', 'CREATE', NULL, 'Created document type: Others', '::1', '2026-04-29 05:46:16'),
(104, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'UPDATE', 1, 'Updated external office: Capitol Resort Hotel Operations Division', '::1', '2026-04-29 05:48:02'),
(105, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'UPDATE', 3, 'Updated external office: General Services Office', '::1', '2026-04-29 05:48:11'),
(106, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Human Resource Management and Development Office', '::1', '2026-04-29 05:48:18'),
(107, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Internal Audit Division', '::1', '2026-04-29 05:48:27'),
(108, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Management Information Services Office', '::1', '2026-04-29 05:48:35'),
(109, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Pangasinan Polytechnic College', '::1', '2026-04-29 05:48:42'),
(110, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Pangasinan Provincial Environment and Natural Resources Office', '::1', '2026-04-29 05:49:06'),
(111, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Pangasinan Provincial Jail', '::1', '2026-04-29 05:49:16'),
(112, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Pangasinan Public Employment Services Office', '::1', '2026-04-29 05:49:23'),
(113, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Accounting Office', '::1', '2026-04-29 05:49:30'),
(114, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Agriculture Office', '::1', '2026-04-29 05:49:37'),
(115, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Archives and Records Center', '::1', '2026-04-29 05:49:46'),
(116, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Assessment Office', '::1', '2026-04-29 05:49:56'),
(117, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Board Secretary', '::1', '2026-04-29 05:50:05'),
(118, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Budget Office', '::1', '2026-04-29 05:50:11'),
(119, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Community Development and Training Office', '::1', '2026-04-29 05:50:20'),
(120, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Disaster Risk Reduction and Management Office', '::1', '2026-04-29 05:50:26'),
(121, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Economic Development and Investment Promotion Office', '::1', '2026-04-29 05:54:03'),
(122, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Engineering Office', '::1', '2026-04-29 05:54:20'),
(123, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Governor\'s Office', '::1', '2026-04-29 05:54:33'),
(124, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Health Office', '::1', '2026-04-29 05:54:42'),
(125, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Hospital Management Services Office', '::1', '2026-04-29 05:54:51'),
(126, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Human Settlements and Urban Development Authority', '::1', '2026-04-29 05:55:02'),
(127, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Information Office', '::1', '2026-04-29 05:55:13'),
(128, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Legal Office', '::1', '2026-04-29 05:55:24'),
(129, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Library Office', '::1', '2026-04-29 05:55:33'),
(130, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Planning and Development Office', '::1', '2026-04-29 05:55:45'),
(131, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Population Cooperative and Livelihood Development Office', '::1', '2026-04-29 05:55:55'),
(132, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Social Welfare and Development Office', '::1', '2026-04-29 05:56:06'),
(133, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Treasurer\'s Office', '::1', '2026-04-29 05:56:22'),
(134, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Tourism and Cultural Affairs Office', '::1', '2026-04-29 05:56:31'),
(135, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Vice Governor\'s Office', '::1', '2026-04-29 05:56:42'),
(136, 2, 'DATA_CHANGE', 'EXT_OFFICE_MGMT', 'CREATE', NULL, 'Created external office: Provincial Veterinary Office', '::1', '2026-04-29 05:56:53'),
(137, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'UPDATE', 1, 'Updated hospital: Asingan Community Hospital', '::1', '2026-04-29 06:14:40'),
(138, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'UPDATE', 2, 'Updated hospital: Bayambang District Hospital', '::1', '2026-04-29 06:15:10'),
(139, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'UPDATE', 3, 'Updated hospital: Bolinao Community Hospital', '::1', '2026-04-29 06:15:19'),
(140, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Dasol Community Hospital', '::1', '2026-04-29 06:15:27'),
(141, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Eastern Pangasinan District Hospital', '::1', '2026-04-29 06:15:36'),
(142, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Lingayen District Hospital', '::1', '2026-04-29 06:17:44'),
(143, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Manaoag Community Hospital', '::1', '2026-04-29 06:17:52'),
(144, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Mangatarem District Hospital', '::1', '2026-04-29 06:18:01'),
(145, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Mapandan Community Hospital', '::1', '2026-04-29 06:18:24'),
(146, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Pangasinan Provincial Hospital', '::1', '2026-04-29 06:18:32'),
(147, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Pozorrubio Community Hospital', '::1', '2026-04-29 06:18:39'),
(148, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Umingan Community Hospital', '::1', '2026-04-29 06:18:46'),
(149, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Urdaneta District Hospital', '::1', '2026-04-29 06:18:55'),
(150, 2, 'DATA_CHANGE', 'HOSPITAL_MGMT', 'CREATE', NULL, 'Created hospital: Western Pangasinan District Hospital', '::1', '2026-04-29 06:19:02'),
(151, 2, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Tansmmital Letter', '::1', '2026-04-29 06:20:03'),
(152, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 5, 'Updated requirement: Municipal/City Ordinance', '::1', '2026-04-29 08:36:03'),
(153, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Transmmital Letter', '::1', '2026-04-29 12:27:55'),
(154, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Transmmital Letter', '::1', '2026-04-29 12:28:09'),
(155, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 1, 'Updated requirement: Transmmital Letter', '::1', '2026-04-29 12:30:24'),
(156, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 1, 'Updated requirement: Transmmital Letter', '::1', '2026-04-29 12:33:07'),
(157, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 1, 'Updated requirement: Transmmital Letter', '::1', '2026-04-29 12:33:57'),
(158, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 1, 'Updated requirement: Transmmital Letter', '::1', '2026-04-29 12:34:03'),
(159, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 1, 'Updated requirement: Transmmital Letter', '::1', '2026-04-29 12:37:33'),
(160, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 2, 'Updated requirement: Certificate Posting', '::1', '2026-04-29 12:44:15'),
(161, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Publication/Affidavit of Publication', '::1', '2026-04-29 12:44:42'),
(162, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Minutes of Public Hearing', '::1', '2026-04-29 12:45:18'),
(163, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Attendance Sheet', '::1', '2026-04-29 12:46:29'),
(164, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Copy of Old Ordinance', '::1', '2026-04-29 12:46:48'),
(165, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Certification from Local Finance Committee (LFC)', '::1', '2026-04-29 12:47:01'),
(166, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Endorsement from Department of Human Settlements and Urban Development (DHSUD)', '::1', '2026-04-29 12:47:18'),
(167, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Sangguniang Bayan (SB) Resolution', '::1', '2026-04-29 12:47:34'),
(168, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Department of Agriculture (DA) Certification for Land Use', '::1', '2026-04-29 12:50:59'),
(169, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Department of Agrarian Reform (DAR) Certification', '::1', '2026-04-29 12:51:13'),
(170, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Transfer Certificate of Title (TCT)', '::1', '2026-04-29 12:51:26'),
(171, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Declaration of Real Property', '::1', '2026-04-29 12:51:39'),
(172, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Appropriation Ordinance Number', '::1', '2026-04-29 12:53:17'),
(173, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Supplemental Budget Number', '::1', '2026-04-29 12:56:00'),
(174, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Appropriation Ordinance', '::1', '2026-04-29 12:56:15'),
(175, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Land Bank of the Philippines (LBP) Form/Source of Fund', '::1', '2026-04-29 12:56:27'),
(176, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Resolution Number', '::1', '2026-04-29 12:56:43'),
(177, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Supplemental Investment Program (SIP) Form', '::1', '2026-04-29 12:57:05'),
(178, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Municipal Development Council (MDC) Resolution', '::1', '2026-04-29 12:57:47'),
(179, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 3, 'Updated requirement: Annual Budget for Calendar Year', '::1', '2026-04-29 13:06:00'),
(180, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'UPDATE', 4, 'Updated requirement: Appropriation Number', '::1', '2026-04-29 13:06:22'),
(181, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Table of Contents', '::1', '2026-04-29 13:06:41'),
(182, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Budget Message', '::1', '2026-04-29 13:07:02'),
(183, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Plantilla of Personnel', '::1', '2026-04-29 13:07:24'),
(184, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Budget Review Matrix', '::1', '2026-04-29 13:07:40'),
(185, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Local Expenditure Program', '::1', '2026-04-29 13:07:53'),
(186, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Annual Investment Plan (AIP)', '::1', '2026-04-29 13:08:16'),
(187, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Department of Interior and Local Government (DILG) approved GAD Plan', '::1', '2026-04-29 13:08:29'),
(188, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Office of City Defense (OCD) Certification', '::1', '2026-04-29 13:11:58'),
(189, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Peace and Order Plan', '::1', '2026-04-29 13:12:12'),
(190, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: PPAs/Plans', '::1', '2026-04-29 13:12:26'),
(191, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Executive Committee Resolution', '::1', '2026-04-29 13:12:37'),
(192, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Attachments', '::1', '2026-04-29 13:12:52'),
(193, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Agency/Office', '::1', '2026-04-29 13:13:02'),
(194, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Subject Matter', '::1', '2026-04-29 13:13:12'),
(195, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Memorandum of Agreement (MOA)', '::1', '2026-04-29 13:13:22'),
(196, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Endorsement Letter', '::1', '2026-04-29 13:13:39'),
(197, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Opinion', '::1', '2026-04-29 13:13:57'),
(198, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Legal Opinion', '::1', '2026-04-29 13:14:42'),
(199, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Certification of Availability of Funds', '::1', '2026-04-29 13:14:49'),
(200, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Resolution', '::1', '2026-04-29 13:14:59'),
(201, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Draft Memorandum of Agreement (MOA)', '::1', '2026-04-29 13:15:09'),
(202, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Draft Ordinance', '::1', '2026-04-29 13:15:26'),
(203, 1, 'DATA_CHANGE', 'CHECKLIST_MGMT', 'CREATE', NULL, 'Created requirement: Details', '::1', '2026-04-29 13:15:37'),
(204, 2, 'DATA_CHANGE', 'ROUTING_OPTION_MGMT', 'CREATE', NULL, 'Created routing option: Noted', '::1', '2026-04-29 14:49:47'),
(205, 2, 'ROUTING', 'DCMT_RTNG', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00001', '::1', '2026-05-01 13:29:50'),
(206, 1, 'DATA_CHANGE', 'DOC_STATUS_MGMT', 'CREATE', NULL, 'Created document status: For Opinion', '::1', '2026-05-01 14:13:49'),
(207, 2, 'ROUTING', 'DCMT_RTNG', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00002', '::1', '2026-05-01 15:44:32'),
(208, 2, 'ROUTING', 'DCMT_RTNG', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00003', '::1', '2026-05-01 16:13:16'),
(209, 1, 'DATA_CHANGE', 'USER_MGMT', 'UPDATE', 3, 'Updated user: client (SP User)', '::1', '2026-05-01 16:24:53'),
(210, 1, 'DATA_CHANGE', 'USER_MGMT', 'UPDATE', 3, 'Updated user: client (Client User)', '::1', '2026-05-01 16:25:16'),
(211, 1, 'DATA_CHANGE', 'USER_MGMT', 'CREATE', 4, 'Created new user: spsec (SP User)', '::1', '2026-05-01 16:27:26'),
(212, 4, 'SUCCESS', 'DCMT_INBOX', 'RECEIVE', NULL, 'Received document. Tracking No: 2026-00001', '::1', '2026-05-02 07:45:14'),
(213, 4, 'SUCCESS', 'DCMT_INBOX', 'RECEIVE', NULL, 'Received document (2026-00002) as SP Secretary.', '::1', '2026-05-02 08:56:10'),
(214, 1, 'DATA_CHANGE', 'USER_MGMT', 'UPDATE', 3, 'Updated user: client (Client User)', '::1', '2026-05-02 13:32:12'),
(215, 4, 'SUCCESS', 'DCMT_INBOX', 'RECEIVE', NULL, 'Received document (2026-00003) as SP Secretary.', '::1', '2026-05-04 03:13:39'),
(216, 2, 'ROUTING', 'DCMT_RTNG', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00004', '::1', '2026-05-04 03:18:34'),
(217, 2, 'DATA_CHANGE', 'SOURCE_TYPE_MGMT', 'DELETE', 5, 'Deleted source type: Client', '::1', '2026-05-04 03:57:37'),
(218, 4, 'SUCCESS', 'DCMT_PROC', 'ROUTE', NULL, 'Routed document (2026-00003)', '::1', '2026-05-04 15:22:05'),
(219, 1, 'DATA_CHANGE', 'USER_MGMT', 'CREATE', 5, 'Created new user: committee (Committee User)', '::1', '2026-05-05 14:13:05'),
(220, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00002) as Committee.', '::1', '2026-05-07 16:11:03'),
(221, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'CREATE', NULL, 'Created new opinion office: Provincial Legal Office (PLO)', '::1', '2026-05-08 14:43:20'),
(222, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'UPDATE', 1, 'Updated opinion office: Provincial Legal Office (PLOs)', '::1', '2026-05-08 14:45:15'),
(223, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'DELETE', 1, 'Deleted opinion office: Provincial Legal Office', '::1', '2026-05-08 14:45:55'),
(224, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'UPDATE', 1, 'Updated opinion office: Provincial Legal Office (PLO)', '::1', '2026-05-08 14:47:38'),
(225, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'CREATE', NULL, 'Created new opinion office: Local Finance Committee (LFC)', '::1', '2026-05-08 14:49:29'),
(226, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'DELETE', 1, 'Deleted opinion office: Provincial Legal Office', '::1', '2026-05-08 14:49:32'),
(227, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'CREATE', NULL, 'Created opinion status: Pending', '::1', '2026-05-08 16:28:31'),
(228, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'CREATE', NULL, 'Created opinion status: Favorable', '::1', '2026-05-08 16:30:38'),
(229, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'UPDATE', 1, 'Updated opinion status: Pending', '::1', '2026-05-08 16:30:43'),
(230, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'DELETE', 1, 'Deleted opinion status: Pending', '::1', '2026-05-08 16:30:46'),
(231, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'CREATE', NULL, 'Created opinion status: Unfavorable', '::1', '2026-05-08 16:31:09'),
(232, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'CREATE', NULL, 'Created opinion status: For 2nd Endorsement', '::1', '2026-05-08 16:31:22'),
(233, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'UPDATE', 4, 'Updated opinion status: For 2nd Endorsement', '::1', '2026-05-08 16:31:38'),
(234, 5, 'DATA_CHANGE', 'OPINION_STATUS_MGMT', 'UPDATE', 4, 'Updated opinion status: For 2nd Endorsement', '::1', '2026-05-08 16:31:41'),
(235, 1, 'DATA_CHANGE', 'MUNI_CITY_MGMT', 'UPDATE', 1, 'Updated municipality/city: Agno', '::1', '2026-05-09 16:02:01'),
(236, 1, 'DATA_CHANGE', 'USER_MGMT', 'UPDATE', 5, 'Updated user: committee (Committee User)', '::1', '2026-05-09 16:03:37'),
(237, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'UPDATE', 1, 'Updated opinion office: Provincial Legal Office (PLO)', '::1', '2026-05-09 16:14:02'),
(238, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'UPDATE', 1, 'Updated opinion office: Provincial Legal Office (PLO)', '::1', '2026-05-09 16:14:06'),
(239, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00002) as Committee.', '::1', '2026-05-09 17:06:16'),
(240, 5, 'SUCCESS', 'DCMT_ENDORSE', 'ENDORSE', NULL, 'Endorsed Document ID: 2 to 2 office(s).', '::1', '2026-05-10 16:55:45'),
(241, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00001) as Committee.', '::1', '2026-05-10 17:01:34'),
(242, 5, 'SUCCESS', 'DCMT_ENDORSE', 'ENDORSE', NULL, 'Endorsed Document ID: 1 to 2 office(s).', '::1', '2026-05-10 17:01:43'),
(243, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00003) as Committee.', '::1', '2026-05-10 17:03:19'),
(244, 5, 'SUCCESS', 'DCMT_ENDORSE', 'ENDORSE', NULL, 'Endorsed Document ID: 3 to 2 office(s).', '::1', '2026-05-10 17:04:14'),
(245, 5, 'DATA_CHANGE', 'OPINION_OFFICE_MGMT', 'UPDATE', 1, 'Updated opinion office: Provincial Legal Office (PLO)', '::1', '2026-05-10 17:07:39'),
(246, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00004) as Committee.', '::1', '2026-05-10 17:12:43'),
(247, 5, 'SUCCESS', 'DCMT_ENDORSE', 'ENDORSE', NULL, 'Endorsed Document ID: 4 to 1 office(s).', '::1', '2026-05-10 17:19:26'),
(248, 2, 'ROUTING', 'DCMT_RTNG', 'CREATE', NULL, 'Created new document. Tracking No: 2026-00005', '::1', '2026-05-10 18:19:34'),
(249, 5, 'SUCCESS', 'DCMT_COMMITTEE_INBOX', 'RECEIVE', NULL, 'Received document (2026-00005) as Committee.', '::1', '2026-05-10 18:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `committees`
--

CREATE TABLE `committees` (
  `committee_id` int NOT NULL,
  `committee_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `committees`
--

INSERT INTO `committees` (`committee_id`, `committee_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'COMMITTEE ON AGRICULTURE', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(2, 'COMMITTEE ON APPROPRIATIONS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(3, 'COMMITTEE ON BARANGAY AND RURAL DEVELOPMENT, PUBLIC ORDER AND SAFETY', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(4, 'COMMITTEE ON CHILDREN, WOMEN, SENIOR CITIZENS, FAMILY AFFAIRS AND SOCIAL WELFARE', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(5, 'COMMITTEE ON COOPERATIVE, MICRO, SMALL AND MEDIUM BUSINESS AND ENTREPRENEURSHIP DEVELOPMENT', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(6, 'COMMITTEE ON ECONOMIC AFFAIRS AND WAYS AND MEANS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(7, 'COMMITTEE ON EDUCATION, ARTS AND CULTURE', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(8, 'COMMITTEE ON ENVIRONMENT, NATURAL RESOURCES AND ENERGY', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(9, 'COMMITTEE ON GOOD GOVERNMENT AND ACCOUNTABILITY OF PUBLIC OFFICERS, JUSTICE AND HUMAN RIGHTS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(10, 'COMMITTEE ON HEALTH', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(11, 'COMMITTEE ON HOUSING, LAND UTILIZATION AND AGRARIAN REFORM', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(12, 'COMMITTEE ON HUMAN RESOURCES AND DEVELOPMENT, LABOR AND EMPLOYMENT CONCERNS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(13, 'COMMITTEE ON INFORMATION AND COMMUNICATION TECHNOLOGY AND GAMES AND AMUSEMENT', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(14, 'COMMITTEE ON INFRASTRUCTURE, PUBLIC SERVICES AND UTILITIES', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(15, 'COMMITTEE ON INTER-LOCAL GOVERNMENT AND PEOPLE’S AND NON-GOVERNMENTAL ORGANIZATIONS RELATIONS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(16, 'COMMITTEE ON LAWS AND ORDINANCES', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(17, 'COMMITTEE ON RULES, PRIVILEGES AND ETHICS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(18, 'COMMITTEE ON TOURISM, FOREIGN AFFAIRS AND MIGRANT WORKERS CONCERNS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL),
(19, 'COMMITTEE ON YOUTH AND SPORTS', 'active', 0, '2026-05-10 21:52:09', '2026-05-10 21:52:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `communication_categories`
--

CREATE TABLE `communication_categories` (
  `communication_category_id` int NOT NULL,
  `communication_category_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `communication_categories`
--

INSERT INTO `communication_categories` (`communication_category_id`, `communication_category_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'MIS', 'active', 0, '2026-04-03 18:26:15', '2026-04-20 22:14:38', NULL, 2),
(2, 'Personnel Services', 'active', 0, '2026-04-20 21:14:55', '2026-04-28 22:50:28', 2, 2),
(3, 'Posting', 'active', 0, '2026-04-23 21:04:22', '2026-04-28 22:50:38', 2, 2),
(4, 'Records Services', 'active', 0, '2026-04-28 22:50:49', '2026-04-28 22:50:49', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int NOT NULL,
  `tracking_number` varchar(20) NOT NULL,
  `date_received` date NOT NULL,
  `subject_matter` text NOT NULL,
  `document_type_id` int DEFAULT NULL,
  `source_type_id` int DEFAULT NULL,
  `source_name` varchar(255) DEFAULT NULL,
  `source_external_office_id` int DEFAULT NULL,
  `source_hospital_id` int DEFAULT NULL,
  `muni_city_id` int DEFAULT NULL,
  `current_routing_option_id` int DEFAULT NULL,
  `communication_category_id` int DEFAULT NULL,
  `current_owner_user_id` int DEFAULT NULL,
  `version` int DEFAULT '1',
  `status` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `tracking_number`, `date_received`, `subject_matter`, `document_type_id`, `source_type_id`, `source_name`, `source_external_office_id`, `source_hospital_id`, `muni_city_id`, `current_routing_option_id`, `communication_category_id`, `current_owner_user_id`, `version`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '2026-00001', '2026-05-01', 'TESTING ROUTE: AN ORDINANCE ESTABLISHING A COMPREHENSIVE WASTE MANAGEMENT PROGRAM IN THE MUNICIPALITY OF BAYAMBANG, PROVIDING PENALTIES FOR VIOLATIONS, AND FOR OTHER PURPOSES', 10, 5, 'Zyron Manangan', NULL, NULL, 11, 3, NULL, 5, 1, 12, 2, '2026-05-01 13:29:50', '2026-05-10 17:01:43'),
(2, '2026-00002', '2026-05-01', '2ND TEST: AN ACT ESTABLISHING FREE SCHOOL SUPPLIES PROGRAM FOR PUBLIC ELEMENTARY STUDENTS', 7, 5, 'Zyron Manangan', NULL, NULL, 24, 3, NULL, 5, 1, 12, 2, '2026-05-01 15:44:32', '2026-05-10 16:55:45'),
(3, '2026-00003', '2026-05-02', '3RD ROUTING TEST: AN ACT EXPANDING ACCESS TO MENTAL HEALTH SERVICES IN ALL PUBLIC HOSPITALS', 2, 3, NULL, NULL, 2, NULL, 3, NULL, 5, 1, 12, 2, '2026-05-01 16:13:16', '2026-05-10 17:04:14'),
(4, '2026-00004', '2026-05-04', 'TEST NO.4 HAHAHA', 1, 5, 'ZYRON', NULL, NULL, 11, 3, NULL, 5, 1, 12, 2, '2026-05-04 03:18:34', '2026-05-10 17:19:26'),
(5, '2026-00005', '2026-05-11', 'Committee Test: 101', 9, 2, NULL, 16, NULL, NULL, 3, NULL, 5, 1, 1, 2, '2026-05-10 18:19:34', '2026-05-10 18:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `document_attachments`
--

CREATE TABLE `document_attachments` (
  `attachment_id` int NOT NULL,
  `document_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_attachments`
--

INSERT INTO `document_attachments` (`attachment_id`, `document_id`, `file_name`, `file_path`, `uploaded_by`, `created_at`) VALUES
(1, 1, 'Progress_Monitoring_Form_MySQL_GUI.pdf', 'assets/documents/1777642190_Progress_Monitoring_Form_MySQL_GUI.pdf', 2, '2026-05-01 13:29:50'),
(2, 1, 'RESUME.pdf', 'assets/documents/1777642190_RESUME.pdf', 2, '2026-05-01 13:29:50'),
(3, 2, 'Progress_Monitoring_Form_MySQL_GUI.pdf', 'assets/documents/1777650272_Progress_Monitoring_Form_MySQL_GUI.pdf', 2, '2026-05-01 15:44:32'),
(4, 2, 'RESUME.pdf', 'assets/documents/1777650272_RESUME.pdf', 2, '2026-05-01 15:44:32'),
(5, 3, 'Progress_Monitoring_Form_MySQL_GUI.pdf', 'assets/documents/1777651996_Progress_Monitoring_Form_MySQL_GUI.pdf', 2, '2026-05-01 16:13:16'),
(6, 4, 'fe3d70b31fc691eed0991e78dfd0ade4.jpg', 'assets/documents/1777864714_fe3d70b31fc691eed0991e78dfd0ade4.jpg', 2, '2026-05-04 03:18:34'),
(7, 5, 'MOTHER\'S DAY.png', 'assets/documents/1778437174_MOTHER\'S DAY.png', 2, '2026-05-10 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `document_endorsements`
--

CREATE TABLE `document_endorsements` (
  `endorsement_id` int NOT NULL,
  `document_id` int NOT NULL,
  `opinion_office_id` int NOT NULL,
  `endorsement_round` int DEFAULT '1',
  `endorsement_remarks` text,
  `opinion_status_id` int DEFAULT '1',
  `opinion_remarks` text,
  `opinion_attachment` varchar(255) DEFAULT NULL,
  `compliance_description` text,
  `compliance_attachment` varchar(255) DEFAULT NULL,
  `is_skipped` tinyint(1) DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_endorsements`
--

INSERT INTO `document_endorsements` (`endorsement_id`, `document_id`, `opinion_office_id`, `endorsement_round`, `endorsement_remarks`, `opinion_status_id`, `opinion_remarks`, `opinion_attachment`, `compliance_description`, `compliance_attachment`, `is_skipped`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 1, 'ENDORSE KO SAINYO BEH', 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 16:55:45', '2026-05-10 16:55:45'),
(2, 2, 14, 1, 'ENDORSE KO SAINYO BEH', 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 16:55:45', '2026-05-10 16:55:45'),
(3, 1, 6, 1, NULL, 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 17:01:43', '2026-05-10 17:01:43'),
(4, 1, 15, 1, NULL, 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 17:01:43', '2026-05-10 17:01:43'),
(5, 3, 12, 1, NULL, 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 17:04:14', '2026-05-10 17:04:14'),
(6, 3, 2, 1, NULL, 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 17:04:14', '2026-05-10 17:04:14'),
(7, 4, 6, 1, NULL, 1, NULL, NULL, NULL, NULL, 0, 5, NULL, '2026-05-10 17:19:26', '2026-05-10 17:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `document_history`
--

CREATE TABLE `document_history` (
  `document_history_id` int NOT NULL,
  `document_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_history`
--

INSERT INTO `document_history` (`document_history_id`, `document_id`, `user_id`, `action`, `remarks`, `created_at`) VALUES
(1, 1, 2, 'CREATED', 'Document initially created and status set to Pending.', '2026-05-01 13:29:50'),
(2, 2, 2, 'CREATED', 'Document initially created and status set to Pending.', '2026-05-01 15:44:32'),
(3, 3, 2, 'CREATED', 'Document initially created and status set to Pending.', '2026-05-01 16:13:16'),
(4, 1, 4, 'RECEIVED', 'Document was claimed and received by user.', '2026-05-02 07:45:14'),
(5, 2, 4, 'RECEIVED', 'Document was claimed and officially received by SP Secretary.', '2026-05-02 08:56:10'),
(6, 3, 4, 'RECEIVED', 'Document was claimed and officially received by SP Secretary.', '2026-05-04 03:13:39'),
(7, 4, 2, 'CREATED', 'Document initially created and status set to Pending.', '2026-05-04 03:18:34'),
(8, 3, 4, 'ROUTED', 'Document routed to the Plenary division for appropriate action.', '2026-05-04 15:22:05'),
(9, 2, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-07 16:11:03'),
(10, 2, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-09 17:06:16'),
(11, 2, 5, 'ENDORSED', 'Document endorsed to 2 office(s) for opinion.', '2026-05-10 16:55:45'),
(12, 1, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-10 17:01:34'),
(13, 1, 5, 'ENDORSED', 'Document endorsed to 2 office(s) for opinion.', '2026-05-10 17:01:43'),
(14, 3, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-10 17:03:19'),
(15, 3, 5, 'ENDORSED', 'Document endorsed to 2 office(s) for opinion.', '2026-05-10 17:04:14'),
(16, 4, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-10 17:12:43'),
(17, 4, 5, 'ENDORSED', 'Document endorsed to 1 office(s) for opinion.', '2026-05-10 17:19:26'),
(18, 5, 2, 'CREATED', 'Document initially created and status set to Pending.', '2026-05-10 18:19:34'),
(19, 5, 5, 'RECEIVED', 'Document was claimed and officially received by Committee.', '2026-05-10 18:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `document_requirement`
--

CREATE TABLE `document_requirement` (
  `id` int NOT NULL,
  `document_type_id` int NOT NULL,
  `requirement_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_requirement`
--

INSERT INTO `document_requirement` (`id`, `document_type_id`, `requirement_id`) VALUES
(23, 6, 5),
(42, 9, 1),
(43, 8, 1),
(44, 6, 1),
(45, 7, 1),
(46, 4, 1),
(47, 3, 1),
(48, 5, 1),
(49, 6, 2),
(50, 5, 2),
(51, 6, 6),
(52, 5, 6),
(53, 6, 7),
(54, 7, 7),
(55, 8, 8),
(56, 6, 8),
(57, 7, 8),
(58, 6, 9),
(59, 6, 10),
(60, 6, 11),
(61, 6, 12),
(62, 6, 13),
(63, 6, 14),
(64, 6, 15),
(65, 6, 16),
(66, 8, 17),
(67, 3, 17),
(68, 8, 18),
(69, 3, 18),
(70, 8, 19),
(71, 8, 20),
(72, 8, 21),
(73, 7, 21),
(74, 8, 22),
(75, 7, 22),
(76, 9, 23),
(77, 8, 23),
(78, 7, 23),
(79, 4, 23),
(80, 9, 3),
(81, 4, 3),
(82, 9, 4),
(83, 4, 4),
(84, 9, 24),
(85, 4, 24),
(86, 9, 25),
(87, 4, 25),
(88, 9, 26),
(89, 9, 27),
(90, 1, 27),
(91, 9, 28),
(92, 9, 29),
(93, 4, 29),
(94, 9, 30),
(95, 4, 30),
(96, 9, 31),
(97, 4, 31),
(98, 4, 32),
(99, 9, 33),
(100, 4, 33),
(101, 3, 34),
(102, 10, 35),
(103, 3, 35),
(104, 10, 36),
(105, 10, 37),
(106, 10, 38),
(107, 2, 38),
(108, 5, 39),
(109, 2, 39),
(110, 1, 39),
(111, 10, 40),
(112, 2, 40),
(113, 5, 41),
(114, 1, 42),
(115, 2, 43),
(116, 2, 44),
(117, 5, 45),
(118, 11, 46);

-- --------------------------------------------------------

--
-- Table structure for table `document_routes`
--

CREATE TABLE `document_routes` (
  `route_id` int NOT NULL,
  `document_id` int DEFAULT NULL,
  `routed_by_user_id` int DEFAULT NULL,
  `routed_to_option_id` int DEFAULT NULL,
  `action_taken` varchar(50) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_routes`
--

INSERT INTO `document_routes` (`route_id`, `document_id`, `routed_by_user_id`, `routed_to_option_id`, `action_taken`, `remarks`, `created_at`) VALUES
(1, 1, 2, 1, 'CREATED', 'SAMPLE ROUTING ----> PLEASE RECEIVE', '2026-05-01 13:29:50'),
(2, 2, 2, 1, 'CREATED', 'Please review', '2026-05-01 15:44:32'),
(3, 3, 2, 1, 'CREATED', 'PLEASE REVIEW', '2026-05-01 16:13:16'),
(4, 1, 4, NULL, 'RECEIVED', 'Document officially received from Inbox.', '2026-05-02 07:45:14'),
(5, 2, 4, NULL, 'RECEIVED', 'Document officially received by SP Secretary.', '2026-05-02 08:56:10'),
(6, 3, 4, NULL, 'RECEIVED', 'Document officially received by SP Secretary.', '2026-05-04 03:13:39'),
(7, 4, 2, 1, 'CREATED', '111', '2026-05-04 03:18:34'),
(8, 3, 4, 2, 'ROUTED', 'Document routed to the Plenary division for appropriate action.', '2026-05-04 15:22:05'),
(9, 2, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-07 16:11:03'),
(10, 2, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-09 17:06:16'),
(11, 1, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-10 17:01:34'),
(12, 3, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-10 17:03:19'),
(13, 4, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-10 17:12:43'),
(14, 5, 2, 3, 'CREATED', 'Initial document submission', '2026-05-10 18:19:34'),
(15, 5, 5, NULL, 'RECEIVED', 'Document officially received by Committee.', '2026-05-10 18:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `document_statuses`
--

CREATE TABLE `document_statuses` (
  `document_status_id` int NOT NULL,
  `document_status_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_statuses`
--

INSERT INTO `document_statuses` (`document_status_id`, `document_status_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Pending', 'active', 0, '2026-04-03 12:47:18', '2026-04-20 22:16:14', NULL, 1),
(2, 'Approved', 'active', 0, '2026-04-08 00:16:07', '2026-04-28 22:55:19', NULL, 1),
(3, 'Withdrawn', 'active', 0, '2026-04-23 21:04:41', '2026-04-28 22:55:38', 1, 1),
(4, 'Deferred', 'active', 0, '2026-04-28 20:07:12', '2026-04-28 22:55:59', 1, 1),
(5, 'Noted', 'active', 0, '2026-04-28 20:12:20', '2026-04-28 22:56:11', 1, 1),
(6, 'Under Processing', 'active', 0, '2026-04-28 20:12:29', '2026-04-28 22:59:07', 1, 1),
(7, 'Lay on the Table', 'active', 0, '2026-04-28 20:12:37', '2026-04-28 22:57:03', 1, 1),
(8, 'Referred', 'active', 0, '2026-04-28 20:12:49', '2026-04-28 22:57:20', 1, 1),
(9, 'Remanded', 'active', 0, '2026-04-28 20:12:59', '2026-04-28 22:59:11', 1, 1),
(10, 'Returned to Plenary', 'active', 0, '2026-04-28 20:13:19', '2026-04-28 22:59:48', 1, 1),
(11, 'For Committee Report', 'active', 0, '2026-04-28 20:13:25', '2026-04-28 22:59:44', 1, 1),
(12, 'For Opinion', 'active', 0, '2026-05-01 22:13:49', '2026-05-01 22:13:49', 1, NULL),
(13, 'For Calendar', 'active', 0, '2026-05-12 00:00:00', '2026-05-12 00:00:00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_submitted_requirements`
--

CREATE TABLE `document_submitted_requirements` (
  `id` int NOT NULL,
  `document_id` int DEFAULT NULL,
  `requirement_id` int DEFAULT NULL,
  `is_checked` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_submitted_requirements`
--

INSERT INTO `document_submitted_requirements` (`id`, `document_id`, `requirement_id`, `is_checked`, `created_at`) VALUES
(1, 1, 10, 1, '2026-05-01 13:29:50'),
(2, 1, 16, 1, '2026-05-01 13:29:50'),
(3, 1, 11, 1, '2026-05-01 13:29:50'),
(4, 1, 7, 1, '2026-05-01 13:29:50'),
(5, 2, 8, 1, '2026-05-01 15:44:32'),
(6, 2, 7, 1, '2026-05-01 15:44:32'),
(7, 2, 23, 1, '2026-05-01 15:44:32'),
(8, 2, 21, 1, '2026-05-01 15:44:32'),
(9, 2, 22, 1, '2026-05-01 15:44:32'),
(10, 2, 1, 1, '2026-05-01 15:44:32'),
(11, 3, 44, 1, '2026-05-01 16:13:16'),
(12, 3, 39, 1, '2026-05-01 16:13:16'),
(13, 3, 38, 1, '2026-05-01 16:13:16'),
(14, 3, 40, 1, '2026-05-01 16:13:16'),
(15, 3, 43, 1, '2026-05-01 16:13:16'),
(16, 4, 36, 1, '2026-05-04 03:18:34'),
(17, 4, 38, 1, '2026-05-04 03:18:34'),
(18, 4, 37, 1, '2026-05-04 03:18:34'),
(19, 5, 3, 1, '2026-05-10 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `document_type_id` int NOT NULL,
  `document_type_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`document_type_id`, `document_type_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Provincial Tax Ordinance', 'active', 0, '2026-04-02 23:45:23', '2026-04-29 13:40:16', NULL, 1),
(2, 'Provincial Resolution', 'active', 0, '2026-04-08 00:18:34', '2026-04-29 13:42:06', NULL, 1),
(3, 'Provincial Appropriation Ordinance for Supplemental Budget', 'active', 0, '2026-04-20 18:50:29', '2026-04-29 13:43:05', 1, 1),
(4, 'Provincial Appropriation Ordinance for Annual Budget', 'active', 0, '2026-04-23 21:04:33', '2026-04-29 13:44:02', 1, 1),
(5, 'Provincial Ordinance', 'active', 0, '2026-04-29 13:44:17', '2026-04-29 13:44:17', 1, NULL),
(6, 'Municipal/City Ordinance', 'active', 0, '2026-04-29 13:44:25', '2026-04-29 13:44:25', 1, NULL),
(7, 'Municipal/City Resolution', 'active', 0, '2026-04-29 13:44:36', '2026-04-29 13:44:36', 1, NULL),
(8, 'Municipal/City Appropriation Ordinance for Supplemental Budget', 'active', 0, '2026-04-29 13:44:46', '2026-04-29 13:44:46', 1, NULL),
(9, 'Municipal/City Appropriation Ordinance for Annual Budget', 'active', 0, '2026-04-29 13:44:56', '2026-04-29 13:44:56', 1, NULL),
(10, 'Communication', 'active', 0, '2026-04-29 13:45:06', '2026-04-29 13:45:06', 1, NULL),
(11, 'Administrative Cases', 'active', 0, '2026-04-29 13:45:17', '2026-04-29 13:45:17', 1, NULL),
(12, 'Complaint', 'active', 0, '2026-04-29 13:45:25', '2026-04-29 13:45:25', 1, NULL),
(13, 'Others', 'inactive', 0, '2026-04-29 13:46:16', '2026-05-08 00:13:08', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `external_offices`
--

CREATE TABLE `external_offices` (
  `external_office_id` int NOT NULL,
  `external_office_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `external_offices`
--

INSERT INTO `external_offices` (`external_office_id`, `external_office_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Capitol Resort Hotel Operations Division', 'active', 0, '2026-04-03 16:09:52', '2026-04-29 13:48:02', NULL, 2),
(2, 'General Services Office', 'active', 0, '2026-04-23 21:04:01', '2026-04-29 13:52:24', 2, 2),
(3, 'Human Resource Management and Development Office', 'active', 0, '2026-04-29 13:48:18', '2026-04-29 13:52:26', 2, NULL),
(4, 'Internal Audit Division', 'active', 0, '2026-04-29 13:48:27', '2026-04-29 13:52:29', 2, NULL),
(5, 'Management Information Services Office', 'active', 0, '2026-04-29 13:48:35', '2026-04-29 13:52:33', 2, NULL),
(6, 'Pangasinan Polytechnic College', 'active', 0, '2026-04-29 13:48:42', '2026-04-29 13:52:36', 2, NULL),
(7, 'Pangasinan Provincial Environment and Natural Resources Office', 'active', 0, '2026-04-29 13:49:06', '2026-04-29 13:52:38', 2, NULL),
(8, 'Pangasinan Provincial Jail', 'active', 0, '2026-04-29 13:49:16', '2026-04-29 13:52:41', 2, NULL),
(9, 'Pangasinan Public Employment Services Office', 'active', 0, '2026-04-29 13:49:23', '2026-04-29 13:52:44', 2, NULL),
(10, 'Provincial Accounting Office', 'active', 0, '2026-04-29 13:49:30', '2026-04-29 13:52:46', 2, NULL),
(11, 'Provincial Agriculture Office', 'active', 0, '2026-04-29 13:49:37', '2026-04-29 13:52:49', 2, NULL),
(12, 'Provincial Archives and Records Center', 'active', 0, '2026-04-29 13:49:46', '2026-04-29 13:52:51', 2, NULL),
(13, 'Provincial Assessment Office', 'active', 0, '2026-04-29 13:49:56', '2026-04-29 13:52:53', 2, NULL),
(14, 'Provincial Board Secretary', 'active', 0, '2026-04-29 13:50:05', '2026-04-29 13:52:55', 2, NULL),
(15, 'Provincial Budget Office', 'active', 0, '2026-04-29 13:50:11', '2026-04-29 13:52:58', 2, NULL),
(16, 'Provincial Community Development and Training Office', 'active', 0, '2026-04-29 13:50:20', '2026-04-29 13:53:00', 2, NULL),
(17, 'Provincial Disaster Risk Reduction and Management Office', 'active', 0, '2026-04-29 13:50:26', '2026-04-29 13:53:03', 2, NULL),
(18, 'Provincial Economic Development and Investment Promotion Office', 'active', 0, '2026-04-29 13:54:03', '2026-04-29 13:54:03', 2, NULL),
(19, 'Provincial Engineering Office', 'active', 0, '2026-04-29 13:54:20', '2026-04-29 13:54:20', 2, NULL),
(20, 'Provincial Governor\'s Office', 'active', 0, '2026-04-29 13:54:33', '2026-04-29 13:54:33', 2, NULL),
(21, 'Provincial Health Office', 'active', 0, '2026-04-29 13:54:42', '2026-04-29 13:54:42', 2, NULL),
(22, 'Provincial Hospital Management Services Office', 'active', 0, '2026-04-29 13:54:51', '2026-04-29 13:54:51', 2, NULL),
(23, 'Provincial Human Settlements and Urban Development Authority', 'active', 0, '2026-04-29 13:55:02', '2026-04-29 13:55:02', 2, NULL),
(24, 'Provincial Information Office', 'active', 0, '2026-04-29 13:55:13', '2026-04-29 13:55:13', 2, NULL),
(25, 'Provincial Legal Office', 'active', 0, '2026-04-29 13:55:24', '2026-04-29 13:55:24', 2, NULL),
(26, 'Provincial Library Office', 'active', 0, '2026-04-29 13:55:33', '2026-04-29 13:55:33', 2, NULL),
(27, 'Provincial Planning and Development Office', 'active', 0, '2026-04-29 13:55:45', '2026-04-29 13:55:45', 2, NULL),
(28, 'Provincial Population Cooperative and Livelihood Development Office', 'active', 0, '2026-04-29 13:55:55', '2026-04-29 13:55:55', 2, NULL),
(29, 'Provincial Social Welfare and Development Office', 'active', 0, '2026-04-29 13:56:06', '2026-04-29 13:56:06', 2, NULL),
(30, 'Provincial Treasurer\'s Office', 'active', 0, '2026-04-29 13:56:22', '2026-04-29 13:56:22', 2, NULL),
(31, 'Provincial Tourism and Cultural Affairs Office', 'active', 0, '2026-04-29 13:56:31', '2026-04-29 13:56:31', 2, NULL),
(32, 'Provincial Vice Governor\'s Office', 'active', 0, '2026-04-29 13:56:42', '2026-04-29 13:56:42', 2, NULL),
(33, 'Provincial Veterinary Office', 'active', 0, '2026-04-29 13:56:53', '2026-04-29 13:56:53', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hospital_id` int NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `hospital_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Asingan Community Hospital', 'active', 0, '2026-04-03 16:58:52', '2026-04-29 14:14:40', NULL, 2),
(2, 'Bayambang District Hospital', 'active', 0, '2026-04-21 20:45:08', '2026-04-29 14:15:10', 2, 2),
(3, 'Bolinao Community Hospital', 'active', 0, '2026-04-23 21:04:07', '2026-04-29 14:15:19', 2, 2),
(4, 'Dasol Community Hospital', 'active', 0, '2026-04-29 14:15:27', '2026-04-29 14:15:27', 2, NULL),
(5, 'Eastern Pangasinan District Hospital', 'active', 0, '2026-04-29 14:15:36', '2026-04-29 14:15:36', 2, NULL),
(6, 'Lingayen District Hospital', 'active', 0, '2026-04-29 14:17:44', '2026-04-29 14:17:44', 2, NULL),
(7, 'Manaoag Community Hospital', 'active', 0, '2026-04-29 14:17:52', '2026-04-29 14:17:52', 2, NULL),
(8, 'Mangatarem District Hospital', 'active', 0, '2026-04-29 14:18:01', '2026-04-29 14:18:01', 2, NULL),
(9, 'Mapandan Community Hospital', 'active', 0, '2026-04-29 14:18:24', '2026-04-29 14:18:24', 2, NULL),
(10, 'Pangasinan Provincial Hospital', 'active', 0, '2026-04-29 14:18:32', '2026-04-29 14:18:32', 2, NULL),
(11, 'Pozorrubio Community Hospital', 'active', 0, '2026-04-29 14:18:39', '2026-04-29 14:18:39', 2, NULL),
(12, 'Umingan Community Hospital', 'active', 0, '2026-04-29 14:18:46', '2026-04-29 14:18:46', 2, NULL),
(13, 'Urdaneta District Hospital', 'active', 0, '2026-04-29 14:18:55', '2026-04-29 14:18:55', 2, NULL),
(14, 'Western Pangasinan District Hospital', 'active', 0, '2026-04-29 14:19:02', '2026-04-29 14:19:02', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `muni_cities`
--

CREATE TABLE `muni_cities` (
  `muni_city_id` int NOT NULL,
  `muni_city_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `muni_cities`
--

INSERT INTO `muni_cities` (`muni_city_id`, `muni_city_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Agno', 'active', 0, '2026-04-03 12:15:51', '2026-05-10 00:02:01', NULL, 1),
(2, 'Aguilar', 'active', 0, '2026-04-08 00:15:47', '2026-04-29 13:01:33', NULL, 1),
(3, 'Alaminos', 'active', 0, '2026-04-23 21:04:37', '2026-04-29 13:19:28', 1, 1),
(4, 'Alcala', 'active', 0, '2026-04-29 13:00:34', '2026-04-29 13:19:32', 1, 1),
(5, 'Anda', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(6, 'Asingan', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(7, 'Balungao', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(8, 'Bani', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(9, 'Basista', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(10, 'Bautista', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(11, 'Bayambang', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(12, 'Binalonan', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(13, 'Binmaley', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(14, 'Bolinao', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(15, 'Bugallon', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(16, 'Burgos', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(17, 'Calasiao', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(18, 'Dasol', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(19, 'Infanta', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(20, 'Labrador', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(21, 'Laoac', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(22, 'Lingayen', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(23, 'Mabini', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(24, 'Malasiqui', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(25, 'Manaoag', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(26, 'Mangaldan', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(27, 'Mangatarem', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(28, 'Mapandan', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(29, 'Natividad', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(30, 'Pozorrubio', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(31, 'Rosales', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(32, 'San Carlos', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(33, 'San Fabian', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(34, 'San Manuel', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(35, 'San Nicolas', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(36, 'San Quintin', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(37, 'Santa Barbara', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(38, 'Santa Maria', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(39, 'Santo Tomas', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(40, 'Sison', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(41, 'Sual', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(42, 'Tayug', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(43, 'Umingan', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(44, 'Urbiztondo', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(45, 'Urdaneta', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL),
(46, 'Villasis', 'active', 0, '2026-04-29 13:20:38', '2026-04-29 13:20:38', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opinion_offices`
--

CREATE TABLE `opinion_offices` (
  `opinion_office_id` int NOT NULL,
  `opinion_office_code` varchar(10) NOT NULL,
  `opinion_office_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `opinion_offices`
--

INSERT INTO `opinion_offices` (`opinion_office_id`, `opinion_office_code`, `opinion_office_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'PLO', 'Provincial Legal Office', 'active', 0, '2026-05-08 22:43:20', '2026-05-11 01:07:39', 5, 5),
(2, 'LFC', 'Local Finance Committee', 'active', 0, '2026-05-08 22:49:29', '2026-05-08 22:49:29', 5, NULL),
(3, 'PPDO', 'Provincial Planning and Development Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(4, 'HRMDO', 'Human Resource Management and Development Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(5, 'PHO', 'Provincial Health Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(6, 'CSC', 'Civil Service Commission', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(7, 'OPAG', 'Office of the Provincial Agriculturist', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(8, 'PTO', 'Provincial Treasurer\'s Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(9, 'HOUSING', 'Provincial Human Settlements and Urban Development Authority', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(10, 'ENRO', 'Pangasinan Environment and Natural Resources Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(11, 'TOURISM', 'Provincial Tourism and Cultural Affairs Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(12, 'DILG', 'Department of the Interior and Local Government', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(13, 'PAO', 'Provincial Accounting Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(14, 'DAR', 'Department of Agrarian Reform', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(15, 'DHSUD', 'Department of Human Settlements and Urban Development', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(16, 'PSWDO', 'Provincial Social Welfare and Development Office', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL),
(17, 'NHIP', 'National Historical Institute of the Philippines', 'active', 0, '2026-05-08 22:53:20', '2026-05-08 22:53:20', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opinion_statuses`
--

CREATE TABLE `opinion_statuses` (
  `opinion_status_id` int NOT NULL,
  `opinion_status_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `opinion_statuses`
--

INSERT INTO `opinion_statuses` (`opinion_status_id`, `opinion_status_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Pending', 'active', 0, '2026-05-09 00:28:31', '2026-05-09 00:30:58', 5, 5),
(2, 'Favorable', 'active', 0, '2026-05-09 00:30:38', '2026-05-09 00:30:38', 5, NULL),
(3, 'Unfavorable', 'active', 0, '2026-05-09 00:31:09', '2026-05-09 00:31:09', 5, NULL),
(4, 'For 2nd Endorsement', 'active', 0, '2026-05-09 00:31:22', '2026-05-09 00:31:41', 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `requirement_id` int NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`requirement_id`, `requirement_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Transmmital Letter', 'active', 0, '2026-04-09 13:06:50', '2026-04-29 20:37:33', NULL, 1),
(2, 'Certificate Posting', 'active', 0, '2026-04-19 15:24:30', '2026-04-29 20:44:15', NULL, 1),
(3, 'Annual Budget for Calendar Year', 'active', 0, '2026-04-19 15:24:38', '2026-04-29 21:06:00', NULL, 1),
(4, 'Appropriation Number', 'active', 0, '2026-04-23 21:03:45', '2026-04-29 21:06:22', 2, 1),
(5, 'Municipal/City Ordinance', 'active', 0, '2026-04-29 14:20:03', '2026-04-29 16:36:03', 2, 1),
(6, 'Publication/Affidavit of Publication', 'active', 0, '2026-04-29 20:44:42', '2026-04-29 20:44:42', 1, NULL),
(7, 'Minutes of Public Hearing', 'active', 0, '2026-04-29 20:45:18', '2026-04-29 20:45:18', 1, NULL),
(8, 'Attendance Sheet', 'active', 0, '2026-04-29 20:46:29', '2026-04-29 20:46:29', 1, NULL),
(9, 'Copy of Old Ordinance', 'active', 0, '2026-04-29 20:46:48', '2026-04-29 20:46:48', 1, NULL),
(10, 'Certification from Local Finance Committee (LFC)', 'active', 0, '2026-04-29 20:47:01', '2026-04-29 20:47:01', 1, NULL),
(11, 'Endorsement from Department of Human Settlements and Urban Development (DHSUD)', 'active', 0, '2026-04-29 20:47:18', '2026-04-29 20:47:18', 1, NULL),
(12, 'Sangguniang Bayan (SB) Resolution', 'active', 0, '2026-04-29 20:47:34', '2026-04-29 20:47:34', 1, NULL),
(13, 'Department of Agriculture (DA) Certification for Land Use', 'active', 0, '2026-04-29 20:50:59', '2026-04-29 20:50:59', 1, NULL),
(14, 'Department of Agrarian Reform (DAR) Certification', 'active', 0, '2026-04-29 20:51:13', '2026-04-29 20:51:13', 1, NULL),
(15, 'Transfer Certificate of Title (TCT)', 'active', 0, '2026-04-29 20:51:26', '2026-04-29 20:51:26', 1, NULL),
(16, 'Declaration of Real Property', 'active', 0, '2026-04-29 20:51:39', '2026-04-29 20:51:39', 1, NULL),
(17, 'Appropriation Ordinance Number', 'active', 0, '2026-04-29 20:53:17', '2026-04-29 20:53:17', 1, NULL),
(18, 'Supplemental Budget Number', 'active', 0, '2026-04-29 20:56:00', '2026-04-29 20:56:00', 1, NULL),
(19, 'Appropriation Ordinance', 'active', 0, '2026-04-29 20:56:15', '2026-04-29 20:56:15', 1, NULL),
(20, 'Land Bank of the Philippines (LBP) Form/Source of Fund', 'active', 0, '2026-04-29 20:56:27', '2026-04-29 20:56:27', 1, NULL),
(21, 'Resolution Number', 'active', 0, '2026-04-29 20:56:43', '2026-04-29 20:56:43', 1, NULL),
(22, 'Supplemental Investment Program (SIP) Form', 'active', 0, '2026-04-29 20:57:05', '2026-04-29 20:57:05', 1, NULL),
(23, 'Municipal Development Council (MDC) Resolution', 'active', 0, '2026-04-29 20:57:47', '2026-04-29 20:57:47', 1, NULL),
(24, 'Table of Contents', 'active', 0, '2026-04-29 21:06:41', '2026-04-29 21:06:41', 1, NULL),
(25, 'Budget Message', 'active', 0, '2026-04-29 21:07:02', '2026-04-29 21:07:02', 1, NULL),
(26, 'Plantilla of Personnel', 'active', 0, '2026-04-29 21:07:24', '2026-04-29 21:07:24', 1, NULL),
(27, 'Budget Review Matrix', 'active', 0, '2026-04-29 21:07:40', '2026-04-29 21:07:40', 1, NULL),
(28, 'Local Expenditure Program', 'active', 0, '2026-04-29 21:07:53', '2026-04-29 21:07:53', 1, NULL),
(29, 'Annual Investment Plan (AIP)', 'active', 0, '2026-04-29 21:08:15', '2026-04-29 21:08:15', 1, NULL),
(30, 'Department of Interior and Local Government (DILG) approved GAD Plan', 'active', 0, '2026-04-29 21:08:29', '2026-04-29 21:08:29', 1, NULL),
(31, 'Office of City Defense (OCD) Certification', 'active', 0, '2026-04-29 21:11:58', '2026-04-29 21:11:58', 1, NULL),
(32, 'Peace and Order Plan', 'active', 0, '2026-04-29 21:12:12', '2026-04-29 21:12:12', 1, NULL),
(33, 'PPAs/Plans', 'active', 0, '2026-04-29 21:12:26', '2026-04-29 21:12:26', 1, NULL),
(34, 'Executive Committee Resolution', 'active', 0, '2026-04-29 21:12:37', '2026-04-29 21:12:37', 1, NULL),
(35, 'Attachments', 'active', 0, '2026-04-29 21:12:52', '2026-04-29 21:12:52', 1, NULL),
(36, 'Agency/Office', 'active', 0, '2026-04-29 21:13:02', '2026-04-29 21:13:02', 1, NULL),
(37, 'Subject Matter', 'active', 0, '2026-04-29 21:13:12', '2026-04-29 21:13:12', 1, NULL),
(38, 'Memorandum of Agreement (MOA)', 'active', 0, '2026-04-29 21:13:22', '2026-04-29 21:13:22', 1, NULL),
(39, 'Endorsement Letter', 'active', 0, '2026-04-29 21:13:39', '2026-04-29 21:13:39', 1, NULL),
(40, 'Opinion', 'active', 0, '2026-04-29 21:13:57', '2026-04-29 21:13:57', 1, NULL),
(41, 'Legal Opinion', 'active', 0, '2026-04-29 21:14:42', '2026-04-29 21:14:42', 1, NULL),
(42, 'Certification of Availability of Funds', 'active', 0, '2026-04-29 21:14:49', '2026-04-29 21:14:49', 1, NULL),
(43, 'Resolution', 'active', 0, '2026-04-29 21:14:59', '2026-04-29 21:14:59', 1, NULL),
(44, 'Draft Memorandum of Agreement (MOA)', 'active', 0, '2026-04-29 21:15:09', '2026-04-29 21:15:09', 1, NULL),
(45, 'Draft Ordinance', 'active', 0, '2026-04-29 21:15:26', '2026-04-29 21:15:26', 1, NULL),
(46, 'Details', 'active', 0, '2026-04-29 21:15:37', '2026-04-29 21:15:37', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `routing_options`
--

CREATE TABLE `routing_options` (
  `routing_option_id` int NOT NULL,
  `routing_option_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `routing_options`
--

INSERT INTO `routing_options` (`routing_option_id`, `routing_option_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'SP Secretary', 'active', 0, '2026-04-03 18:17:47', '2026-04-19 21:22:49', NULL, NULL),
(2, 'Plenary', 'active', 0, '2026-04-19 21:23:34', '2026-05-01 21:36:53', NULL, 2),
(3, 'Committee', 'active', 0, '2026-04-19 21:23:59', '2026-04-28 22:47:31', NULL, 2),
(4, 'Noted', 'active', 0, '2026-04-29 22:49:47', '2026-04-29 22:49:47', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `source_types`
--

CREATE TABLE `source_types` (
  `source_type_id` int NOT NULL,
  `source_type_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `source_types`
--

INSERT INTO `source_types` (`source_type_id`, `source_type_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'SP Member', 'active', 0, '2026-04-03 17:45:49', '2026-04-19 00:01:37', NULL, NULL),
(2, 'External Office', 'active', 0, '2026-04-19 00:01:58', '2026-04-19 00:01:58', NULL, NULL),
(3, 'Hospital', 'active', 0, '2026-04-19 00:02:03', '2026-04-19 00:02:03', NULL, NULL),
(4, 'Agency', 'active', 0, '2026-04-19 00:02:08', '2026-04-19 00:02:08', NULL, NULL),
(5, 'Client', 'active', 0, '2026-04-19 00:02:12', '2026-05-04 11:58:09', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

CREATE TABLE `user_accounts` (
  `user_account_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_role_id` int NOT NULL,
  `account_status` enum('Active','Deactivated','Blocked') DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`user_account_id`, `username`, `email`, `password_hash`, `user_role_id`, `account_status`, `last_login`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_deleted`) VALUES
(1, 'master', 'zyrnmanangan@gmail.com', '$2y$10$pF776SgnG0ofzvSTUV0jXOVes22EOL9HnvEPt6hbxkQmpVH5s81W.', 6, 'Active', '2026-05-10 00:00:46', '2026-04-03 11:46:43', '2026-05-10 00:00:46', NULL, NULL, 0),
(2, 'admin', 'zyr.manangan@gmail.com', '$2y$10$dMv6mhS8k8ueMqsZ9txdXeP8hTFX.Ybe6dgrVPByvFDiMpgKQ8WGS', 1, 'Active', '2026-05-11 02:22:57', '2026-04-03 11:47:31', '2026-05-11 02:22:57', NULL, 1, 0),
(3, 'client', 'client@gmail.com', '$2y$10$vEO3aTqAjDX0SqEppDFKQOR/o7DsDA/tquSuqYr1ttRuep.xD4xPi', 5, 'Active', '2026-05-02 21:32:21', '2026-04-23 21:06:18', '2026-05-02 21:32:21', 1, 1, 0),
(4, 'spsec', 'spsec@gmail.com', '$2y$10$pYorIFro90vz3RPMxVtHY.MCYEvxFKcATNEAnV02vsS7ISBv9IPlm', 5, 'Active', '2026-05-11 02:22:42', '2026-05-02 00:27:26', '2026-05-11 02:22:42', 1, NULL, 0),
(5, 'committee', 'committee@gmail.com', '$2y$10$LoTyAT.9wz2RERoKfXmJ/.6kS0B6mBozv4ZL/RS.KNY8rujZOt5km', 3, 'Active', '2026-05-11 09:40:28', '2026-05-05 22:13:05', '2026-05-11 09:40:28', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_info_id` int NOT NULL,
  `user_account_id` int NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_info_id`, `user_account_id`, `first_name`, `last_name`, `middle_name`, `suffix`, `contact_number`, `profile_picture`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 1, 'Zei', 'Manangan', '', '', '', 'assets/profiles/profile_69cf3869d6f88.png', '2026-04-03 11:46:43', '2026-04-03 11:47:53', NULL),
(2, 2, 'Zyron', 'Manangan', '', '', '', NULL, '2026-04-03 11:47:31', '2026-04-23 20:46:01', 1),
(3, 3, 'Client', 'User', '', '', '', NULL, '2026-04-23 21:06:18', '2026-05-02 21:32:12', 1),
(4, 4, 'SP', 'User', NULL, NULL, NULL, NULL, '2026-05-02 00:27:26', '2026-05-02 00:27:26', NULL),
(5, 5, 'Committee', 'User', '', '', '', NULL, '2026-05-05 22:13:05', '2026-05-10 00:03:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int NOT NULL,
  `user_role_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_role_name`, `status`, `is_deleted`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Admin', 'active', 0, '2026-04-03 11:44:20', '2026-04-20 22:16:01', NULL, 1),
(2, 'Client', 'active', 0, '2026-04-03 11:44:36', '2026-04-03 11:45:03', NULL, NULL),
(3, 'Committee', 'active', 0, '2026-04-03 11:45:27', '2026-04-28 23:00:30', NULL, 1),
(4, 'Plenary', 'active', 0, '2026-04-03 11:45:41', '2026-04-23 20:48:32', NULL, 1),
(5, 'SP Secretary', 'active', 0, '2026-04-03 11:45:54', '2026-04-23 20:48:37', NULL, 1),
(6, 'Super Admin', 'active', 0, '2026-04-03 11:46:05', '2026-04-23 20:48:42', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agendas`
--
ALTER TABLE `agendas`
  ADD PRIMARY KEY (`agenda_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `committee_id` (`committee_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `committees`
--
ALTER TABLE `committees`
  ADD PRIMARY KEY (`committee_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `communication_categories`
--
ALTER TABLE `communication_categories`
  ADD PRIMARY KEY (`communication_category_id`),
  ADD UNIQUE KEY `communication_category_name` (`communication_category_name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `unique_tracking_version` (`tracking_number`,`version`),
  ADD KEY `document_type_id` (`document_type_id`),
  ADD KEY `source_type_id` (`source_type_id`),
  ADD KEY `source_external_office_id` (`source_external_office_id`),
  ADD KEY `source_hospital_id` (`source_hospital_id`),
  ADD KEY `muni_city_id` (`muni_city_id`),
  ADD KEY `current_routing_option_id` (`current_routing_option_id`),
  ADD KEY `communication_category_id` (`communication_category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `document_endorsements`
--
ALTER TABLE `document_endorsements`
  ADD PRIMARY KEY (`endorsement_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `opinion_office_id` (`opinion_office_id`),
  ADD KEY `opinion_status_id` (`opinion_status_id`),
  ADD KEY `fk_endorse_cb` (`created_by`),
  ADD KEY `fk_endorse_ub` (`updated_by`);

--
-- Indexes for table `document_history`
--
ALTER TABLE `document_history`
  ADD PRIMARY KEY (`document_history_id`),
  ADD KEY `fk_history_document` (`document_id`),
  ADD KEY `fk_history_user` (`user_id`);

--
-- Indexes for table `document_requirement`
--
ALTER TABLE `document_requirement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_requirement` (`requirement_id`);

--
-- Indexes for table `document_routes`
--
ALTER TABLE `document_routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `routed_by_user_id` (`routed_by_user_id`),
  ADD KEY `routed_to_option_id` (`routed_to_option_id`);

--
-- Indexes for table `document_statuses`
--
ALTER TABLE `document_statuses`
  ADD PRIMARY KEY (`document_status_id`),
  ADD UNIQUE KEY `document_status_name` (`document_status_name`),
  ADD KEY `fk_requirements_created_by` (`created_by`),
  ADD KEY `fk_requirements_updated_by` (`updated_by`);

--
-- Indexes for table `document_submitted_requirements`
--
ALTER TABLE `document_submitted_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `requirement_id` (`requirement_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`document_type_id`),
  ADD UNIQUE KEY `document_type_name` (`document_type_name`),
  ADD KEY `fk_document_types_created_by` (`created_by`),
  ADD KEY `fk_document_types_updated_by` (`updated_by`);

--
-- Indexes for table `external_offices`
--
ALTER TABLE `external_offices`
  ADD PRIMARY KEY (`external_office_id`),
  ADD UNIQUE KEY `external_office_name` (`external_office_name`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hospital_id`),
  ADD UNIQUE KEY `hospital_name` (`hospital_name`),
  ADD KEY `fk_hospitals_created_by` (`created_by`),
  ADD KEY `fk_hospitals_updated_by` (`updated_by`);

--
-- Indexes for table `muni_cities`
--
ALTER TABLE `muni_cities`
  ADD PRIMARY KEY (`muni_city_id`),
  ADD UNIQUE KEY `muni_city_name` (`muni_city_name`);

--
-- Indexes for table `opinion_offices`
--
ALTER TABLE `opinion_offices`
  ADD PRIMARY KEY (`opinion_office_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `opinion_statuses`
--
ALTER TABLE `opinion_statuses`
  ADD PRIMARY KEY (`opinion_status_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`requirement_id`);

--
-- Indexes for table `routing_options`
--
ALTER TABLE `routing_options`
  ADD PRIMARY KEY (`routing_option_id`),
  ADD UNIQUE KEY `routing_option_name` (`routing_option_name`),
  ADD KEY `fk_routing_options_created_by` (`created_by`),
  ADD KEY `fk_routing_options_updated_by` (`updated_by`);

--
-- Indexes for table `source_types`
--
ALTER TABLE `source_types`
  ADD PRIMARY KEY (`source_type_id`),
  ADD UNIQUE KEY `source_type_name` (`source_type_name`),
  ADD KEY `fk_source_types_created_by` (`created_by`),
  ADD KEY `fk_source_types_updated_by` (`updated_by`);

--
-- Indexes for table `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`user_account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_role_id` (`user_role_id`),
  ADD KEY `fk_users_created_by` (`created_by`),
  ADD KEY `fk_users_updated_by` (`updated_by`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_info_id`),
  ADD UNIQUE KEY `user_account_id` (`user_account_id`),
  ADD KEY `fk_user_info_updated_by` (`updated_by`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD UNIQUE KEY `user_role_name` (`user_role_name`),
  ADD KEY `fk_user_roles_created_by` (`created_by`),
  ADD KEY `fk_user_roles_updated_by` (`updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agendas`
--
ALTER TABLE `agendas`
  MODIFY `agenda_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `committees`
--
ALTER TABLE `committees`
  MODIFY `committee_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `communication_categories`
--
ALTER TABLE `communication_categories`
  MODIFY `communication_category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_attachments`
--
ALTER TABLE `document_attachments`
  MODIFY `attachment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document_endorsements`
--
ALTER TABLE `document_endorsements`
  MODIFY `endorsement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document_history`
--
ALTER TABLE `document_history`
  MODIFY `document_history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `document_requirement`
--
ALTER TABLE `document_requirement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `document_routes`
--
ALTER TABLE `document_routes`
  MODIFY `route_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `document_statuses`
--
ALTER TABLE `document_statuses`
  MODIFY `document_status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `document_submitted_requirements`
--
ALTER TABLE `document_submitted_requirements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `document_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `external_offices`
--
ALTER TABLE `external_offices`
  MODIFY `external_office_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hospital_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `muni_cities`
--
ALTER TABLE `muni_cities`
  MODIFY `muni_city_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `opinion_offices`
--
ALTER TABLE `opinion_offices`
  MODIFY `opinion_office_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `opinion_statuses`
--
ALTER TABLE `opinion_statuses`
  MODIFY `opinion_status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `requirement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `routing_options`
--
ALTER TABLE `routing_options`
  MODIFY `routing_option_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `source_types`
--
ALTER TABLE `source_types`
  MODIFY `source_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_accounts`
--
ALTER TABLE `user_accounts`
  MODIFY `user_account_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_info_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agendas`
--
ALTER TABLE `agendas`
  ADD CONSTRAINT `fk_agenda_comm` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`committee_id`),
  ADD CONSTRAINT `fk_agenda_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `committees`
--
ALTER TABLE `committees`
  ADD CONSTRAINT `committees_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `committees_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`source_type_id`) REFERENCES `source_types` (`source_type_id`),
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`source_external_office_id`) REFERENCES `external_offices` (`external_office_id`),
  ADD CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`source_hospital_id`) REFERENCES `hospitals` (`hospital_id`),
  ADD CONSTRAINT `documents_ibfk_5` FOREIGN KEY (`muni_city_id`) REFERENCES `muni_cities` (`muni_city_id`),
  ADD CONSTRAINT `documents_ibfk_6` FOREIGN KEY (`current_routing_option_id`) REFERENCES `routing_options` (`routing_option_id`),
  ADD CONSTRAINT `documents_ibfk_7` FOREIGN KEY (`communication_category_id`) REFERENCES `communication_categories` (`communication_category_id`),
  ADD CONSTRAINT `documents_ibfk_8` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `documents_ibfk_9` FOREIGN KEY (`status`) REFERENCES `document_statuses` (`document_status_id`);

--
-- Constraints for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD CONSTRAINT `document_attachments_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `document_endorsements`
--
ALTER TABLE `document_endorsements`
  ADD CONSTRAINT `fk_endorse_cb` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_endorse_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_endorse_office` FOREIGN KEY (`opinion_office_id`) REFERENCES `opinion_offices` (`opinion_office_id`),
  ADD CONSTRAINT `fk_endorse_status` FOREIGN KEY (`opinion_status_id`) REFERENCES `opinion_statuses` (`opinion_status_id`),
  ADD CONSTRAINT `fk_endorse_ub` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `document_history`
--
ALTER TABLE `document_history`
  ADD CONSTRAINT `fk_history_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`user_account_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_requirement`
--
ALTER TABLE `document_requirement`
  ADD CONSTRAINT `fk_requirement` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`requirement_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_routes`
--
ALTER TABLE `document_routes`
  ADD CONSTRAINT `document_routes_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_routes_ibfk_2` FOREIGN KEY (`routed_by_user_id`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `document_routes_ibfk_3` FOREIGN KEY (`routed_to_option_id`) REFERENCES `routing_options` (`routing_option_id`);

--
-- Constraints for table `document_statuses`
--
ALTER TABLE `document_statuses`
  ADD CONSTRAINT `fk_communication_categories_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_communication_categories_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_document_statuses_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_document_statuses_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_external_offices_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_external_offices_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_muni_cities_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_muni_cities_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_requirements_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_requirements_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `document_submitted_requirements`
--
ALTER TABLE `document_submitted_requirements`
  ADD CONSTRAINT `document_submitted_requirements_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_submitted_requirements_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`requirement_id`);

--
-- Constraints for table `document_types`
--
ALTER TABLE `document_types`
  ADD CONSTRAINT `fk_document_types_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_document_types_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `fk_hospitals_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_hospitals_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `opinion_offices`
--
ALTER TABLE `opinion_offices`
  ADD CONSTRAINT `opinion_offices_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `opinion_offices_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `opinion_statuses`
--
ALTER TABLE `opinion_statuses`
  ADD CONSTRAINT `opinion_statuses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `opinion_statuses_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `routing_options`
--
ALTER TABLE `routing_options`
  ADD CONSTRAINT `fk_routing_options_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_routing_options_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `source_types`
--
ALTER TABLE `source_types`
  ADD CONSTRAINT `fk_source_types_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_source_types_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);

--
-- Constraints for table `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `user_accounts_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`user_role_id`);

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `fk_user_info_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_accounts` (`user_account_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`user_account_id`),
  ADD CONSTRAINT `fk_user_roles_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_accounts` (`user_account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
