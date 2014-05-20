<?php

namespace Stuki\OAuth2\Client\Provider;

use Stuki\OAuth2\Client\Entity\User;

class Eventbrite extends AbstractProvider
{
    protected $requireState = false;

    public function __construct($options)
    {
        parent::__construct($options);
        $this->headers = array(
            'Authorization' => 'Bearer'
        );
    }

    public function urlAuthorize()
    {
        return 'https://www.eventbrite.com/oauth/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://www.eventbrite.com/oauth/token';
    }

    public function urlUserDetails(\Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return 'https://www.eventbrite.com/json/user_get?access_token='.$token;
    }

    public function userDetails($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        $user = new User;
        $user->exchangeArray(array(
            'uid' => $response->user->user_id,
            'email' => $response->user->email,
        ));

        return $user;
    }

    public function userUid($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->user->user_id;
    }

    public function userEmail($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return isset($response->user->email) && $response->user->email ? $response->user->email : null;
    }

    public function userScreenName($response, \Stuki\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->user->user_id;
    }
}
