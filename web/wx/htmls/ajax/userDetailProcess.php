<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-30
 * Time: 上午11:35
 */
require_once dirname(__DIR__)."/../../includes/Func.php";
require_once dirname(__DIR__)."/../../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../../includes/keyServer.class.php";

if(getSession() == 0)
    exit();

if(isset($_POST['id']))
    $id = $_POST['id'];
else
    $id = getID();
echo json_encode(KeyServer::getKey($id), JSON_UNESCAPED_UNICODE);