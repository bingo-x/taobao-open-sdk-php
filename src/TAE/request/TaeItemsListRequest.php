<?php namespace TAE\request;

use TAE\request\PRequest;

/**
 * TOP API: taobao.tae.items.list request
 *
 * @author auto create
 * @since 1.0, 2015.03.13
 */
class TaeItemsListRequest extends PRequest {
    /**
     * 商品ID，英文逗号(,)分隔，最多50个。优先级低于open_iid，open_iids存在的话，则忽略本参数
     **/
    private $numIids;

    /**
     * 商品混淆ID，英文逗号(,)分隔，最多50个。优先级高于open_iid，本参数存在的话，则忽略num_iids
     **/
    private $openIids;

    protected $apiMethodName = "taobao.tae.items.list";

    public function setNumIids($numIids) {
        $this->numIids = $numIids;
        $this->apiParas["num_iids"] = $numIids;
    }

    public function getNumIids() {
        return $this->numIids;
    }

    public function setOpenIids($openIids) {
        $this->openIids = $openIids;
        $this->apiParas["open_iids"] = $openIids;
    }

    public function getOpenIids() {
        return $this->openIids;
    }
}
