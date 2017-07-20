<?php

namespace SirWise\HttpClient;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use SirWise\Client;
use SirWise\Exception\ErrorException;
use SirWise\Exception\RuntimeException;
use SirWise\Exception\TokenExpiredException;
use SirWise\HttpClient\Listener\AuthListener;
use SirWise\HttpClient\Listener\ErrorListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use SirWise\Oauth2\GrantType\GrantTypeInterface;

/**
 * Performs requests on SirWise API. API documentation should be self-explanatory.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class HttpClient implements HttpClientInterface
{
    protected $options = array(
        'base_url'      => 'https://api.sirwise.com/',

        'user_agent'    => 'php-sirwise-api (http://sirwise.com/voltio/php-sirwise-api)',
        'timeout'       => 10,

        'api_limit'     => 5000,
        'api_version'   => 'v1',
        'realm'         => 'tenant',
        'tenant'        => 'mcare',

        'cache_dir'     => null,
        'content_type'  => 'application/json'
    );

    protected $headers = array();
    protected $authListener;
    protected $callback;

    private $lastResponse;
    private $lastRequest;

    /** @var Client $baseClient */
    private $baseClient = null;

    /**
     * @param array           $options
     * @param ClientInterface $client
     */
    public function __construct(array $options = array(), ClientInterface $client = null)
    {
        $this->options = array_merge($this->options, $options);

        $base_url = $this->options['base_url'] . '/' . $this->options['api_version'];

        $client = $client ?: new GuzzleClient($base_url, $this->options);
        $this->client  = $client;

        $this->addListener('request.error', array(new ErrorListener($this->options), 'onRequestError'));
        $this->clearHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->headers = array(
            'Accept' => $this->options['content_type'],
            'User-Agent' => sprintf('%s', $this->options['user_agent']),
        );
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->client->getEventDispatcher()->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->client->addSubscriber($subscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, null, 'GET', $headers, array('query' => $parameters));
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $body = null,  array $headers = array(), array $parameters = array())
    {
        return $this->request($path, $body, 'POST', $headers, array('query' => $parameters));
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, $body = null, array $headers = array(), array $parameters = array())
    {
        return $this->request($path, $body, 'PATCH', $headers, array('query' => $parameters));
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $body = null, array $headers = array())
    {
        return $this->request($path, $body, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $body, array $headers = array(), array $params = array())
    {
        return $this->request($path, $body, 'PUT', $headers, array('query' => $params));
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, $body = null, $httpMethod = 'GET', array $headers = array(), array $options = array())
    {
        $request = $this->createRequest($httpMethod, $path, $body, $headers, $options);

        try {
            $response = $this->client->send($request);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 'E4017') {
                throw new TokenExpiredException($e->getMessage(), $e->getCode(), $e);
            }
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->lastRequest  = $request;
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshTokenGrantType = null, Client $baseClient = null)
    {
        /*
        if ($this->authListener) {
            $this->client->getEventDispatcher()->removeListener('request.before_send', $this->authListener);
        }
        */

        if ($baseClient) {
            $this->baseClient = $baseClient;
        }

        $this->authListener = new AuthListener($grantType, $refreshTokenGrantType);

        $this->addListener('request.before_send', array(
            $this->authListener, 'onRequestBeforeSend'
        ));

        $this->addListener('request.error', array(
            $this->authListener, 'onRequestError'
        ), 1);
    }

    /**
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function createRequest($httpMethod, $path, $body = null, array $headers = array(), array $options = array())
    {
        return $this->client->createRequest(
            $httpMethod,
            $path,
            array_merge($this->headers, $headers),
            $body,
            $options
        );
    }
}
