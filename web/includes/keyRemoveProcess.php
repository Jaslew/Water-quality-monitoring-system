<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-2-28
 * Time: 下午6:15
 */

require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['readyId'])))
    exit();

$readyId = $_POST['readyId'];  //待修改的id

//检查操作者是否拥相应权限
$id = getID();
$isAdmin = AdminServer::isAdmin($id);

if($isAdmin != 1){
    echo -1;
    exit();
}

//检查所删除的id号是否为最高级用户
if($id == $readyId){
    echo -1;
    exit();
}

//要注意判断所修改id是否属于操作者
$r = AdminServer::delUser($readyId, $id);

if($r == -1){
    echo 0;
}elseif ($r == ""){
    echo 1;
}else{
    $header = dirname(__DIR__)."/images/header/".$r;
    unlink($header);
    echo 1;
}