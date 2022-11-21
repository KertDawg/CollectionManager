
CREATE DATABASE Collections;
CREATE USER 'collections'@'localhost' IDENTIFIED BY 'Th3D@t3rbaSePa#s';
GRANT SELECT,DELETE,UPDATE,INSERT ON Collections.* TO 'collections'@'localhost';
