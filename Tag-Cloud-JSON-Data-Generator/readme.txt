Tag Cloud JASON Data Generator v1.02 9/19/2012 by Mark Wong
----------------------------------------------------------

DESCRIPTION:
-----------
An application that accesses comment data from database parameterized by user_id and website_id, parses it into keywords, calculates frequency of keywords and encodes this data into JSON format suitable for a tag cloud generator.

ASSUMPTIONS:
-----------
Tag cloud generator will size tags based on frequency of tag in comment data
Use PHP Data Objects (PDO) for database access so sql injection protection is inherent

DEVELOPMENT ENVIRONMENT/REQUIREMENTS:
-------------------------------------
- Ubuntu Linux v11.04
- Apache Httpd Server v2.4
- PHP v5.3.15
- MySQL v5.1.54
- Chrome browser v21.x

INSTALLATION INSTRUCTIONS:
--------------------------
database:
	- create database `comments_db`;
	- run comments.sql script
		- mysql -uusername -ppassword comments_db < comments.sql

source code:
	- drop form.php, header.php and generate_json_tag_cloud.php into web root folder, change folder/folder permissions as necessary'
	- configure database parameters in header.php
	- hit http://<hostname>/form.php


VERSION HISTORY:
--------------------------
v1.01	9/19/2012	added measures against xss attacks
v1.02	9/20/2012	fixed potential bug involving mysql_real_escape_string
