<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding Google Scholar metadata to the document
 */
class plgPublicationsGooglescholar extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		if (!App::isSite()
		 || Request::getWord('format') == 'raw'
		 || Request::getInt('no_html'))
		{
			return;
		}

		$view = $this->view();

		$publication->authors();
		$publication->license();
		
		// Add metadata
		Document::setMetaData('citation_title', $view->escape($publication->title));

		$nullDate = '0000-00-00 00:00:00';

		$thedate = $publication->publish_up;

		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->accepted;
		}
		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->submitted;
		}
		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->created;
		}
		if ($thedate && $thedate != $nullDate)
		{	
			// Publication was published from the site
			if ($publication->isAuthor($publication->created_by)) {
				Document::setMetaData('citation_publication_date', Date::of($thedate)->toLocal('Y/m/d'));
			} 
			
			// Publication was published from outside the site then imported into the site
			else {
				Document::setMetaData('citation_online_date', Date::of($thedate)->toLocal('Y/m/d'));
			}
		}

		if ($doi = $publication->version->get('doi'))
		{
			Document::setMetaData('citation_doi', $view->escape($doi));
		}

		// Add "citation_pdf_url" if there is a file attached to the publication
		$query = new \Hubzero\Database\Query;
		$primary_files = $query->select('*')
							   ->from('#__publication_attachments')
							   ->whereEquals('publication_id', $publication->id)
							   ->whereEquals('role', 1)
							   ->fetch();

		foreach ($primary_files as $file) {
			$file_absolute_path = Request::base() . 'publications' . DS . $publication->id . DS . 'serve' . DS . $file->role . DS . $file->id . '?el=' . $file->ordering;
			Document::setMetaData('citation_pdf_url', $file_absolute_path);
		}

		foreach ($publication->_authors as $contributor)
		{
			if ($contributor->role && strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			if ($contributor->name)
			{
				$name = stripslashes($contributor->name);
			}
			else
			{
				$name = stripslashes($contributor->p_name);
			}

			if (!$contributor->organization)
			{
				$contributor->organization = $contributor->p_organization;
			}
			$contributor->organization = stripslashes(trim($contributor->organization ? $contributor->organization : ""));

			Document::setMetaData('citation_author', $view->escape($name));

			if ($contributor->organization)
			{
				Document::setMetaData('citation_author_institution', $view->escape($contributor->organization));
			}
		}

		// NOTE: Have to comment out as there aren't suitable fields in the database
		// Implement requirement 2D in https://scholar.google.ca/intl/en/scholar/inclusion.html#indexing
		// $publicationCategoryAlias = $publication->category()->alias;
		// $isPublicationJournal = strpos($publicationCategoryAlias, "journal") !== false;
		// $isPublicationConference = strpos($publicationCategoryAlias, "conference") !== false;
		// if ($isPublicationJournal) {
		// 	Document::setMetaData("citation_journal_title", $view->escape($publication->title));
		// }
		// if ($isPublicationConference) {
		// 	Document::setMetaData("citation_conference_title", $view->escape($publication->title));
		// }
	}
}
