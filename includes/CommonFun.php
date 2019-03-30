<?php

require_once 'SqlHelper.php';
require_once 'RedisHelper.php';
require_once 'Mycrypt.class.php';

//定义单片机端在线判据，时间单位 s;
define("timeOut", 300);

/*
 * 获取指点站点ip和端口号
 * 先检查redis中是否有记录，没有再从mysql中查找
 * 如果不在线返回 0，在线则返回一个包含ip和port的数组
 */
function isOnline($id, $no){
    $redisHelper = new RedisHelper();
    $r = $redisHelper->hget($id, $no);
    if($r){
        $info = explode(":", $r);
        if(time() - $info[2] < timeOut){
            $res['ip'] = $info[0];
            $res['port'] = $info[1];
            return $res;
        }else{
            return 0;
        }
    }else{
        $sqlHelper = new SqlHelper();
        $sql = "select ip , port , time from border where belongid = '$id' and no = '$no'";
        $res = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($res && (time() - $res['time'] < timeOut)){
            return $res;
        }else{
            return 0;
        }
    }
}

/*
 * 获取当前id所有在线终端的ip地址和端口号
 * @return 没有在线终端时返回 null
 */
function getBorderOnLine($id){
    $redisHelper = new RedisHelper();
    $res = $redisHelper->hvals($id);
    $i = 2; $j = 0;
    while(isset($res[$i])){
        $border = explode(":", $res[$i]);
        if($border[2] && time() - $border[2] < timeOut){
            $borders[$j]['ip'] = $border[0];
            $borders[$j]['port'] = $border[1];
        }
        $i++;
        $j++;
    }
    return isset($borders) ? $borders : null;
}

/*
 * 传入 $id
 * 获取当前id站点总数和状态
 * @return $stations['001'] = 1;
 * 001表示站点号，1表示在线，0表示离线
 * 若站点为空返回 null
 */
function getStationState($id){
    //优先在redis中查找，redis中没有记录时查找mysql
    $redisHelper = new RedisHelper();
    $ifexists = $redisHelper->hexists($id, 'fd');
    $time = time();
    $stations = null;
    if($ifexists == 1){
        $res = $redisHelper->hkeys($id);
        $vals = $redisHelper->hvals($id);
        $i = 2;
        while (isset($res[$i])){
            $border = explode(":", $vals[$i]);
            $stations[$res[$i]] = (($time - $border[2]) < timeOut ? 1 : 0);
            $i++;
        }
    }else{
        //在mysql中查找
        $sqlHelper = new SqlHelper();
        $sql = "select no , time from border where belongid = '$id'";
        $res = $sqlHelper->execute_dql_arr($sql);
        if($res){
            foreach ($res as $key)
                $stations[$key['no']] = (($time -$key['time']) < timeOut ? 1 : 0);
        }
        $sqlHelper->close();
    }
    return $stations;
}

/*
 * 第一次过滤数据->aes解密->第二次过滤->生成数组
 */
function parsePacket($p){
    //判断包的完整性,不完整直接丢弃
    $p = trim($p);
    if(substr($p, 0, 1) != "(" || substr($p, -1, 1) != ")"){
        return 0;
    }
    $p  = substr($p, 1, -1);
    $mycrypt = new Mycrypt();
    $decrypt = trim($mycrypt->decrypt($p));
    //再次判断包的完整性
    if(substr($decrypt, 0, 1) != "(" || substr($decrypt, -1, 1) != ")"){
        return 0;
    }
    $data = substr($decrypt, 2 , -2);
    $data = explode('|', $data);
    foreach ($data as $key => $val){
        $info[substr($val, 0, 2)] = substr($val, 2);
    }
    return $info;
}

/*
 * @param $p
 * 不进行解密，直接解析
 */
function parsePacket2($p){
    $p = trim($p);
    if(substr($p, 0, 1) != "(" || substr($p, -1, 1) != ")"){
        return 0;
    }
    $p  = substr($p, 1, -1);
    $p = base64_decode($p);
    //判断包的完整性
    if(substr($p, 0, 1) != "(" || substr($p, -1, 1) != ")"){
        return 0;
    }
    $data = substr($p, 2 , -2);
    $data = explode('|', $data);
    foreach ($data as $key => $val){
        $info[substr($val, 0, 2)] = substr($val, 2);
    }
    return $info;
}


/*
 * 打包函数，将数据按格式打包
 * 返回可直接发送的数据串
 */
//function packPacket($data){
//    $mycrypt = new Mycrypt();
//    $str = $mycrypt->encrypt($data);
//    $str = "(".$str.")";
//    return $str;
//}
/*  base64 **/

function packPacket($data){
    return $str;
}

/*
 * 密码正确返回 1,错误返回 0
 */
function pwdCheck($id, $ky){
    //优先在redis中查找，没有再向mysql查找
    $redisHelper = new RedisHelper();
    $ky2 = $redisHelper->hget($id, $ky);
    if(!$ky2){
        $sqlHelper = new SqlHelper();
        $sql = "select password from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $ky2 = $r['password'];
        $sqlHelper->close();
    }
    if($ky == $ky2){
        return 1;
    }else{
        return 0;
    }
}

/*
 * 传入 $id
 * @return belongid拥有的用户名，密码，权限数据表
 */
function getIdTable($id){
    $sqlHelper = new SqlHelper();
    $sql = "select belongid from admin where id = '$id'";
    $r = $sqlHelper->execute_dql($sql);
    $belongid = $r['belongid'];
    $sql = "select id , roleid , password from admin where belongid = '$belongid' and roleid != 3";
    $res = $sqlHelper->execute_dql_arr($sql);
    if($res){
        foreach ($res as $group){
            $n = count($group);
            for($i = 0; $i < $n ; $i++){
                $data[$group['id']]['ky'] = $group['password'];
                $data[$group['id']]['role'] = $group['roleid'];
            }
        }
        $data = json_encode($data);
    }
    $sqlHelper->close();
    return isset($data) ? $data : null;
}

/*
 * 传入 $id, $no
 * @return hz
 */
function getStationHz($id, $no){
    $sqlHelper = new SqlHelper();
    $sql = "select hz from station where belongid = '$id' and no = '$no'";
    $r = $sqlHelper->execute_dql($sql);
    $sqlHelper->close();
    if($r){
        return $r['hz'];
    }else{
        return null;
    }
   
    
}












