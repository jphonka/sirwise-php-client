<?php

namespace SirWise\Api;

/**
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class CurrentUser extends AbstractApi
{
    public function show()
    {
        return $this->get('self');
    }
}
