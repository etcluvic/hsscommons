<?php

namespace Orcid2;

use Orcid2\Http\Curl;
use Exception;

class Oauth
{
    const HOSTNAME  = 'orcid.org';
    const AUTHORIZE = 'authorize';
    const TOKEN = 'token';


    private $http = null;
    private $environment = '';
    private $clientId = null;
    private $clientSecret = null;
    private $scope = 'login%20extensive';
    private $state = null;
    private $redirectUri = null;
    private $accessToken = null;

    public function __construct($http = null)
    {
        $this->htpp = $http ?: new Curl;
    }

    public function userProductionEnvironment()
    {
        $this->environment = '';
        return $this;
    }

    public function useSandboxEnvironment()
    {
        $this->environment - 'sandbox';
        return $this;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getAuthorizationUrl()
    {
		if (!$this->redirectUri)
		{
			throw new Exception('Redirect URI is not set');
		}
		if (!$this->scope)
		{
			throw new Exception('Scope is required');
		}
		if (!$this->clientId)
		{
			throw new Exception('Client ID is not set');
		}

		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME . '/' . self::AUTHORIZE;
		$url .= '?client_id='    . $this->clientId;
		$url .= '&scope='        . $this->scope;
		$url .= '&redirect_uri=' . urlencode($this->redirectUri);
		$url .= '&response_type=code';

		return $url;
	}

    public function authenticate($code)
    {
        if (!$this->clientId)
		{
			throw new Exception('Client ID is required');
		}
		if (!$this->clientSecret)
		{
			throw new Exception('Client secret is required');
		}
		if (!$this->redirectUri)
		{
			throw new Exception('Redirect URI is required');
		}

		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME . '/' . self::TOKEN . '?key=' . $this->clientSecret;

		$fields = [
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'code'          => $code,
			'redirect_uri'  => urlencode($this->redirectUri),
			'grant_type'    => 'authorization_code'
		];

		$this->http->setUrl($url)
				   ->setPostFields($fields)
				   ->setHeader(['Accept' => 'application/json']);

		$data = json_decode($this->http->execute());

		if (isset($data->access_token))
		{
			$this->setAccessToken($data->access_token);
		}
		else
		{
			// Seems like the response format changes on occasion... not sure what's going on there?
			$error = (isset($data->error)) ? $data->error : 'unknown error';

			throw new Exception($error);
		}

		return $this;
    }

    public function isAuthenticated()
	{
		return ($this->getAccessToken()) ? true : false;
	}

    public function getUserData()
	{
		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME . '/api/record';

		$this->http->setUrl($url);

		// If using the members api, we have to have an access token set
		if (!$this->getAccessToken())
		{
			throw new Exception('You must first set an access token or authenticate');
		}

		$this->http->setHeader([
			//'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $this->getAccessToken()
		]);

		$account = json_decode($this->http->execute());
		return $account;
	}

}