<?php

namespace SirWise\HttpClient\Listener;

use SirWise\Exception\TokenExpiredException;
use SirWise\Exception\TwoFactorAuthenticationRequiredException;
use SirWise\HttpClient\Message\ResponseMediator;
use Guzzle\Common\Event;
use SirWise\Exception\ApiLimitExceedException;
use SirWise\Exception\ErrorException;
use SirWise\Exception\RuntimeException;
use SirWise\Exception\ValidationFailedException;

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

            if (null != $remaining && 1 > $remaining && 'rate_limit' !== substr($request->getResource(), 1, 10)) {
                throw new ApiLimitExceedException($limit);
            }

            $content = ResponseMediator::getContent($response);
            throw new RuntimeException(isset($content['message']) ? $content['message'] : $content, $response->getStatusCode());
        };
    }
}
