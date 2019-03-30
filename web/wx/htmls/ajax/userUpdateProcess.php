<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-30
 * Time: 下午1:16
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['roleid'])))
    exit();

$readyId = $_POST['id'];  //待修改的id
$name = $_POST['name'];
$roleid = $_POST['roleid'];
$tel = isset($_POST['tel']) ? $_POST['tel'] : "";
$email = isset($_POST['email']) ? $_POST['email'] : "";

if(isset($_POST['password']))
    $password = $_POST['password'];
else
    $password = "";

//简单过滤，待修改的 roleid 是否符合要求
if(!($roleid == 2 || $roleid == 3)){
    echo 0;
    exit();
}

//检查操作者是否拥相应权限
$id = getID();
$isAdmin = AdminServer::isAdmin($id);

if($isAdmin == 1){
    //如果操作者是系统管理员，则拥有所有操作权
    if($readyId == $id){
        $roleid = 1;
    }
}else{
    if($id != $readyId){
        echo -1;
        exit();
    }
}
//判断 $readyId 是否合法
if(!AdminServer::isIdOK($readyId, $id)){
    echo -1;
    exit();
}

//更新用户资料
echo AdminServer::editeUser($readyId, $id, $name, $roleid, $tel, $email, $password);