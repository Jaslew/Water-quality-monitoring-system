<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-16
 * Time: 上午11:21
 */
require_once "stationServer.class.php";
require_once "station.class.php";
require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['no']) && isset($_POST['name']) && isset($_POST['pos'])
    && isset($_POST['charge']) && isset($_POST['tel']) && isset($_POST['email'])
    && isset($_POST['hz'])))
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

$station->no = $_POST['no'];
$station->belongid = $admin->getBelongid();
$station->charge = $_POST['charge'];
$station->email = $_POST['email'];
$station->pos = $_POST['pos'];
$station->name = $_POST['name'];
$station->atime = time();
$station->hz = $_POST['hz'];
$station->tel = $_POST['tel'];

echo StationServer::updateStation($station);