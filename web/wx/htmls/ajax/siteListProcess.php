<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-29
 * Time: 上午9:43
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../includes/stationServer.class.php";
require_once dirname(__DIR__)."/../../../includes/CommonFun.php";

if(getSession() == 0)
    exit();

$id = getID();

$admin = AdminServer::getAdmin($id);

//所有站点在线信息
$noList = getStationState($admin->getBelongid());
//  $noList['001'] = 0
$noListName = StationServer::getStationInfo($admin->getBelongid());

$lists = array();
foreach ($noListName as $s){
    // $s['no']
    $l = array();
    $l['no'] = $s->no;
    $l['name'] = $s->name;
    $l['state'] = isset($noList[$s->no]) ? $noList[$s->no] : 0;
    $lists[] = $l;
}

echo json_encode($lists);