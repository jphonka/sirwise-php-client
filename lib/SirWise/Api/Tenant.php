<?php

namespace SirWise\Api;

class Tenant extends AbstractApi
{
    /**
     *
     * Request all tenants.
     *
     * @return array list of tenants found
     */
    public function all()
    {
        return $this->get('tenants');
    }

    /**
     * Get extended information about a tenant by its id.
     *
     * @param string $id tenant id of the tenant to show
     *
     * @return array tenant resource
     */
    public function show($id)
    {
        return $this->get('tenants/'.rawurlencode($id));
    }

    public function findFirstByName($name)
    {
        $result = $this->get('tenants', array('q' => "name:$name", 'expand' => 'items'));
        if ($result->totalItems) {
            return current($result->items);
        }
        return false;
    }

}