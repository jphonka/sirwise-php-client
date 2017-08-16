<?php

namespace SuperWise\Api;

class Order extends AbstractApi
{
    public function all($params = [])
    {
        return $this->get('orders', $params);
    }
    
    public function show($id, $params = [])
    {
        return $this->get('orders/' . rawurldecode($id), $params);
    }

    public function create($body = [], $params = [])
    {
        return $this->post('orders/', $body, $params);
    }
    
    public function edit($id, $body = [], $params = [])
    {
        return $this->put('orders/' . rawurldecode($id), $body, $params);
    }

    public function remove($id, $params = [])
    {
        return $this->delete('orders/' . rawurldecode($id), $params);
    }
}