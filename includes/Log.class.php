<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 17-11-14
 * Time: 下午1:47
 */

define("LOG", dirname(__FILE__)."/../log/mylog.log");

class Log{

    public static function write($type, $content){
        $str = "[".$type."] ".date("Y-m-d H:i:s")." ".$content."\r\n";

        if(is_writable(LOG)){
            file_put_contents(LOG, $str,FILE_APPEND);
        }
    }
}