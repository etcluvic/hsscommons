<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

/**
*
* Modified by CANARIE Inc. for the HSSCommons project.
*
* Summary of changes: Written by CANARIE Inc. Based on HUBzero's Plugin of plg_members_impact, with implicit permission under original MIT licence.
*
*/

// No direct access
defined('_HZEXEC_') or die();

include_once PATH_APP . DS . 'components' . DS. 'com_members' . DS . 'helpers' . DS . 'Orcid' . DS . 'OrcidHandler.php';

/**
 * Members Plugin class for author's repository
 */
class plgMembersRepository extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Publication areas
	 *
	 * @var  array
	 */
	private $_areas = null;

	/**
	 * Publication stats
	 *
	 * @var  boolean
	 */
	protected $_stats = null;

	/**
	 * Publication categories
	 *
	 * @var  array
	 */
	private $_cats  = null;

	/**
	 * Record count
	 *
	 * @var  integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_database = App::get('db');
		
		$path = \Component::path('com_publications');
		require_once $path . DS . 'tables' . DS . 'logs.php';
		require_once $path . DS . 'tables' . DS . 'publication.php';
		require_once $path . DS . 'tables' . DS . 'author.php';
		require_once $path . DS . 'tables' . DS . 'category.php';
		require_once $path . DS . 'tables' . DS . 'version.php';
		require_once $path . DS . 'helpers' . DS . 'html.php';
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if (($user->get('id') == $member->get('id') && $this->params->get('show_repository', 0) == 1) || $this->params->get('show_repository', 0) == 2)
		{
			// Check if user has any publications
			// $pubLog = new \Components\Publications\Tables\Version($this->_database);
			// $this->_stats = $pubLog->getPubVersions($member->get('id'));
			
			$pubLog = new \Components\Publications\Tables\Publication($this->_database);
			$this->_stats = $pubLog->getRecords(array(
				"sortby" => "title",		// Default
				"dev" => 1,
				"status" => array(0,1,3,4,5,6),
				"author" => $member->get('id')
			));


			$areas['repository'] = Lang::txt('PLG_MEMBERS_REPOSITORY_MENU_TITLE');
			$areas['icon']   = 'f02d';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		if ($returnhtml)
		{
			require_once \Component::path('com_publications') . DS . 'tables' . DS . 'version.php';

			$this->_option = $option;

			// Which view
			$task = Request::getVar('action', '');

			switch ($task)
			{
				case 'getPubInfo':
					$this->_getPubInfo();
					exit;
				case 'view':
				default:        $arr['html'] = $this->_view($member->get('id'));   break;
			}
		}

		//get meta
		$arr['metadata'] = array();
		$arr['metadata']['count'] = $this->_stats ? count($this->_stats) : 0;

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param   integer  $uid
	 * @return  string
	 */
	protected function _view($uid = 0)
	{
		// Build the final HTML
		$view = $this->view('default', 'stats');

		// Get pub stats for each publication
		// $pubLog = new \Components\Publications\Tables\Version($this->_database);
		// $view->pubstats = $this->_stats ? $this->_stats : $pubLog->getPubVersions($uid);

		$pubLog = new \Components\Publications\Tables\Publication($this->_database);
		$view->pubstats = $pubLog->getRecords(array(
			"sortby" => "title",		// Default
			"dev" => 1,
			"status" => array(0,1,3,4,5,6),
			"author" => $uid
		));

		// Output HTML
		$view->option    = $this->_option;
		$view->database  = $this->_database;
		$view->uid       = $uid;
		$view->pubconfig = Component::params('com_publications');

		// Get current user's ORCID from database
		$orcidRow = \Hubzero\Auth\Link::all()
					->whereEquals('user_id', User::get('id'))
					->row();
		$orcid = $orcidRow->username;
		$view->orcid = $orcid;

		$orcidWorks = [];
		if ($orcid) {
			// Get user access token
			$query = new \Hubzero\Database\Query;
			$accessTokens = $query->select('*')
								->from('#__xprofiles_tokens')
								->whereEquals('user_id', User::get('id'))
								->fetch();

			if (count($accessTokens) > 0) {
				// Read the current user's ORCID works
				$orcidHandler = new \Components\Members\Helpers\Orcid\OrcidHandler;
				$orcidHandler->setAccessToken($accessTokens[0]->token);
				$orcidHandler->setOrcid($orcid);
				$orcidWorks = $orcidHandler->getAllWorks();
			}
		}
		$view->orcidWorks = $orcidWorks;
		$view->totalOrcidWorks = count($orcidWorks);

		// Construct a list of publication ids in this repo
		$pubIds = [];
		foreach($view->pubstats as $stat) {
			$pubIds[] = $stat->version_id;
		}

		// Get the putcodes of all ORCID imported publications into this repo
		$orcidImportedPutCodes = [];
		$query = new \Hubzero\Database\Query;
		$orcidImportedPubs = $query->select('element_id, handler_id, params')
									->from('#__publication_handler_assoc')
									->whereIn('publication_version_id', $pubIds)
									->fetch();
		foreach($orcidImportedPubs as $orcidPub) {
			$paramFields = explode('=', $orcidPub->params);
			$location = $paramFields[1];

			// Only get publications that this user imported to his/her personal repo
			if ($orcidPub->handler_id == User::get('id') && $location === 'repo')
			$orcidImportedPutCodes[] = $orcidPub->element_id;
		}
		$view->orcidImportedPutCodes = $orcidImportedPutCodes;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Return a list of categories
	 *
	 * @return  array
	 */
	public function &onMembersContributionsAreas()
	{
		$areas = array();

		//if ($this->params->get('contributions', 0) == 1)
		//{
			$areas = array(
				'repository' => Lang::txt('PLG_MEMBERS_REPOSITORY_PUBLICATIONS')
			);
		//}

		$this->_areas = $areas;

		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param   string  $user_id   Field to join on user ID
	 * @param   string  $username  Field to join on username
	 * @return  string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$query = "SELECT COUNT(DISTINCT P.id) FROM `#__publications` AS P,
				`#__publication_versions` as V,
				`#__publication_authors` as A
				WHERE V.publication_id=P.id AND A.publication_version_id = V.id
				AND A.user_id=" . $user_id . " AND
				V.state=1 AND A.status=1 AND A.role!='submitter'";
		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param   object   $member      Current member
	 * @param   string   $option      Component name
	 * @param   string   $authorized  Authorization level
	 * @param   integer  $limit       Number of record to return
	 * @param   integer  $limitstart  Record return start
	 * @param   string   $sort        Field to sort records on
	 * @param   array    $areas       Areas to return data for
	 * @return  array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		if (is_array($areas) && $limit && count($this->onMembersContributionsAreas()) > 0)
		{
			if (!isset($areas[$this->_name])
			  && !in_array($this->_name, $areas)
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\User)
		{
			if (!$member->get('id'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('id');
				$username  = $member->get('username');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
				$username  = $member->username;
			}
		}

		// Instantiate some needed objects
		$database = App::get('db');
		$objP = new \Components\Publications\Tables\Publication($database);

		// Build query
		$filters = array(
			'sortby' => $sort,
			'limit'  => $limit,
			'start'  => $limitstart,
			'author' => $uidNumber
		);

		if (!$limit)
		{
			if (!$this->params->get('contributions'))
			{
				return 0;
			}

			$results = $objP->getCount($filters);
			return $results;
		}
		else
		{
			if (!$this->params->get('contributions'))
			{
				return array();
			}

			$rows = $objP->getRecords($filters);

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					if ($row->alias)
					{
						$sef = Route::url('index.php?option=com_publications&alias=' . $row->alias . '&v=' . $row->version_number);
					}
					else
					{
						$sef = Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number);
					}
					$rows[$key]->href = $sef;
					$rows[$key]->text = $rows[$key]->abstract;
					$rows[$key]->section = 'repository';
					$rows[$key]->author = $uidNumber == User::get('id') ? true : false;
				}
			}

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		$database = App::get('db');
		$thedate  = Date::of($row->published_up)->toLocal('d M Y');

		// Get version authors
		$pa = new \Components\Publications\Tables\Author($database);
		$authors = $pa->getAuthors($row->version_id);

		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">' . $thedate . ' <span>|</span> ' . stripslashes($row->cat_name);
		if ($authors)
		{
			$html .= ' <span>|</span>' . Lang::txt('PLG_MEMBERS_REPOSITORY_CONTRIBUTORS') . ': ' . \Components\Publications\Helpers\Html::showContributors($authors, false, true) . "\n";
		}
		if ($row->doi)
		{
			$html .= ' <span>|</span> doi:' . $row->doi . "\n";
		}
		if (!$row->project_provisioned && ((isset($row->project_private) && $row->project_private != 1) || $row->author == true))
		{
			$url  = 'index.php?option=com_projects&alias=' . $row->project_alias;
			$url .= $row->author == true ? '&active=publications&pid=' . $row->id : '';
			$html .= ' <span>|</span> Project: ';
			$html .= '<a href="';
			$html .= Route::url($url) . '">';
			$html .= $row->project_title;
			$html .='</a>';
			$html .= "\n";
		}
		$html .= '</p>' . "\n";
		if ($row->text)
		{
			$html .= "\t\t<p>" . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->text)), 300) . "</p>\n";
		}
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}

	/**
	 * Get publication info
	 *
	 * @return  void
	 */
	protected function _getPubInfo()
	{
		// Get publication ID
		$pubId = Request::getInt('pubId', 0);
		// If confirm is true, publication is exported. Otherwise, only return publication info in JSON format
		$confirm = Request::getInt('confirm', 0);

		// Get all versions of this publication
		$query = new \Hubzero\Database\Query;
		$publicationVersions = $query->select('*')
									->from('#__publication_versions')
									->whereEquals('publication_id', $pubId)
									->fetch();
		
		// Get the last version to export
		$lastVersion = end($publicationVersions);
		Log::debug(get_object_vars($lastVersion));

		$pubResponse = new stdClass;
		$pubResponse->work = new stdClass;
		
		// Title
		$translatedTitle = 'translated-title';
		$journalTitle = 'journal-title';
		$pubResponse->work->title = new StdClass;
		$pubResponse->work->title->title = $lastVersion->title;
		$pubResponse->work->title->subtitle = '';
		$pubResponse->work->title->$translatedTitle = '';

		// Journal title
		$pubResponse->work->$journalTitle = '';

		// Short description
		$shortDescription = 'short-description';
		$pubResponse->work->shortDescription = $lastVersion->description;

		// Citation
		$citationType = 'citation-type';
		$citationValue = 'citation-value';
		$pubResponse->work->citation = new stdClass;
		$pubResponse->work->citation->$citationType = 'bibtex';
		$pubResponse->work->citation->$citationValue = $lastVersion->release_notes;

		// Type
		$pubResponse->work->type = 'journal-article';
		
		// Publication date
		$publicationDate = 'publication-date';
		$pubResponse->work->$publicationDate = new stdClass;
	
		$publishedUp = $lastVersion->published_up;
		if (!$publishedUp) {
			$pubResponse->work->$publicationDate->year = '';
			$pubResponse->work->$publicationDate->month = '';
			$pubResponse->work->$publicationDate->day = '';
		} else {
			$publishedUpArray = explode(' ', $publishedUp);
			$publishedUpDate = $publishedUpArray[0];
			$publishedUpDate = explode('-', $publishedUpDate);
			$pubResponse->work->$publicationDate->year = intval($publishedUpDate[0]);
			$pubResponse->work->$publicationDate->month = intval($publishedUpDate[1]);
			$pubResponse->work->$publicationDate->day = intval($publishedUpDate[2]);
		}

		// External identifiers
		$externalIds = 'external-ids';
		$externalId = 'external-id';
		$externalIdType = 'external-id-type';
		$externalIdValue = 'external-id-value';
		$externalIdUrl = 'external-id-url';
		$externalIdRelationship = 'external-id-relationship';
		$pubResponse->work->$externalIds = new stdClass;
		$pubResponse->work->$externalIds->$externalId = [];
		
		if ($lastVersion->doi) {
			$pubResponse->work->$externalIds->{$externalId}[] = new stdClass;
			$pubResponse->work->$externalIds->$externalId[0]->$externalIdType = 'doi';
			$pubResponse->work->$externalIds->$externalId[0]->$externalIdValue = $lastVersion->doi;
			$pubResponse->work->$externalIds->$externalId[0]->$externalIdUrl = 'https://doi.org/' . $lastVersion->doi;
			$pubResponse->work->$externalIds->$externalId[0]->$externalIdRelationship = 'self';
		}

		// URL
		$pubResponse->work->url = 'https://hsscommons.ca/publications/' . $pubId;

		// Contributors
		$contributorOrcid = 'contributor-orcid';
		$creditName = 'credit-name';
		$contributorAttributes = 'contributor-attributes';
		$contributorSequence = 'contributor-sequence';
		$contributorRole = 'contributor-role';
		$pubResponse->work->contributors = new stdClass;
		$pubResponse->work->contributors->contributor = [];

		// Get all authors of this publication
		$query = new \Hubzero\Database\Query;
		$publicationAuthors = $query->select('*')
									->from('#__publication_authors')
									->whereEquals('publication_version_id', $lastVersion->id)
									->fetch();
		
		$addedContributors = [];
		foreach($publicationAuthors as $author) {
			if (!in_array($author->user_id, $addedContributors))
			{
				// $pubResponse->work->contributors->contributor[] = new stdClass;
				$newContributor = new stdClass;

				// Check if this author has ORCID
				$query = new \Hubzero\Database\Query;
				$orcids = $query->select('username')
								->from('#__auth_link')
								->whereEquals('user_id', $author->user_id)
								->fetch();
				if (count($orcids) > 0) {
					$orcid = $orcids[0]->username;
					$newContributor->$contributorOrcid = new stdClass;
					$newContributor->$contributorOrcid->uri = 'https://orcid.org/' . $orcid;
					$newContributor->$contributorOrcid->path = $orcid;
					$newContributor->$contributorOrcid->host = "orcid.org";
				}

				// Add names
				$newContributor->$creditName = $author->name;
				
				// Add attributes
				$newContributor->$contributorAttributes = new stdClass;
				$newContributor->$contributorAttributes->$contributorSequence = 'first';
				$newContributor->$contributorAttributes->$contributorRole = 'author';

				// Add this contributor to the list
				$pubResponse->work->contributors->contributor[] = $newContributor;

				// Add this contributor to the list of added contributors
				$addedContributors[] = $author->user_id;
			}
		}

		// Language code
		$languageCode = 'language-code';
		$pubResponse->work->$languageCode = 'en';

		// Country
		$pubResponse->work->country = "CA";

		echo json_encode($pubResponse);
		exit;
	}
}
