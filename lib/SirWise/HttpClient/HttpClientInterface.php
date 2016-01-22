<?php

namespace SirWise\HttpClient;

use SirWise\Exception\InvalidArgumentException;
use Guzzle\Http\Message\Response;
use SirWise\Oauth2\GrantType\GrantTypeInterface;

/**
 * Performs requests on SirWise API. API documentation should be self-explanatory.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
interface HttpClientInterface
{
    /**
     * Send a GET request.
     *
     * @param string $path       Request path
     * @param array  $parameters GET Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function get($path, array $parameters = array(), array $headers = array());

    /**
     * Send a POST request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function post($path, $body = null, array $headers = array());

    /**
     * Send a PATCH request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @internal param array $parameters Request body
     *
     * @return Response
     */
    public function patch($path, $body = null, array $headers = array());

    /**
     * Send a PUT request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function put($path, $body, array $headers = array());

    /**
     * Send a DELETE request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function delete($path, $body = null, array $headers = array());

    /**
     * Send a request to the server, receive a response,
     * decode the response and returns an associative array.
     *
     * @param string $path       Request path
     * @param mixed  $body       Request body
     * @param string $httpMethod HTTP method to use
     * @param array  $headers    Request headers
     *
     * @return Response
     */
    public function request($path, $body, $httpMethod = 'GET', array $headers = array());

    /**
     * Change an option value.
     *
     * @param string $name  The option name
     * @param mixed  $value The value
     *
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value);

    /**
     * Set HTTP headers.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * Authenticate a user for all next requests.
     * @param GrantTypeInterface $grantType
     * @param GrantTypeInterface $refreshTokenGrantType
     * @return
     */
    public function authenticate(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshTokenGrantType = null);
}
