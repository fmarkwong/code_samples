/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50515
 Source Host           : localhost
 Source Database       : comments_db

 Target Server Type    : MySQL
 Target Server Version : 50515
 File Encoding         : utf-8

*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `comments`
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `website_id` smallint(5) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `comments`
-- ----------------------------
BEGIN;
INSERT INTO `comments` VALUES ('1', '1', '1', 'I love this site, I really enjoyed the testimonies'), ('7', '4', '3', 'This site for discipleship is very useful'), ('2', '1', '2', 'I\'m not sure what the point of this site was, I liked godlife better'), ('3', '2', '2', 'Jesus is the Way, the Truth and the Life!'), ('4', '3', '2', 'Jesus has been so good to me!'), ('5', '4', '1', 'Jesus is my Lord and Savior!'), ('6', '4', '2', 'Please pray for me, that God would help me, thank you.'), ('8', '2', '3', 'I can see that discipleship is a big part of this site.'), ('9', '1', '3', 'oh, wow!  this is going to really help me with my discipleship group!');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
