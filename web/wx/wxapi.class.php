<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-10
 * Time: 下午9:18
 */

class WXAPI{
    private static $html = "http://139.224.116.68/wx/htmls/";
    private static $token = "hello";
    private static $appid = "wx2c96425bb8a78473";
    private static $appsecret = "ba86962f9be8c1e7c30eab0c01cb13cf";

    //服务器签名验证
    public static function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];

        $tmpArr = array($timestamp, self::$token, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echostr;
        }else{
            echo "";
        }
    }

    //将获取到的 access_token 存储起来
    public static function getAccessToken(){
        $appid = self::$appid;
        $appsecret = self::$appsecret;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $curlobj = curl_init();
        //设置访问的url
        curl_setopt($curlobj, CURLOPT_URL, $url);
        //执行后不直接打印出
        curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, true);
        //设置https 支持
        date_default_timezone_get('PRC');   //使用cookies时，必须先设置时区
        curl_setopt($curlobj, CURLOPT_SSL_VERIFYPEER, 0);  //终止从服务端验证
        $output = json_decode(curl_exec($curlobj));  //执行获取内容
        curl_close($curlobj);          //关闭curl
        if(property_exists($output, 'access_token'))
            $output = $output->access_token;
        else
            $output = "";
        file_put_contents('access_token', $output);
    }

    //设置微信菜单,有返回结果
    public static function buttonSet(){
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::$appid;
        $buttons = array(
            'button' =>array(
                array(
                    'name'=>urlencode("控制面板"),
                    'type'=>'view',
                    //'url'=>$url."&redirect_uri=".urlencode(self::$html."index.php")."&response_type=code&scope=snsapi_base#wechat_redirect"
                    'url'=>"http://139.224.116.68/wx/htmls/index.php"
                ),
                array(
                    'name'=>urlencode("关于我们"),
                    'type'=>'view',
                    'url'=>"http://www.jiangnan.edu.cn/"
                )
            )
        );

        $post_fields = urldecode(json_encode($buttons));
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".file_get_contents("access_token");
        $curlobj = curl_init();
        //设置访问的url
        curl_setopt($curlobj, CURLOPT_URL, $url);
        //执行后不直接打印出
        curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, true);
        //设置https 支持
        date_default_timezone_get('PRC');   //使用cookies时，必须先设置时区
        curl_setopt($curlobj, CURLOPT_SSL_VERIFYPEER, 0);  //终止从服务端验证
        curl_setopt($curlobj,CURLOPT_POST,1);
        curl_setopt($curlobj,CURLOPT_POSTFIELDS, $post_fields);//post操作的所有数据的字符串。
        $output = json_decode(curl_exec($curlobj));  //执行获取内容
        curl_close($curlobj);          //关闭curl
        return $output;
    }

    //设置消息回复
    public static function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

        //extract post data
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgType = $postObj->MsgType;
            $textTpl = "<xml>
               <ToUserName><![CDATA[%s]]></ToUserName>
               <FromUserName><![CDATA[%s]]></FromUserName>
               <CreateTime>%s</CreateTime>
               <MsgType><![CDATA[%s]]></MsgType>
               <Content><![CDATA[%s]]></Content>
               </xml>";
            $contentStr = "";
            //如果是点击事件
            if($msgType == "event"){
                $eventKey =$postObj->EventKey;
                //
            }elseif($msgType == "text"){
                //
            }
            $time = time();
            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

}

