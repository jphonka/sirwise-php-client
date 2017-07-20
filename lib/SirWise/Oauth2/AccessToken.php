<?php

namespace SirWise\Oauth2;

class AccessToken
{
    /** @var string */
    protected $token;

    /** @var \DateTime|null */
    protected $expires;

    /** @var string */
    protected $type;

    /** @var AccessToken|null */
    protected $refreshToken;

    /** @var array */
    protected $data;

    /**
     * @param string $token
     * @param string $type The token type (from OAuth2 key 'token_type').
     * @param array  $data Other token data.
     */
    public function __construct($token, $type, array $data = [])
    {
        $this->token = $token;
        $this->type = $type;
        $this->data = $data;
        if (isset($data['expires'])) {
            $this->expires = new \DateTime();
            $this->expires->setTimestamp($data['expires']);
        } elseif (isset($data['expiresIn'])) {
            $this->expires = new \DateTime();
            $this->expires->add(new \DateInterval(sprintf('PT%sS', $data['expiresIn'])));
        }
        if (isset($data['refreshToken'])) {
            $this->refreshToken = new self($data['refreshToken'], 'refreshToken');
        }
    }

    /** @return bool */
    public function isExpired()
    {
        return $this->expires !== null && $this->expires->getTimestamp() < time();
    }

    /** @return \DateTime|null */
    public function getExpires()
    {
        return $this->expires;
    }

    /** @return array */
    public function getData()
    {
        return $this->data;
    }

    /** @return string */
    public function getScope()
    {
        return isset($this->data['scope']) ? $this->data['scope'] : '';
    }

    /** @return string */
    public function getToken()
    {
        return $this->token;
    }

    /** @return AccessToken|null */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /** @return string */
    public function getType()
    {
        return isset($this->data['tokenType']) ? $this->data['tokenType'] : '';
    }

}