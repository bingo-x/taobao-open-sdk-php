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

    public function __construct($appkey = null, $appsecret = null) {
        $this->appkey = $appkey;
        $this->appsecret = $appsecret;
    }

    public function execute($request) {
        $result = [
            'code' => 0,
            'msg' => '',
        ];
        //组装系统参数
        $sysParams["app_key"] = $this->appkey;
        $sysParams["v"] = $this->apiVersion;
        $sysParams["format"] = $this->format;
        $sysParams["sign_method"] = $this->signMethod;
        $sysParams["method"] = $request->getApiMethodName();
        $sysParams["timestamp"] = gmdate("Y-m-d H:i:s", time() + 28800);
        $sysParams["partner_id"] = $this->sdkVersion;

        //获取业务参数
        $apiParams = $request->getApiParas();

        //签名
        $sysParams["sign"] = $this->sign(array_merge($apiParams, $sysParams));

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?" . http_build_query($sysParams);

        //发起HTTP请求
        try {
            $response = $this->curl($requestUrl, $apiParams);
        } catch (Exception $e) {
            $result['code'] = $e->getCode();
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $result['data'] = $this->toFormat($response);
        return $result;
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

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if("@" != substr($v, 0, 1)) {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    protected function sign($params) {
        ksort($params);

        $stringToBeSigned = $this->appsecret;
        foreach ($params as $k => $v) {
            if("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        $stringToBeSigned .= $this->appsecret;

        return strtoupper(md5($stringToBeSigned));
    }

    protected function toFormat($data) {
        switch ($this->format) {
            case 'json':
                $data = json_decode($data);
                break;
            case 'xml':
                $data_tmp = @simplexml_load_string($data);
                $data = $data_tmp !== false ? $data_tmp : $data;
                break;
            default:
                break;
        }
        return $data;
    }
}
