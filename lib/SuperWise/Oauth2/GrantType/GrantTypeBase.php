<?php

namespace SuperWise\Oauth2\GrantType;

use SuperWise\Oauth2\AccessToken;
use Guzzle\Http\ClientInterface;
use Guzzle\Common\Collection;

abstract class GrantTypeBase implements GrantTypeInterface
{
    /** @var ClientInterface The token endpoint client */
    protected $client;

    /** @var Collection Configuration settings */
    protected $config;

    /** @var string */
    protected $grantType = '';

    /**
     * @param ClientInterface $client
     * @param array           $config
     */
    public function __construct(ClientInterface $client, array $config = [])
    {
        $this->client = $client;
        $this->config = Collection::fromConfig($config, $this->getDefaults(), $this->getRequired());
    }

    /**
     * Get default configuration items.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [
            'client_secret' => '',
            'scope' => '',
            'token_url' => 'token',
            'auth_location' => 'headers',
            'tenant' => 'mcare'
        ];
    }

    /**
     * Get required configuration items.
     *
     * @return string[]
     */
    protected function getRequired()
    {
        return ['client_id'];
    }

    /**
     * Get additional options, if any.
     *
     * @return array|null
     */
    protected function getAdditionalOptions()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        $config = $this->config->toArray();

        $body = $config;
        $body['grantType'] = $this->grantType;
        unset($body['token_url'], $body['auth_location'], $body['tenant']);


        $requestOptions = [];

        if ($config['auth_location'] !== 'body') {
            $requestOptions['auth'] = [$config['client_id'], $config['client_secret']];
            unset($body['client_secret']);
        }

        $requestOptions['body'] = $body;

        if ($additionalOptions = $this->getAdditionalOptions()) {
            $requestOptions = array_merge_recursive($requestOptions, $additionalOptions);
        }

        $parameters = $requestOptions['body'];
        $parameters["clientId"] = $config["client_id"];

        $request = $this->client->post(
            $config['token_url'],
            $requestOptions,
            (count($parameters) === 0) ? null : json_encode($parameters, empty($parameters) ? JSON_FORCE_OBJECT : 0)
        );
        if (isset($config['tenant']))  {
            $request->setHeader('Tenant', $config['tenant']);
        }

        $request->setAuth($this->config['client_id'], $this->config['client_secret']);
        $request->setHeader('accept', 'application/json');

        print_r([
           $request->getUrl(),
           $request->getPort(),
           $request->getHost(),
           $request->getPath(),
           $request->getMethod()
        ]);

        $response = $request->send();

        $data = $response->json();

        $token = new AccessToken($data['accessToken'], $data['tokenType'], $data);
        return $token;
    }
}