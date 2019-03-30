<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-2-28
 * Time: 下午4:32
 */

require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['name']) && isset($_POST['roleid']) && isset($_POST['password'])))
    exit();

$name = $_POST['name'];
$roleid = $_POST['roleid'];
$tel = isset($_POST['tel']) ? $_POST['tel'] : "";
$email = isset($_POST['email']) ? $_POST['email'] : "";
$password = $_POST['password'];

//简单过滤
if(!($roleid == 2 || $roleid == 3)){
    echo 0;
    exit();
}

//检查是否拥相应权限
$id = getID();
$isAdmin = AdminServer::isAdmin($id);

if($isAdmin != 1){
    echo -1;
    exit();
}

echo AdminServer::addUser($id, $name, $roleid, $tel, $email, $password);
