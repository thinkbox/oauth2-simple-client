OAuth2 Simple Client
=============

[![Build Status](https://travis-ci.org/StukiOrg/oauth2-simple-client.png?branch=master)](https://travis-ci.org/StukiOrg/oauth2-simple-client)
[![Coverage Status](https://coveralls.io/repos/StukiOrg/oauth2-simple-client/badge.png)](https://coveralls.io/r/StukiOrg/oauth2-simple-client)
[![Total Downloads](https://poser.pugx.org/stuki/oauth2-simple-client/downloads.png)](https://packagist.org/packages/stuki/oauth2-simple-client)

This OAuth2 client is a simply better way to use OAuth2 in your application.  


Included Providers
------------------

- Google
- Facebook
- Github
- Microsoft
- LinkedIn
- Box
- Instagram
- Eventbrite


Installation 
------------

```sh
$ php composer.phar require stuki/oauth2-simple-client dev-master
```
For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).


Use
---

```php
use Stuki\OAuth2\Client;

$provider = new Client\Provider\<ProviderName>(array(
    'clientId'  =>  'id',
    'clientSecret'  =>  'secret',
    'redirectUri'   =>  'https://your-registered-redirect-uri/'
));

if ( ! isset($_GET['code'])) {
    // No authorization code; send user to get one
    // Some providers support and/or require an application state token
    header('Location: ' . $provider->getAuthorizationUrl(array('state' => 'token'));
    exit;
} else {
    // Get an authorization token
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code'],
        ]);
    } catch (\Exception $e) {
        die('handle exception');
    }

    // Store the access token for future use 
    echo $token->access_token;
    
    // Some providers support refresh tokens
    echo $token->refresh_token;

    // Number of seconds until the access token expires; consider refreshing
    echo $token->expires_in;

    // Some user details are provided through the client
    // See Stuki\OAuth2\Client\Entity\User
    $userDetails = $provider->getUserDetails($token);
}
```

Refresh a Token
---------------

```php
use Stuki\OAuth2\Client;

$provider = new Client\Provider\<ProviderName>(array(
    'clientId'  =>  'id',
    'clientSecret'  =>  'secret',
    'redirectUri'   =>  'https://your-registered-redirect-uri/'
));

$grant = new Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```
