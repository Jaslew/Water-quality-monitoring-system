<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-16
 * Time: 下午2:19
 */
require_once "stationServer.class.php";
require_once "station.class.php";
require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!( isset($_POST['no']) && isset($_POST['name']) && isset($_POST['hz']) ))
    exit();

//检查是否拥相应权限
$id = getID();
$admin = AdminServer::getAdmin($id);
$roleid = $admin->getRoleid();

if($roleid != 1 && $roleid != 2){
    echo 2;
    exit();
}

$station = new Station();
$station->belongid = $admin->getBelongid();
$station->no = $_POST['no'];
$station->name = $_POST['name'];
$station->hz = $_POST['hz'];
$station->charge = isset($_POST['charge']) ? $_POST['charge'] : "";
$station->email = isset($_POST['email']) ? $_POST['email'] : "";
$station->pos = isset($_POST['pos']) ? $_POST['pos'] : "";
$station->tel = isset($_POST['tel']) ? $_POST['tel'] : "";
$station->atime = time();

echo StationServer::addStation($station);