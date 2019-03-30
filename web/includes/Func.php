<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-15
 * Time: 下午3:06
 */
//记录用户当前登录时间信息
function setNowTime($user){
    //先保存上次的登陆信息再重置
    $nowTime="nowTime".$user;
    $lastTime="lastTime".$user;
    if(isset($_COOKIE[$nowTime])){
        $info=$_COOKIE[$nowTime];
        setcookie($lastTime,$info,time()+3600*24*30*6, "/");
    }
    setcookie($nowTime,date("Y年n月j日 H:i"),time()+3600*24*30*6,"/");
}

//返回上次登录时间信息
function getLastTime($user){
    $lastTime="lastTime".$user;
    if(!isset($_COOKIE[$lastTime]))
        $info="您是第一次登录";
    else{
        $info="您上次登录时间是 ";
        $info.=$_COOKIE[$lastTime];
    }
    return $info;
}

//设置当前会话
function setSession($key,$val){
    if(!session_id())
        session_start();
    $_SESSION[$key]=$val;
}

//判断当前会话是否存在
function getSession(){
    if(!session_id())
        session_start();
    if(isset($_SESSION["user"]))
        return 1;
    else
        return 0;
}

//获取当前会话
function getID(){
    if(!session_id())
        session_start();
    return $_SESSION["user"];
}

//防止用户非法登陆
function checkLogin(){
    if(!session_id())
        session_start();
    if(!isset($_SESSION['user'])){
        header("Location:login.html");
        exit();
    }
}
//用户安全退出
function logOut(){
    if(!session_id())
        session_start();
    session_destroy();
}