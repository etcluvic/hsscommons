<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Publications\Models\Orm\Publication;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for a publication
 */
class Publications extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'publication';

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		if ($as == 'title')
		{
			return Lang::txt('Publication');
		}
		return parent::type($as);
	}

	/**
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_publications')
		{
			return false;
		}

		if (!Request::getString('id'))
		{
			return false;
		}

		if (!Request::getString('v'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		$id = ($id ?: Request::getInt('id', 0));

		$pub = null;

		if (!$id)
		{
			$this->setError(Lang::txt('Publication id is not provided.'));
			return false;
		}

		if (!$pub = Publication::one($id)) {
			$this->setError(Lang::txt('Publication is not founded.'));
			return false;
		}

		$version = $pub->getActiveVersion();
		
		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $version->get('id'))
		     ->set('created', $version->get('created'))
		     ->set('created_by', $version->get('created_by'))
		     ->set('title', $version->get('title'))
		     ->set('description', \Hubzero\Utility\Str::truncate(strip_tags($version->description), 200))
		     ->set('url', Route::url($version->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
