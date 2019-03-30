<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-15
 * Time: 下午5:44
 */

require_once "station.class.php";
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class StationServer{
    /*
     * 获取单个的Station对象
     */
    public static function getStationSingle($belongid, $no){
        $sqlHelper = new SqlHelper();
        $sql = "select a.belongid,name,a.no,pos,hz,atime,ctime,tel,email,charge,b.ip,b.port,time from station a,border b where a.belongid = '$belongid' ";
        $sql .= "and a.belongid = b.belongid and a.no = '$no' and a.no = b.no ";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r) {
            $station = new Station();
            $station->belongid = $r['belongid'];
            $station->name = $r['name'];
            $station->no = $r['no'];
            $station->pos = $r['pos'];
            $station->hz = $r['hz'];
            $station->atime = $r['atime'];
            $station->ctime = $r['ctime'];
            $station->tel = $r['tel'];
            $station->email = $r['email'];
            $station->charge = $r['charge'];
            $station->time = $r['time'];
            $station->ip = $r['ip'];
            $station->port = $r['port'];
            return $station;
        }else{
            return 0;
        }
    }
    /*
     * 获取完整的Station对象
     */
    public static function getStation($belongid, $rowStart, $pageRow){
        $sqlHelper = new SqlHelper();
        $sql = "select a.belongid,name,a.no,pos,hz,atime,ctime,tel,email,charge,b.ip,b.port,time from station a,border b where a.belongid = '$belongid' ";
        $sql .= "and a.belongid = b.belongid and a.no = b.no limit $rowStart,$pageRow";
        $r = $sqlHelper->execute_dql_arr($sql);
        $sqlHelper->close();
        if($r) {
            $stations = array();
            foreach ($r as $k) {
                $station = new Station();
                $station->belongid = $k['belongid'];
                $station->name = $k['name'];
                $station->no = $k['no'];
                $station->pos = $k['pos'];
                $station->hz = $k['hz'];
                $station->atime = $k['atime'];
                $station->ctime = $k['ctime'];
                $station->tel = $k['tel'];
                $station->email = $k['email'];
                $station->charge = $k['charge'];
                $station->time = $k['time'];
                $station->ip = $k['ip'];
                $station->port = $k['port'];
                $stations[] = $station;
            }
            //附加上总公共的记录数
            $stations[] = self::getRow($belongid, $rowStart, $pageRow);
            return $stations;
        }else{
            return 0;
        }
    }

    /***
     * $belongid
     * $rowStart
     * $pageRow
     * 获取符合条件的总行数
     * return 行数
     */
    public static function getRow($belongid, $rowStart, $pageRow){
        $sqlHelper = new SqlHelper();
        $sql = "select count(*) from station where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r && $r['count(*)']){
            return $r['count(*)'];
        }else{
            return 0;
        }
    }

    /*
     * 传入 belongid
     * 获取station对象站点号，站点名
     */
    public static function getStationInfo($belongid){
        $sqlHelper = new SqlHelper();
        $stations = array();
        $sql = "select no,name from station where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql_arr($sql);
        $sqlHelper->close();
        //装填Station对象
        if($r){
            foreach ($r as $s){
                $station = new Station();
                $station->no = $s['no'];
                $station->name = $s['name'];
                $stations[] = $station;
            }
        }
        return $stations;
    }

    /*
     *  更新station
     *  传入 station对象
     *  @return 1 or 0
     */
    public static function updateStation($station){
        $no = $station->no;
        $belongid = $station->belongid;
        $charge = $station->charge;
        $email = $station->email;
        $pos = $station->pos;
        $name = $station->name;
        $atime = $station->atime;
        $hz = $station->hz;
        $tel = $station->tel;
        $sqlHelper = new SqlHelper();
        $sql = "select hz from station where belongid = '$belongid' and no = '$no'";
        $r = $sqlHelper->execute_dql($sql);
        //站点号不合法
        if(!$r)
            return 0;

        $sql = "update station set charge = '$charge',email = '$email',pos = '$pos', name = '$name',";
        $sql .= "atime = '$atime',tel = '$tel' ";

        //如果修改了频率，需要设置对应标志位 tag2 为 1;
        if($hz != $r['hz'])
            $sql .= ",hz = '$hz',tag2 = 1 ";

        $sql .= "where belongid = '$belongid' and no = '$no'";
        $r = $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
        return $r;
    }

    public static function addStation($station){
        $no = $station->no;
        $belongid = $station->belongid;
        $charge = $station->charge;
        $email = $station->email;
        $pos = $station->pos;
        $name = $station->name;
        $atime = $station->atime;
        $hz = $station->hz;
        $tel = $station->tel;
        $ctime = $atime;
        $sqlHelper = new SqlHelper();
        //检查站点号是否已经存在
        $sql = "select no from station where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql_arr($sql);
        foreach ($r as $nos){
            if($nos['no'] == $no)
                return 0;
        }
        //插入数据
        $sql = "insert into station values('$belongid','$no','$name','$pos','$hz','$atime','$ctime'";
        $sql .= ",'$tel','$email','$charge','1','1')";
        $sqlHelper->execute_dml($sql);
        //在border表中追加相应记录
        $sql = "insert into border values('$belongid','$no','','',1500000000)";
        $sqlHelper->execute_dml($sql);
        //在switch表中添加记录
        $sql = "insert into switch(belongid, no) values('$belongid','$no')";
        $r = $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
        return $r;

    }

    public static function removeStation($belongid, $noList){
        $sqlHelper = new SqlHelper();
        $sql = "delete from station where belongid = '$belongid' and (";
        for($i = 0; $i < count($noList); $i++){
            $sql .= " no = '$noList[$i]' ";
            if($i < count($noList) - 1)
                $sql .= "or";
        }
        $sql .= " )";
        $r = $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
        return $r;
    }

}