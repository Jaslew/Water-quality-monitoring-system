<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-29
 * Time: 下午12:08
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../includes/stationServer.class.php";

if(getSession() == 0)
    exit();

if(!isset($_POST['no']))
    exit();

$belongid = AdminServer::getBelongid($id = getID());

echo json_encode(StationServer::getStationSingle($belongid, $_POST['no']),JSON_UNESCAPED_UNICODE);