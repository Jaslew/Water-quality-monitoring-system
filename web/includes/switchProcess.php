<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-18
 * Time: 上午8:55
 */

/******
    返回值 0 :参数错误
    返回值 -1 ：站点不在线
    返回值 -2 ：权限不足
 ******/
require_once "Func.php";
require_once "adminServer.class.php";

if(getSession() == 0)
    exit();

if(!isset($_POST['type']) || !isset($_POST['no'])){
    echo 0;
    exit();
}
$type = $_POST['type'];
//组装包,需要有 终极id和no号
$no = $_POST['no'];
$id = getID();
//判断是否为最高级管理员
$isadmin = AdminServer::isAdmin($id);
if(!$isadmin){
    echo "-2";
    exit();
}
$ky = AdminServer::getMD5Key($id);
if($type == "get"){
    $msg = "(|no".$no."|tyorder|id".$id."|ky".$ky."|gs1|)";
}
elseif($type == "set"){
    if(!isset($_POST['ss'])){
        echo 0;
        exit();
    }
    $msg = "(|no".$no."|tyorder|id".$id."|ky".$ky."|ss".$_POST['ss']."|)";
}else{
    echo 0;
    exit();
}

$buf = "";
$timeout = array('sec'=>2,'usec'=>0);
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($sock,SOL_SOCKET,SO_RCVTIMEO,$timeout);
$len = strlen($msg);
socket_sendto($sock, $msg, $len, 0, '127.0.0.1', 9502);
$from  =  '' ;
$port  =  0 ;
socket_recvfrom ( $sock ,  $buf ,  12 ,  0 ,  $from ,  $port );
socket_close($sock);
echo $buf;
