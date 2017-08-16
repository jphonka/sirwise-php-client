<?php

namespace SuperWise\Api;


use Hubi\Api\Users;
use Hubi\SuperWise;


/**
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class CurrentUser extends AbstractApi
{
    public function show($options = [])
    {
        return $this->get('self', array_merge($options,
                [
                    'expand' => User::getExpands(),
                ]
            )
        );
    }
}
