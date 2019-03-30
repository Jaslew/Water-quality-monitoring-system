<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-21
 * Time: 下午2:29
 */


require_once "dataServer.class.php";
require_once "chart.class.php";
require_once "adminServer.class.php";
require_once "Func.php";

if(getSession() == 0)
    exit();

if(!(isset($_POST['start']) && isset($_POST['end']) && isset($_POST['no'])
    && isset($_POST['queryType']) && isset($_POST['target']))){
    exit();
}

$start =  strtotime($_POST['start']);
$end = strtotime($_POST['end']);
$end += 24*60*60;
$no = $_POST['no'];
$id = getID();
$belongid = AdminServer::getBelongid($id);
$queryType = $_POST['queryType'];
$target = $_POST['target'];
//简单过滤
$targets = array("tm","ph","ox","el","nt","cl","po","nh","ca");
$queryTypes = array("h","d","m","y");
if(!in_array($target, $targets) || !in_array($queryType, $queryTypes)){
    echo 0;
    exit();
}

$dataServer = new DataServer();
$data =  $dataServer::getChart($belongid,$no,$start,$end,$target);
if($data){
    $charts = array();
    switch ($queryType){
        case $queryType == "h":{
           foreach ($data as $d){
               $chart = new Chart();
               $chart->time = date("Y-m-d H:i:s",$d->time);
               $chart->val = $d->$target;
               $charts[] = $chart;
           }
           break;
        }
        case $queryType == "d":{
            $type = date("d",$data[0]->time);
            $timeTemp = date("Y-m-d",$data[0]->time);
            $valTemp = 0;
            $i = 0; //同一天数据个数
            foreach ($data as $d){
                if($type == date("d", $d->time)){
                    //同一天,将数据累加
                    $i++;
                    $valTemp += $d->$target;
                }else{
                    $chart = new Chart();
                    $chart->val = round($valTemp/$i, 2);
                    $chart->time = $timeTemp;
                    $charts[] = $chart;

                    $i = 1;
                    $valTemp = $d->$target;
                    $timeTemp = date("Y-m-d", $d->time);
                    $type = date("d", $d->time);
                }
            }
            $chart = new Chart();
            $chart->val = round($valTemp/$i, 2);
            $chart->time = $timeTemp;
            $charts[] = $chart;
            break;
        }
        case $queryType == "m":{
            $type = date("m",$data[0]->time);
            $timeTemp = date("Y-m",$data[0]->time);
            $valTemp = 0;
            $i = 0; //同一天数据个数
            foreach ($data as $d){
                if($type == date("m", $d->time)){
                    //同一天,将数据累加
                    $i++;
                    $valTemp += $d->$target;
                }else{
                    $chart = new Chart();
                    $chart->val = round($valTemp/$i, 2);
                    $chart->time = $timeTemp;
                    $charts[] = $chart;

                    $i = 1;
                    $valTemp = $d->$target;
                    $timeTemp = date("Y-m", $d->time);
                    $type = date("m", $d->time);
                }
            }
            $chart = new Chart();
            $chart->val = round($valTemp/$i, 2);
            $chart->time = $timeTemp;
            $charts[] = $chart;
            break;
        }
        case $queryType == "y":{
            $type = date("Y",$data[0]->time);
            $timeTemp = date("Y",$data[0]->time)."年";
            $valTemp = 0;
            $i = 0; //同一天数据个数
            foreach ($data as $d){
                if($type == date("Y", $d->time)){
                    //同一天,将数据累加
                    $i++;
                    $valTemp += $d->$target;
                }else{
                    $chart = new Chart();
                    $chart->val = round($valTemp/$i, 2);
                    $chart->time = $timeTemp;
                    $charts[] = $chart;

                    $i = 1;
                    $valTemp = $d->$target;
                    $timeTemp = date("Y", $d->time)."年";
                    $type = date("Y", $d->time);
                }
            }
            $chart = new Chart();
            $chart->val = round($valTemp/$i, 2);
            $chart->time = $timeTemp;
            $charts[] = $chart;
            break;
        }
    }
    echo json_encode($charts);
}else{
    echo 0;
}