<?php

namespace Stuki\OAuth2\Client\Provider;

use Stuki\OAuth2\Client\Entity\User;
use Stuki\OAuth2\Client\Provider\AbstractProvider;

class Box extends AbstractProvider
{
    public $responseType = 'json';
    protected $requireState = true;

    public function urlAuthorize()
    {
        return 'https://www.box.com/api/oauth2/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://www.box.com/api/oauth2/token';
    }

    public function urlUserDetails(\Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        $this->headers = array(
            'Authorization' => 'Bearer ' . $token
        );

        return 'https://api.box.com/2.0/users/me';
    }

    public function userDetails($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        $user = new User;

        $name = (isset($response->name)) ? $response->name : null;
        $email = (isset($response->email)) ? $response->email : null;

        $user->exchangeArray(array(
            'uid' => $response->id,
            'nickname' => $response->login,
            'name' => $name,
            'email' => $email,
            'imageUrl' => $response->avatar_url,
        ));

        return $user;
    }

    public function userUid($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->id;
    }

    public function userEmail($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return isset($response->email) && $response->email ? $response->email : null;
    }

    public function userScreenName($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->name;
    }
}
