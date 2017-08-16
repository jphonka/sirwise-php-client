<?php

namespace SuperWise\Api;

use SuperWise\Api\User\Emails;

/**
 * Searching users, getting user information.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
class User extends AbstractApi
{
    static public function getExpands()
    {
        return 'addresses(items),emails(items),phones(items),defaultServiceLocation,currentLocation,defaultDelivery,invoiceRecipients(items(address)),options';
    }

    /**
     * Request all users.
     * @param array $parameters Query parameters
     * @return array list of users found
     */
    public function all($parameters = [])
    {
        return $this->get('users', $parameters);
    }

    /**
     * Get extended information about a user by its username.
     *
     * @param string $id user id of the user to show
     * @param array $parameters Query parameters
     *
     * @return array user resource
     */
    public function show($id, $parameters = [])
    {
        return $this->get('users/'.rawurlencode($id), $parameters);
    }

    public function edit($id, $parameters = [])
    {
        return $this->put('users/' . rawurldecode($id), $parameters);
    }

    public function create($body = [], $parameters = [])
    {
        return $this->post('users/', $body, $parameters);
    }

    public function remove($id, $parameters = [])
    {
        return $this->delete($id, $parameters);
    }

    /**
     * @return Emails
     */
    public function emails()
    {
        return new Emails($this->client);
    }
}
