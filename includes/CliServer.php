<?php

require_once 'SqlHelper.php';
require_once 'RedisHelper.php';
require_once 'Mycrypt.class.php';

//定义数据查询最长时间跨度(15天)，时间单位 s;
define("timeIn", 1296000);

class CliServer{  
    
    /*
     * 若redis中不存在记录则从mysql中拷贝
     */
    public static function setRecord($id, $fd){
        $redisHelper = new RedisHelper();
        $ifExist = $redisHelper->hexists($id, 'fd');
        if(!$ifExist){
            $sqlHelper = new SqlHelper();
            $sql = "select belongid from admin where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            $belongid = $r['belongid'];
            $sql = "select no , ip , port , time from border where belongid = '$belongid'";
            $res = $sqlHelper->execute_dql_arr($sql);
            $sql = "select password from admin where id = '$id'";
            $res2 = $sqlHelper->execute_dql($sql);
            $redisHelper->hset($id, 'ky', $res2['password']);
            $redisHelper->hset($id, 'fd', $fd);
            foreach ($res as $key){
                $redisHelper->hset($id, $key['no'], $key['ip'].":".$key['port'].":".$key['time']);
            }
            $redisHelper->hset('fdtoid', $fd, $id);
            $sqlHelper->close();
        }
    }
    
    /*
     * 用户关闭连接时删除redis中的相关记录
     * 传入 fd
     */
    public static function delRecord($fd){
        $rediselper = new RedisHelper();
        $id = $rediselper->hget('fdtoid', $fd);
        $rediselper->hdel('fdtoid', $fd);
        $rediselper->del($id);
    }
    
    /*
     *获取用户名,上次登录时间,并更新当前时间到数据库
     *传入id
     *@return loginInfo['name'],loginInfo['time']
     */
    public static function getNT($id){
        $loginInfo = array();
        $time = time();
        $sqlHelper = new SqlHelper();
        $sql = "select name , lasttime from admin where id = '$id' ";
        $res = $sqlHelper->execute_dql_arr($sql);
        if($res){
            $loginInfo['name'] = $res[0]['name'];
            $loginInfo['time'] = $res[0]['lasttime'];
        }
        $sql = "update admin set lasttime = '$time' where id = '$id'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
        return $loginInfo;
    }
    
    /*
     * 修改用户名
     * 成功返回 非0值， 失败返回 0
     */
    public static function setUserSecret($name, $password, $id){
        $sqlHelper = new SqlHelper();
        $str1 = "";
        $str2 = "";
        $str3 = "";
        $st = "002";
        if($name){
            $str1 = " name = '$name' ";
        }
        if($password){
            $str2 = " password = md5('$password') ";
        }
        if($name && $password){
            $str3 = ",";
            $st = "012";
        }
        if($name || $password){
            $sql = "update admin set".$str1.$str3.$str2."where id = '$id'";
            $r = $sqlHelper->execute_dml($sql);
            $sqlHelper->close();
            if(!$r)
                $st = "001";
        }else{
            $st = "001";
        }
        return $st;
    }

    /*
     * 将当前id(已经是belongid)的所有站点更新本地用户表标志tag都设置为1
     */
    public static function setStationTag0($id){
        $sqlHelper = new SqlHelper();
        $sql = "update station set tag1 = 1 where belongid = '$id'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
    }

    /*
     * 将当前id所属的belongid的所有站点更新本地用户表标志tag都设置为1
     */
    public static function setStationTag1($id){
        $belongid = null;
        $sqlHelper = new SqlHelper();
        $sql = "select belongid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        if(isset($r['belongid']))
            $belongid = $r['belongid'];
        if($belongid){
            $sql = "update station set tag1 = 1 where belongid = '$belongid'";
            $sqlHelper->execute_dml($sql);
        }
        $sqlHelper->close();
    }
    
    /*
     * 将当前id所属的belongid的所有站点更新hz标志tag都设置为1
     */
    public static function setStationTag2($id, $no){
        $belongid = null;
        $sqlHelper = new SqlHelper();
        $sql = "select belongid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        if(isset($r['belongid']))
            $belongid = $r['belongid'];
        if($belongid){
            $sql = "update station set tag2 = 1 where belongid = '$belongid' and no = '$no'";
            $sqlHelper->execute_dml($sql);
        }
        $sqlHelper->close();
    }
    
    /*
     * 重置redis中的密码
     */
    public static function resetRedisPW($id, $ky){
        $redisHelper = new RedisHelper();
        $redisHelper->hset($id, 'ky', md5($ky));
    }
    
    /*
     * 装填客户端请求的数据，
     * 返回已加密的数据包
     */
    public static function getHistoryData($client){
        $id = $client['id'];
        //isdo为真则执行数据库操作
        $index = array();
        $nos = array();
        $isdo = FALSE;
        $i = 0;
        //附初值,时间跨度默认情况下为过去最近24小时
        if(isset($client['dd']) && isset($client['du'])){
            $dd = $client['dd'];
            $du = $client['du'];
        }elseif(isset($client['dd']) && !isset($client['du'])){
            $dd = $client['dd'];
            $du = $client['dd'] + 24*60*60;
        }elseif(!isset($client['dd']) && isset($client['du'])){
            $du = $client['du'];
            $dd = $du - 24*60*60;
        }else{
            $time = time();
            $dd = $time - 24*60*60;
            $du = $time;
        }
        if($dd < $du  && $du - $dd < timeIn ){
            //在规定的时间跨度内予以执行
            if(isset($client['tm'])){
                $index[$i] = 'tm';
                $nos[$i++] = $client['tm'];
            }
            if(isset($client['ph'])){
                $index[$i] = 'ph';
                $nos[$i++] = $client['ph'];
            }
            if(isset($client['ox'])){
                $index[$i] = 'ox';
                $nos[$i++] = $client['ox'];
            }
            if(isset($client['el'])){
                $index[$i] = 'el';
                $nos[$i++] = $client['el'];
            }
            if(isset($client['nt'])){
                $index[$i] = 'nt';
                $nos[$i++] = $client['nt'];
            }
            if(isset($client['po'])){
                $index[$i] = 'po';
                $nos[$i++] = $client['po'];
            }
            if(isset($client['nh'])){
                $index[$i] = 'nh';
                $nos[$i++] = $client['nh'];
            }
            if(isset($client['cl'])){
                $index[$i] = 'cl';
                $nos[$i++] = $client['cl'];
            }
            if(isset($client['ca'])){
                $index[$i] = 'ca';
                $nos[$i++] = $client['ca'];
            }
            $index[$i] = 'time';
            //检查是否所有站点号都相等
            foreach ($nos as $no){
                if($nos[0] == $no){
                    $isdo = TRUE;
                }else{
                    $isdo = FALSE;
                    break;
                }
            }//foreach
        }//if
        //执行数据库查询操作
        if($isdo){
            //先获取本次查询所需涉及到的表(最长时间跨度为15天，故最多涉及两张表)
            $no = $nos[0];
            $table_dYm = "_".date("Ym", $dd);
            $table_uYm = "_".date("Ym", $du);
            $n = count($index);
            $sqlHelper = new SqlHelper();
            $sql = "select ";
            for($i = 0; $i < $n; $i++){
                //只涉及单张表
                if($i+1 < $n)
                    $sql .= $index[$i].", ";
                else
                    $sql .= $index[$i]." ";
            }
            $sql .= "from $table_dYm where belongid = '$id' and no = '$no' and time > '$dd' and time < '$du'";   
            if($table_dYm != $table_uYm){
                //涉及两张表
                $sql .= " union all select ";
                for($i = 0; $i < $n; $i++){
                    if($i+1 < $n)
                        $sql .= $index[$i].", ";
                    else
                        $sql .= $index[$i]." ";
                }
                $sql .= "from '$table_uYm' where belongid = '$id' and no = '$no' and time > '$dd' and time < '$du'";
            }
            $res = $sqlHelper->execute_dql_arr($sql);
            $sqlHelper->close();
            if($res){
                foreach ($res as $group){
                    for($i = 0; $i < $n - 1 ; $i++){
                        $data[$group[$index[$n - 1]]][$index[$i]] = $group[$index[$i]];
                    }
                }
                $data = json_encode($data);
            }else {
                $isdo = FALSE;
            }
        }
        return $isdo ? $data : null;
    }
    
    /*
     * 增加站点
     * 增加成功返回 1，权限不够返回 0， 增加失败返回 -1
     */
    public static function addStation($client){
        $id = $client['id'];
        if (isset($client['no']) && isset($client['sa']) && isset($client['sp']) &&
            isset($client['hz']) && isset($client['te']) && isset($client['em']) &&
            isset($client['ce'])){
            $sqlHelper = new SqlHelper();
            $sql = "select roleid from admin where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            if ($r && ($r['roleid'] == 1 || $r['roleid'] == 2)){
                $no = $client['no'];
                $sql = "select no from station where belongid = '$id' and no = '$no'";
                $r = $sqlHelper->execute_dql($sql);
                if(!$r){
                    $sa = $client['sa'];
                    $sp = $client['sp'];
                    $hz = $client['hz'];
                    $te = $client['te'];
                    $em = $client['em'];
                    $ce = $client['ce'];
                    $time = time();
                    $sql = "insert into station values('$id','$no','$sa','$sp','$hz','$time','$time','$te','$em','$ce',0,0)";
                    $r = $sqlHelper->execute_dml($sql);
                    //插入成功后在border表中新建一个记录
                    if($r > 0){
                        $sql = "insert into border(belongid, no, ip, port) values('$id','$no','','')";
                        $r2 = $sqlHelper->execute_dml($sql);
                        $sql  = "insert into switch(belongid, no) values('$id','$no')";
                        $r3 = $sqlHelper->execute_dml($sql);
                        //border表中,switch表中未能插入记录时，回滚操作
                        if($r2 == 0 || $r3 == 0){
                            $sql = "delete from station where belongid = '$id' and no = '$no'";
                            $sqlHelper->execute_dml($sql);
                            $sql = "delete from switch where belongid = '$id' and no = '$no'";
                            $sqlHelper->execute_dml($sql);
                        }
                    }

                    $sqlHelper->close();
                    if($r > 0 && $r2 > 0){
                        return 1;
                    }else{
                            return -1;   //增加数据失败
                        }
                }else{
                    $sqlHelper->close();
                    return -1;   //站点号已经存在
                }
            }else{
                $sqlHelper->close();
                return 0;   //权限不够
            }
        }else {
            return -1; //用户所填参数不足
        }
        
    }
    
    /*
     * 删除站点
     * 删除成功时返回1，权限不够返回0，删除失败返回 -1
     */
    public static function delStation($client){
        $id = $client['id'];
        if(isset($client['no'])){
            $no = $client['no'];
            $sqlHelper = new SqlHelper();
            $sql = "select roleid from admin where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            if($r && ($r['roleid'] == 1 || $r['roleid'] == 2)){
                $sql = "delete from station where belongid = '$id' and no = '$no'";
                $r = $sqlHelper->execute_dml($sql);
                $sqlHelper->close();
                //删除成功时数据库会同步删除border表中的对应站点以及数据表中对应数据
                if ($r == 1){
                    return 1;
                }else{
                    return -1;  //删除失败
                }      
            }else{
                $sqlHelper->close();
                return 0;  //权限不够
            }
        }else{
            return -1;  //参数不足
        }
        
    }
    
    /*
     * 修改站点
     * 修改成功返回1，权限不够返回0，修改失败返回-1
     */
    public static function alterStation($client){
        $id = $client['id'];
        if (isset($client['no']) && (isset($client['sa']) || isset($client['sp']) ||
            isset($client['hz']) || isset($client['te']) || isset($client['em']) ||
            isset($client['ce']))){
            $sqlHelper = new SqlHelper();
            $sql = "select roleid from admin where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            if($r && ($r['roleid'] == 1 || $r['roleid'] == 2)){
                $no = $client['no'];
                $alter = array();
                $index = array();
                $i = 0;
                $sql = "select no from station where belongid = '$id' and no = '$no'";
                $r = $sqlHelper->execute_dql($sql);
                if($r){
                    if (isset($client['sa'])){
                        $index[$i] = 'name';
                        $alter[$i++] = $client['sa'];
                    }
                    if (isset($client['sp'])){
                        $index[$i] = 'pos';
                        $alter[$i++] = $client['sp'];
                    }
                    if (isset($client['hz'])){
                        $index[$i] = 'hz';
                        $alter[$i++] = $client['hz'];
                    }
                    if (isset($client['te'])){
                        $index[$i] = 'tel';
                        $alter[$i++] = $client['te'];
                    }
                    if (isset($client['em'])){
                        $index[$i] = 'email';
                        $alter[$i++] = $client['em'];
                    }
                    if (isset($client['ce'])){
                        $index[$i] = 'charge';
                        $alter[$i++] = $client['ce'];
                    }
                    $index[$i] = 'atime';
                    $alter[$i] = time();
                    $n = count($alter);
                    $sql = "update station set ";
                    for ($i = 0 ; $i < $n ; $i++){
                        $key = $index[$i];
                        $val = $alter[$i];
                        if($i + 1 < $n ){
                            $sql .= " $key = '$val' , ";
                        }else{
                            $sql .= " $key = '$val' ";
                        }
                    }
                    $sql .= " where belongid = '$id' and no = '$no'";
                    $r = $sqlHelper->execute_dml($sql);
                    $sqlHelper->close();
                    if ($r == 1){
                        return 1;
                    }else{
                        return -1;      //执行失败
                    }
                }else{
                    $sqlHelper->close();
                    return -1;      //站点号不存在
                }
            }else{
                $sqlHelper->close();
                return 0;     //权限不够
            }
        }
            
    }
    
    
    /*
     * 增加用户
     * @成功返回一个id号，因权限导致的失败返回 0，其他情况返回 -1
     */
    public static function addUser($client){
        $id = $client['id'];
        if(isset($client['ua']) && isset($client['uk'])){
            //检查是否是终极id
            $sqlHelper = new sqlHelper();
            $sql = "select roleid from admin where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            if($r && $r['roleid'] == 1){
                $ro = isset($client['ro']) ? $client['ro'] : 3;
                if($ro == 2 || $ro == 3){
                    $ua = $client['ua'];
                    $uk = $client['uk'];
                    $time = time();
                    $try = 0;
                    while ($try == 0){
                        $newid = substr($time,5,5).rand(10,99);
                        $sql = "insert into admin values('$newid', '$id', $ro, '$ua', md5($uk), $time, '','','')";
                        $try = $sqlHelper->execute_dml($sql);
                    }
                    $sqlHelper->close();
                    return $newid;
                }else{
                    $sqlHelper->close();
                    return -1;    //ro参数有误
                }
            }else{
                $sqlHelper->close();
                return 0;    //权限不够
            }
        }else{
            return -1;     //参数不足，执行失败
        }
    }
    
    /*
     * 删除用户
     * @删除成功返回1，权限不够返回0，删除失败返回-1
     */
    public static function delUser($client){
        $id = $client['id'];
        if (isset($client['ni'])){
            $ni = $client['ni'];
            if($ni != $id){
                $sqlHelper = new SqlHelper();
                $sql = "select roleid from admin where id = '$id'";
                $r = $sqlHelper->execute_dql($sql);
                if($r && $r['roleid'] == 1){
                    $sql = "delete from admin where belongid = '$id' and id = '$ni'";
                    $r = $sqlHelper->execute_dml($sql);
                    $sqlHelper->close();
                    if ($r == 1){
                        return 1;
                    }else{
                        return -1;    //删除失败
                    }
                }else{
                    $sqlHelper->close();
                    return 0;     //权限不够
                }
            }else{
                return -1;     //参数有误
            }
        }else{
            return -1;    //参数不够
        }
    }

    /*
     * 修改用户权限
     * 成功返回1，权限不够返回0，失败返回-1
     */
    public static function alterUserRole($client){
        $id = $client['id'];
        if (isset($client['ni']) && isset($client['ro'])){
            $ni = $client['ni'];
            $ro = $client['ro'];
            $sqlHelper = new SqlHelper();
            //先检查所给 ni 是否存在
            $sql = "select id from admin where id = '$ni'";
            $r = $sqlHelper->execute_dql($sql);
            if($r && $id != $ni){
                $sql = "select roleid from admin where id = '$id'";
                $r = $sqlHelper->execute_dql($sql);
                if($r && $r['roleid'] == 1){
                    if($ro == 2 || $ro == 3){
                        $sql = "update admin set roleid = $ro where id = '$ni'";
                        $r = $sqlHelper->execute_dml($sql);
                        $sqlHelper->close();
                        if($r == 1){
                            return 1;
                        }else{
                            return -1;
                        }
                    }else{
                        $sqlHelper->close();
                        return -1;    //ro参数有误
                    }
                }else{
                    $sqlHelper->close();
                    return 0;    //权限不足
                }
            }else{
                $sqlHelper->close();
                return -1;   //参数有误
            }
        }else{
            return -1;    //参数不足
        }
    }

    /*
     * 获取用户权限信息
     * @return 权限等级，查询失败返回 0
     */
    public static function getUserRole($id){
        $sqlHelper = new SqlHelper();
        $sql = "select roleid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r){
            return $r['roleid'];
        }else{
            return 0;
        }
    }






    
}



















