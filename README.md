OAuth2 Client
=============

[![Build Status](https://travis-ci.org/stuki/oauth2-client.png?branch=master)](https://travis-ci.org/stuki/oauth2-client)
[![Coverage Status](https://coveralls.io/repos/stuki/oauth2-client/badge.png)](https://coveralls.io/r/stuki/oauth2-client)
[![Total Downloads](https://poser.pugx.org/stuki/oauth2-client/downloads.png)](https://packagist.org/packages/stuki/oauth2-client)
[![Latest Stable Version](https://poser.pugx.org/stuki/oauth2-client/v/stable.png)](https://packagist.org/packages/stuki/oauth2-client)

This OAuth2 library is a better, simple, way to use OAuth2 in your application.  


Included Providers
------------------

- Eventbrite
- Facebook
- Github
- Google
- Instagram
- LinkedIn
- Microsoft


Installation 
------------

```sh
$ php composer.phar require stuki/oauth2-client dev-master
```
For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).


Use
---

```php
$provider = new League\OAuth2\Client\Provider\<ProviderName>(array(
    'clientId'  =>  'XXXXXXXX',
    'clientSecret'  =>  'XXXXXXXX',
    'redirectUri'   =>  'https://your.site/callback'
));

if ( ! isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    header('Location: '.$provider->getAuthorizationUrl());
    exit;

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // If you are using Eventbrite you will need to add the grant_type parameter (see below)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
        'grant_type' => 'authorization_code'
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $userDetails = $provider->getUserDetails($token);

        // Use these details to create a new profile
        printf('Hello %s!', $userDetails->firstName);

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->access_token;
    
    // Use this to get a new access token if the old one expires
    echo $token->refresh_token;

    // Number of seconds until the access token will expire, and need refreshing
    echo $token->expires_in;
}
```

### Refreshing a Token

```php
$provider = new League\OAuth2\Client\Provider\<ProviderName>(array(
    'clientId'  =>  'XXXXXXXX',
    'clientSecret'  =>  'XXXXXXXX',
    'redirectUri'   =>  'https://your-registered-redirect-uri/'
));

$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

## Testing

``` bash
$ phpunit
```

Third-Party Providers
---------------------

If you extend this library with a new OAuth2 provider your contribution of that code is welcome.
