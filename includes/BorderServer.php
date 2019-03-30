<?php

require_once 'SqlHelper.php';
require_once 'RedisHelper.php';
require_once 'Mycrypt.class.php';
require_once dirname(__FILE__).'/../configs/warn.php';

class BorderServer{

    //解析来自内网的请求
    public static function parseData($data){
        //判断包的完整性,不完整直接丢弃
        if(substr($data, 0, 1) != "(" || substr($data, -1, 1) != ")"){
            return 0;
        }
        $data = trim($data);
        $data = substr($data, 2 , -2);
        $data = explode('|', $data);
        foreach ($data as $key => $val){
            $info[substr($val, 0, 2)] = substr($val, 2);
        }
        return $info;
    }
   
    //存储终端设备信息
    public static function setRecord($id, $no, $ip, $port){
        //先检查客户端是否在线，在线时将设备信息更新到redis，否则更新到数据库
        $redisHelper = new RedisHelper();
        $info = $redisHelper->hget($id, 'fd');
        $time = time();
        if($info){
            //客户端在线
            $str = $ip.":".$port.":".$time;
            $redisHelper->hset($id, $no, $str);
        }else{
            //客户端不在线
            $sqlHelper = new SqlHelper();
            $sql = "update border set ip = '$ip' , port = '$port' , time = '$time' where belongid = '$id' and no = '$no'";
            $sqlHelper->execute_dml($sql);
            $sqlHelper->close();
        }
    }
    
    /*
     * 将所有采样数据存储到数据库
     * 表必须存在
     */
    public static function setData($border){
        $sqlHelper = new SqlHelper();
        if(isset($border['id']) && isset($border['no']) && isset($border['tm'])
           && isset($border['ph']) && isset($border['ox']) && isset($border['el'])
           && isset($border['nt']) && isset($border['po']) && isset($border['nh'])
           && isset($border['cl']) && isset($border['ca'])){
            $table = "_".date("Y").date('m');
            $id = $border['id'];
            $no = $border['no'];
            $time = time();
            $tm = $border['tm'];
            $ph = $border['ph'];
            $ox = $border['ox'];
            $el = $border['el'];
            $nt = $border['nt'];
            $po = $border['po'];
            $nh = $border['nh'];
            $cl = $border['cl'];
            $ca = $border['ca'];
            $sql = "show tables like '$table'";
            $r = $sqlHelper->execute_dql($sql);
            if(!$r){
                //如果表不存在则新建
                $sql = "create table $table(belongid varchar(7),no varchar(3),time int unsigned,tm varchar(6),";
                $sql .= "ph varchar(6),ox varchar(6),el varchar(6),nt varchar(6),po varchar(6),nh varchar(6),cl varchar(6),";
                $sql .= "ca varchar(6),foreign key(belongid,no) references station(belongid,no) on delete cascade on update cascade,";
                $sql .= "primary key(belongid,no,time))ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
                $sqlHelper->execute_dml($sql);
            }
            $sql = "insert into $table values('$id','$no',$time,'$tm','$ph','$ox','$el','$nt','$po','$nh','$cl','$ca')";
            $sqlHelper->execute_dml($sql);
            $sqlHelper->close();
        }
    }

    /*
    * 设置通知表
    */
    public static function setNotice($border){
        $sqlHelper = new SqlHelper();
        if(isset($border['id']) && isset($border['no']) && isset($border['tm'])
            && isset($border['ph']) && isset($border['ox']) && isset($border['el'])
            && isset($border['nt']) && isset($border['po']) && isset($border['nh'])
            && isset($border['cl']) && isset($border['ca'])){
            $id = $border['id'];
            $no = $border['no'];
            $time = time();
            $tm = $border['tm'];
            $ph = $border['ph'];
            $ox = $border['ox'];
            $el = $border['el'];
            $nt = $border['nt'];
            $po = $border['po'];
            $nh = $border['nh'];
            $cl = $border['cl'];
            $ca = $border['ca'];
            $text = "亲爱的用户，您的站点号为 $no 的站点以下参数不合格：";
            if($tm > Warm::$tm_up || $tm < Warm::$tm_down)
                $text .= " 温度, ";
            if($ph > Warm::$ph_up || $ph < Warm::$ph_down)
                $text .= " PH, ";
            if($ox > Warm::$ox_up || $ox < Warm::$ox_down)
                $text .= " 含氧量, ";
            if($el > Warm::$el_up || $el <Warm::$el_down)
                $text .= " 电导率, ";
            if($nt > Warm::$nt_up || $nt < Warm::$nt_down)
                $text .= " 浊度, ";
            if($po > Warm::$po_up || $po < Warm::$po_down)
                $text .= " 总磷, ";
            if($nh > Warm::$nh_up || $nh < Warm::$nh_down)
                $text .= " 氨氮, ";
            if($cl > Warm::$cl_up || $cl <Warm::$cl_down)
                $text .= " 氯含量, ";
            if($ca > Warm::$ca_up || $ca <Warm::$ca_down)
                $text .= " 碳含量 ";
            $sql = "select id from admin where belongid = '$id'";
            $r = $sqlHelper->execute_dql_arr($sql);
            if($r){
                foreach ($r as $i){
                    $id = $i['id'];
                    $sql = "insert into notice values('$id','$time', 0, '$text')";
                    $sqlHelper->execute_dml($sql);
                }
            }
            $sqlHelper->close();
        }
    }
    
    /*
     * 在数据库中检查所给站点号是否已经注册
     * 传入 $id, $no
     * 已经注册返回非0值
     */
    public static function noCheck($id, $no){
        $sqlHelper = new SqlHelper();
        $sql = "select no from station where belongid = '$id' and no = '$no'";
        $r = $sqlHelper->execute_dql($sql);
        if($r){
            return 1; 
        }else{
            return 0;
        }
    }
    
    /*
     * 打包数据
     */
    public static function packPacket($border, $port){
        $mycrypt = new Mycrypt();
        $data = "(|no".$border['no']."|ty".$border['ty']."|id".$border['id']."|ky".$border['ky']."|pt".$port;
        if(isset($border['gs']))
            $data .= "|gs1|)";
        elseif (isset($border['ss'])){
            $state = "|ss".$border['ss']."|)";
            $data .= $state;
        }
        $p = $mycrypt->encrypt($data);
        $p = "(".$p.")";
        return $p;
    }
    
    public static function getClient($id){
        $redisHelper = new RedisHelper();
        return $redisHelper->hget($id, 'fd');
    }
    
    /*
     * 返回待处理事务 $tags
     * $tags[0]:用户密码表更新标志
     * $tags[1]:hz更新标志
     */
    public static function getStationTag($id, $no){
        $sqlHelper = new SqlHelper();
        $sql = "select tag1 , tag2 from station where belongid = '$id' and no = '$no'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r){
            $tags[0] = $r['tag1'];
            $tags[1] = $r['tag2']; 
            return $tags;
        }else{
            return null;
        }
        
    }
    
    public static function setStationTag1($id, $no){
        $sqlHelper = new SqlHelper();
        $sql = "update station set tag1 = 0 where belongid = '$id' and no = '$no'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
    }
    
    public static function setStationTag2($id, $no){
        $sqlHelper = new SqlHelper();
        $sql = "update station set tag2 = 0 where belongid = '$id' and no = '$no'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
    }

    public static function setStationTag3($id, $no){
        $sqlHelper = new SqlHelper();
        $sql = "update station set tag1 = 0, tag2 = 0 where belongid = '$id' and no = '$no'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
    }
    
    /*
     * 传入 id
     * @return password
     */
    public static function getMd5Ky($id){
        $sqlHelper = new SqlHelper();
        $sql = "select password from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        return $r['password'];
    }
    
    
    
    
    
    
    
    
}