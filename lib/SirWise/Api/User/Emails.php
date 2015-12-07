<?php

namespace SirWise\Api\User;

use SirWise\Api\AbstractApi;
use SirWise\Exception\InvalidArgumentException;

/**
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class Emails extends AbstractApi
{
    /**
     * List emails for the authenticated user.
     *
     * @param $user
     * @return array
     */
    public function all($user)
    {
        return $this->get('users/'.rawurlencode($user).'/emails');
    }

    /**
     * Adds one or more email for the authenticated user.
     *
     * @param $user
     * @param string|array $emails
     * @return array
     */
    public function add($user, $emails)
    {
        if (is_string($emails)) {
            $emails = array($emails);
        } elseif (0 === count($emails)) {
            throw new InvalidArgumentException();
        }

        return $this->post('users/'.rawurlencode($user).'/emails', $emails);
    }

    /**
     * Removes one or more email for the authenticated user.
     *
     * @param $user
     * @param $email
     * @return array
     * @internal param array|string $emails
     *
     */
    public function remove($user, $email)
    {
        return $this->delete('users/'.rawurlencode($user).'/emails/'.rawurlencode($email));
    }
}
