<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-28
 * Time: 上午7:58
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../includes/stationServer.class.php";
require_once dirname(__DIR__)."/../../../includes/CommonFun.php";

if(getSession() == 0)
    exit();

$id = getID();

$admin = AdminServer::getAdmin($id);
$belongid = $admin->getBelongid();
$station = StationServer::getStationInfo($belongid);

echo json_encode($station);