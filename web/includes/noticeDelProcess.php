<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-16
 * Time: 下午4:11
 */
require_once "noticeServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!isset($_POST['time'])){
    exit();
}

$id = getID();
$time = $_POST['time'];

echo NoticeServer::delNotice($id, $time);