<?php

namespace StukiOrg\OAuth2\Client\Test\Provider;

use \Mockery as m;

class FacebookTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    protected function setUp()
    {
        $this->provider = new \Stuki\OAuth2\Client\Provider\Facebook(array(
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ));
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl(array('state' => 'mock_state'));
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
    }

    public function testUrlAccessToken()
    {
        $url = $this->provider->urlAccessToken();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/access_token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getBody')->times(1)->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token&uid=1');

        $client = m::mock('Guzzle\Service\Client');
        $client->shouldReceive('setBaseUrl')->times(1);
        $client->shouldReceive('post->send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', array('code' => 'mock_authorization_code'));

#    print_r($token);die();

        $this->assertEquals('mock_access_token', $token->accessToken);
        $this->assertEquals(3600, $token->expires_in);
        $this->assertEquals('mock_refresh_token', $token->refreshToken);
        $this->assertEquals('1', $token->uid);
    }

    public function testScopes()
    {
        $this->assertEquals(array('offline_access', 'email', 'read_stream'), $this->provider->getScopes());
    }

    public function testUserData()
    {
        $postResponse = m::mock('Guzzle\Http\Message\Response');
        $postResponse->shouldReceive('getBody')->times(1)->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token&uid=1');

        $getResponse = m::mock('Guzzle\Http\Message\Response');
        $getResponse->shouldReceive('getBody')->andReturn('{"id": 12345, "name": "mock_name", "username": "mock_username", "first_name": "mock_first_name", "last_name": "mock_last_name", "email": "mock_email", "Location": "mock_home", "bio": "mock_description", "link": "mock_facebook_url"}');
        $getResponse->shouldReceive('getInfo')->andReturn(array('url' => 'mock_image_url'));

        $client = m::mock('Guzzle\Service\Client');
        $client->shouldReceive('setBaseUrl')->times(1);
        $client->shouldReceive('post->send')->times(1)->andReturn($postResponse);
        $client->shouldReceive('get->send')->andReturn($getResponse);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', array('code' => 'mock_authorization_code'));
        $user = $this->provider->getUserDetails($token);

        $this->assertEquals(12345, $this->provider->getUserUid($token));
        $this->assertEquals(array('mock_first_name', 'mock_last_name'), $this->provider->getUserScreenName($token));
        $this->assertEquals('mock_email', $this->provider->getUserEmail($token));
        $this->assertEquals('mock_email', $user->email);
    }
}
