<?php

namespace SirWise;

use SirWise\Api\ApiInterface;
use SirWise\Api\AbstractApi;
use SirWise\Exception\InvalidArgumentException;
use SirWise\Exception\BadMethodCallException;
use SirWise\HttpClient\HttpClient;
use SirWise\HttpClient\HttpClientInterface;
use SirWise\Oauth2\GrantType\GrantTypeInterface;

/**
 * Simple PHP SirWise client.
 *
 * @method Api\CurrentUser me()
 * @method Api\User user()
 * @method Api\User users()
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 *
 * Website: http://github.com/voltio/sirwise-php-client
 *
 */
class Client
{
    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Not indicates the new login, but allows
     * usage of unauthenticated rate limited requests for given client_id + client_secret.
     */
    const AUTH_URL_CLIENT_ID = 'url_client_id';

    /**
     * Constant for authentication method. Indicates the new favored login method
     * with username and password via HTTP Authentication.
     */
    const AUTH_HTTP_PASSWORD = 'http_password';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * @var array
     */
    private $options = array(
        'base_url'    => 'https://api.sirwise.com/',

        'user_agent'  => 'sirwise-php-client (http://github.com/voltio/sirwise-php-client)',
        'timeout'     => 10,

        'api_limit'     => 5000,
        'api_version'   => 'v1',

        'cache_dir'     => null,
        'content_type'  => 'application/json'
    );

    /**
     * The Guzzle instance used to communicate with SirWise.
     *
     * @var HttpClient
     */
    private $httpClient;

    public $tenant;

    /**
     * Instantiate a new SirWise client.
     *
     * @param null|HttpClientInterface $httpClient SirWise http client
     */
    public function __construct(HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractApi
     */
    public function api($name)
    {
        switch ($name) {
            case 'me':
            case 'self':
                $api = new Api\CurrentUser($this);
                break;
            case 'users':
                $api = new Api\User($this);
                break;
            case '':
            case 'system':
                $api = new Api\System($this);
                break;
            case 'tenant':
                $api = new Api\Tenant($this);
                break;
            case 'order':
                $api = new Api\Order($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests.
     * @param GrantTypeInterface $grantType
     * @param GrantTypeInterface $refreshTokenGrantType
     */
    public function authenticate(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshTokenGrantType = null)
    {
        $this->getHttpClient()->authenticate($grantType, $refreshTokenGrantType, $this);
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient($this->options);
        }

        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->getHttpClient()->clearHeaders();
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->getHttpClient()->setHeaders($headers);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setTenant($name)
    {
        if (is_string($name) || is_null($name)) {
            $this->getHttpClient()->setHeaders(['Tenant' => $name]);
            $this->tenant = $name;
            return $this;
        }
        throw new InvalidArgumentException(sprintf('Invalid tenant name: "%s"', $name));
    }

    /**
     * @param string $realm
     * @return $this
     */
    public function setRealm($realm)
    {
        if ($realm) {
            if (is_string($realm)) {
                $this->getHttpClient()->setHeaders(['Realm' => $realm]);
                return $this;
            }
            throw new InvalidArgumentException(sprintf('Invalid realm: "%s"', $realm));
        }
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        $this->options[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }
}
