<?php
/**
 * @author Jaslew
 * @date 2017/9/7
 * 9501：客户端
 * 9502: 控制端和内网
 */
require_once 'includes/CliFunc.php';
require_once 'includes/BorderFunc.php';
require_once 'includes/Log.class.php';

class Server{

        private $serv;
        
        public function __construct(){
            //主端口9501 用于监听客户端请求 tcp连接
            $this->serv = new swoole_server('0.0.0.0', 9501);
            
            //附加端口9502 监听控制器以及内网的udp包
            $this->serv->listen('0.0.0.0', 9502, SWOOLE_SOCK_UDP);
            
            //端口参数绑定
            $this->serv->set(parse_ini_file(dirname(__FILE__).'/configs/config.ini',true)['serv']);
            
            //tcp函数绑定
            $this->serv->on('connect', 'CliFunc::onConnect');
            $this->serv->on('receive', 'CliFunc::onReceive');
            $this->serv->on('close', 'CliFunc::onClose');
            
            //udp函数绑定
            $this->serv->on('packet', 'BorderFunc::onPacket');
            $this->serv->on('task', 'BorderFunc::onTask');
            $this->serv->on('finish', 'BorderFunc::onFinish');

            //服务器重启事件日志记录
            $this->serv->on('workerstart', function ($serv, $workerId){
                Log::write("reload","worker".$workerId." reloaded");
            });

            //服务器关闭事件日志记录
            $this->serv->on('workerstop', function ($serv, $workerId){
                Log::write("stop","worker".$workerId." stopped");
            });

            //启动服务
            $this->serv->start();
        }        
    }

    $server = new Server();

    Log::write("start","server started");
