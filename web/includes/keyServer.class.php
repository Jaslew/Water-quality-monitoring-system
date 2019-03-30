<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-2-28
 * Time: 下午12:41
 */

require_once "key.class.php";
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class KeyServer{
    /*****
     * 功能： 按照showType查询用户
     * 传入： 用户id,展示类型，当前页，一页展示的记录数
     * 返回 key对象 或 0
     *****/
    public static function getKeyInfo($id, $showType, $rowStart, $pageRow){
        $sqlHelper = new SqlHelper();
        //判断当前账号权限
        $sql = "select roleid,belongid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        if(!$r)
            return 0;
        $belongid = $r['belongid'];
        $roleid = $r['roleid'];
        if($roleid == 1){
            if($showType == 0){
                $sql = "select id, roleid, name, lasttime, tel, email, header from admin where belongid = '$belongid' limit $rowStart,$pageRow";
            }else{
                $sql = "select id, roleid, name, lasttime, tel, email, header from admin where belongid = '$belongid' ";
                $sql .= "and roleid = $showType limit $rowStart,$pageRow";
            }
            $r = $sqlHelper->execute_dql_arr($sql);
            $sqlHelper->close();
            if($r){
                $keys = array();
                foreach ($r as $k){
                    $key = new Key();
                    $key->id = $k['id'];
                    $key->roleid = $k['roleid'];
                    $key->name = $k['name'];
                    $key->lasttime = $k['lasttime'];
                    $key->email = $k['email'];
                    $key->tel = $k['tel'];
                    $key->header = $k['header'];
                    $keys[] = $key;
                }
                //附加上总公共的记录数
                $keys[] = self::getRow($showType, $belongid, $roleid);
                return $keys;
            }else{
                return 0;
            }
        }elseif($roleid == 2 || $roleid == 3){
            if($showType == 0){
                $sql = "select id, roleid, name, lasttime, tel, email, header from admin where id = '$belongid' or id = '$id'";
            }elseif($showType == 1){
                $sql = "select id, roleid, name, lasttime, tel, email, header from admin where id = '$belongid'";
            }else{
                $sql = "select id, roleid, name, lasttime, tel, email, header from admin where id = '$id' and roleid = $showType";
            }
            $r = $sqlHelper->execute_dql_arr($sql);
            if($r){
                $keys = array();
                foreach ($r as $k){
                    $key = new Key();
                    $key->id = $k['id'];
                    $key->roleid = $k['roleid'];
                    $key->name = $k['name'];
                    $key->lasttime = $k['lasttime'];
                    $key->email = $k['email'];
                    $key->tel = $k['tel'];
                    $key->header = $k['header'];
                    $keys[] = $key;
                }
                //附加上总公共的记录数
                $keys[] = self::getRow($showType, $belongid, $roleid);
                return $keys;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    //获取符合查询条件的总行数
    public static function getRow($showType, $belongid, $roleid){
        $sqlHelper = new SqlHelper();
        if($roleid == 1){
            if($showType == 0){
                $sql = "select count(*) from admin where belongid = '$belongid'";
            }else{
                $sql = "select count(*) from admin where belongid = '$belongid' and roleid = $showType";
            }
            $r = $sqlHelper->execute_dql($sql);
            if($r){
                return $r['count(*)'];
            }else{
                return 0;
            }
        }elseif($roleid == 2 || $roleid == 3){
            return $showType == 0 ? 2 :($showType == $roleid ? 1 : 0);
        }else{
            return 0;
        }
    }

    //获取当前 belongid 对应的所有admin对象
    public static function getKeyAll($belongid){
        $sqlHelper = new SqlHelper();
        $sql = "select id, roleid, name, lasttime, tel, email, header from admin where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql_arr($sql);
        $sqlHelper->close();
        //装填Key对象
        if($r){
            $keys = array();
            foreach ($r as $k){
                $key = new Key();
                $key->id = $k['id'];
                $key->roleid = $k['roleid'];
                $key->name = $k['name'];
                $key->lasttime = $k['lasttime'];
                $key->email = $k['email'];
                $key->tel = $k['tel'];
                $key->header = $k['header'];
                $keys[] = $key;
            }
            return $keys;
        }else{
            return 0;
        }
    }

    //获取当前 id 对应的所有admin对象以及他的belongid对象
    public static function getKeyByID($id, $belongid){
        $sqlHelper = new SqlHelper();
        $sql = "select id, roleid, name, lasttime, tel, email, header from admin where id = '$id' or id = '$belongid'";
        $r = $sqlHelper->execute_dql_arr($sql);
        $sqlHelper->close();
        //装填Key对象
        if($r){
            $keys = array();
            foreach ($r as $k){
                $key = new Key();
                $key->id = $k['id'];
                $key->roleid = $k['roleid'];
                $key->name = $k['name'];
                $key->lasttime = $k['lasttime'];
                $key->email = $k['email'];
                $key->tel = $k['tel'];
                $key->header = $k['header'];
                $keys[] = $key;
            }
            return $keys;
        }else{
            return 0;
        }
    }

    //获取当前 id对应的admin对象
    public static function getKey($id){
        $sqlHelper = new SqlHelper();
        $sql = "select id, roleid, name, lasttime, tel, email, header from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        //装填Key对象
        if($r){
            $key = new Key();
            $key->id = $r['id'];
            $key->roleid = $r['roleid'];
            $key->name = $r['name'];
            $key->lasttime = $r['lasttime'];
            $key->email = $r['email'];
            $key->tel = $r['tel'];
            $key->header = $r['header'];
            return $key;
        }else{
            return 0;
        }
    }
}