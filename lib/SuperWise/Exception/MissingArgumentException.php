<?php

namespace SuperWise\Exception;

/**
 * MissingArgumentException.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class MissingArgumentException extends ErrorException
{
    public function __construct($required, $code = 0, $previous = null)
    {
        if (is_string($required)) {
            $required = array($required);
        }

        parent::__construct(sprintf('One or more of required ("%s") parameters is missing!', implode('", "', $required)), $code, $previous);
    }
}
