<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-18
 * Time: 下午9:56
 */
require_once "Func.php";
require_once "switchServer.class.php";
require_once "adminServer.class.php";

if(getSession() == 0)
    exit();
if(!isset($_POST['no']))
    exit();
$no = $_POST['no'];
$id = getID();
//判断是否为最高级管理员
$belongid = AdminServer::getBelongid($id);

echo json_encode(SwitchServer::getSWName($belongid, $no), JSON_UNESCAPED_UNICODE);