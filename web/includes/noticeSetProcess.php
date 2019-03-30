<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-4
 * Time: 下午3:10
 */
require_once "noticeServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['time']))){
    exit();
}

$id = getID();

echo NoticeServer::setNotice($id, $_POST['time']);