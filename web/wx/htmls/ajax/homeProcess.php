<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-27
 * Time: 下午9:54
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../../includes/CommonFun.php";

if(getSession() == 0)
    exit();

$id = getID();

$admin = AdminServer::getAdmin($id);

//所有站点在线信息
$noList = getStationState($admin->getBelongid());

//在线站点个数
$online = 0;
//总站点数
$stations = count($noList);
//用户数 = 2
$users = 2;

if($noList){
    foreach ($noList as $s => $v){
        if($v == 1)
            $online++;
    }
}

//查找用户总数
if($admin->getRoleid() == 1){
    $users = AdminServer::getAdminAllNum($admin->getBelongid());
}

$r = array(
    "online"=> $online,
    "stations"=> $stations,
    "users"=> $users
);

echo json_encode($r);