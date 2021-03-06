<?php

namespace SuperWise\Api;

use SuperWise\Client;
use SuperWise\HttpClient\Message\ResponseMediator;

/**
 * Abstract class for Api classes.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * The client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Number of items per page (SuperWise pagination).
     *
     * @var null|int
     */
    protected $perPage;

    protected $query;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function configure()
    {
    }

    /**
     * @return null|int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param null|int $perPage
     * @return mixed
     */
    public function setPerPage($perPage)
    {
        $this->perPage = (null === $perPage ? $perPage : (int)$perPage);

        return $this;
    }

    public function setQuery($query)
    {

    }

    /**
     * Send a GET request with query parameters.
     *
     * @param string $path Request path.
     * @param array $parameters GET parameters.
     * @param array $requestHeaders Request Headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function get($path, array $parameters = array(), $requestHeaders = array())
    {
        if (null !== $this->perPage && !isset($parameters['per_page'])) {
            $parameters['per_page'] = $this->perPage;
        }
        if (array_key_exists('ref', $parameters) && is_null($parameters['ref'])) {
            unset($parameters['ref']);
        }
        $response = $this->client->getHttpClient()->get($path, $parameters, $requestHeaders);

        return ResponseMediator::getContent($response, false);
    }

    /**
     * Send a HEAD request with query parameters.
     *
     * @param string $path Request path.
     * @param array $parameters HEAD parameters.
     * @param array $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function head($path, array $parameters = array(), $requestHeaders = array())
    {
        if (array_key_exists('ref', $parameters) && is_null($parameters['ref'])) {
            unset($parameters['ref']);
        }

        $response = $this->client->getHttpClient()->request($path, null, 'HEAD', $requestHeaders, array(
            'query' => $parameters
        ));

        return $response;
    }

    /**
     * Send a POST request with JSON-encoded parameters.
     *
     * @param string $path Request path.
     * @param array $body POST body to be JSON encoded.
     * @param array $params Query parameters
     * @param array $requestHeaders Request headers.
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function post($path, array $body = array(), array $params = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->post($path, $this->createJsonBody($body), $params, $requestHeaders);
        return ResponseMediator::getContent($response, false);
    }

    /**
     * Send a POST request with raw data.
     *
     * @param string $path Request path.
     * @param string $body Request body.
     * @param array $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function postRaw($path, $body, $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->post(
            $path,
            $body,
            $requestHeaders
        );

        return ResponseMediator::getContent($response, false);
    }

    /**
     * Send a PATCH request with JSON-encoded parameters.
     *
     * @param string $path Request path.
     * @param array $body POST parameters to be JSON encoded.
     * @param array $requestHeaders Request headers.
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function patch($path,  array $body = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->patch(
            $path,
            $this->createJsonBody($body),
            $requestHeaders
        );

        return ResponseMediator::getContent($response, false);
    }

    /**
     * Send a PUT request with JSON-encoded parameters.
     *
     * @param string $path Request path.
     * @param array $body POST body to be JSON encoded.
     * @param array $requestHeaders Request headers.
     * @param array $parameters Query parameters
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function put($path, array $body = array(), array $parameters = [], $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->put(
            $path,
            $this->createJsonBody($body),
            $requestHeaders,
            $parameters
        );

        return ResponseMediator::getContent($response, false);
    }

    /**
     * Send a DELETE request with JSON-encoded parameters.
     *
     * @param string $path Request path.
     * @param array $parameters POST parameters to be JSON encoded.
     * @param array $requestHeaders Request headers.
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->delete(
            $path,
            $this->createJsonBody($parameters),
            $requestHeaders
        );

        return ResponseMediator::getContent($response, false);
    }

    /**
     * Create a JSON encoded version of an array of parameters.
     *
     * @param array $parameters Request parameters
     *
     * @return null|string
     */
    public function createJsonBody(array $parameters)
    {
        return (count($parameters) === 0) ? null : json_encode($parameters, empty($parameters) ? JSON_FORCE_OBJECT : 0);
    }
}
