<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-2-28
 * Time: 下午5:44
 */

require_once "adminServer.class.php";
require_once "Func.php";
require_once "header.class.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['roleid']) && isset($_POST['imgParam'])))
    exit();

$readyId = $_POST['id'];  //待修改的id
$name = $_POST['name'];
$roleid = $_POST['roleid'];
$tel = isset($_POST['tel']) ? $_POST['tel'] : "";
$email = isset($_POST['email']) ? $_POST['email'] : "";
$imgParam = json_decode($_POST['imgParam'], true);
$dest = "";

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

//检查是否需要使用默认图片, 图片过滤
if(isset($_FILES['imgFile'])){
    if($_FILES['imgFile']['size'] > 500000){
        echo 9;
        exit();
    }
    $types = array("image/jpg","image/jpeg","image/png","image/gif");
    if(in_array($_FILES['imgFile']['type'], $types)){
        $ext = explode(".",$_FILES['imgFile']['name'])[1];
        $dest = dirname(__DIR__)."/images/header/"."img".$readyId.".".$ext;
        //移动图片到指定文件夹
        $r = move_uploaded_file($_FILES['imgFile']['tmp_name'],$dest);
        if(!$r){
            echo 0;
            exit();
        }
    }else{
        echo 8;
        exit();
    }
}

//头像规范化
$header = new Header();
$header->setImg($dest, $imgParam, $readyId);

//更新用户资料
echo AdminServer::editeUser($readyId, $id, $name, $roleid, $tel, $email, $password);