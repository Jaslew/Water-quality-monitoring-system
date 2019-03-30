<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-2-28
 * Time: 下午12:15
 */

require_once "Func.php";
require_once "keyServer.class.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['showType']) && isset($_POST['pageNow']) && isset($_POST['pageRow'])))
    exit();

$id = getID();
$showType = $_POST['showType'];
$pageRow = $_POST['pageRow'];
$pageNow = $_POST['pageNow'];

//简单过滤
if($pageNow < 1)
    exit();

$rowStart = ($pageNow - 1) * $pageRow;

$keyServer = new KeyServer();
echo json_encode($keyServer::getKeyInfo($id, $showType, $rowStart, $pageRow));