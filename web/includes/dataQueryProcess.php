<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-18
 * Time: 下午2:20
 *
 * 传入 belongid, no, pageNow, pageRow, start, end
 */

require_once "dataServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['start']) && isset($_POST['end']) && isset($_POST['no'])
    && isset($_POST['belongid']) && isset($_POST['pageNow']) && isset($_POST['pageRow']))){
    exit();
}

$start =  strtotime($_POST['start']);
$end = strtotime($_POST['end']);
$end += 24*60*60;
$no = $_POST['no'];
$belongid = $_POST['belongid'];
$pageNow = $_POST['pageNow'];
$pageRow = $_POST['pageRow'];

//简单过滤
if($pageNow < 1)
    exit();

$startRow = ($pageNow - 1) * $pageRow;

$dataServer = new DataServer();
$data = $dataServer::getPage($belongid,$no,$start,$end,$startRow,$pageRow);
echo json_encode($data);