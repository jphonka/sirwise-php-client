<?php

namespace SuperWise\HttpClient\Listener;

use SuperWise\Exception\TokenExpiredException;
use SuperWise\Exception\TwoFactorAuthenticationRequiredException;
use SuperWise\HttpClient\Message\ResponseMediator;
use Guzzle\Common\Event;
use SuperWise\Exception\ApiLimitExceedException;
use SuperWise\Exception\ErrorException;
use SuperWise\Exception\RuntimeException;
use SuperWise\Exception\ValidationFailedException;

/**
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class ErrorListener
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function onRequestError(Event $event)
    {
        /** @var $request \Guzzle\Http\Message\Request */
        $request = $event['request'];
        $response = $request->getResponse();

        if ($response->isClientError() || $response->isServerError()) {

            $remaining = (string) $response->getHeader('X-Rate-Limit-Remaining');
            $limit = $response->getHeader('X-Rate-Limit-Limit');
            //die($response->getBody());

            if (null !== $remaining && 1 > $remaining && 'rate_limit' !== substr($request->getResource(), 1, 10)) {
                //throw new ApiLimitExceedException($limit);
            }

            $content = ResponseMediator::getContent($response);
            throw new RuntimeException($content, $response->getStatusCode());
        };
    }
}
