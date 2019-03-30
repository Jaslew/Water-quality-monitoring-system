<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-3-1
 * Time: 上午10:08
 */

require_once "Func.php";
require_once "stationServer.class.php";
require_once "adminServer.class.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['pageNow']) && isset($_POST['pageRow'])))
    exit();

$id = getID();
$pageRow = $_POST['pageRow'];
$pageNow = $_POST['pageNow'];

//简单过滤
if($pageNow < 1){
    echo 0;
    exit();
}

$rowStart = ($pageNow - 1) * $pageRow;

$admin = AdminServer::getAdmin($id);
$belongid = $admin->getBelongid();


echo json_encode(StationServer::getStation($belongid, $rowStart, $pageRow));