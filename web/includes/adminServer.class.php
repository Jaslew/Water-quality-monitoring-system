<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-15
 * Time: 下午4:55
 */

require_once "admin.class.php";
require_once dirname(__DIR__)."/../includes/SqlHelper.php";

class AdminServer{
    /*
     * 获取当前id对应Admin对象
     */
    public static function getAdmin($id){
        $admin = new Admin();
        $sqlHelper = new SqlHelper();
        $sql = "select belongid, roleid, name, header, tel, email, header from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        //装填Admin对象
        if($r){
            $admin->setId($id);
            $admin->setBelongid($r['belongid']);
            $admin->setRoleid($r['roleid']);
            $admin->setName($r['name']);
            $admin->setHeader($r['header']);
            $admin->setTel($r['tel']);
            $admin->setEmail($r['email']);
        }
        $sqlHelper->close();
        return $admin;
    }

    /**
     * 获取 $belongid 对应的所有 admin对象
     */
    public static function getAdminAll($belongid){
        $sqlHelper = new SqlHelper();
        $sql = "select id, roleid, name, header, tel, email, header from admin where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql_arr($sql);
        $sqlHelper->close();
        //装填Admin对象
        if($r){
            $admins = array();
            foreach ($r as $s){
                $admin = new Admin();
                $admin->setId($s['id']);
                $admin->setBelongid($belongid);
                $admin->setRoleid($s['roleid']);
                $admin->setName($s['name']);
                $admin->setHeader($s['header']);
                $admin->setTel($s['tel']);
                $admin->setEmail($s['email']);
                $admins[] = $admin;
            }
            return $admins;
        }else{
            return 0;
        }
    }

    /**
     * 获取 $belongid 对应的所有 admin对象个数
     */
    public static function getAdminAllNum($belongid){
        $sqlHelper = new SqlHelper();
        $sql = "select count(*) from admin where belongid = '$belongid'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        //装填Admin对象
        if($r){
            return $r['count(*)'];
        }else{
            return 0;
        }
    }

    /***
     * 判断当前 id 是否存在,当前操作者是否能够操作$readyid
     * 是返回1 ；否返回 0
     */
    public static function isIdOK($readyid, $id){
        $sqlHelper = new SqlHelper();
        $sql = "select id, belongid from admin where id = '$readyid'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r){
            //当前 $readyid 存在，检查是否有操作权
            if($id == $r['belongid'] || $id == $r['id']){
                return 1;
            }else{
                return 0;
            }
        }else
            //当前 $readyId 不存在
            return 0;
    }

    /***
     * 判断传入传入的 id 是否为最高管理员
     * 是返回1 ；否返回 0
     */
    public static function isAdmin($id){
        $sqlHelper = new SqlHelper();
        $sql = "select belongid from role where belongid = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if(isset($r['belongid']) && $r['belongid'] == $id)
            return 1;
        return 0;
    }

    /**
     * 查询终级用户 md5密码
     */
    public static function getMD5Key($id){
        $sqlHelper = new SqlHelper();
        $sql = "select password from admin where id = '$id' and belongid = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if(isset($r['password']))
            return $r['password'];
        return 0;
    }

    /***
     * 获取当前 ID 角色
     * 返回角色代号
     */
    public static function getRole($id){
        $sqlHelper = new SqlHelper();
        $sql = "select roleid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        if($r && isset($r['roleid']))
            return $r['roleid'];
        else
            return 0;
    }

    /***
     * 添加用户
     * 返回 新用户 ID
     */
    public static function addUser($id, $name, $roleid, $tel, $email, $password){
        $sqlHelper = new SqlHelper();
        $time = time();
        $try = 0;
        while ($try == 0){
            $newid = substr($time,5,5).rand(10,99);
            $sql = "insert into admin values('$newid', '$id', $roleid, '$name', md5($password), $time, '','$tel','$email')";
            $try = $sqlHelper->execute_dml($sql);
        }
        $sqlHelper->close();
        return $newid;
    }

    /***
     * 修改用户
     * 成功返回 1， 由权限导致的失败返回 -1
     */
    public static function editeUser($readyId, $id, $name, $roleid, $tel, $email, $password){
        $sqlHelper = new SqlHelper();
        //检查操作者是否拥有待修改id的操作权限
        $sql = "select id from admin where belongid = '$id' and id = '$readyId'";
        $r = $sqlHelper->execute_dql($sql);
        if($r && $r['id']){
            //最高权限修改
            $sql = "update admin set name = '$name',roleid = $roleid,tel = '$tel', email = '$email' ";
            if($password != "")
                $sql .= ", password = md5($password) ";
            $sql .= "where belongid = '$id' and id = '$readyId'";
            $r = $sqlHelper->execute_dml($sql);
            $sqlHelper->close();
            return $r;
        }elseif($readyId == $id){
            //不具有最高权限，可修改自己，除角色外
            $sql = "update admin set name = '$name',tel = '$tel', email = '$email' ";
            if($password != "")
                $sql .= ", password = md5($password) ";
            $sql .= "where id = '$id'";
            $r = $sqlHelper->execute_dml($sql);
            $sqlHelper->close();
            return $r;
        }else{
            return -1;
        }
    }

    /***
     * 删除用户
     * 成功返回 header， 失败返回 -1
     */
    public static function delUser($readyId, $id){
        $sqlHelper = new SqlHelper();
        //检查操作者是否拥有待修改id的操作权限
        $sql = "select id, header from admin where belongid = '$id' and id = '$readyId'";
        $r = $sqlHelper->execute_dql($sql);
        if($r && $r['id']){
            $sql = "delete from admin where belongid = '$id' and id = '$readyId'";
            $sqlHelper->execute_dml($sql);
            return $r['header'];
        }else{
            $sqlHelper->close();
            return -1;
        }
    }

    /***
     * 更新登录时间
     * 传入 id
     * 返回上次登录时间戳
     */
    public static function setTime($id){
        $sqlHelper = new SqlHelper();
        $time = time();
        //检查操作者是否拥有待修改id的操作权限
        $sql = "select lasttime from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sql = "update admin set lasttime = '$time' where id = '$id'";
        $sqlHelper->execute_dml($sql);
        return $r['lasttime'];
    }

    /***
     * 设置头像
     */
    public static function setHeader($id, $header){
        $sqlHelper = new SqlHelper();
        //检查操作者是否拥有待修改id的操作权限
        $sql = "update admin set header = '$header' where id = '$id'";
        $sqlHelper->execute_dml($sql);
        $sqlHelper->close();
    }

    /***
     * 获取头像
     */
    public static function getHeader($id){
        $sqlHelper = new SqlHelper();
        $sql = "select header from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        return $r['header'];
    }

    public static function getBelongid($id){
        $sqlHelper = new SqlHelper();
        $sql = "select belongid from admin where id = '$id'";
        $r = $sqlHelper->execute_dql($sql);
        $sqlHelper->close();
        return $r['belongid'];
    }

}