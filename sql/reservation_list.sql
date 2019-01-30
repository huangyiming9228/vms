/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : vms

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-01-30 17:25:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for reservation_list
-- ----------------------------
DROP TABLE IF EXISTS `reservation_list`;
CREATE TABLE `reservation_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `tel` varchar(11) CHARACTER SET utf8mb4 NOT NULL,
  `car_no` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `visit_time` datetime NOT NULL,
  `visit_reason` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `submitter` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `submitter_no` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `submit_time` datetime NOT NULL,
  `status` int(1) NOT NULL,
  `audit_remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `audit_time` datetime DEFAULT NULL,
  `audit_person` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `audit_person_no` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of reservation_list
-- ----------------------------
INSERT INTO `reservation_list` VALUES ('23', '黄从富', '17780110770', '川A12345', '2019-01-29 07:00:00', '探访亲友', '开发', 'admin', '2019-01-28 14:16:25', '2', '车辆已进入我校黑名单，不予入校。', '2019-01-30 17:20:30', '开发', 'admin');
INSERT INTO `reservation_list` VALUES ('24', '黄从富', '17780110770', '川A23456', '2019-01-29 08:00:00', '探访亲友', '开发', 'admin', '2019-01-28 14:16:29', '1', '同意入校。', '2019-01-30 16:10:21', '开发', 'admin');
INSERT INTO `reservation_list` VALUES ('25', '黄从富', '17780110770', '川A34567', '2019-01-29 10:00:00', '探访亲友', '开发', 'admin', '2019-01-28 14:16:32', '2', '车辆在黑名单内，不予入校。', '2019-01-29 16:10:49', '开发', 'admin');
INSERT INTO `reservation_list` VALUES ('26', '黄从富', '17780110770', '川A12345', '2019-01-29 07:00:00', '探访亲友', '开发', 'admin', '2019-01-28 14:16:35', '0', '', '2019-01-30 17:19:06', null, null);
