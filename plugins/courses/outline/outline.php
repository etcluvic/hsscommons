<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Courses\Models\Assetgroup;
use Components\Courses\Models\Unit;
use Components\Courses\Models\Course;

/**
 * Courses Plugin class for the outline
 */
class plgCoursesOutline extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Obj)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('description', Lang::txt('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f0ae');

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getString('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		// Check to see if user is member and plugin access requires members
		$sparams = new \Hubzero\Config\Registry($course->offering()->section()->get('params'));
		if (!$course->offering()->section()->access('view') && !$sparams->get('preview', 0))
		{
			$response->set('html', '<p class="info">' . Lang::txt('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>');
			return $response;
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$this->css();

			// Course and action
			$this->course = $course;
			$action = strtolower(Request::getWord('action', ''));

			if ($action === 'save') {
				return $this->saveTask();
			}

			$this->view = $this->view('default', 'outline');
			$this->view->option     = Request::getCmd('option', 'com_courses');
			$this->view->controller = Request::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $course->config();

			switch ($action)
			{
				case 'build':
					$this->_build();
				break;

				default:
					$this->js();

					$this->_display();
				break;
			}

			$response->set('html', $this->view->loadTemplate());
		}

		// Return the output
		return $response;
	}

	/**
	 * Set the layout to the default outline view
	 *
	 * @return  void
	 */
	private function _display()
	{
		if (($unit = Request::getString('unit', '')))
		{
			$this->view->setLayout('unit');
		}
		if (($group = Request::getString('group', '')))
		{
			$this->view->setLayout('lecture');
		}

		if (isset($unit))
		{
			$this->view->unit = $unit;
		}
		if (isset($group))
		{
			$this->view->group = $group;
		}
	}

	/**
	 * Show the builder interface
	 *
	 * @return  string
	 */
	private function _build()
	{
		if (!$this->course->access('manage'))
		{
			App::abort(401, Lang::txt('Not Authorized'));
			return;
		}

		// If we have a scope set, we're loading a specific outline piece (ex: a unit)
		if ($scope = Request::getWord('scope', false))
		{
			// Setup view
			$this->view->setLayout("edit{$scope}");
			$this->css('selector.css');
			$this->css('build.css');
			$this->css($scope . '.css');
			$this->js($scope);

			// Add file uploader JS
			$this->js('jquery.iframe-transport', 'system');
			$this->js('jquery.fileupload', 'system');

			$this->view->title         = "Edit {$scope}";
			$this->view->scope         = $scope;
			$this->view->scope_id      = Request::getInt('scope_id');

			return;
		}

		$this->css('jquery.ui.css', 'system');

		// Add outline builder style and script
		$this->css('build.css');
		$this->js('build');

		// Add Content box plugin
		$this->js('contentbox', 'system');
		$this->css('contentbox.css', 'system');

		// Add underscore
		$this->js('underscore-min', 'system');
		$this->js('jquery.hoverIntent', 'system');

		// Add file uploader JS
		$this->js('jquery.iframe-transport', 'system');
		$this->js('jquery.fileupload', 'system');

		// Use datetime picker, rather than just datepicker
		$this->js('jquery.timepicker', 'system');

		// Setup view
		$this->view->setLayout('build');

		$this->view->title = 'Edit Outline';
	}

	public function saveTask()
	{
		$course_id      = Request::getInt('course_id', 0);
		$offering_alias = Request::getCmd('offering', '');
		$section_id     = Request::getInt('section_id', '');

		// Load the course page
		$course   = Course::getInstance($course_id);
		$offering = $course->offering($offering_alias);
		$section  = $course->offering()->section($section_id);

		// Make sure we have an incoming 'id'
		$id = Request::getInt('id', null);

		// Create our unit model
		$unit = Unit::getInstance($id);

		// Check to make sure we have a unit object
		if (!is_object($unit))
		{
			App::abort(500, 'Failed to instantiate a unit object');
		}

		if ($section_id = Request::getInt('section_id', 0))
		{
			$unit->set('section_id', $section_id);
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $unit->get('title');
		$title = (!empty($title)) ? $title : 'New Unit';

		// Set our values
		$unit->set('title', Request::getString('title', $title));
		$unit->set('alias', strtolower(str_replace(' ', '', $unit->get('title'))));

		$offset = Config::get('offset');

		// If we have dates coming in, save those
		if ($publish_up = Request::getString('publish_up', ''))
		{
			$unit->set('publish_up', Date::of($publish_up, $offset)->toSql());
		}
		if ($publish_down = Request::getString('publish_down', ''))
		{
			$unit->set('publish_down', Date::of($publish_down, $offset)->toSql());
		}

		// When creating a new unit
		if (!$id)
		{
			$unit->set('offering_id', Request::getInt('offering_id', 0));
			$unit->set('created', Date::toSql());
			$unit->set('created_by', User::get('id'));
		}

		// Save the unit
		if (!$unit->store())
		{
			App::abort(500, "Saving unit {$id} failed ({$unit->getError()})");
		}

		// Create a placeholder for our return object
		$assetGroups = [];

		// If this is a new unit, give it some default asset groups
		// Create a top level asset group for each of lectures, homework, and exam
		if (!$id)
		{
			// Get the courses config
			$config = Component::params('com_courses');
			$asset_groups = explode(',', $config->get('default_asset_groups', 'Lectures, Homework, Exam'));
			array_map('trim', $asset_groups);
			foreach ($asset_groups as $key)
			{	
				// Get our asset group object
				$assetGroup = new Assetgroup(null);

				$assetGroup->set('title', $key);
				$assetGroup->set('alias', strtolower(str_replace(' ', '', $assetGroup->get('title'))));
				$assetGroup->set('unit_id', $unit->get('id'));
				$assetGroup->set('parent', 0);
				$assetGroup->set('created', Date::toSql());
				$assetGroup->set('created_by', User::get('id'));

				// Save the asset group
				if (!$assetGroup->store())
				{
					App::abort(500, 'Asset group save failed');
				}
				$return = new stdclass();
				$return->assetgroup_id    = $assetGroup->get('id');
				
				$return->assetgroup_title = $assetGroup->get('title');
				$return->course_id        = $course_id;
				$return->assetgroup_style = '';
				
				$assetGroups[] = $return;
			}
		}

	// 	// Need to return the content of the prerequisites view (not sure of a better way to do this at the moment)
	// 	// @FIXME: need to handle this another way...shouldn't be loading up views from API!
	// 	/*$view = new \Hubzero\Plugin\View(array(
	// 		'folder'  => 'courses',
	// 		'element' => 'outline',
	// 		'name'    => 'outline',
	// 		'layout'  => '_prerequisites'
	// 	));

	// 	$view->set('scope', 'unit')
	// 	     ->set('scope_id', $unit->get('id'))
	// 	     ->set('section_id', $this->course->offering()->section()->get('id'))
	// 	     ->set('items', clone($this->course->offering()->units()));*/

		echo json_encode(array(
			'unit_id'        => $unit->get('id'),
			'unit_title'     => $unit->get('title'),
			'course_id'      => $course_id,
			'assetgroups'    => $assetGroups,
			'course_alias'   => $course->get('alias'),
			'offering_alias' => $offering_alias,
			'section_id'     => (isset($section_id) ? $section_id : $course->offering()->section()->get('id')),
			'prerequisites'  => ''//$view->loadTemplate()
		));
		exit();
	}
}
