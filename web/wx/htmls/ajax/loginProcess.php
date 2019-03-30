<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-5-1
 * Time: 上午10:39
 */

require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../../includes/CommonFun.php";

if(!(isset($_POST['id']) && isset($_POST['password'])))
    exit();
$r = pwdCheck($_POST['id'], md5($_POST['password']));

echo $r;

if($r == 1){
    //设置cookie
    $water = array("id"=>$_POST['id'],"password"=>$_POST['password']);
    $water = serialize($water);
    setcookie("WATER", $water, time()+6*30*24*3600);     //保存六个月
    setSession("user", $_POST['id']);
    exit();
}
