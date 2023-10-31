<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Components\Members\Helpers\Orcid;

use Components\Members\Helpers\Orcid\Http\Curl;
use Exception;
use Orcid;
use stdClass;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Orcid' . DS . 'Http' . DS . 'Curl.php';

/**
 * Orcid api oauth class
 **/
class OrcidHandler extends Orcid\Oauth
{
    /**
     * API endpoint constants
     **/
    const HOSTNAME  = 'orcid.org';
    const AUTHORIZE = 'oauth/authorize';
    const TOKEN     = 'oauth/token';
    const VERSION   = '2.1';

     /**
     * The http tranport object
     *
     * @var  object
     **/
    private $http = null;

    /**
     * The ORCID api access level
     *
     * @var  string
     **/
    private $level = 'api';

    /**
     * The ORCID environment type
     *
     * @var  string
     **/
    private $environment = '';

    /**
     * The oauth client ID
     *
     * @var  string
     **/
    private $clientId = null;

    /**
     * The oauth client secret
     *
     * @var  string
     **/
    private $clientSecret = null;

    /**
     * The oauth request scope
     *
     * @var  string
     **/
    private $scope = null;

    /**
     * The oauth request state
     *
     * @var  string
     **/
    private $state = null;

    /**
     * The oauth redirect URI
     *
     * @var  string
     **/
    private $redirectUri = null;

    /**
     * The login/registration page email address
     *
     * @var  string
     **/
    private $email = null;

    /**
     * The login/registration page orcid
     *
     * @var  string
     **/
    private $orcid = null;

    /**
     * The login/registration page family name
     *
     * @var  string
     **/
    private $familyNames = null;

    /**
     * The login/registration page given name
     *
     * @var  string
     **/
    private $givenNames = null;

    /**
     * Whether or not to show the login page as opposed to the registration page
     *
     * @var  bool
     **/
    private $showLogin = false;

    /**
     * The oauth access token
     *
     * @var  string
     **/
    private $accessToken = null;

    /**
     * Constructs a new instance
     *
     * @param   object  $http  a request tranport object to inject
     * @return  void
     * @uses    Orcid\Http\Curl
     **/
    public function __construct($http = null)
    {
        $this->http = $http ?: new Curl;
    }

    /**
     * Select environment based on the URL
     * 
     * @return  $this
     */
    public function selectEnvironment()
    {
        if (strpos(Request::base(), 'hsscommons.ca') === false || strpos(Request::base(), 'test.hsscommons.ca') !== false) {
			$this->environment = 'sandbox';
		} else {
			$this->environment = '';
		}
        return $this;
    }

    /**
     * Grabs the user's profile and parse it into an easy-to-read object
     *
     * You'll probably call this method after completing the proper oauth exchange.
     * But, in theory, you could call this without oauth and pass in a ORCID iD,
     * assuming you use the public API endpoint.
     *
     * @param   string  $orcid  the orcid to look up, if not already set as class prop
     * @return  object
     * @throws  Exception
     **/
    public function getProfile($orcid = null)
    {
        $this->selectEnvironment();
        $this->http->setUrl($this->getApiEndpoint('record', $orcid));

        if ($this->level == 'api') {
            // If using the members api, we have to have an access token set
            if (!$this->getAccessToken()) {
                throw new Exception('You must first set an access token or authenticate');
            }

            $this->http->setHeader([
                'Content-Type'  => 'application/vnd.orcid+json',
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]);
        } else {
            $this->http->setHeader('Accept: application/vnd.orcid+json');
        }

        $profileJSON = json_decode($this->http->execute());
        Log::debug(get_object_vars($profileJSON));

        $person = $profileJSON->person;
        $givenNameField = "given-names";
        $familyNameField = "family-name";

        $profile = new stdClass();
        if ($person->name->visibility === "PUBLIC") {
            $profile->givenName = $person->name->$givenNameField->value;
            $profile->surname = $person->name->$familyNameField->value;
            $profile->name = $profile->givenName . ' ' . $profile->surname;
        }

        if ($person->biography->visibility === "PUBLIC") {
            $profile->bio = $person->biography->content;
        }



        return $profile;
    }

    /**
     * Creates the qualified api endpoint for retrieving the desired data
     *
     * @param   string  $endpoint  the shortname of the endpoint
     * @param   string  $orcid     the orcid to look up, if not already specified
     * @return  string
     **/
    private function getApiEndpoint($endpoint, $orcid = null)
    {
        $url  = 'https://';
        $url .= $this->level . '.';
        $url .= (!empty($this->environment)) ? $this->environment . '.' : '';
        $url .= self::HOSTNAME;
        $url .= '/v' . self::VERSION . '/';
        $url .= $orcid ?: $this->getOrcid();
        $url .= '/' . $endpoint;

        return $url;
    }
}
