# ************************************************************
# Sequel Ace SQL dump
# Version 20042
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 192.168.1.34 (MySQL 8.0.31-0ubuntu0.22.04.1)
# Database: Collections
# Generation Time: 2022-11-21 20:41:30 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table Color
# ------------------------------------------------------------

CREATE TABLE `Color` (
  `ColorID` char(36) NOT NULL DEFAULT '',
  `ColorCode` varchar(40) NOT NULL DEFAULT '',
  `ColorName` varchar(40) NOT NULL DEFAULT '',
  `TextCode` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`ColorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `Color` WRITE;
/*!40000 ALTER TABLE `Color` DISABLE KEYS */;

INSERT INTO `Color` (`ColorID`, `ColorCode`, `ColorName`, `TextCode`)
VALUES
	('','','- NONE -',''),
	('3b3571a3-471e-4411-82e9-88c1e9811b47','#ffffff','Burnt Umber on White','#6e260e'),
	('70144400-13e4-4277-942e-818a60d074d9','#007f00','Green on White','#ffffff'),
	('744f4117-64a1-4128-ab17-de4df8a97411','#ffffff','Black on White','#000000'),
	('b4aabe21-e7f1-4dd1-a5d4-43d9fcb0804e','#00007f','Grey on Blue','#ffff00'),
	('cd9c0c3d-797c-4a72-9928-15609de75006','#ffffff','Red on White','#ff0000');

/*!40000 ALTER TABLE `Color` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Icon
# ------------------------------------------------------------

CREATE TABLE `Icon` (
  `IconID` char(36) NOT NULL DEFAULT '',
  `IconCode` varchar(40) NOT NULL DEFAULT '',
  `IconName` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`IconID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `Icon` WRITE;
/*!40000 ALTER TABLE `Icon` DISABLE KEYS */;

INSERT INTO `Icon` (`IconID`, `IconCode`, `IconName`)
VALUES
	('','','- NONE -'),
	('0ec13f38-c118-4087-86f2-2e15b30d0898','table_restaurant','Table'),
	('C65CC857-AE51-4798-A59C-7D2787A44E30','casino','Dice');

/*!40000 ALTER TABLE `Icon` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Item
# ------------------------------------------------------------

CREATE TABLE `Item` (
  `ItemID` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ItemName` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `UserID` char(36) NOT NULL,
  PRIMARY KEY (`ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table ItemLocation
# ------------------------------------------------------------

CREATE TABLE `ItemLocation` (
  `ItemLocationID` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ItemID` char(36) NOT NULL,
  `LocationID` char(36) NOT NULL,
  PRIMARY KEY (`ItemLocationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table ItemTag
# ------------------------------------------------------------

CREATE TABLE `ItemTag` (
  `ItemTagID` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `TagID` char(36) NOT NULL,
  `ItemID` char(36) NOT NULL,
  PRIMARY KEY (`ItemTagID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table Location
# ------------------------------------------------------------

CREATE TABLE `Location` (
  `LocationID` char(36) NOT NULL,
  `LocationName` varchar(200) NOT NULL,
  `UserID` char(36) NOT NULL,
  `ColorID` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `IconID` char(36) NOT NULL,
  PRIMARY KEY (`LocationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table Log
# ------------------------------------------------------------

CREATE TABLE `Log` (
  `LogID` char(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `LogSeverity` int NOT NULL,
  `LogType` int NOT NULL,
  `LogUserID` char(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `LogDateTime` datetime NOT NULL,
  `LogMessage` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`LogID`),
  KEY `LogDateTime` (`LogDateTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table Photo
# ------------------------------------------------------------

CREATE TABLE `Photo` (
  `PhotoID` char(36) NOT NULL,
  `ItemID` char(36) NOT NULL,
  `PhotoData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`PhotoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table Setting
# ------------------------------------------------------------

CREATE TABLE `Setting` (
  `Key` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Value` varchar(200) NOT NULL,
  PRIMARY KEY (`Key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table Tag
# ------------------------------------------------------------

CREATE TABLE `Tag` (
  `TagID` char(36) NOT NULL,
  `TagName` varchar(200) NOT NULL,
  `UserID` char(36) NOT NULL,
  `IconID` char(36) NOT NULL,
  `ColorID` char(36) NOT NULL,
  PRIMARY KEY (`TagID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



# Dump of table User
# ------------------------------------------------------------

CREATE TABLE `User` (
  `UserID` char(36) NOT NULL,
  `UserName` varchar(200) NOT NULL,
  `PasswordHash` varchar(200) NOT NULL,
  `Active` int NOT NULL,
  `Admin` int NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
