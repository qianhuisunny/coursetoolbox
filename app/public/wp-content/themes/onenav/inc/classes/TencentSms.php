<?php
namespace Tencent\ioSms;
/**
 * 腾讯云短信SDK轻量版
 * 
 * 腾讯云SDK2.0 https://github.com/qcloudsms/qcloudsms_php
 */
class SendSms {
    private $appid;
    private $appkey;

    /**
     * 构造函数
     *
     * @param string $appid  sdkappid
     * @param string $appkey sdkappid对应的appkey
     */
    public function __construct($appid, $appkey) {
        $this->appid  = $appid;
        $this->appkey = $appkey;
    }
    /**
     * 获取 SDK 地址
     * 
     * @return string sdk 地址
     */
    public function getUrl($is_multi = false) {
        $url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms";
        if($is_multi)
            $url = "https://yun.tim.qq.com/v5/tlssmssvr/sendmultisms2";
        return $url;
    }
    /**
     * 生成随机数
     *
     * @return int 随机数结果
     */
    public function getRandom() {
        return rand(100000, 999999);
    }
    /**
     * 生成签名
     *
     * @param string $appkey        sdkappid对应的appkey
     * @param string $random        随机正整数
     * @param string $curTime       当前时间
     * @param string|array $phone   手机号码
     * @return string  签名结果
     */
    public function calculateSig($appkey, $random, $curTime, $phone) {
        $_phone = $phone;
        if(is_array($phone)){
            $_phone = $phone[0];
            for ($i = 1; $i < count($phone); $i++) {
                $_phone .= ("," . $phone[$i]);
            }
        }
        return hash("sha256", "appkey={$appkey}&random={$random}&time={$curTime}&mobile={$_phone}");
    }
    /**
     * 多组电话号码转数组
     * @param string $nationCode
     * @param array $phones
     * @return array
     */
    public function phonesToArray($nationCode, $phones) {
        $i = 0;
        $tel = array();
        do {
            $telElement = array(
                'nationcode' => $nationCode,
                'mobile'     => $phones[$i],
            );
            array_push($tel, $telElement);
        } while (++$i < count($phones));
        return $tel;
    }

    /**
     * 普通发送
     *
     * 普通发送需明确指定内容，如果有多个签名，请在内容中以【】的方式添加到信息内容中，否则系统将使用默认签名。
     *
     * @param int    $type        短信类型，0 为普通短信，1 营销短信
     * @param string $nationCode  国家码，如 86 为中国
     * @param string|array $phone 不带国家码的手机号，支持多个电话群发
     * @param string $msg         信息内容，必须与申请的模板格式一致，否则将返回错误
     * @param string $extend      扩展码，可填空串
     * @param string $ext         服务端原样返回的参数，可填空串
     * @return string 应答json字符串，详细内容参见腾讯云协议文档
     */
    public function send($type, $nationCode, $phone, $msg, $extend = "", $ext = "") {
        $random   = $this->getRandom();
        $curTime  = time();
        $wholeUrl = $this->getUrl(is_array($phone)) . "?sdkappid=" . $this->appid . "&random=" . $random;
        $sig      = $this->calculateSig($this->appkey, $random, $curTime, $phone);

        $tel = array(
            'nationcode'=>"".$nationCode,
            'mobile'    =>"".$phone,
        );
        if(is_array($phone)){
            $tel = $this->phonesToArray($nationCode, $phone);
        }
        $data = array(
            'tel'    => $tel,
            'type'   => (int)$type,
            'msg'    => $msg,
            'sig'    => $sig,
            'time'   => $curTime,
            'extend' => $extend,
            'ext'    => $ext,
        );
        return $this->sendCurlPost($wholeUrl, $data);
    }
    /**
     * 发送指定模板
     *
     * @param string $nationCode  国家码，如 86 为中国
     * @param string|array $phone 不带国家码的手机号，支持多个电话群发
     * @param int    $templId     模板 id
     * @param array  $params      模板参数列表，如模板 {1}...{2}...{3}，那么需要带三个参数
     * @param string $sign        签名，如果填空串，系统会使用默认签名
     * @param string $extend      扩展码，可填空串
     * @param string $ext         服务端原样返回的参数，可填空串
     * @return string 应答json字符串，详细内容参见腾讯云协议文档
     */
    public function sendParam($nationCode, $phone, $templId = 0, $params = "", $sign = "", $extend = "", $ext = "") {
        $random   = $this->getRandom();
        $curTime  = time();
        $wholeUrl = $this->getUrl(is_array($phone)) . "?sdkappid=" . $this->appid . "&random=" . $random;
        $sig      = $this->calculateSig($this->appkey, $random, $curTime, $phone);

        $tel = array(
            'nationcode' => $nationCode,
            'mobile'     => $phone,
        );
        if(is_array($phone)){
            $tel = $this->phonesToArray($nationCode, $phone);
        }
        $data = array(
            'tel'    => $tel,
            'sig'    => $sig,
            'tpl_id' => $templId,
            'params' => $params,
            'sign'   => $sign,
            'time'   => $curTime,
            'extend' => $extend,
            'ext'    => $ext,
        );
        return $this->sendCurlPost($wholeUrl, $data);
    }
    /**
     * 发送请求
     *
     * @param string $url      请求地址
     * @param array  $dataObj  请求内容
     * @return string 应答json字符串
     */
    public function sendCurlPost($url, $dataObj) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataObj));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $ret = curl_exec($curl);
        if (false == $ret) {
            $result = "{ \"result\":" . -2 . ",\"errmsg\":\"" . curl_error($curl) . "\"}";
        } else {
            $rsp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "{ \"result\":" . -1 . ",\"errmsg\":\"". $rsp
                        . " " . curl_error($curl) ."\"}";
            } else {
                $result = $ret;
            }
        }
        curl_close($curl);
        return $result;
    }
}