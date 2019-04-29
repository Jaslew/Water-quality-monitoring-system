# Water-quality-monitoring-system
#工程文件夹包括网页前端代码，网站后台，服务器配置和数据库配置代码。  
该项目主要实现基于linux系统的云端平台的搭建，开发者需根据提供的接口自行实现下位机和手机app的开发。  
用户可实现功能包括：  
1.以报表或图表形式查看历史或实时水质数据，如水温，溶解氧，电导率等。  
2.可添加或删除采集数据的站点，设置采样频率，查看站点信息等。    
3.可添加或删除多个站点管理员，实现权限控制。  
4.可对下位机预留的开关实现远程实时控制。  

#服务器基本环境:  
php7(必要模块：mysqli,gd(支持jpeg),mcrypt,redis,swoole),apache,mysql,swoole,redis。

#根目录：  
config--包含服务器配置，数据库以及其它配置信息。  
includes--服务器核心php代码。  
log--服务器状态和错误日志。  
web--网站前端和后台代码。  
doc--手机app和下位机开发接口文档。  
server.php--服务器启动程序。  

#docker
docker pull jaslew2019/i-centos:v1
