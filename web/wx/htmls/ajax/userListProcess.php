<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-30
 * Time: 上午10:11
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../includes/keyServer.class.php";

if(getSession() == 0)
    exit();
$id = getID();

$belongid = AdminServer::getBelongid($id);

if($id == $belongid){
    $keys = KeyServer::getKeyAll($belongid);
}else{
    $keys = KeyServer::getKeyByID($id, $belongid);
}

//0或者对象
echo json_encode($keys, JSON_UNESCAPED_UNICODE);