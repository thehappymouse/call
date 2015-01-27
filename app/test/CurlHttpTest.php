<?php

/**
 * Class  使用curl扩展测试各应用
 */
class CurlHttpTest extends \UnitTestCase
{

    private static $cookie = "";

    private function getCookie($user = "admin", $pass = "123")
    {
        if (self::$cookie == "") {
            /*-----保存COOKIE-----*/

            $url = "http://192.168.1.7:8090/ams/index/loginCheck";

            $post = "UserName=admin&Password=123"; //POST数据

            $ch = curl_init($url); //初始化


            curl_setopt($ch, CURLOPT_HEADER, 1); //将头文件的信息作为数据流输出

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回获取的输出文本流

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //发送POST数据

            $content = curl_exec($ch); //执行curl并赋值给$content

            preg_match('/Set-Cookie:(.*);/iU', $content, $str); //正则匹配
            curl_close($ch); //关闭curl
            $cookie = $str[1]; //获得COOKIE（SESSIONID）
            self::$cookie = $cookie;
        }

        return self::$cookie;
    }

    /**
     *
     * @param $url
     * @param array $params
     */
    private function getResponse($url, $params, $cookie = null)
    {
        $post = "";
        $params["phpunit"] = "1";

        foreach ($params as $key => $v) {
            if ($post != "") $post .= "&";
            $post .= $key . "=" . $v;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie ? $cookie : $this->getCookie());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回获取的输出文本流,并非直接输出
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }

    public function testSegments()
    {
        //$this->getCookie("武国", "123");
        $url = 'http://192.168.1.7:8090/ams/Info/TeamList?ID=1&Type=1';
echo "F";

        $url2 = 'http://192.168.1.7:8090/ams/Info/UserList?ID=2&Type=Name';
    }

    public function testRequestParm()
    {
        $url2 = 'http://192.168.1.7:8090/ams/reminder/SearchFee'; //催费查询的地址
        $params = array(
            "Number" => "QC5SB132",
            "CustomerNumber" => "",
//            "FromData" => 	"2014-06-12",
//            "ToData" => 	"2014-06-19",
            "Arrears" => 1, #1，大于等一， 2小于
            "ArrearsValue" => 40, #欠费金额
        );
        $c = $this->getResponse($url2, $params);
        $data = json_decode($c);
        if ($data == null) {
            echo $c;
        }
        echo $c;
        $this->assertNotNull($data);
    }
}
