<?php

namespace SirWise\Api;

class Devices extends AbstractApi
{
    public function all($params = [])
    {
        return $this->get('devices', array_merge(['expand' => 'items'], $params));
    }

    public function show($id, $params = [])
    {
        return $this->get('device/' . rawurldecode($id), $params);
    }

    public function update($id, $data, $params = [])
    {
        $this->patch('devices/' . rawurldecode($id), $data, $params);
    }

    public function create($data, $params)
    {
        return $this->post('devices', $data, $params);
    }

    public function find($serialNumber, $imeiNumber = null, $external = true, $params = [])
    {
        if (!empty($serialNumber)) {
            $params['q'] = 'serialNumber:' . $serialNumber;
        } else {
            $params['q'] = 'imeiNumber:' . $imeiNumber;
        }
        if ($external) {
            $params['source'] = 'provider';
        }
        return $this->get('devices', $params);
    }

    public function findBySerial($serial, $external = true, $params = [])
    {
        return $this->find($serial, null, $external, $params);
    }

    public function findByImei($imei, $external = true, $params = [])
    {
        return $this->find(null, $imei, $external, $params);
    }

}
