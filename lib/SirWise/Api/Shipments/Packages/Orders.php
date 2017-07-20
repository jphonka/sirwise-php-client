<?php

namespace SirWise\Api\Shipments\Packages;

use SirWise\Api\AbstractApi;

class Orders extends AbstractApi
{
    public function all($shipmentId, $packageId, $params = [])
    {
        return $this->get('shipments/' . rawurldecode($shipmentId) . '/packages/' . rawurldecode($packageId) . '/orders',
            array_merge(['expand' => 'items'], $params)
        );
    }
}