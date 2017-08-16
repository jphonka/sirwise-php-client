<?php

namespace SuperWise\Api\Orders;

use SuperWise\Api\AbstractApi;


class Notes extends \SuperWise\Api\AbstractApi
{
    public function all($orderId, $params = [])
    {
        return $this->get('orders/' . rawurldecode($orderId) . '/notes', $params);
    }
    
    public function create($orderId, $body = [], $params = [])
    {
        return $this->post('orders/' . rawurldecode($orderId), '/notes', $body, $params);
    }
}