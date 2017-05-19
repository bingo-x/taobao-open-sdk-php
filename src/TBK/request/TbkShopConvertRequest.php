<?php namespace TBK\request;

use TBK\request\PRequest;

/**
 * TOP API: taobao.tbk.item.convert
 *
 * @author auto create
 * @since 1.0, 2015.07.02
 */
class TbkShopConvertRequest extends PRequest {
    /**
     * 广告位ID，区分效果位置
     **/
    private $adzoneId;

    /**
     * 商品ID串，用','分割，从taobao.tbk.item.get接口获取num_iid字段，最大40个
     **/
    private $numIids;

    /**
     * 链接形式：1：PC，2：无线，默认：１
     **/
    private $platform;

    /**
     * 自定义输入串，英文和数字组成，长度不能大于12个字符，区分不同的推广渠道
     **/
    private $unid;

    protected $apiMethodName = "taobao.tbk.item.convert";


    public function setAdzoneId($adzoneId) {
        $this->adzoneId = $adzoneId;
        $this->apiParas["adzone_id"] = $adzoneId;
    }

    public function getAdzoneId() {
        return $this->adzoneId;
    }

    public function setNumIids($numIids) {
        $this->numIids = $numIids;
        $this->apiParas["num_iids"] = $numIids;
    }

    public function getNumIids() {
        return $this->numIids;
    }

    public function setPlatform($platform) {
        $this->platform = $platform;
        $this->apiParas["platform"] = $platform;
    }

    public function getPlatform() {
        return $this->platform;
    }

    public function setUnid($unid) {
        $this->unid = $unid;
        $this->apiParas["unid"] = $unid;
    }

    public function getUnid() {
        return $this->unid;
    }
}

