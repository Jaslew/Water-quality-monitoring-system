drop database if exists server3;
create database server3;
use server3;


/*创建一个终极用户时，要先在role表中添加
删除一个终极用户时，只需要删除role表中belongid为指定id的记录即可
同步创建switch表*/

/*最高管理员id*/
create table role(
  belongid varchar(7),
  primary key(belongid)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*管理员或用户id,所属管理员id,所属角色，管理员或用户姓名，密码, 用户上次登录时间，头像*/
create table admin(
  id varchar(7),
  belongid varchar(7),
  roleid tinyint unsigned,
  name varchar(32),
  password varchar(40),
  lasttime int unsigned,
  header varchar(32),
  tel varchar(16),
  email varchar(32),
  foreign key(belongid) references role(belongid)
    on delete cascade
    on update cascade,
  primary key(id,belongid)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*站点所属人id，站点号，站点名，位置，采样频率,修改时间，创建时间，电话，邮箱，负责人,更新终端用户表标志，更新终端频率标志
注：添加站点时要同步在border,switch中添加记录，删除站点时，会删除数据库中所有相关记录
*/
create table station(
  belongid varchar(7),
  no varchar(3),
  name varchar(32),
  pos varchar(128),
  hz varchar(32),
  atime int unsigned,
  ctime int unsigned,
  tel varchar(16),
  email varchar(32),
  charge varchar(32),
  tag1 tinyint default 0,
  tag2 tinyint default 0,
  foreign key(belongid) references role(belongid)
    on delete cascade
    on update cascade,
  primary key(belongid,no)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*站点所属人id，站点号，ip ,port ,在线凭据*/
create table border(
  belongid varchar(7),
  no varchar(3),
  ip varchar(15),
  port varchar(5),
  time int unsigned,
  foreign key(belongid,no) references station(belongid,no)
    on delete cascade
    on update cascade,
  primary key(belongid,no)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*开关设置*/
create table switch(
  belongid varchar(7),
  no varchar(3),
  sw1 varchar(32) default "开关1",
  sw2 varchar(32) default "开关2",
  sw3 varchar(32) default "开关3",
  sw4 varchar(32) default "开关4",
  sw5 varchar(32) default "开关5",
  sw6 varchar(32) default "开关6",
  sw7 varchar(32) default "开关7",
  sw8 varchar(32) default "开关8",
  foreign key(belongid,no) references station(belongid,no)
    on delete cascade
    on update cascade,
  primary key(belongid,no)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*通知表*/
create table notice(
  id varchar(7),
  time int unsigned,
  isread bool,
  content varchar(128),
  foreign key(id) references admin(id)
    on delete cascade
    on update cascade,
  primary key(id, time)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*一个月一张表。如有一个用户从2017.1.1开始存储数据，到2019.1.1开始将删除2017.1.1的数据，到2019.2.1将删除2017.2.1的数据，如此循环*/
/*即数据最多保存2年时间，表名为 “_YM”，Y为四位数年，M为两位数月*/
/*所属管理员id,站点号，更新时间，tm温度，ph酸碱度，ox含氧量，el导电率，nt浊度，po总磷，nh氨氮，cl氯含量，ca碳含量*/

create table _201804(
  belongid varchar(7),
  no varchar(3),
  time int unsigned,
  tm varchar(6),
  ph varchar(6),
  ox varchar(6),
  el varchar(6),
  nt varchar(6),
  po varchar(6),
  nh varchar(6),
  cl varchar(6),
  ca varchar(6),
  foreign key(belongid,no) references station(belongid,no)
    on delete cascade
    on update cascade,
  primary key(belongid,no,time)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*测试数据*/

insert into role values('0000001');

insert into admin values('0000001','0000001',1,'jaslew',md5(123456),1507110642,'','131215','dwe@qq.com');
insert into admin values('0002101','0000001',2,'jaslew',md5(123456),1507110642,'','131215','dwe@qq.com');
insert into admin values('0002102','0000001',2,'jaslew',md5(123456),1507110642,'','131215','dwe@qq.com');
insert into admin values('0002103','0000001',2,'jaslew',md5(123456),1507110642,'','131215','dwe@qq.com');


insert into station values('0000001','001','JNU','WX','25',1507110642,1507110642,'13093028699','lq1007f@qq.com','jaslew','0',0);
insert into station values('0000001','102','JNU','WX','25',1507110642,1507110642,'13093028699','lq1007f@qq.com','jaslew','0',0);
insert into station values('0000001','103','JNU','WX','25',1507110642,1507110642,'13093028699','lq1007f@qq.com','jaslew','0',0);
insert into station values('0000001','104','JNU','WX','25',1507110642,1507110642,'13093028699','lq1007f@qq.com','jaslew','0',0);

insert into border values('0000001','001','','',1507110642);
insert into border values('0000001','102','','',1507110642);
insert into border values('0000001','103','','',1507110642);
insert into border values('0000001','104','','',1507110642);

insert into switch(belongid, no) values('0000001','001');
insert into switch(belongid, no) values('0000001','102');
insert into switch(belongid, no) values('0000001','103');
insert into switch(belongid, no) values('0000001','104');

insert into _201805 values('0000001','001',1525745725,'17.1','7.23','8.45','87','0.214','15.3','16.2','13.1','14.01');
insert into _201805 values('0000001','001',1525745735,'17.5','7.13','8.53','78','0.114','14.0','16.5','13.1','12.41');
insert into _201805 values('0000001','001',1525745745,'17.8','7.45','8.54','98','0.124','13.0','16.4','12.1','13.01');
insert into _201805 values('0000001','001',1525745755,'17.9','7.36','8.11','92','0.432','14.0','16.4','13.14','12.01');
insert into _201805 values('0000001','001',1525745765,'16.8','7.00','7.22','78','0.354','12.0','16.5','12.31','15.01');
insert into _201805 values('0000001','001',1525745775,'16.5','7.76','8.54','74','0.545','15.0','16.8','11.1','12.61');
insert into _201805 values('0000001','001',1525745785,'16.1','7.57','9.25','93','0.143','15.6','16.9','14.5','11.01');
insert into _201805 values('0000001','001',1525745795,'17.2','7.23','6.44','92','0.655','13.0','16.1','16.5','12.31');
insert into _201805 values('0000001','001',1525745805,'16.0','7.96','8.16','84','0.323','15.2','16.2','14.2','14.01');
insert into _201805 values('0000001','001',1525745815,'17.4','7.32','8.25','87','0.014','15.0','16.4','13.1','12.01');

insert into notice values("0000001",1524905205,0,"测试消息");
insert into notice values("0000001",1524905206,0,"测试消息");
insert into notice values("0000001",1524905207,0,"测试消息");
insert into notice values("0000001",1524905208,0,"测试消息");
insert into notice values("0000001",1524905209,0,"测试消息");
insert into notice values("0000001",1524905211,0,"测试消息");
insert into notice values("0000001",1524905212,0,"测试消息");
insert into notice values("0000001",1524905213,0,"测试消息");
insert into notice values("0000001",1524905214,0,"测试消息");
insert into notice values("0000001",1524905215,0,"测试消息");
insert into notice values("0000001",1524905215,0,"测试消息");
