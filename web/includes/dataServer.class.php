<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-18
 * Time: 下午2:18
 */
require_once "data.class.php";
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class DataServer{

    /*
     * 查询符合条件的数据行数
     * 传入 belongid, 站点号, 起始日期(时间戳), 终止日期（时间戳）,这里没有时间跨度限制
     * 返回 行数,错误时返回0
     */
    public static function getRow($belongid, $no, $start, $end){
        if($start <= $end){
            //过滤时间上限
            if($end > time()){
                $end = time();
            }

            $sqlHelper = new SqlHelper();
            $sy = date("Y",$start);
            $ey = date("Y",$end);
            $sm = date("m",$start);
            $em = date("m",$end);
            $temp = array();
            $tables = array();
            //拼接表名
            for($i = $sy; $i <= $ey; $i++){
                if($i == $sy){
                    $j = $sm;
                }else{
                    $j = 1;
                }
                if($i == $ey){
                    $k = $em;
                }else{
                    $k = 12;
                }
                for($a = $j; $a <= $k; $a++){
                    //将当前月份格式化为2位数
                    if(preg_match("/^\d{2}$/",$a)){
                        $temp[] = "_".$i.$a;
                    }else{
                        $temp[] = "_".$i."0".$a;
                    }
                }
            }
            //以上获取到的$temp为用户自定义输入的表名的集合
            //过滤不存在的表名
            $tempCount = count($temp);
            for ($i = 0; $i < $tempCount; $i++){
                $sql = "show tables like '$temp[$i]'";
                if($sqlHelper->execute_dql($sql))
                    $tables[] = $temp[$i];
            }
            $sql = "";
            $tableCount = count($tables);
            for ($i = 0; $i < $tableCount; $i++){
                $sql .= "select count(*) from $tables[$i] where belongid ='$belongid' and no = '$no'";
                $sql .= " and time >= $start and time <= $end";
                if($i < $tableCount -1)
                    $sql .= " union all ";
            }
            if($sql){
                $r = $sqlHelper->execute_dql_arr($sql);
                $sqlHelper->close();
                $sum = 0;
                foreach ($r as $s){
                    $sum += $s['count(*)'];
                }
                return $sum;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    /*
     *获取数据
     *
     */
    public static function getPage($belongid, $no, $start, $end, $startRow, $pageRow){
        if($start <= $end){
            //过滤时间上限
            if($end > time()){
                $end = time();
            }

            $sqlHelper = new SqlHelper();
            $sy = date("Y",$start);
            $ey = date("Y",$end);
            $sm = date("m",$start);
            $em = date("m",$end);
            $temp = array();
            $tables = array();
            //拼接表名
            for($i = $sy; $i <= $ey; $i++){
                if($i == $sy){
                    $j = $sm;
                }else{
                    $j = 1;
                }
                if($i == $ey){
                    $k = $em;
                }else{
                    $k = 12;
                }
                for($a = $j; $a <= $k; $a++){
                    //将当前月份格式化为2位数
                    if(preg_match("/^\d{2}$/",$a)){
                        $temp[] = "_".$i.$a;
                    }else{
                        $temp[] = "_".$i."0".$a;
                    }
                }
            }
            //以上获取到的$temp为用户自定义输入的表名的集合
            //过滤不存在的表名
            $tempCount = count($temp);
            for ($i = 0; $i < $tempCount; $i++){
                $sql = "show tables like '$temp[$i]'";
                if($sqlHelper->execute_dql($sql))
                    $tables[] = $temp[$i];
            }
            $sql = "";
            $tableCount = count($tables);
            for ($i = 0; $i < $tableCount; $i++){
                $sql .= "select time,tm,ph,ox,el,nt,po,nh,cl,ca from $tables[$i] where belongid ='$belongid' and no = '$no'";
                $sql .= " and time >= $start and time <= $end";
                if($i < $tableCount -1)
                    $sql .= " union all ";
            }
            if($sql){
                $sql .= " limit $startRow,$pageRow";
                $r = $sqlHelper->execute_dql_arr($sql);
                $sqlHelper->close();
                if($r){
                    $datas = array();
                    foreach ($r as $d){
                        $data = new Data();
                        $data->time = $d['time'];
                        $data->ca = $d['ca'];
                        $data->cl = $d['cl'];
                        $data->el = $d['el'];
                        $data->nh = $d['nh'];
                        $data->nt = $d['nt'];
                        $data->ox = $d['ox'];
                        $data->ph = $d['ph'];
                        $data->tm = $d['tm'];
                        $data->po = $d['po'];
                        $datas[] = $data;
                    }
                    $datas[] = self::getRow($belongid,$no,$start,$end);
                    return $datas;
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    /*
     * 获取给定参数对应日期图表数据
     */
    public static function getChart($belongid, $no, $start, $end, $target){
        if($start <= $end) {
            //过滤时间上限
            if ($end > time()) {
                $end = time();
            }

            $sqlHelper = new SqlHelper();
            $sy = date("Y", $start);
            $ey = date("Y", $end);
            $sm = date("m", $start);
            $em = date("m", $end);
            $temp = array();
            $tables = array();
            //拼接表名
            for ($i = $sy; $i <= $ey; $i++) {
                if ($i == $sy) {
                    $j = $sm;
                } else {
                    $j = 1;
                }
                if ($i == $ey) {
                    $k = $em;
                } else {
                    $k = 12;
                }
                for ($a = $j; $a <= $k; $a++) {
                    //将当前月份格式化为2位数
                    if (preg_match("/^\d{2}$/", $a)) {
                        $temp[] = "_" . $i . $a;
                    } else {
                        $temp[] = "_" . $i . "0" . $a;
                    }
                }
            }
            //以上获取到的$temp为用户自定义输入的表名的集合
            //过滤不存在的表名
            $tempCount = count($temp);
            for ($i = 0; $i < $tempCount; $i++) {
                $sql = "show tables like '$temp[$i]'";
                if ($sqlHelper->execute_dql($sql))
                    $tables[] = $temp[$i];
            }
            $sql = "";
            $tableCount = count($tables);
            for($i = 0; $i < $tableCount; $i++){
                $sql .= "select time,$target from $tables[$i] where time >= $start and ";
                $sql .= "time <= $end and no = '$no' and belongid = '$belongid'";
                if($i < $tableCount -1)
                    $sql .= " union all ";
            }
            if($sql){
                $datas = array();
                $r = $sqlHelper->execute_dql_arr($sql);
                $sqlHelper->close();
                foreach ($r as $s){
                    $data = new Data();
                    $data->time = $s['time'];
                    $data->$target = $s[$target];
                    $datas[] = $data;
                }
                return $datas;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
}