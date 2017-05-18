<?php namespace TAE\request;

use TAE\request\PRequest;

/**
 * TOP API: taobao.item.get request
 *
 * @author auto create
 * @since 1.0, 2015.06.18
 */
class ItemDetailGetRequest extends PRequest {
    private $item_id;

    protected $apiMethodName = "taobao.item.detail.get";

    public function setItemId($itemId) {
        $this->item_id = $itemId;
        $this->apiParas["item_id"] = $itemId;
    }

    public function getItemId() {
        return $this->item_id;
    }
}
