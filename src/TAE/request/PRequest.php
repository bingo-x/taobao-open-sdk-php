<?php namespace TAE\request;

use TAE\request\IRequest;

class PRequest implements IRequest {
    protected $apiMethodName;
    protected $fields;
    protected $apiParas = [];
    public function setFields($fields) {
        $this->fields = $fields;
        $this->apiParas["fields"] = $fields;
    }
    public function getFields() {
        return $this->fields;
    }
    public function getApiMethodName() {
        return $this->apiMethodName;
    }
    public function getApiParas() {
        return $this->apiParas;
    }
    public function putOtherTextParam($key, $value) {
        $this->apiParas[$key] = $value;
        $this->$key = $value;
    }
}