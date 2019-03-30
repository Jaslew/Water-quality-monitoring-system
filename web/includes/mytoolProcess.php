<?php
require_once dirname(__DIR__)."/../includes/Mycrypt.class.php";
if(isset($_POST['type'])){
    $type = $_POST['type'];
    $text = $_POST['text'];
    $myCrypt = new Mycrypt();
    if($type == 1){
        //加密
        $text = $myCrypt->encrypt($text);
    }elseif ($type == 2){
        $text = $myCrypt->decrypt($text);
    }
    echo $text;
}
