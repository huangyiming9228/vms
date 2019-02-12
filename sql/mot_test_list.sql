/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : vms

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-02-12 17:31:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mot_test_list
-- ----------------------------
DROP TABLE IF EXISTS `mot_test_list`;
CREATE TABLE `mot_test_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `applicant` varchar(50) NOT NULL,
  `car_owner` varchar(50) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `car_no` varchar(10) NOT NULL,
  `tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `displacement` varchar(20) DEFAULT NULL,
  `applicant_unit` varchar(255) NOT NULL,
  `apply_type` varchar(20) NOT NULL,
  `submitter` varchar(50) NOT NULL,
  `submitter_no` varchar(20) NOT NULL,
  `submit_time` datetime NOT NULL,
  `status` int(1) NOT NULL,
  `audit_person` varchar(50) DEFAULT NULL,
  `audit_person_no` varchar(20) DEFAULT NULL,
  `audit_time` datetime DEFAULT NULL,
  `audit_remark` varchar(255) DEFAULT NULL,
  `driving_license_id` int(10) NOT NULL,
  `campus_card_id` int(10) NOT NULL,
  `relationship_proof_id` int(10) DEFAULT NULL,
  `payment_proof_id` int(10) DEFAULT NULL,
  `loan_agreement_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of mot_test_list
-- ----------------------------
