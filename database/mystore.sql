-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 14, 2025 at 09:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mystore`
--

-- --------------------------------------------------------

--
-- Table structure for table `attack_type`
--

CREATE TABLE `attack_type` (
  `attack_type_id` int(11) NOT NULL,
  `model_name` varchar(50) DEFAULT NULL,
  `brief_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attack_type`
--

INSERT INTO `attack_type` (`attack_type_id`, `model_name`, `brief_description`) VALUES
(0, 'dddd', 'fffff'),
(1, 'Cyberattack', 'Unauthorized access to the vehicle’s software'),
(2, 'Sensor Tampering', 'Manipulation or obstruction of vehicle sensors'),
(3, 'Signal Interruption', 'Interruption in communication signals'),
(4, 'Data Injection', 'Injection of malicious data into the system'),
(5, 'Physical Damage', 'Direct physical damage to vehicle components');

-- --------------------------------------------------------

--
-- Table structure for table `driver_interventions`
--

CREATE TABLE `driver_interventions` (
  `intervention_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `intervention_timestamp` date NOT NULL,
  `reason` varchar(50) NOT NULL,
  `duration_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_interventions`
--

INSERT INTO `driver_interventions` (`intervention_id`, `test_id`, `intervention_timestamp`, `reason`, `duration_id`) VALUES
(2, 2, '2024-02-15', 'System failed to recognize stop sign', 8),
(3, 3, '2024-03-20', 'Avoidance maneuver for road hazard', 6),
(4, 4, '2024-04-05', 'Software glitch caused braking issue', 10),
(5, 5, '2024-05-12', 'Driver intervention due to foggy conditions', 7),
(0, 0, '0000-00-00', 'ee', 3333),
(0, 0, '0000-00-00', 'ee', 3333),
(0, 0, '0000-00-00', 'ee', 3333);

-- --------------------------------------------------------

--
-- Table structure for table `environment_factors`
--

CREATE TABLE `environment_factors` (
  `environment_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `lighting_conditions` varchar(50) NOT NULL,
  `road_type` varchar(50) NOT NULL,
  `temperature_id` int(11) NOT NULL,
  `traffic_density` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `environment_factors`
--

INSERT INTO `environment_factors` (`environment_id`, `test_id`, `lighting_conditions`, `road_type`, `temperature_id`, `traffic_density`) VALUES
(1, 1, 'Daylight', 'Highway', 25, 'Low'),
(2, 2, 'Night', 'Urban', 18, 'High'),
(3, 3, 'Overcast', 'Rural', 20, 'Medium'),
(4, 4, 'Rainy', 'Suburban', 15, 'High'),
(5, 5, 'Foggy', 'Mountain', 10, 'Low'),
(0, 0, 'dd', 'ddd', 0, 'dddd');

-- --------------------------------------------------------

--
-- Table structure for table `failure_events`
--

CREATE TABLE `failure_events` (
  `failure_event_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `failure_type_id` int(11) NOT NULL,
  `failure_event_timestamp` date NOT NULL,
  `severity` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failure_events`
--

INSERT INTO `failure_events` (`failure_event_id`, `test_id`, `failure_type_id`, `failure_event_timestamp`, `severity`) VALUES
(2, 1, 2, '2024-01-01', 'Medium'),
(3, 2, 3, '2024-01-02', 'Low'),
(4, 3, 4, '2024-01-03', 'Critical'),
(5, 4, 5, '2024-01-04', 'High');

-- --------------------------------------------------------

--
-- Table structure for table `failure_types`
--

CREATE TABLE `failure_types` (
  `failure_type_id` int(11) NOT NULL,
  `model_name` varchar(50) NOT NULL,
  `brief_description` varchar(255) NOT NULL,
  `make` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

CREATE TABLE `maintenance_records` (
  `maintenance_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_date` date NOT NULL,
  `brief_description` varchar(255) NOT NULL,
  `the_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_records`
--

INSERT INTO `maintenance_records` (`maintenance_id`, `vehicle_id`, `maintenance_date`, `brief_description`, `the_cost`) VALUES
(0, 1, '2023-01-01', 'Brake pad replacement', 200.50),
(0, 2, '2023-02-15', 'Oil change and filter replacement', 150.00),
(0, 3, '2023-03-10', 'Battery replacement', 1000.00),
(0, 4, '2023-04-05', 'Tire rotation and alignment', 75.00),
(0, 5, '2023-05-20', 'Windshield replacement', 300.00),
(0, 0, '2025-02-13', 'edqd', 111.00);

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `personnel_id` int(11) NOT NULL,
  `model_name` varchar(50) NOT NULL,
  `the_role` varchar(50) NOT NULL,
  `contact_info` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `model_name`, `the_role`, `contact_info`) VALUES
(1, 'John Doe', 'Test Engineer', 'john.doe@example.com'),
(2, 'Jane Smith', 'Software Developer', 'jane.smith@example.com'),
(3, 'Alice Brown', 'Vehicle Technician', 'alice.brown@example.com'),
(4, 'Bob Johnson', 'Cybersecurity Specialist', 'bob.johnson@example.com'),
(5, 'Charlie Lee', 'Project Manager', 'charlie.lee@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `safety_issues`
--

CREATE TABLE `safety_issues` (
  `vehicle_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `issue_description` varchar(255) NOT NULL,
  `severity_level` enum('Low','Medium','High','Critical') NOT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `safety_issues`
--

INSERT INTO `safety_issues` (`vehicle_id`, `issue_date`, `issue_description`, `severity_level`, `status`) VALUES
(1, '2024-01-01', 'Brake failure detected', 'Critical', 'Open'),
(2, '2024-02-15', 'Airbag malfunction', 'High', 'In Progress'),
(3, '2024-03-10', 'Engine overheating', 'Medium', 'Resolved'),
(4, '2024-04-05', 'Tire pressure warning', 'Low', 'Open'),
(5, '2024-05-20', 'Seatbelt sensor issue', 'High', 'Closed');

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `sensor_id` int(11) NOT NULL,
  `sensor_type` varchar(50) NOT NULL,
  `manufacture` varchar(50) NOT NULL,
  `model_name` varchar(50) NOT NULL,
  `sensor_range` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`sensor_id`, `sensor_type`, `manufacture`, `model_name`, `sensor_range`) VALUES
(0, 'Lidar', 'Velodyne', 'VLP-16', 100),
(0, 'Camera', 'Sony', 'IMX500', 50),
(0, 'Ultrasonic', 'Bosch', 'USS4', 5),
(0, 'Radar', 'Continental', 'ARS408', 250),
(0, 'Infrared', 'FLIR', 'ThermoCam', 30);

-- --------------------------------------------------------

--
-- Table structure for table `sensor_data`
--

CREATE TABLE `sensor_data` (
  `data_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `sensor_timestamp` date NOT NULL,
  `sensor_type` varchar(50) NOT NULL,
  `sensor_unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensor_data`
--

INSERT INTO `sensor_data` (`data_id`, `test_id`, `sensor_id`, `sensor_timestamp`, `sensor_type`, `sensor_unit`) VALUES
(0, 1, 1, '2024-01-01', 'Temperature', 'Celsius'),
(0, 2, 2, '2024-01-01', 'Humidity', 'Percentage'),
(0, 2, 3, '2024-01-02', 'Pressure', 'Pascal'),
(0, 3, 4, '2024-01-03', 'Temperature', 'Celsius'),
(0, 4, 5, '2024-01-04', 'Light', 'Lumens');

-- --------------------------------------------------------

--
-- Table structure for table `software_updates`
--

CREATE TABLE `software_updates` (
  `update_id` int(11) NOT NULL,
  `software_version_id` int(11) NOT NULL,
  `update_date` date NOT NULL,
  `changelog` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `software_updates`
--

INSERT INTO `software_updates` (`update_id`, `software_version_id`, `update_date`, `changelog`) VALUES
(1, 101, '2023-01-25', 'Fixed minor bugs in braking system'),
(2, 102, '2023-03-30', 'Improved obstacle detection algorithm'),
(3, 103, '2023-06-20', 'Added support for new sensor hardware'),
(4, 104, '2023-09-15', 'Enhanced cybersecurity measures'),
(5, 105, '2023-12-10', 'AI diagnostics update');

-- --------------------------------------------------------

--
-- Table structure for table `software_versions`
--

CREATE TABLE `software_versions` (
  `software_version_id` int(11) NOT NULL,
  `version_number` varchar(50) NOT NULL,
  `release_date` date NOT NULL,
  `changelog` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `software_versions`
--

INSERT INTO `software_versions` (`software_version_id`, `version_number`, `release_date`, `changelog`) VALUES
(101, 'v1.0', '2023-01-15', 'Initial release'),
(102, 'v1.1', '2023-03-20', 'Bug fixes and performance improvements'),
(103, 'v2.0', '2023-06-10', 'Major feature update'),
(104, 'v2.1', '2023-09-05', 'Added support for new sensors');

-- --------------------------------------------------------

--
-- Table structure for table `success_criteria`
--

CREATE TABLE `success_criteria` (
  `criteria_id` int(11) NOT NULL,
  `brief_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success_criteria`
--

INSERT INTO `success_criteria` (`criteria_id`, `brief_description`) VALUES
(1, 'No collisions occurred during the test'),
(2, 'Test completed within the expected time frame'),
(3, 'System accurately detected all obstacles'),
(4, 'All planned routes were successfully navigated'),
(5, 'No critical failures were reported'),
(0, 'ddf');

-- --------------------------------------------------------

--
-- Table structure for table `test_personnel`
--

CREATE TABLE `test_personnel` (
  `test_personnel_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `assigned_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_personnel`
--

INSERT INTO `test_personnel` (`test_personnel_id`, `test_id`, `personnel_id`, `assigned_role`) VALUES
(1, 1, 1, 'Lead Engineer'),
(2, 2, 2, 'Developer Support'),
(3, 3, 3, 'Hardware Technician'),
(4, 4, 4, 'Cybersecurity Expert'),
(5, 5, 5, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `test_scenarios`
--

CREATE TABLE `test_scenarios` (
  `scenario_id` int(11) NOT NULL,
  `brief_description` varchar(255) NOT NULL,
  `the_location` varchar(100) NOT NULL,
  `weather_conditions` varchar(50) NOT NULL,
  `traffic_conditions` varchar(50) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_scenarios`
--

INSERT INTO `test_scenarios` (`scenario_id`, `brief_description`, `the_location`, `weather_conditions`, `traffic_conditions`, `duration`) VALUES
(1, 'Urban driving under clear skies', 'City Center', 'Clear', 'Moderate Traffic', 60),
(2, 'Highway driving during rain', 'Highway 21', 'Rain', 'Low Traffic', 45),
(3, 'Rural road navigation in fog', 'Countryside', 'Foggy', 'No Traffic', 90),
(4, 'Nighttime city driving', 'Downtown', 'Clear', 'Heavy Traffic', 120),
(5, 'Mountain driving in snow', 'Rocky Hills', 'Snow', 'Light Traffic', 120),
(0, 'daADDAD', 'ASDS', 'ADDVGJV', 'AGSVDHGV', 22);

-- --------------------------------------------------------

--
-- Table structure for table `test_success`
--

CREATE TABLE `test_success` (
  `test_success_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `criteria_timestamp` date NOT NULL,
  `details` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_success`
--

INSERT INTO `test_success` (`test_success_id`, `test_id`, `criteria_id`, `criteria_timestamp`, `details`) VALUES
(1, 1, 1, '2024-01-01', 'No collisions occurred during the test'),
(2, 1, 2, '2024-01-01', 'Test completed within the expected time frame'),
(3, 2, 3, '2024-01-02', 'System accurately detected all obstacles'),
(4, 3, 4, '2024-01-03', 'All planned routes were successfully navigated'),
(5, 4, 5, '2024-01-04', 'No critical failures were reported');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model_name` varchar(50) NOT NULL,
  `manufacture_year` int(11) NOT NULL,
  `software_version_id` int(11) NOT NULL,
  `autonomy_level` varchar(50) NOT NULL,
  `brief_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `make`, `model_name`, `manufacture_year`, `software_version_id`, `autonomy_level`, `brief_description`) VALUES
(1, 'Tesla', 'Model S', 2023, 101, 'Level 5', 'Electric autonomous sedan'),
(2, 'Ford', 'F-150', 2022, 102, 'Level 4', 'Heavy-duty pickup truck'),
(3, 'Toyota', 'Corolla', 2021, 103, 'Level 3', 'Compact sedan'),
(4, 'BMW', 'X5', 2020, 104, 'Level 4', 'Luxury SUV'),
(5, 'Audi', 'Q7', 2023, 105, 'Level 5', 'Luxury crossover');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_sensors`
--

CREATE TABLE `vehicle_sensors` (
  `vehicle_sensor_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `installation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_sensors`
--

INSERT INTO `vehicle_sensors` (`vehicle_sensor_id`, `vehicle_id`, `sensor_id`, `installation_date`) VALUES
(1, 1, 1, '2023-01-15'),
(3, 2, 3, '2022-11-05'),
(4, 3, 4, '2021-08-20'),
(5, 4, 5, '2020-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_status`
--

CREATE TABLE `vehicle_status` (
  `status_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `status_date` date NOT NULL,
  `current_status` varchar(50) NOT NULL,
  `notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_status`
--

INSERT INTO `vehicle_status` (`status_id`, `vehicle_id`, `status_date`, `current_status`, `notes`) VALUES
(1, 1, '2023-01-05', 'Operational', 'No issues detected'),
(2, 2, '2023-02-10', 'Under Maintenance', 'Brake replacement scheduled'),
(3, 3, '2023-03-15', 'Operational', 'Battery performance verified'),
(4, 4, '2023-04-20', 'Faulty', 'Sensor malfunction detected'),
(5, 5, '2023-05-25', 'Decommissioned', 'Vehicle retired from service');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_tests`
--

CREATE TABLE `vehicle_tests` (
  `test_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `test_date` date NOT NULL,
  `outcome` varchar(50) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_tests`
--

INSERT INTO `vehicle_tests` (`test_id`, `vehicle_id`, `scenario_id`, `test_date`, `outcome`, `duration`) VALUES
(1, 1, 1, '2024-01-01', 'Success', 60),
(2, 2, 2, '2024-01-02', 'Failure', 45),
(3, 3, 3, '2024-01-03', 'Success', 90),
(4, 4, 4, '2024-01-04', 'Failure', 120),
(5, 5, 5, '2024-01-05', 'Success', 120);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attack_type`
--
ALTER TABLE `attack_type`
  ADD PRIMARY KEY (`attack_type_id`);

--
-- Indexes for table `failure_types`
--
ALTER TABLE `failure_types`
  ADD PRIMARY KEY (`failure_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
