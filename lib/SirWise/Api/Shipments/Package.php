<?php

namespace SirWise\Api\Shipments;

class Packages extends \SirWise\Api\AbstractApi
{
    public function all($shipmentId, $params = [])
    {
        return $this->get('shipments/' . rawurldecode($shipmentId) . '/packages', array_merge(['expand' => 'items', $params]));
    }
    
    
}