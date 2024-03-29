<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? $this->_params->get('icons_limit') : 0;

$popup = '<ol class="sharelinks">';
$title = Lang::txt('PLG_PUBLICATION_SHARE_VIEWING', Config::get('sitename'), stripslashes($this->publication->title));
$metadata  = '<div class="share" style="display: flex; justify-content: start;">'."\n";
$metadata .= "\t".Lang::txt('PLG_PUBLICATION_SHARE').': ';
$metadata .= '<div class="icon-wrapper" style="display: flex; justify-content: space-evenly; width: 100%; margin-top:3px; flex-wrap: wrap;">';

// Available options
$sharing = array('facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'delicious', 'reddit', 'email', 'copy-url');

foreach ($sharing as $shared)
{
	if ($this->_params->get('share_' . $shared, 1) == 1)
	{
		// Show activity
		$link = $this->view('_item')
			->set('option', $this->option)
			->set('publication', $this->publication)
			->set('name', $shared)
			->loadTemplate();

		$metadata .= (!$limit || $i <= $limit) ? $link : '';
		$popup 	  .= '<li class="';
		$popup 	  .= ($i % 2) ? 'odd' : 'even';
		$popup    .= '">'. $link . '</li>';
		$i++;
	}
}

// Pop up more
if ($limit > 0 && $i > $limit)
{
	$metadata .= '...';
}
$popup .= '</ol>';

// Show pop-up?
if ($limit > 0)
{
	$metadata .= '<dl class="shareinfo">'."\n";
	$metadata .= "\t".'<dd>'."\n";
	$metadata .= "\t\t".'<p>'."\n";
	$metadata .= "\t\t\t".Lang::txt('PLG_PUBLICATION_SHARE_RESOURCE')."\n";
	$metadata .= "\t\t".'</p>'."\n";
	$metadata .= "\t\t".'<div>'."\n";
	$metadata .= $popup;
	$metadata .= "\t\t".'</div>'."\n";
	$metadata .= "\t".'</dd>'."\n";
	$metadata .= '</dl>'."\n";
}
$metadata .= '</div></div>'."\n";

echo $metadata;
