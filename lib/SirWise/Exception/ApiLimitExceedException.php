<?php

namespace SirWise\Exception;

/**
 * ApiLimitExceedException.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class ApiLimitExceedException extends RuntimeException
{
    public function __construct($limit = 5000, $code = 0, $previous = null)
    {
        parent::__construct('You have reached SirWise daily limit! Actual limit is: '. $limit, $code, $previous);
    }
}
