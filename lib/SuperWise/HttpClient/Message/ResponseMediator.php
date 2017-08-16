<?php

namespace SuperWise\HttpClient\Message;

use Guzzle\Http\Message\Response;
use SuperWise\Exception\ApiLimitExceedException;

class ResponseMediator
{
    public static function getContent(Response $response, $assoc = true)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, $assoc);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $body;
        }

        return $content;
    }

    public static function getPagination(Response $response)
    {
        $header = (string) $response->getHeader('Link');

        if (empty($header)) {
            return null;
        }

        $pagination = array();
        foreach (explode(',', $header) as $link) {
            preg_match('/<(.*)>; rel="(.*)"/i', trim($link, ','), $match);

            if (3 === count($match)) {
                $pagination[$match[2]] = $match[1];
            }
        }

        return $pagination;
    }

    public static function getApiLimit(Response $response)
    {
        $remainingCalls = (string) $response->getHeader('X-Rate-Limit-Remaining');

        if (null !== $remainingCalls && 1 > $remainingCalls) {
            throw new ApiLimitExceedException($remainingCalls);
        }
        
        return $remainingCalls;
    }
}
