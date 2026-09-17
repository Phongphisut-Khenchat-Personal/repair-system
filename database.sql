-- ระบบแจ้งซ่อมทั่วไป
-- สร้างฐานข้อมูล ตาราง และผู้ใช้งานแอปพลิเคชัน
-- อย่าใช้ root เป็นผู้ใช้งานของแอปพลิเคชัน

CREATE DATABASE IF NOT EXISTS repair_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE repair_system;

CREATE TABLE IF NOT EXISTS repairs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  repair_date DATETIME NOT NULL,
  reporter VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- บัญชีเข้าใช้งานระบบ: admin / Admin123!
-- เปลี่ยนรหัสผ่านก่อนใช้งานจริง
INSERT IGNORE INTO users (username, password_hash, display_name)
VALUES (
  'admin',
  '$2y$10$eWbCF3S3Nyd9JcAvmPxc3.lO0BF9WhQ6ey8TDEqxG.kpTNlDdwWSm',
  'ผู้ดูแลระบบ'
);

-- ผู้ใช้งานฐานข้อมูลสำหรับแอปพลิเคชัน
-- ตัวอย่างรหัสผ่านด้านล่างใช้สำหรับทดสอบเท่านั้น
CREATE USER IF NOT EXISTS 'repair_app'@'localhost' IDENTIFIED BY 'ChangeMe_RepairApp_123!';
CREATE USER IF NOT EXISTS 'repair_app'@'127.0.0.1' IDENTIFIED BY 'ChangeMe_RepairApp_123!';

GRANT SELECT, INSERT, UPDATE, DELETE ON repair_system.* TO 'repair_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON repair_system.* TO 'repair_app'@'127.0.0.1';

FLUSH PRIVILEGES;
