<?php
    
require_once 'BorderServer.php';
require_once 'CommonFun.php';

class BorderFunc{
    
    //udp收包程序
    public static function onPacket($serv, $data, $cliInfo){
        
        $ip = $cliInfo['address'];
        //先判断包来自内网还是外网
        if($ip == "127.0.0.1"){
            //来自内网
            //对包做解析，并最终获取目标 ip,port
            $border = BorderServer::parseData($data);
            $isOn = isOnline($border['id'], $border['no']);
$data = BorderServer::packPacket($border, $cliInfo['port']);
var_dump($data);
            if($isOn != 0){
                //终端在线时对包进行加工处理(同时追加内网发出的端口号)          
                $data = BorderServer::packPacket($border, $cliInfo['port']);
                //将包发出
                $serv->sendto($isOn['ip'], $isOn['port'], $data);
            }else{
                //客户端不在线,返回-1
                $serv->sendto("127.0.0.1", $cliInfo['port'], "-1");
            }
        }else{
            //来自外网
            //对包做过滤和解析，包不完整返回 0，否则返回一个数组,
            $border = parsePacket2($data);
            if($border != 0){
                //必要参数齐全时往下执行
                if(isset($border['ty']) && isset($border['id']) && isset($border['ky']) && isset($border['no'])){

                    /*
                     * 这里先不检查密码正确性，而是先检查是否需要更新本地用户数据库。
                     * 如果终端接收到来自服务器的密码和本地密码比对不上的，
                     * 极有可能是因为本地密码没有和服务器同步，因此要先更新本地用户密码表。
                     * 这样做可能不太安全，但我们的目的是必须保证已注册终端总是能够连上服务器。
                     * by Jaslew
                     */

                    //对于收到的包检查是否有待处理事务，如更新用户表的操作
                    $tags = BorderServer::getStationTag($border['id'], $border['no']);
                    //不是状态告知包，也不是第一次初始化的包
                    if($tags && !isset($border['state']) && !isset($border['aa'])) {
                        //要求终端更新当前站点本地用户表
                        if ($tags[0] == 1) {
                            $ud = getIdTable($border['id']);
                            $str = "(|no" . $border['no'] . "|tyorder|id" . $border['id'] . "|ky" . $border['ky'] . "|ud" . $ud . "|tg1|)";
                            $str = packPacket($str);
                            $serv->sendto($ip, $cliInfo['port'], $str);
                        }elseif ($tags[1] == 1) {
                            //要求终端更新当前站点 hz
                            $hz = getStationHz($border['id'], $border['no']);
                            $str = "(|no" . $border['no'] . "|tyorder|id" . $border['id'] . "|ky" . $border['ky'] . "|hz" . $hz . "|tz1|)";
                            $str = packPacket($str);
                            $serv->sendto($ip, $cliInfo['port'], $str);
                        }
                    }

                    //用户名密码校验
                    $b = pwdCheck($border['id'], $border['ky']);

                    if($b == 1){

                            /*                 ****************************************
                            *                  **********用户身份合法时才往下执行**********
                            *                  ****************************************
                            */

                        $border['ip'] = $cliInfo['address'];
                        $border['port'] = $cliInfo['port'];

                        switch ($border['ty']){
                            
                            //更新设备信息，同时记录数据
                            case $border['ty'] == "data":{

                                $serv->task($border);
                                break;

                            }//case data end
                            
                            case $border['ty'] == "state":{

                                //由内网发出的包，将结果反馈到内网
                                if(isset($border['pt'])){

                                    $serv->sendto("127.0.0.1", $border['pt'], $border['ss']);

                                }

                                //更新终端用户密码表的包
                                elseif(isset($border['tg'])){

                                    //如果是更新成功的反馈包,则tag减1
                                    if($border['tg'] == 0)
                                        BorderServer::setStationTag1($border['id'], $border['no']);

                                }

                                //更新终端 hz 的包
                                elseif(isset($border['tz'])){

                                    //如果是修改 hz 成功的反馈包
                                    if($border['tz'] == 0)
                                        BorderServer::setStationTag2($border['id'], $border['no']);

                                }

                                //需要转发给客户端的包
                                else{

                                    //客户端在线则将反馈转发给客户端
                                    $fd = BorderServer::getClient($border['id']);
                                    if($fd > 0){
                                        $serv->send($fd, $data);
                                    }

                                }

                                break;

                            }//case state end
                            
                            case $border['ty'] == "command":{

                                if(isset($border['aa'])){
                                    //初始化终端本地数据
                                    $ud = getIdTable($border['id']);
                                    $hz = getStationHz($border['id'], $border['no']);
                                    if($ud && $hz){
                                        $data = "(|no".$border['no']."|tydata|id".$border['id']."|ky".$border['ky'];
                                        $data .= "|hz".$hz."|ud".$ud."|)";
                                        $data = packPacket($data);
                                        $serv->sendto($ip, $cliInfo['port'], $data);
                                    }
                                    //将tg,tz设置为0
                                    BorderServer::setStationTag3($border['id'], $border['no']);
                                }
                                
                                break;

                            }//case command end
                            
                            
                        }//switch end
                    
                    }
                }
            }
        }//else
        
    } 
    
    //task异步非阻塞任务
    public static function onTask($serv, $task_id, $from_id, $data){
        //将包中的设备信息（no,ip,port,time）存储到redis或mysql数据库
        //先检查当前站点是否合法
        if(BorderServer::noCheck($data['id'], $data['no']) == 1){
            BorderServer::setRecord($data['id'], $data['no'], $data['ip'], $data['port']);
            BorderServer::setData($data);
            //检查数据，设置通知
            BorderServer::setNotice($data);
        }
    }
    
    public static function onFinish($serv, $task_id, $data){
        //
    }
}
