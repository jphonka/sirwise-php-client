<?php

namespace SirWise\HttpClient\Listener;

use Guzzle\Common\Event;
use SirWise\Client;
use SirWise\Exception\RuntimeException;
use SirWise\Oauth2\AccessToken;
use SirWise\Oauth2\GrantType\GrantTypeInterface;
use SirWise\Oauth2\GrantType\RefreshTokenGrantTypeInterface;


class AuthListener
{
    /** @var AccessToken|null */
    protected $accessToken;
    /** @var AccessToken|null */
    protected $refreshToken;

    /** @var GrantTypeInterface */
    protected $grantType;
    /** @var RefreshTokenGrantTypeInterface */
    protected $refreshTokenGrantType;

    public function __construct(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshTokenGrantType = null)
    {
        $this->grantType = $grantType;
        $this->refreshTokenGrantType = $refreshTokenGrantType;
    }

    public function onRequestError(Event $event)
    {
        if ($event['response']->getStatusCode() == 401) {
            if ($event['request']->getHeader('X-Retry-Count')) {
                // We already retried once, give up.
                return;
            }
            // Acquire a new access token, and retry the request.
            $newAccessToken = $this->acquireAccessToken();
            if ($newAccessToken) {
                $newRequest = clone $event['request'];
                $newRequest->setHeader('Authorization', 'Bearer ' . $newAccessToken->getToken());
                $newRequest->setHeader('X-Retry-Count', '1');
                $event['response'] = $newRequest->send();
                $event->stopPropagation();
            }
        }
    }

    /**
     * Get a new access token.
     *
     * @return AccessToken|null
     */
    protected function acquireAccessToken()
    {
        $accessToken = null;
        if ($this->refreshTokenGrantType) {
            // Get an access token using the stored refresh token.
            if ($this->refreshToken) {
                $this->refreshTokenGrantType->setRefreshToken($this->refreshToken->getToken());
            }
            if ($this->refreshTokenGrantType->hasRefreshToken()) {
                $accessToken = $this->refreshTokenGrantType->getToken();
            }
        }
        if (!$accessToken && $this->grantType) {
            // Get a new access token.
            $accessToken = $this->grantType->getToken();

        }
        return $accessToken ?: null;
    }


    /**
     * Get the access token.
     *
     * @return AccessToken|null Oauth2 access token
     */
    public function getAccessToken()
    {
        if ($this->accessToken && $this->accessToken->isExpired()) {
            // The access token has expired.
            $this->accessToken = null;
        }
        if (null === $this->accessToken) {
            // Try to acquire a new access token from the server.
            $this->accessToken = $this->acquireAccessToken();
            if ($this->accessToken) {
                $this->refreshToken = $this->accessToken->getRefreshToken();
            }
        }
        return $this->accessToken;
    }
    /**
     * Get the refresh token.
     *
     * @return AccessToken|null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
    /**
     * Set the access token.
     *
     * @param AccessToken|string $accessToken
     * @param string             $type
     * @param int                $expires
     */
    public function setAccessToken($accessToken, $type = null, $expires = null)
    {
        if (is_string($accessToken)) {
            $accessToken = new AccessToken($accessToken, $type, ['expires' => $expires]);
        } elseif (!$accessToken instanceof AccessToken) {
            throw new \InvalidArgumentException('Invalid access token');
        }
        $this->accessToken = $accessToken;
        $this->refreshToken = $accessToken->getRefreshToken();
    }
    /**
     * Set the refresh token.
     *
     * @param AccessToken|string $refreshToken The refresh token
     */
    public function setRefreshToken($refreshToken)
    {
        if (is_string($refreshToken)) {
            $refreshToken = new AccessToken($refreshToken, 'refresh_token');
        } elseif (!$refreshToken instanceof AccessToken) {
            throw new \InvalidArgumentException('Invalid refresh token');
        }
        $this->refreshToken = $refreshToken;
    }

    /**
     * Request before-send event handler.
     *
     * Adds the Authorization header if an access token was found.
     *
     * @param Event $event Event received
     */
    public function onRequestBeforeSend(Event $event)
    {
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $event['request']->setHeader('Authorization', 'Bearer ' . $accessToken->getToken());
        }
    }
}
