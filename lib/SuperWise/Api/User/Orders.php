<?php

namespace SuperWise\Api\User;

use SuperWise\Api\AbstractApi;

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