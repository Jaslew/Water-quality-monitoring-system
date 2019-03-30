<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-18
 * Time: 下午8:53
 */
require_once "Func.php";
require_once "switchServer.class.php";
require_once "adminServer.class.php";

if(getSession() == 0)
    exit();

if(!isset($_POST['text'])|| !isset($_POST['no'])){
    exit();
}
if($_POST['text'] == "")
    exit();

$no = $_POST['no'];
$id = getID();
$names = explode(",", $_POST['text']);
//获取终极id
$belongid = AdminServer::getBelongid($id);
echo SwitchServer::switchUpdate($belongid, $no, $names);