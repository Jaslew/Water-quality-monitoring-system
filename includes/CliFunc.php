<?php
/**
 * @author Jaslew
 * @uses 端口回调方法
 */

require_once 'CliServer.php';
require_once 'CommonFun.php';
require_once 'Log.class.php';

class CliFunc{
    
    //连接程序
    public static function onConnect($serv, $fd, $from_id){
        //记录用户连接事件
        Log::write("connect", "client".$fd." connected");
    }
    
    //收包程序
    public static function onReceive($serv, $fd, $from_id, $data){

        //对包做过滤和解析，包不完整返回 0，否则返回一个数组,
        $client = parsePacket($data);

        //包合法时往下执行
        if($client != 0){

            //必要参数齐全时执行
            if(isset($client['ty']) && isset($client['id']) && isset($client['ky'])){

                //检查用户名和密码
                $goOn = pwdCheck($client['id'], $client['ky']);

                if($goOn == 0){

                    //用户身份不合法，回复状态码并断开连接
                    $str = "(|tystate|id".$client['id']."|ky".$client['ky']."|st004|)";
                    $str = packPacket($str);
                    $serv->send($fd, $str);
                    $serv->close($fd, true);

                }else{

                    /*                  ****************************************
                     *                  **********用户身份合法时才往下执行**********
                     *                  ****************************************
                     */

                    //记录用户操作事件
                    Log::write($client['ty'], "client".$fd);

                    //若redis中没有记录，从mysql中拷贝记录到redis
                    CliServer::setRecord($client['id'],$fd);
                    
                    //针对用户指令做相应处理
                    switch ($client['ty']){
                        //登录操作
                        case $client['ty'] == "login":{
                            //如果用户是登录操作，返回一个用户名和上次登录时间同时更新登录时间
                            $loginInfo = CliServer::getNT($client['id']);
                            $str = "(|tystate|id".$client['id']."|ky".$client['ky']."|st002|na";
                            $str .= $loginInfo['name']."|ti".$loginInfo['time']."|)";
                            $str = packPacket($str);
                            $serv->send($fd, $str);

                            break;
                        }//case login end
                        
                        //指令操作
                        case $client['ty'] == "order":{
                            //对站点的操作
                            if (isset($client['at'])){
                                switch ($client['at']){
                                    case $client['at'] == "add":{
                                        //增加站点
                                        $r = CliServer::addStation($client);
                                        if($r == 1){
                                            $st = "002";
                                        }elseif($r == 0){
                                            $st = "001";
                                        }else{
                                            $st = "004";
                                        }

                                        break;
                                    }
                                    case $client['at'] == "del":{
                                        //删除站点
                                        $r = CliServer::delStation($client);
                                        if($r == 1){
                                            $st = "002";
                                        }elseif($r == 0){
                                            $st = "001";
                                        }else{
                                            $st = "004";
                                        }

                                        break;
                                    }
                                    case $client['at'] == "alter":{
                                        //修改站点
                                        $r = CliServer::alterStation($client);
                                        if($r == 1){
                                            $st = "002";
                                        }elseif($r == 0){
                                            $st = "001";
                                        }else{
                                            $st = "004";
                                        }
                                        //将hz标记设置为1
                                        if(isset($client['hz']) && $st == "002"){
                                            CliServer::setStationTag2($client['id'], $client['no']);
                                            //发送修改hz指令到在线终端
                                            $data = "(|tyorder|id".$client['id']."|ky".$client['ky']."|hz".$client['hz']."|tz1|)";
                                            $data = packPacket($data);
                                            $borders = getBorderOnLine($client['id']);
                                            if($borders){
                                                foreach ($borders as $border){
                                                    $ip = $border['ip'];
                                                    $port = $border['port'];
                                                    $serv->sendto($ip, $port, $data);
                                                }
                                            }
                                        }

                                        break;
                                    }//case 
                                }//switch
                                
                                //给客户端回复
                                $data = "(|tystate|id".$client['id']."|ky".$client['ky']."|rq".$client['rq']."|st".$st."|)";
                                $data = packPacket($data);
                                $serv->send($fd, $data);
                                
                            }

                            //增删改用户的操作
                            elseif(isset($client['au'])){
                                switch ($client['au']){
                                    case $client['au'] == "add":{
                                        $newid = CliServer::addUser($client);
                                        if($newid == 0){
                                            $st = "001";
                                            $str = "|st001|)";
                                        }elseif ($newid == -1){
                                            $st = "004";
                                            $str = "|st004|)";
                                        }else{
                                            $st = "002";
                                            $str = "|ni".$newid."|st002|)";
                                        }

                                        $data = "(|tystate|id".$client['id']."|ky".$client['ky']."|rq".$client['rq'].$str;
                                        $data = packPacket($data);
                                        $serv->send($fd, $data);

                                        break;
                                    }
                                    case $client['au'] == "del":{
                                        $r = CliServer::delUser($client);
                                        if($r == 1){
                                            $st = "002";
                                        }elseif($r == 0){
                                            $st = "001";
                                        }else{
                                            $st = "004";
                                        }

                                        $data = "(|tystate|id".$client['id']."|ky".$client['ky']."|rq".$client['rq']."|st".$st."|)";
                                        $data = packPacket($data);
                                        $serv->send($fd, $data);

                                        break;
                                    }
                                    case $client['au'] == "alter":{
                                        $r = CliServer::alterUserRole($client);
                                        if($r == 1){
                                            $st = "002";
                                        }elseif($r == 0){
                                            $st = "001";
                                        }else{
                                            $st = "004";
                                        }


                                        $data = "(|tystate|id".$client['id']."|ky".$client['ky']."|rq".$client['rq']."|st".$st."|)";
                                        $data = packPacket($data);
                                        $serv->send($fd, $data);

                                        break;
                                    }


                                }//switch

                                if($st == "002"){
                                    //更新所有在线终端用户数据表
                                    //将数据库中当前id对应belongid所有站点更新本地表标志都为 1
                                    CliServer::setStationTag0($client['id']);
                                    //发送修改用户表指令到在线终端
                                    $borders = getBorderOnLine($client['id']);
                                    if($borders){
                                        $ud = getIdTable($client['id']);
                                        $data = "(|tyorder|id".$client['id']."|ky".$client['ky']."|ud".$ud."|tg1|)";
                                        $data = packPacket($data);
                                        foreach ($borders as $border){
                                            $ip = $border['ip'];
                                            $port = $border['port'];
                                            $serv->sendto($ip, $port, $data);
                                        }
                                    }
                                }

                            }//elseif end


                            //对终端的直接操作
                            else{
                                
                                $isOn = isOnline($client['id'], $client['no']);
                                if($isOn != 0){
                                    //在线时转发
                                    $serv->sendto($isOn['ip'], $isOn['port'], $data);
                                }else{
                                    //不在线回复状态码
                                    $str = "(|tystate|no".$client['no']."|id".$client['id']."|ky".$client['ky']."|st003|)";
                                    $str = packPacket($str);
                                    $serv->send($fd, $str);
                                }
                                
                            }//else
                                
                            break;
                        }//case order end
                        
                        //修改服务器登录用户名，密码的操作
                        case $client['ty'] == "set":{

                            if(!isset($client['rq']))
                                $client['rq'] = null;
                            if(!isset($client['rn']))
                                $client['rn'] = null;
                            if(!isset($client['rp']))
                                $client['rp'] = null;
                            
                            //重置数据库用户名，密码。结果返回到 st
                            $st = CliServer::setUserSecret($client['rn'], $client['rp'], $client['id']);
                            $str = "(|tystate|id".$client['id']."|ky".md5($client['rp'])."|rq".$client['rq']."|st".$st."|)";
                            $str = packPacket($str);
                            $serv->send($fd, $str);
                            
                            //重置redis中存储的密码
                            CliServer::resetRedisPW($client['id'], $client['rp']);
                            
                            //用户有修改密码的指令且修改成功
                            if($client['rp'] != null && $st != "001"){
                                //获取当前id所属id的用户数据表
                                $ud = getIdTable($client['id']);
                                $data = "(|tyorder|id".$client['id']."|ky".$client['ky']."|ud".$ud."|tg1|)";
                                $data = packPacket($data);
                                //将数据库中当前id对应belongid所有站点更新本地表标志都为 1
                                CliServer::setStationTag1($client['id']);
                                //发送修改用户表指令到在线终端
                                $borders = getBorderOnLine($client['id']);
                                if($borders){
                                    foreach ($borders as $border){
                                        $ip = $border['ip'];
                                        $port = $border['port'];
                                        $serv->sendto($ip, $port, $data);
                                    }
                                }
                            }
                            break;
                        }//case set end
                        
                        //响应客户端请求
                        case $client['ty'] == "command":{
                            //客户端请求获取站点信息
                            if(isset($client['gn'])){
                                $gn = $client['gn'];
                                $stations = getStationState($client['id']);
                                $data = "(|tydata|id".$client['id']."|ky".$client['ky']."|rq".$client['rq'];
                                //获取所有站点在线信息
                                if ($stations && $gn == "000"){
                                    $i = 1;
                                    foreach ($stations as $n => $state){
                                        $data .= "|n".$i.$n.$state;
                                        $i++;
                                    }
                                    $data .= "|st002";
                                }elseif($stations && $gn != "000"){
                                   $data .= isset($stations[$gn]) ? ("|n1".$gn.$stations[$gn]."|st002") : ("|st004");
                                }else{
                                    $data .= "|st001";
                                }
                                $data .= "|)";
                                $data = packPacket($data);
                                $serv->send($fd, $data);
                            }// 获取站点信息 if end
                            
                            //客户端请求获取终端历史数据
                            elseif(isset($client['tm']) || isset($client['ph']) || isset($client['ox']) ||
                               isset($client['el']) || isset($client['nt']) || isset($client['po']) ||
                               isset($client['nh']) || isset($client['cl']) || isset($client['ca'])){
                                //$data 格式为 “json”；
                                //如果没有获取到数据 data为 0
                                $data = CliServer::getHistoryData($client);
                                if($data){
                                    $data = "(|tydata|id".$client['id']."|ky".$client['ky']."|rq".$client['rq']."|da".$data."|st002|)";
                                }else{
                                    $data = "(|tydata|id".$client['id']."|ky".$client['ky']."|rq".$client['rq']."|st004"."|)";
                                }
                                $data = packPacket($data);
                                $serv->send($fd, $data);
                            }//获取数据elseif end

                            //客户端请求获取用户权限信息
                            elseif(isset($client['gr'])){
                                $gr = $client['gr'];
                                $roleid = CliServer::getUserRole($gr);
                                if($roleid > 0 ){
                                    $str = "|ro".$roleid."|st002|)";
                                }else{
                                    $str = "|st004|)";
                                }
                                $data = "(|tydata|id".$client['id']."|ky".$client['ky']."|rq".$client['rq'].$str;
                                $data = packPacket($data);
                                $serv->send($fd, $data);
                            }
                            break;
                        }//case command end
                    
                        
                        
                    }//switch
                    
                    
                }
            }
        }
    }
    
    
    //关闭程序
    public static function onClose($serv ,$fd, $from_id){
        Log::write("connect", "client".$fd." disconnected");
        CliServer::delRecord($fd);
    }

}






