<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-5-1
 * Time: 上午10:58
 */
require_once dirname(__DIR__)."/../../includes/Func.php";

if(!isset($_POST['t'])){
    echo 0;
    exit();
}
$t = $_POST['t'];
if($t == "session"){
    echo getSession();
}elseif ($t == "cookie"){
    $r = isset($_COOKIE['WATER']) ? unserialize($_COOKIE['WATER']) : 0;
    echo json_encode($r, JSON_UNESCAPED_UNICODE);
}