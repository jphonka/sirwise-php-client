<?php

namespace SuperWise\Oauth2\GrantType;

interface RefreshTokenGrantTypeInterface extends GrantTypeInterface
{
    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken);
    /**
     * @return bool
     */
    public function hasRefreshToken();
}