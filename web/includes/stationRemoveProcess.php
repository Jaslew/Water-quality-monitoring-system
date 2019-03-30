<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-17
 * Time: 下午4:43
 */
require_once "stationServer.class.php";
require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

$n = count($_POST);
$noList = array();
for($i = 0; $i < $n - 1; $i++){
    $key = "no".$i;
    if(isset($_POST[$key]))
        $noList[] = $_POST[$key];
}

if(($n - 1) != count($noList))
    exit();

//检查是否拥相应权限
$id = getID();
$admin = AdminServer::getAdmin($id);
$roleid = $admin->getRoleid();
$belongid = $admin->getBelongid();

if($roleid != 1 && $roleid != 2){
    echo 2;
    exit();
}


$stationServer = new StationServer();
echo $stationServer::removeStation($belongid,$noList);