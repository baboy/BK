drop table wp_media_content;
truncate table wp_media;
truncate table wp_media_attr;
truncate table wp_media_serial_video;
CREATE TABLE `wp_media_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `content` longtext,
  UNIQUE KEY `id` (`id`),
  KEY `sid` (`sid`),
  CONSTRAINT `wp_media_content_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `wp_media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

truncate wp_media_list;
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',1,'爱情');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',2,'战争');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',3,'喜剧');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',4,'科幻');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',5,'恐怖');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',6,'动画');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',7,'动作');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',8,'风月');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',9,'剧情');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',10,'歌舞');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',11,'纪录');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',12,'魔幻');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',13,'灾难');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',14,'悬疑');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',15,'传记');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',16,'警匪');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',17,'伦理');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',18,'惊悚');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',19,'谍战');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',20,'历史');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',21,'武侠');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',22,'青春');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',23,'文艺');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','type',24,'其它');

insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',1,'内地');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',2,'香港');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',3,'台湾');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',4,'日本');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',5,'韩国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',6,'美国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',7,'英国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',8,'法国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',9,'德国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',10,'意大利');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',11,'西班牙');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',12,'俄罗斯');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',13,'加拿大');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',14,'印度');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',15,'泰国');
insert into wp_media_list(gid,`key`,`index`,value) values('movie','area',16,'其它');


insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',1,'偶像');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',2,'家庭');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',3,'历史');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',4,'年代');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',5,'言情');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',6,'武侠');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',7,'古装');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',8,'都市');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',9,'农村');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',10,'军旅');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',11,'刑侦');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',12,'喜剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',13,'悬疑');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',14,'情景');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',15,'传记');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',16,'科幻');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',17,'动画');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',18,'动作');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',19,'真人秀');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',20,'栏目');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',21,'谍战');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',22,'伦理');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',23,'战争');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',24,'神话');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',25,'惊悚');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','type',26,'其它');

insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',1,'内地');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',2,'港剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',3,'台剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',4,'美剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',5,'韩剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',6,'英剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',7,'泰剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',8,'日剧');
insert into wp_media_list(gid,`key`,`index`,value) values('serial','area',9,'其它');