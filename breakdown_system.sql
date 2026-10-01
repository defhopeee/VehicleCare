
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_phone` varchar(20) NOT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `vehicle_brand_id` int(11) DEFAULT NULL,
  `transmission` enum('automatic','manual','other') DEFAULT 'manual',
  `plate_number` varchar(20) DEFAULT NULL,
  `fault_id` int(11) DEFAULT NULL,
  `garage_id` int(11) DEFAULT NULL,
  `mechanic_id` int(11) DEFAULT NULL,
  `booking_type` enum('repair','spare_part','inspection') DEFAULT 'repair',
  `description` text DEFAULT NULL,
  `client_latitude` decimal(10,7) DEFAULT NULL,
  `client_longitude` decimal(10,7) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`booking_id`),
  KEY `vehicle_brand_id` (`vehicle_brand_id`),
  KEY `fault_id` (`fault_id`),
  KEY `garage_id` (`garage_id`),
  KEY `mechanic_id` (`mechanic_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`vehicle_brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`fault_id`) REFERENCES `faults` (`fault_id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`garage_id`) REFERENCES `garages` (`garage_id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`mechanic_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,NULL,'Jane Wambui','0722334455','jane.wambui@example.com',5,'automatic','KDA 231B',1,1,1,'repair','Engine misfiring and losing power on uphill drives.',NULL,NULL,'2026-09-20','09:00:00','completed',NULL,'2026-09-28 14:37:30'),(2,NULL,'Kevin Otieno','0733445566','kevin.otieno@example.com',10,'manual','KCF 118P',7,1,2,'repair','Brake pedal feels soft, needs inspection before a long trip.',NULL,NULL,'2026-09-30','11:30:00','confirmed',NULL,'2026-09-28 14:37:30'),(3,NULL,'Amina Yusuf','0711223344',NULL,11,'manual','KDB 552L',5,1,NULL,'repair','Dashboard warning lights flashing intermittently.',NULL,NULL,'2026-10-02','14:00:00','pending','','2026-09-28 14:37:30'),(4,NULL,'David Mutua','0700998877','david.mutua@example.com',8,'automatic','KDD 009X',10,3,8,'inspection','Pre-purchase inspection before buying this used car.',NULL,NULL,'2026-09-28','10:00:00','in_progress',NULL,'2026-09-28 14:37:30'),(5,NULL,'Susan Chebet','0745112233',NULL,NULL,'manual','KCE 774M',4,2,5,'spare_part','Need 4 new tyres, size 195/65 R15.',NULL,NULL,'2026-10-01','08:30:00','pending',NULL,'2026-09-28 14:37:30'),(6,NULL,'Brian Kamau','0788664422','brian.kamau@example.com',14,'automatic','KDG 340R',8,3,6,'repair','Car bounces excessively on bumps, suspicious of worn shocks.',NULL,NULL,'2026-09-15','13:00:00','cancelled',NULL,'2026-09-28 14:37:30'),(14,NULL,'Felix Omondi','0711556677','felix.omondi@example.com',2,'manual',NULL,6,4,NULL,'repair','Needs an oil change before a long upcountry trip.',NULL,NULL,'2026-10-03','09:30:00','confirmed',NULL,'2026-10-01 08:52:37'),(15,NULL,'Mercy Wangari','0722667788',NULL,9,'manual',NULL,NULL,NULL,NULL,'spare_part','Looking for 2 LED headlight bulbs for a Volkswagen Golf.',NULL,NULL,'2026-10-04',NULL,'pending',NULL,'2026-10-01 08:52:37'),(16,NULL,'Hassan Omar','0733778899','hassan.omar@example.com',13,'manual',NULL,10,5,12,'inspection','Pre-purchase inspection on a used Hyundai.',NULL,NULL,'2026-10-06','12:00:00','confirmed',NULL,'2026-10-01 08:52:37'),(17,NULL,'Nancy Chepkoech','0744889900',NULL,3,'manual',NULL,2,6,NULL,'repair','Due for a full service and oil change.',NULL,NULL,'2026-09-10','08:00:00','completed',NULL,'2026-10-01 08:52:37');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Toyota','brands/toyota.png'),(2,'Nissan','brands/nissan.png'),(3,'Mazda','brands/mazda.png'),(4,'Subaru','brands/subaru.png'),(5,'Honda','brands/honda.png'),(6,'Mitsubishi','brands/mitsubishi.png'),(7,'Mercedes-Benz','brands/mercedes-benz.png'),(8,'BMW','brands/bmw.png'),(9,'Volkswagen','brands/volkswagen.png'),(10,'Ford','brands/ford.png'),(11,'Isuzu','brands/isuzu.png'),(12,'Land Rover','brands/land-rover.png'),(13,'Hyundai','brands/hyundai.png'),(14,'Kia','brands/kia.png'),(15,'Peugeot','brands/peugeot.png');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `sender` enum('client','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`session_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
INSERT INTO `chat_messages` VALUES (1,1,'client','Hi, is the Nairobi garage open today?',1,'2026-09-28 14:37:30'),(2,1,'admin','Yes we are open 24/7, how can we help?',1,'2026-09-28 14:37:30'),(3,1,'client','Great, I need a brake check.',1,'2026-09-28 14:37:30'),(4,1,'admin','Please book through our Book Now page and we will assign a mechanic.',1,'2026-09-28 14:37:30'),(5,2,'client','My car will not start, I need urgent help.',1,'2026-09-28 14:37:30'),(6,2,'admin','We are dispatching the nearest mechanic now, please share your location.',1,'2026-09-28 14:37:30'),(7,3,'client','Do you sell Toyota oil filters?',1,'2026-09-28 14:37:30'),(8,3,'admin','Yes, check our Spare Parts page under the Filters category.',1,'2026-09-28 14:37:30'),(9,3,'client','Thank you!',1,'2026-09-28 14:37:30'),(11,5,'client','Hi, do you tow motorcycles too?',1,'2026-10-01 08:52:58'),(12,5,'admin','Currently we only tow cars and small vans, sorry about that.',1,'2026-10-01 08:52:58'),(13,6,'client','What time does the Nakuru garage close?',1,'2026-10-01 08:52:58'),(14,6,'admin','We are open 24/7 at all our garages, including Nakuru.',1,'2026-10-01 08:52:58'),(15,7,'client','My booking is still pending, any update?',1,'2026-10-01 08:52:58'),(16,8,'client','Can I pay via Mpesa at the garage?',1,'2026-10-01 08:52:58'),(17,8,'admin','Yes, cash and Mpesa are both accepted at all our garages.',1,'2026-10-01 08:52:58'),(18,9,'client','Do you have Isuzu truck parts?',1,'2026-10-01 08:52:58'),(19,9,'admin','Yes, check our Spare Parts page and filter by Isuzu.',1,'2026-10-01 08:52:58'),(20,10,'client','Is the Thika garage open on Sundays?',1,'2026-10-01 08:52:58'),(21,10,'admin','Yes, all our garages operate 24/7 including Sundays.',1,'2026-10-01 08:52:58'),(22,11,'client','Thanks for the quick repair yesterday!',1,'2026-10-01 08:52:58');
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
DROP TABLE IF EXISTS `chat_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `chat_sessions` DISABLE KEYS */;
INSERT INTO `chat_sessions` VALUES (1,'Winnie Achieng','winnie.achieng@example.com','closed','2026-09-28 14:37:30','2026-09-28 14:37:30'),(2,'Tom Mboya',NULL,'closed','2026-10-01 09:24:19','2026-09-28 14:37:30'),(3,'Sarah Wanjiku','sarah.wanjiku@example.com','closed','2026-09-28 14:37:30','2026-09-28 14:37:30'),(5,'Brian Otieno',NULL,'closed','2026-10-01 08:52:50','2026-10-01 08:52:50'),(6,'Faith Chebet','faith.chebet@example.com','closed','2026-10-01 08:52:50','2026-10-01 08:52:50'),(7,'Kevin Njogu',NULL,'open','2026-10-01 08:52:50','2026-10-01 08:52:50'),(8,'Mary Wairimu','mary.wairimu@example.com','closed','2026-10-01 08:52:50','2026-10-01 08:52:50'),(9,'Samuel Kiprono',NULL,'closed','2026-10-01 08:52:50','2026-10-01 08:52:50'),(10,'Joan Atieno','joan.atieno@example.com','open','2026-10-01 08:52:50','2026-10-01 08:52:50'),(11,'Peter Mwaura',NULL,'closed','2026-10-01 08:52:50','2026-10-01 08:52:50');
/*!40000 ALTER TABLE `chat_sessions` ENABLE KEYS */;
DROP TABLE IF EXISTS `faults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faults` (
  `fault_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`fault_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `faults` DISABLE KEYS */;
INSERT INTO `faults` VALUES (1,'Engine Repair','All engine-related diagnostics and repairs',NULL),(2,'Transmission / Gearbox','Automatic and manual gearbox repairs',NULL),(3,'Painting & Body Work','Dents, scratches, full respray',NULL),(4,'Tyre & Wheel','Puncture, replacement, alignment',NULL),(5,'Electrical Systems','Battery, wiring, ECU, lights',NULL),(6,'Lubrication & Oil Change','Engine oil, filters, greasing',NULL),(7,'Brakes','Pads, discs, brake fluid',NULL),(8,'Suspension','Shock absorbers, bushings',NULL),(9,'Air Conditioning','Gas refill, compressor',NULL),(10,'General Inspection','Full vehicle checkup',NULL);
/*!40000 ALTER TABLE `faults` ENABLE KEYS */;
DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `type` enum('feedback','complaint','inquiry') DEFAULT 'feedback',
  `status` enum('new','read','resolved') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,'Grace Nyambura','grace.nyambura@example.com','0722112233','Smooth booking experience','The booking process was quick and the mechanic showed up on time. Thank you!','feedback','read','2026-09-28 14:37:30'),(2,'Peter Mwangi','peter.mwangi@example.com','0733221144','Mechanic arrived late','My appointment was for 2pm but the mechanic only arrived after 3:30pm with no notice.','complaint','read','2026-09-28 14:37:30'),(3,'Alice Njeri',NULL,'0711998877','Spare parts availability','Do you currently have BMW brake pads in stock at the Nairobi garage?','inquiry','resolved','2026-09-28 14:37:30'),(4,'Moses Kiplagat','moses.kiplagat@example.com','0700556677','Great service in Kisumu','Kisumu Car Clinic fixed my gearbox issue quickly and the pricing was fair.','feedback','new','2026-09-28 14:37:30'),(7,'James Mutiso','james.mutiso@example.com','0700112244','Eldoret garage experience','Collins at Eldoret Auto Care did a great job on my suspension.','feedback','new','2026-10-01 08:52:45'),(8,'Lydia Wambui',NULL,'0711223355','Pricing question','How much does a full brake pad replacement cost on average?','inquiry','new','2026-10-01 08:52:45'),(9,'Patrick Omondi','patrick.omondi@example.com','0722334466','Missed appointment','I booked for 10am but nobody showed up and nobody called.','complaint','new','2026-10-01 08:52:45'),(10,'Catherine Njoki','catherine.njoki@example.com','0733445577','Thika highway garage','Very convenient location right off the highway, quick service.','feedback','read','2026-10-01 08:52:45'),(11,'Dennis Kiptanui',NULL,'0744556688','Spare part stock','Do you have brake discs for a Subaru Forester in stock?','inquiry','resolved','2026-10-01 08:52:45'),(12,'Agnes Mueni','agnes.mueni@example.com','0755667799','Great mechanic','Joseph in Meru was very professional and explained everything clearly.','feedback','resolved','2026-10-01 08:52:45');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
DROP TABLE IF EXISTS `garages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `garages` (
  `garage_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `county` varchar(80) DEFAULT NULL,
  `physical_location` varchar(200) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT '24/7',
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`garage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `garages` DISABLE KEYS */;
INSERT INTO `garages` VALUES (1,'Nairobi Auto Hub','Nairobi','Industrial Area, Dar es Salaam Rd',-1.2863889,36.8172230,'+254700111222','+254700111222','info@nairobiautohub.co.ke','24/7','Full-service garage with towing.','garages/garage-1.jpg',1,'2026-09-28 13:23:37'),(2,'Mombasa Motor Works','Mombasa','Nyali, Links Road',-4.0434770,39.6588710,'+254700333444','+254700333444','info@mombasamotor.co.ke','24/7','Coastal specialists in engines and AC.','garages/garage-2.jpg',1,'2026-09-28 13:23:37'),(3,'Kisumu Car Clinic','Kisumu','Kondele, Highway',-0.0917020,34.7679560,'+254700555666','+254700555666','info@kisumucarclinic.co.ke','24/7','Trusted upcountry workshop.','garages/garage-1.jpg',1,'2026-09-28 13:23:37'),(4,'Nakuru Speed Garage','Nakuru','Section 58, Nakuru-Nairobi Hwy',-0.3030990,36.0800260,'+254700777888','+254700777888',NULL,'24/7','Reliable repairs and tyre services for upcountry travelers.','garages/garage-2.jpg',1,'2026-10-01 08:51:44'),(5,'Eldoret Auto Care','Uasin Gishu','Eldoret Town, Uganda Road',0.5203600,35.2697790,'+254700999000','+254700999000',NULL,'24/7','Highlands specialists in engine and suspension work.','garages/garage-1.jpg',1,'2026-10-01 08:51:44'),(6,'Thika Roadside Garage','Kiambu','Thika Superhighway, Exit 10',-1.0332500,37.0693300,'+254701111222','+254701111222',NULL,'24/7','Fast response garage along the busy Thika highway.','garages/garage-2.jpg',1,'2026-10-01 08:51:44'),(7,'Nyeri Mountain Motors','Nyeri','Nyeri Town, Kimathi Way',-0.4206670,36.9476360,'+254701222333','+254701222333',NULL,'24/7','Trusted workshop serving Mt Kenya region drivers.','garages/garage-1.jpg',1,'2026-10-01 08:51:44'),(8,'Machakos Garage Hub','Machakos','Machakos Town, Mbolu Road',-1.5167810,37.2637180,'+254701333444','+254701333444',NULL,'24/7','Covers Machakos and the Nairobi-Mombasa corridor.','garages/garage-2.jpg',1,'2026-10-01 08:51:44'),(9,'Kakamega Car Clinic','Kakamega','Kakamega Town, Kisumu-Webuye Rd',0.2827300,34.7518000,'+254701444555','+254701444555',NULL,'24/7','Western Kenya full-service repair and parts garage.','garages/garage-1.jpg',1,'2026-10-01 08:51:44'),(10,'Meru Auto Works','Meru','Meru Town, Nchiru Road',0.0472000,37.6498030,'+254701555666','+254701555666',NULL,'24/7','Serving Meru and surrounding farming communities.','garages/garage-2.jpg',1,'2026-10-01 08:51:44');
/*!40000 ALTER TABLE `garages` ENABLE KEYS */;
DROP TABLE IF EXISTS `mechanics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mechanics` (
  `mechanic_id` int(11) NOT NULL AUTO_INCREMENT,
  `garage_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialty_fault_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 5.0,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mechanic_id`),
  KEY `garage_id` (`garage_id`),
  KEY `specialty_fault_id` (`specialty_fault_id`),
  CONSTRAINT `mechanics_ibfk_1` FOREIGN KEY (`garage_id`) REFERENCES `garages` (`garage_id`) ON DELETE SET NULL,
  CONSTRAINT `mechanics_ibfk_2` FOREIGN KEY (`specialty_fault_id`) REFERENCES `faults` (`fault_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `mechanics` DISABLE KEYS */;
INSERT INTO `mechanics` VALUES (1,1,'James Mwangi',1,'+254701000001',NULL,NULL,NULL,4.8,1,'2026-09-28 13:40:55'),(2,1,'Peter Otieno',7,'+254701000002',NULL,NULL,NULL,4.6,1,'2026-09-28 13:40:55'),(3,1,'Grace Wanjiru',5,'+254701000003',NULL,NULL,NULL,4.9,1,'2026-09-28 13:40:55'),(4,2,'Ali Hassan',9,'+254701000004',NULL,NULL,NULL,4.7,1,'2026-09-28 13:40:55'),(5,2,'Fatuma Juma',4,'+254701000005',NULL,NULL,NULL,4.5,1,'2026-09-28 13:40:55'),(6,3,'Brian Kiptoo',2,'+254701000006',NULL,NULL,NULL,4.6,1,'2026-09-28 13:40:55'),(7,3,'Mary Achieng',6,'+254701000007',NULL,NULL,NULL,4.8,1,'2026-09-28 13:40:55'),(8,3,'Samuel Ruto',10,'+254701000008',NULL,NULL,NULL,4.4,1,'2026-09-28 13:40:55'),(11,4,'Daniel Kiprotich',7,'+254702000001',NULL,NULL,NULL,4.6,1,'2026-10-01 08:51:58'),(12,5,'Collins Kipchoge',1,'+254702000002',NULL,NULL,NULL,4.7,1,'2026-10-01 08:51:58'),(13,6,'Esther Nyambura',4,'+254702000003',NULL,NULL,NULL,4.5,1,'2026-10-01 08:51:58'),(14,7,'John Mwangi',8,'+254702000004',NULL,NULL,NULL,4.9,1,'2026-10-01 08:51:58'),(15,8,'Lucy Mutheu',5,'+254702000005',NULL,NULL,NULL,4.4,1,'2026-10-01 08:51:58'),(16,9,'Victor Wafula',2,'+254702000006',NULL,NULL,NULL,4.6,1,'2026-10-01 08:51:58'),(17,10,'Joseph Kirimi',10,'+254702000007',NULL,NULL,NULL,4.8,1,'2026-10-01 08:51:58');
/*!40000 ALTER TABLE `mechanics` ENABLE KEYS */;
DROP TABLE IF EXISTS `part_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `part_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `part_categories` DISABLE KEYS */;
INSERT INTO `part_categories` VALUES (1,'Tyres',NULL),(2,'Lubricants & Oils',NULL),(3,'Filters',NULL),(4,'Brake Parts',NULL),(5,'Batteries',NULL),(6,'Suspension',NULL),(7,'Engine Parts',NULL),(8,'Body & Paint',NULL),(9,'Accessories',NULL),(10,'Electrical Parts',NULL);
/*!40000 ALTER TABLE `part_categories` ENABLE KEYS */;
DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `fault_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`service_id`),
  KEY `fault_id` (`fault_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`fault_id`) REFERENCES `faults` (`fault_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Full Engine Diagnostic',1,2500.00,'Computerized diagnostic scan and report.',NULL,1),(2,'Gearbox Oil Change',2,4500.00,'Manual or automatic transmission fluid service.',NULL,1),(3,'Full Respray (single color)',3,45000.00,'Complete exterior respray, factory-matched color.',NULL,1),(4,'Tyre Replacement (per tyre)',4,1500.00,'Fitting, balancing and valve replacement.',NULL,1),(5,'Wheel Alignment',4,2000.00,'Computerized 4-wheel alignment.',NULL,1),(6,'Battery & Electrical Check',5,1200.00,'Battery, alternator and wiring health check.',NULL,1),(7,'Full Oil & Filter Service',6,5200.00,'Engine oil, oil filter and general inspection.',NULL,1),(8,'Brake Pad Replacement (per axle)',7,3500.00,'Pads, inspection and brake fluid top-up.',NULL,1),(9,'Shock Absorber Replacement (pair)',8,8500.00,'Front or rear pair, includes fitting.',NULL,1),(10,'AC Regas & Service',9,3200.00,'Refrigerant refill and system leak check.',NULL,1),(11,'Pre-Purchase Inspection',10,3000.00,'Full 50-point vehicle inspection report.',NULL,1);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
DROP TABLE IF EXISTS `spare_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spare_parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `garage_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`part_id`),
  UNIQUE KEY `part_code` (`part_code`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  KEY `garage_id` (`garage_id`),
  CONSTRAINT `spare_parts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `part_categories` (`category_id`) ON DELETE SET NULL,
  CONSTRAINT `spare_parts_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  CONSTRAINT `spare_parts_ibfk_3` FOREIGN KEY (`garage_id`) REFERENCES `garages` (`garage_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `spare_parts` DISABLE KEYS */;
INSERT INTO `spare_parts` VALUES (1,'TY-001','205/55R16 Radial Tyre',1,1,1,8500.00,20,'All-season radial tyre, fits most sedans.',NULL,1,'2026-09-28 13:40:55'),(2,'OIL-001','Fully Synthetic Engine Oil 5L',2,1,1,4200.00,35,'5W-30 fully synthetic, long service interval.',NULL,1,'2026-09-28 13:40:55'),(3,'FLT-001','Oil Filter (Toyota/Nissan fit)',3,1,1,650.00,50,'Genuine-spec oil filter cartridge.',NULL,1,'2026-09-28 13:40:55'),(4,'BRK-001','Front Brake Pads Set',4,1,2,3800.00,15,'Ceramic compound, low dust, quiet braking.',NULL,1,'2026-09-28 13:40:55'),(5,'BAT-001','Maintenance-Free Car Battery 12V 60Ah',5,2,2,9800.00,10,'2 year warranty, ready to install.',NULL,1,'2026-09-28 13:40:55'),(6,'SUS-001','Front Shock Absorber Pair',6,3,2,7200.00,8,'OEM-quality gas shock absorbers.',NULL,1,'2026-09-28 13:40:55'),(7,'ENG-001','Timing Belt Kit',7,1,1,6400.00,6,'Belt, tensioner and idler pulley kit.',NULL,1,'2026-09-28 13:40:55'),(8,'TY-002','215/60R16 Radial Tyre',1,4,3,9200.00,18,'All-season radial tyre for SUVs.',NULL,1,'2026-09-28 13:40:55'),(9,'FLT-002','Air Filter (universal fit)',3,7,3,950.00,40,'High-flow panel air filter.',NULL,1,'2026-09-28 13:40:55'),(10,'ACC-001','Wiper Blade Pair',9,1,1,1800.00,25,'All-weather silicone wiper blades.',NULL,1,'2026-09-28 13:40:55'),(11,'OIL-002','Semi-Synthetic Engine Oil 4L',2,10,2,2900.00,30,'10W-40 semi-synthetic, everyday use.',NULL,1,'2026-09-28 13:40:55'),(12,'BRK-002','Rear Brake Discs Pair',4,4,3,11500.00,7,'Vented rear brake discs, direct fit.',NULL,1,'2026-09-28 13:40:55'),(13,'ELEC-ALT-01','Alternator 12V 90A',10,1,1,9500.00,5,'Replacement alternator, fits most sedans.',NULL,1,'2026-10-01 08:52:17'),(14,'ELEC-HLB-01','LED Headlight Bulb Set',10,5,2,2800.00,20,'Bright white LED upgrade, plug and play.',NULL,1,'2026-10-01 08:52:17');
/*!40000 ALTER TABLE `spare_parts` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `role` enum('admin','client','mechanic','garage_owner') NOT NULL DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$zKjWIg61JqICs1IwygJIwujl4OyzMexzvQjCdjBSWw8VSlyqRaDU.','Administrator','admin@vehiclecare.test','0700123456',NULL,'admin','2026-09-28 13:25:15');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

