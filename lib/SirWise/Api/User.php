<?php

namespace SirWise\Api;

use SirWise\Api\User\Emails;

/**
 * Searching users, getting user information.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class User extends AbstractApi
{
    /**
     * Request all users.
     *
     * @return array list of users found
     */
    public function all()
    {
        return $this->get('users');
    }

    /**
     * Get extended information about a user by its username.
     *
     * @param string $id user id of the user to show
     *
     * @return array user resource
     */
    public function show($id)
    {
        return $this->get('users/'.rawurlencode($id));
    }


    /**
     * @return Emails
     */
    public function emails()
    {
        return new Emails($this->client);
    }
}
