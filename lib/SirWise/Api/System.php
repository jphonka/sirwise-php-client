<?php

namespace SirWise\Api;

class System extends AbstractApi
{

    public function clientToken($client_id, $client_secret)
    {
        return $this->post('token', array('client_id' => $client_id, 'client_secret' => $client_secret, 'grant_type' => 'client_credentials'));
    }

    public function userToken($client_id, $client_secret, $username, $password)
    {
        return $this->post('token', array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password'
        ));
    }

    public function confirmEmail($token)
    {
        return $this->post('confirm', array('token'=>$token));
    }

    public function forgotPassword($email)
    {
        return $this->post('forgot', array('email'=>$email));
    }

    public function resetPassword($token, $password)
    {
        return $this->post('reset', array(
            'token' => $token,
            'password' => $password
        ));
    }

    public function changePassword($currentPassword, $password){
        return $this->post('reset', array(
            'currentPassword' => $currentPassword,
            'password' => $password
        ));

    }

    public function status()
    {
        return $this->get('status');
    }

}