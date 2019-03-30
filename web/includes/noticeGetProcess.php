<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-4
 * Time: 上午11:40
 */
require_once "noticeServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['pageNow']) && isset($_POST['pageRow']))){
    exit();
}

$id = getID();
$pageNow = $_POST['pageNow'];
$pageRow = $_POST['pageRow'];

if($pageNow <= 0)
    exit();
$start = ($pageNow - 1) * $pageRow;

echo json_encode(NoticeServer::getNotice($id, $start, $pageRow), JSON_UNESCAPED_UNICODE);