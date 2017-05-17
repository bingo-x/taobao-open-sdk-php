<?php namespace TBK\request;

interface IRequest {
    public function setFields($fields);
    public function getFields();
    public function getApiMethodName();
    public function getApiParas();
    public function putOtherTextParam($key, $value);
}