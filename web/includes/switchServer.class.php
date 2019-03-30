<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-18
 * Time: 下午8:57
 */
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class SwitchServer{
    //获取制定站点按钮名称
    public static function getSWName($belongid, $no){
        $sqlHelper = new SqlHelper();
        $sql = "select sw1,sw2,sw3,sw4,sw5,sw6,sw7,sw8 from switch where belongid = '$belongid' and no = '$no'";
        $r = $sqlHelper->execute_dql($sql);
        $names = [];
        if($r){
            for($i = 0; $i < 8; $i++){
                $sw = "sw".($i+1);
                $names[$i] = $r[$sw];
            }
        }
        $sqlHelper->close();
        return $names;
    }
    //更改按钮名称
    public static function switchUpdate($belongid, $no, $names){
        $sqlHelper = new SqlHelper();
        $sql = "update switch set ";
        $tag = 0;
        for($i = 0; $i < 8; $i++){
            if($names[$i] != ""){
                if($tag == 1)
                    $sql .= ",";
                $sql .= "sw".($i + 1)."='".$names[$i]."' ";
                $tag = 1;
            }
        }
        $sql .= " where belongid = '$belongid' and no = '$no'";
        $r = $sqlHelper->execute_dml($sql);
        return $r;
    }
}