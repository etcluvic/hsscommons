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
        // if (strpos(Request::base(), 'hsscommons.ca') === false || strpos(Request::base(), 'test.hsscommons.ca') !== false) {
        if (strpos(Request::base(), 'hsscommons.ca') === false) {
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
        
        // Fetch to ORCID API failed
        if (isset($profileJSON->error)) {
            $error = new stdClass;
            $error->error = $profileJSON->error;
            $error->errorDescription = $profileJSON->error_description;
            return $error;
        }

        $person = $profileJSON->person;
        $givenNameField = "given-names";
        $familyNameField = "family-name";
        $researcherUrls = "researcher-urls";
        $researcherUrl = "researcher-url";
        $activitiesSummary = "activities-summary";
        $educationSummary = "education-summary";
        $roleTitle = "role-title";
        $employmentSummary = "employment-summary";

        $profile = new stdClass();
        // Add name
        if ($person->name->visibility === "PUBLIC") {
            $profile->givenName = $person->name->$givenNameField->value;
            $profile->surname = $person->name->$familyNameField->value;
            $profile->name = $profile->givenName . ' ' . $profile->surname;
        }

        // Add bio
        if (isset($person->biography) && isset($person->biography->visibility) && $person->biography->visibility === "PUBLIC") {
            $profile->bio = $person->biography->content;
        }

        // Add website and social medias
        if (isset($person->$researcherUrls->$researcherUrl)) 
        {
            foreach($person->$researcherUrls->$researcherUrl as $url) {
                if ($url->visibility === "PUBLIC") {
                    $urlValue = $url->url->value;
                    if (strpos($urlValue, "twitter") !== false) {
                        $profile->twitter = $urlValue;
                    } else if (strpos($urlValue, "facebook") !== false) {
                        $profile->facebook = $urlValue;
                    } else if (strpos($urlValue, "linkedin") !== false) {
                        $profile->linkedin = $urlValue;
                    } else {
                        $profile->url = $urlValue;
                    }
                }
            }
        }

        // Add email
        if (isset($person->emails->email) && count($person->emails->email) > 0 && $person->emails->email[0]->visibility === "PUBLIC") {
            $profile->email = $person->emails->email[0]->email;
        } else {
            $profile->email = null;
        }

        // Add education
        $activities = $profileJSON->$activitiesSummary;
        if (count($activities->educations->$educationSummary) > 0) {
            $education = $activities->educations->$educationSummary;
            if ($education[0]->visibility === "PUBLIC") {
                $profile->education = $education[0]->$roleTitle;
            }
        }

        // Add title and affiliation
        if (count($activities->employments->$employmentSummary) > 0) {
            $employment = $activities->employments->$employmentSummary;
            if ($employment[0]->visibility === "PUBLIC") {
                $profile->title = $employment[0]->$roleTitle;
                $profile->affiliation = $employment[0]->organization->name;
            }
        }

        return $profile;
    }

    /**
     * Grabs the user's ORCID works and parse it into an easy-to-read array of objects
     *
     *
     * @param   string  $orcid  the orcid to look up, if not already set as class prop
     * @return  array
     * @throws  Exception
     **/
    public function getAllWorks($orcid = null)
    {
        $this->selectEnvironment();
        $this->http->setUrl($this->getApiEndpoint('works', $orcid));

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

        $worksJSON = json_decode($this->http->execute());
        $worksGroup = $worksJSON->group;

         // Fetch to ORCID API failed
         if (isset($worksJSON->error)) {
            $error = new stdClass;
            $error->error = $worksJSON->error;
            $error->errorDescription = $worksJSON->error_description;
            return $error;
        }

        // Define some string constants
        $workSummary = "work-summary";
        $publicationDate = "publication-date";
        $putCode = "put-code";

        // Translate work type JSON response into human readable string
        // Archie: Don't need this yet as there are too many
        // $workTypes = [
        //     "BOOK" => "book", 
        //     "BOOK_CHAPTER" => "Book chapter",
        //     "BOOK_REVIEW" => "Book"
        // ]

        $works = [];
        foreach($worksGroup as $groupData) {
            // Only read public works
            $workDatas = $groupData->$workSummary;
            $workData = $workDatas[0];
            if ($workData->visibility !== "PUBLIC") {
                continue;
            }
            $work = new stdClass;
            $work->putCode = $workData->$putCode;
            $work->title = isset($workData->title->title) ? $workData->title->title->value : "";
            $work->type = isset($workData->type) ? ucfirst(str_replace('_', ' ', strtolower($workData->type))) : "";
            $works[] = $work;
        }

        return $works;
    }

    /**
     * Grabs the user's multiple ORCID works (put codes provided) and parse it into an easy-to-read array of objects
     *
     *
     * @param   string  $orcid  the orcid to look up, if not already set as class prop
     * @param   string  put codes of selected publications, separated by a comma
     * @return  array
     * @throws  Exception
     **/
    public function getMultipleWorks($orcid = null, $putCodes = '')
    {
        $this->selectEnvironment();
        $this->http->setUrl($this->getApiEndpoint('works' . DS . $putCodes, $orcid));

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

        $worksJSON = json_decode($this->http->execute());
        Log::debug(get_object_vars($worksJSON));
        $worksBulk = $worksJSON->bulk;

         // Fetch to ORCID API failed
         if (isset($worksJSON->error)) {
            $error = new stdClass;
            $error->error = $worksJSON->error;
            $error->errorDescription = $worksJSON->error_description;
            return $error;
        }

        // Define some string constants
        $publicationDate = "publication-date";
        $putCode = "put-code";
        $shortDescription = "short-description";
        $journalTitle = "journal-title";
        $citationValue = "citation-value";
        $externalIds = "external-ids";
        $externalId = "external-id";
        $externalIdType = "external-id-type";
        $externalIdValue = "external-id-value";
        $contributorOrcid = "contributor-orcid";
        $creditName = "credit-name";

        // Translate work type JSON response into human readable string
        // Archie: Don't need this yet as there are too many
        // $workTypes = [
        //     "BOOK" => "Book", 
        //     "BOOK_CHAPTER" => "Book chapter",
        //     "BOOK_REVIEW" => "Book"
        // ];

        $works = [];
        foreach($worksBulk as $bulkData) {
        // Only read public works
            $workData = $bulkData->work;
            if ($workData->visibility !== "PUBLIC") {
                continue;
            }
            $work = new stdClass;
            $work->putCode = $workData->$putCode;
            $work->title = isset($workData->title->title) ? $workData->title->title->value : "";
            $work->type = isset($workData->type) ? ucfirst(str_replace('_', ' ', strtolower($workData->type))) : "";
            // $work->abstract = isset($workData->title->subtitle) ? $workData->title->subtitle->value : "";
            $work->description = isset($workData->$shortDescription) ? $workData->$shortDescription : "";
            $work->journalTitle = isset($workData->$journalTitle) ? $workData->$journalTitle->value : "";
            $work->citation = isset($workData->citation->$citationValue) ? $workData->citation->$citationValue : "";
            
            // Set DOI
            if (isset($workData->$externalIds->$externalId)) {
                $doi = "";
                foreach($workData->$externalIds->$externalId as $id) {
                    if ($id->$externalIdType === "doi") {
                        $doi = $id->$externalIdValue;
                    }
                }
                $work->doi = $doi;
            } else {
                $work->doi = "";
            }

            // Add authors
            $authors = [];
            if (isset($workData->contributors->contributor) && count($workData->contributors->contributor) > 0) {
                foreach($workData->contributors->contributor as $author) {
                    $authorOrcid = "";
                    $authorName = $author->$creditName->value;
                    if (isset($author->$contributorOrcid) && $author->$contributorOrcid && $author->$contributorOrcid->path && $author->$contributorOrcid->path !== "null") {
                        $authorOrcid = $author->$contributorOrcid->path;
                    }
                    $authorNameSegments = explode(" ", $authorName);
                    $authors[] = array(
                                    "orcid" => $authorOrcid, 
                                    "name" => $authorName, 
                                    "givenname" => $authorNameSegments[0], 
                                    "surname" => count($authorNameSegments) >= 2 ? $authorNameSegments[count($authorNameSegments) - 1] : ""
                                );
                }
            } 
            else if (isset($workData->source) && $workData->source) {
                $sourceOrcid = "source-orcid";
                $sourceName = "source-name";
                $authorOrcid = "";
                if (isset($workData->source->$sourceOrcid) && $workData->source->$sourceOrcid && $workData->source->$sourceOrcid->path && $workData->source->$sourceOrcid->path !== "null") {
                    $authorOrcid = $workData->source->$sourceOrcid->path;
                }
                $authorName = $workData->source->$sourceName->value;
                $authorNameSegments = explode(" ", $authorName);
                $authors[] = array(
                                "orcid" => $authorOrcid, 
                                "name" => $authorName, 
                                "givenname" => $authorNameSegments[0], 
                                "surname" => count($authorNameSegments) >= 2 ? $authorNameSegments[count($authorNameSegments) - 1] : ""
                            );
            }
            $work->authors = $authors;

            // Add URL
            $work->url = "";
            if (isset($workData->url) && $workData->url) {
                $work->url = $workData->url->value;
            }

            // Add type
            $work->type = null;
            if (isset($workData->type) && $workData->type) {
                $type = $workData->type;
                $type = str_replace("_", " ", $type);
                $type = ucwords(strtolower($type));
                Log::debug('Type: ' . $type);

                // Search if this type is in the Commons system. If yes, set the type
                $query = new \Hubzero\Database\Query;

                $types = $query->select('*')
                                ->from('#__publication_categories')
                                ->whereEquals('name', $type)
                                ->whereEquals('contributable', 1)
                                ->whereEquals('state', 1)
                                ->fetch();
                if (count($types) > 0) {
                    $work->type = $types[0]->id;
                }
            }
            
            $works[] = $work;
        }

        return $works;
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
