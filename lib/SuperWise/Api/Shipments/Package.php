<?php

namespace SuperWise\Api\Shipments;

class Packages extends \SuperWise\Api\AbstractApi
{
    public function all($shipmentId, $params = [])
    {
        return $this->get('shipments/' . rawurldecode($shipmentId) . '/packages', array_merge(['expand' => 'items', $params]));
    }
    
    
}