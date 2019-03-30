# Water-quality-monitoring-system
#工程文件夹包括网页前端代码，网站后台，服务器配置和数据库配置代码。
该项目主要实现基于linux系统的云端平台的搭建，用户可通过该平台实现手机实时查看下位机采集的水质参数,下位机开关实现实时控制,用户管理等功能。
开发者需根据提供的接口自行实现下位机和手机app的开发。

#服务器基本环境:
php7(必要模块：mysqli,gd(支持jpeg),mcrypt,redis,swoole),apache,mysql,swoole,redis。

#根目录：
config--包含服务器配置，数据库以及其它配置信息。
includes--服务器核心php代码。
log--服务器状态和错误日志。
web--网站前端和后台代码。
doc--手机app和下位机开发接口文档。
server.php--服务器启动程序
