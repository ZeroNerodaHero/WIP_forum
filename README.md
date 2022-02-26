# Legal stuff
This uses google's captcha system

# WIP_forum
Test

structures of MYSQL database:
# DATA1(peepo):
------

TABLE badWord
SCHEMA
```
+-------+--------------+------+-----+---------+-------+
| Field | Type         | Null | Key | Default | Extra |
+-------+--------------+------+-----+---------+-------+
| word  | varchar(100) | YES  | UNI | NULL    |       |
+-------+--------------+------+-----+---------+-------+
```
NOTES:
	probably can use a text file instead
------
TABLE boards
SCHEMA
```
+-------------+-------------+------+-----+---------+-------+
| Field       | Type        | Null | Key | Default | Extra |
+-------------+-------------+------+-----+---------+-------+
| typeOfBoard | varchar(10) | NO   |     | NULL    |       |
| boardName   | varchar(10) | NO   |     | NULL    |       |
+-------------+-------------+------+-----+---------+-------+
```
------
TABLE frontNews
SCHEMA
```
+--------------+--------------+------+-----+-------------------+-----------------------------+
| Field        | Type         | Null | Key | Default           | Extra                       |
+--------------+--------------+------+-----+-------------------+-----------------------------+
| id           | int(11)      | NO   | PRI | NULL              | auto_increment              |
| news_title   | varchar(200) | YES  |     | NULL              |                             |
| post_time    | timestamp    | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| news_content | mediumtext   | YES  |     | NULL              |                             |
+--------------+--------------+------+-----+-------------------+-----------------------------+
```
------
TABLE ipBans
SCHEMA
```
+--------+--------------+------+-----+---------------------+-----------------------------+
| Field  | Type         | Null | Key | Default             | Extra                       |
+--------+--------------+------+-----+---------------------+-----------------------------+
| ip     | bigint(20)   | YES  | UNI | NULL                |                             |
| reason | varchar(300) | YES  |     | NULL                |                             |
| time   | timestamp    | NO   |     | CURRENT_TIMESTAMP   | on update CURRENT_TIMESTAMP |
| expire | timestamp    | NO   |     | 0000-00-00 00:00:00 |                             |
+--------+--------------+------+-----+---------------------+-----------------------------+
```
------
NEW TABLE:
create TABLE peepoAds(
	name varchar(255), 
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	linkToImg varchar(1024), 
	linkToSite varchar(1024) , 
	totalLoads int default 0, 
	totalClicks int default 0, 
	maxPoints int, 
	boardLimited varchar(255), 
	dateAdded timestamp);
```
+--------------+---------------+------+-----+-------------------+-----------------------------+
| Field        | Type          | Null | Key | Default           | Extra                       |
+--------------+---------------+------+-----+-------------------+-----------------------------+
| name         | varchar(255)  | YES  |     | NULL              |                             |
| id           | int(11)       | NO   | PRI | NULL              | auto_increment              |
| linkToImg    | varchar(1024) | YES  |     | NULL              |                             |
| linkToSite   | varchar(1024) | YES  |     | NULL              |                             |
| totalLoads   | int(11)       | YES  |     | NULL              |                             |
| totalClicks  | int(11)       | YES  |     | NULL              |                             |
| maxPoints    | int(11)       | YES  |     | NULL              |                             |
| boardLimited | varchar(255)  | YES  |     | NULL              |                             |
| dateAdded    | timestamp     | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
+--------------+---------------+------+-----+-------------------+-----------------------------+
```

#DATA2(peepoPost):
Two tables structures are used
[BOARDNAME] is used as a placeholder

------
TABLE [BOARDNAME]Threads
SCHEMA
```
+----------+--------------+------+-----+-------------------+-----------------------------+
| Field    | Type         | Null | Key | Default           | Extra                       |
+----------+--------------+------+-----+-------------------+-----------------------------+
| title    | varchar(300) | YES  |     | NULL              |                             |
| threadId | int(11)      | NO   | PRI | NULL              | auto_increment              |
| time     | timestamp    | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| tags     | varchar(50)  | YES  |     | NULL              |                             |
+----------+--------------+------+-----+-------------------+-----------------------------+
```
------
TABLE [BOARDNAME]
SCHEMA
```
+---------+---------------+------+-----+---------+----------------+
| Field   | Type          | Null | Key | Default | Extra          |
+---------+---------------+------+-----+---------+----------------+
| postId  | int(11)       | NO   | PRI | NULL    | auto_increment |
| time    | timestamp     | YES  |     | NULL    |                |
| content | varchar(7500) | YES  |     | NULL    |                |
| ip      | bigint(20)    | YES  |     | NULL    |                |
+---------+---------------+------+-----+---------+----------------+
```
