<?php

namespace SirWise\Api\Orders;

use SirWise\Api\AbstractApi;


class Notes extends \SirWise\Api\AbstractApi
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