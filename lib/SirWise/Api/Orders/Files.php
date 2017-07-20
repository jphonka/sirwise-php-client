<?php

namespace SirWise\Api\Orders;

use SirWise\Api\AbstractApi;

class Files extends AbstractApi
{
    public function all($orderId, $params = [])
    {
        return $this->get('orders/' . rawurldecode($orderId) . '/files', array_merge(['expand' => 'items'], $params));
    }

    public function create($orderId, $filename, $fileData, $params = [])
    {
        $file = $this->post('files', $fileData,
            [
                'name' => $filename,
            ]
        );
        if ($file) {
            $this->post('/orders/' . rawurldecode($orderId) . '/files', [$file->id], $params);
        }
        return $file;
    }
}
