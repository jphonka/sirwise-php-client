<?php

namespace SuperWise\Api\Orders;

use SuperWise\Api\AbstractApi;

class Lines extends AbstractApi
{
    public function all($orderId, $params = [])
    {
        return $this->get('orders/' . rawurldecode($orderId) . '/lines', array_merge(['expand' => 'items'], $params));
    }

    public function create($orderId, $productId, $params = [])
    {
        return $this->post('orders/' . rawurldecode($orderId) . '/lines', array_merge(['product' => $productId], $params));
    }
}