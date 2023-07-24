<?php
namespace Orcid;

use Orcid\Http\Curl;
use Exception;


class Oauth
{
    const HOSTNAME = 'orcid.org';
    const AUTHORIZE = 'authorize';
    const TOKEN = 'token';

    private $http = null;
    private $environment = '';
    private $clientId = null;
    private $clientSecret = null;

    private $email = '';
    private $familyName = '';
    private $givenName = '';

    private $scope = null;

    private $state = null;
    private $redirecteUri = null;
    private $accessToken = null;

    public function __construct($http = null)
    {
        $this->http = $http ?: new Curl;
    }

    public function useProductionEnvironment()
    {
        $this->environment = '';

        return $this;
    }

    public function useSandboxEnvironment()
    {
        $this->environment = 'sandbox';
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
        return $this;
    }

    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
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

    public function getAuthorizationUrl()
    {
        $url = 'https://';
        $url .= (!empty($this->environment)) ? $this->environment . '.' : '';
        $url .= self::HOSTNAME . '/oauth/' . self::AUTHORIZE;
        $url .= '?client_id=' . $this->clientId;
        $url .= '&scope=' . $this->scope;
        $url .= '&redirect_uri=' . urlencode($this->redirectUri);
        $url .- '&response_type=code';

        return $url;
    }


    public function authenticate($code)
    {
        $url = 'https://';
        $url .= (!empty($this->environment)) ? $this->environment . '.' : '';
        $url .= self::HOSTNAME . '/oauth/' . self::TOKEN;
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
			$error = (isset($data->error)) ? $data->error : 'unknown error';

			throw new Exception($error);
		}

		return $this;
    }

    public function isAuthenticated()
    {
        return ($this->getAccessToken()) ? true : false;
    }

}