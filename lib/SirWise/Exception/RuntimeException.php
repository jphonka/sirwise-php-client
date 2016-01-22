<?php

namespace SirWise\Exception;

/**
 * RuntimeException.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface
{

    protected $status;
    protected $developerMessage;
    protected $moreInfo;


    /**
     * @param string|array $error
     * @param int $code
     */
    public function __construct($error, $code = null)
    {
        if (!is_array($error)) {
            $error = ['message' => $error];
        }

        foreach ($error as $field => $value) {
            // TODO: Support additional custom data like validation errors have
            if (in_array($field, ['code', 'message', 'status', 'developerMessage', 'moreInfo'])) {
                $this->$field = $value;
            }
        }

        if (is_int($code)) {
            $this->status = $code;
        }

    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getDeveloperMessage()
    {
        return $this->developerMessage;
    }

    /**
     * @return mixed
     */
    public function getMoreInfo()
    {
        return $this->moreInfo;
    }


}
