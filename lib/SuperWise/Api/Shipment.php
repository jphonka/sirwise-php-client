<?php

namespace SuperWise\Api;

class Shipment extends \SuperWise\Api\AbstractApi
{
    public function all($params = [])
    {
        return $this->get("shipments", $params);
    }

    public function show($id, $params)
    {
        return $this->get("shipments/" . rawurldecode($id), $params);
    }

    public function edit($id, $body, $params)
    {
        return $this->put("shipments/" . rawurldecode($id), $body, $params);
    }

    public function ship($id, $params)
    {
        return $this->put("shipments/" . rawurldecode($id) . "/ship", [], $params);
    }
}