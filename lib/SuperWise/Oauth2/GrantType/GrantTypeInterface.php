<?php

namespace SuperWise\Oauth2\GrantType;

use SuperWise\Oauth2\AccessToken;

interface GrantTypeInterface
{
    /**
     * Get the token data returned by the OAuth2 server.
     *
     * @return AccessToken
     */
    public function getToken();
}