<?php

namespace SirWise\Api\User;

use SirWise\Api\AbstractApi;

class Orders extends AbstractApi
{
    public function all($user, $params = [])
    {
        return $this->get('orders/',
            array_merge([
                'expand' => 'items',
                'handledBy' => $user->id
            ], $params)
        );
    }
}