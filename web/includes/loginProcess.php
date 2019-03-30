<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-14
 * Time: 下午2:59
 */
require_once "Func.php";
require_once "../../includes/CommonFun.php";

if(!(isset($_POST['id']) && isset($_POST['password'])))
    exit();
$r = pwdCheck($_POST['id'], md5($_POST['password']));

echo $r;
//密码正确时记录当前会话
if($r == 1){
    setSession("user", $_POST['id']);
    exit();
}