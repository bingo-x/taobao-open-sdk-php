<?php namespace TBK;

/**
 * Class TbkClient
 * 封装对淘宝开放平台的各种API操作
 * @package TBK
 */
class TbkClient {

    protected $appkey;

    protected $appsecret;

    /**
     * API网关地址。
     * 1、http 网关
     *       正式环境：http://gw.api.taobao.com/router/rest
     *       沙箱环境：http://gw.api.tbsandbox.com/router/rest
     * 2、https 网关
     *       正式环境：https://eco.taobao.com/router/rest
     *      沙箱环境：https://gw.api.tbsandbox.com/router/rest
     * @var string
     */
    protected $gatewayUrl = "http://gw.api.taobao.com/router/rest";

    protected $format = "json";

    protected $connectTimeout;

    protected $readTimeout;

    protected $signMethod = "md5";

    protected $apiVersion = "2.0";

    protected $sdkVersion = "open-sdk-php-20151012";

    protected function generateSign($params) {
        ksort($params);

        $stringToBeSigned = $this->secretKey;
        foreach ($params as $k => $v)
        {
            if("@" != substr($v, 0, 1))
            {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->secretKey;

        return strtoupper(md5($stringToBeSigned));
    }

    public function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
        }
        if ($this->connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        //https 请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($postFields) && 0 < count($postFields))
        {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v)
            {
                if("@" != substr($v, 0, 1))//判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                }
                else//文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart)
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
            else
            {
                $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
                curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
            }
        }
        $reponse = curl_exec($ch);

        if (curl_errno($ch))
        {
            throw new Exception(curl_error($ch),0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode)
            {
                throw new Exception($reponse,$httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
}
