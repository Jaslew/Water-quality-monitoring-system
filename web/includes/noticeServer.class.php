<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-4
 * Time: 上午11:37
 */

require_once "notice.class.php";
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class NoticeServer{
    public static function getNotice($id, $start, $pageRow){
        $sqlHelper = new SqlHelper();
        $sql = "select * from notice where id = '$id' order by time desc limit $start,$pageRow";
        $r = $sqlHelper->execute_dql_arr($sql);
        if($r){
            $notices = array();
            foreach ($r as $n) {
                $notice = new Notice();
                $notice->time = $n['time'];
                $notice->isread = $n['isread'];
                $notice->content = $n['content'];
                $notices[] = $notice;
            }
            //附加总记录数
            $sql = "select count(*) from notice where id = '$id'";
            $r = $sqlHelper->execute_dql($sql);
            $sqlHelper->close();
            $notices[] = $r['count(*)'];
            return $notices;
        }else{
            return 0;
        }
    }

    public static function delNotice($id, $time){
        $sqlHelper = new SqlHelper();
        $sql = "delete from notice where id = '$id' and time = '$time'";
        $r = $sqlHelper->execute_dml($sql);
        return $r;
    }

    public static function setNotice($id, $time){
        $sqlHelper = new SqlHelper();
        $sql = "update notice set isread = 1 where id = '$id' and time = '$time'";
        $r = $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
        return $r;
    }
}