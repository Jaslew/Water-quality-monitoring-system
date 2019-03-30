<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-16
 * Time: 下午4:55
 * ××××××××××××××××××  这里设置预警阈值  ××××××××××××××××××
 */
class Warm{
    //温度
    public static $tm_up = 30;
    public static $tm_down = 0;
    //ph
    public static $ph_up = 8;
    public static $ph_down = 4;
    //含氧量
    public static $ox_up = 100;
    public static $ox_down = 0;
    //电导率
    public static $el_up = 100;
    public static $el_down = 0;
    //浊度
    public static $nt_up = 100;
    public static $nt_down = 0;
    //总磷
    public static $po_up = 100;
    public static $po_down = 0;
    //氨氮
    public static $nh_up = 100;
    public static $nh_down = 0;
    //氯含量
    public static $cl_up = 100;
    public static $cl_down = 0;
    //碳含量
    public static $ca_up = 100;
    public static $ca_down = 0;
}
