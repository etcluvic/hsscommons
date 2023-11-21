<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

/**
 * Modified by CANARIE Inc. for the HSSCommons project.
 *
 * Summary of changes: Written by CANARIE Inc. Based on HUBzero's Module of mod_featuredresource, with implicit permission under original MIT licence.
 */

namespace Modules\Featuredpublication;

use Hubzero\Module\Module;
use Component;
use User;

/**
 * Module class for displaying a random featured publication
 */
class Helper extends Module
{
	/**
	 * Container for properties
	 *
	 * @var  array
	 */
	public $id = 0;

	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_publications') . DS . 'tables' . DS . 'publication.php');

		$database = \App::get('db');

		//Get the admin configured settings
		$filters = array(
			'limit'      => (int)trim($this->params->get('top_number')),
			'start'      => 0,
			'sortby'     => 'popularity',
			'tag'        => trim($this->params->get('tag')),
			'access'     => 'public'
		);

		$row = null;

		// Initiate a publication object
		$rr = new \Components\Publications\Tables\Publication($database);

		// Get records
		$rows = $rr->getRecords($filters, false);
		if (count($rows) > 0)
		{
			$row = $rows[rand(0, count($rows) - 1)];
		}

		$this->cls = trim($this->params->get('moduleclass_sfx'));
		$this->thumb = '';

		// Did we get any results?
		if ($row)
		{
			$config = Component::params('com_publications');

			// Resource
			$id = $row->id;

			include_once(Component::path('com_publications') . DS . 'helpers' . DS . 'html.php');
			
			$path = DS . trim($config->get('webpath', '/site/publications'), DS);
			$path = \Components\Publications\Helpers\Html::buildPubPath($row->id, 0, $path);

			$picture = $this->getImage($path);

			$thumb = $path . DS . $picture;

			if (!is_file(PATH_APP . $thumb))
			{
				$thumb = DS . trim($config->get('defaultpic'));
			}
			
			$this->id    = $id;
			$this->thumb = $thumb;
		}

		$this->row   = $row;

		require $this->getLayoutPath();
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Get a publication image
	 *
	 * @param   string  $path  Path to get publication image from
	 * @return  string
	 */
	private function getImage($path)
	{
		$d = @dir(PATH_APP . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(PATH_APP . $path . DS . $img_file)
				 && substr($entry, 0, 1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png#i", $img_file))
					{
						$images[] = $img_file;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

}

