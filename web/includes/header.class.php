<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-3-3
 * Time: 上午11:11
 */
require_once "Func.php";
require_once "adminServer.class.php";

class Header{
    public function setImg($imgFile, $imgParam, $readyId)
    {
        $x  =  $imgParam['x']; //选择区域左上角x轴坐标
        $y  =  $imgParam['y']; //选择区域左上角y轴坐标
        $w  =  $imgParam['w']; //选择区 的宽
        $h  =  $imgParam['h']; //选择区 的高

        $isUseOrign = 0;
        $header = AdminServer::getHeader($readyId);     //获取数据库中的头像记录
        if(!$imgFile){      //用户没有上传图片
            if($header){
                //数据库中有头像记录
                $imgFile = dirname(__DIR__)."/images/header/".$header;
            }else{
                //使用系统默认头像
                $isUseOrign = 1;
                $imgFile = dirname(__DIR__)."/images/header/header.png";
            }

        }
        $imgInfo = getimagesize($imgFile);

        $sw = $imgInfo[0];  //源图片宽度
        $sh = $imgInfo[1];  //源图片高度

        $imgContent = 200;         //前端正方形画布的宽高
        $imgBoard = 200;            //转换后画布的宽高

        $im = $this->getPicType($imgInfo[2], $imgFile);

        //转换截图起始点坐标
        $x = $x / $imgContent * $sw;
        $y = $y / $imgContent * $sh;

        //转换截图区域宽高
        $w = $imgContent / $w * $imgBoard;
        $h = $imgContent / $h * $imgBoard;

        $newim  = imagecreatetruecolor($imgBoard, $imgBoard); //创建真彩色画布

        //$w,$h是相对于画布缩小或者放大后的数值，例：画布宽度为200，如需缩小一半，则$w为400 （200/（1/2））
        imagecopyresampled($newim ,  $im , 0, 0,  $x,  $y, $w, $h, $sw, $sh);

        //如果使用的是系统默认头像，则新建头像，否则覆盖
        if($isUseOrign == 0){
            //不使用系统默认头像
            $r = imagejpeg($newim , $imgFile);
        }else{
            //使用系统默认头像
            $r = imagejpeg($newim ,dirname(__DIR__)."/images/header/"."img".$readyId.".png");
        }

        //生成新头像成功
        if($r){
            $newHeader = "img".$readyId.".".explode(".", $imgFile)[1];
            if($newHeader != $header){
                //删除旧头像
                $header = dirname(__DIR__)."/images/header/".$header;
                unlink($header);
            }
            AdminServer::setHeader($readyId, $newHeader);
        }

        imagedestroy($im );
        imagedestroy($newim );
    }

    /**
     * function 判断并返回图片的类型(以资源方式返回)
     * @param int $type 图片类型
     * @param string $picname 图片名字
     * @return $im  //图像资源
     */
    public function getPicType($type,$picname)
    {
        $im = null;
        switch($type)
        {
            case 1:  //GIF
                $im = imagecreatefromgif($picname);
                break;
            case 2:  //JPG
                $im = imagecreatefromjpeg($picname);
                break;
            case 3:  //PNG
                $im = imagecreatefrompng($picname);
                break;
            case 4:  //BMP
                $im = imagecreatefromwbmp($picname);
                break;
            default:
                break;
        }
        return $im;
    }
}