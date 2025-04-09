<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<span class="breadcrumbs<?php echo $moduleclass_sfx; ?> pathway<?php echo $moduleclass_sfx; ?>" aria-label="<?php echo Lang::txt('MOD_BREADCRUMBS'); ?>">
	<?php
	if ($params->get('showHere', 1))
	{
		echo '<span class="showHere">' . Lang::txt('MOD_BREADCRUMBS_HERE') . '</span>';
	}

	// Get rid of duplicated entries on trail including home page when using multilanguage
	$deduped = [];
	$lastItem = null;

	foreach ($list as $item) {
		if ($lastItem) {
			$sameName = $item->name === $lastItem->name;
			$sameOrNestedLink =
				!empty($item->link) && !empty($lastItem->link) &&
				(strpos($item->link, $lastItem->link) === 0 || strpos($lastItem->link, $item->link) === 0);

			if ($sameName && $sameOrNestedLink) {
				continue; // Skip duplicate-ish breadcrumb
			}
		}

		$deduped[] = $item;
		$lastItem = $item;
	}

	$list = $deduped;



	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Generate the trail
	foreach ($list as $key => $item) :
		// Make a link if not the last item in the breadcrumbs
		$show_last = $params->get('showLast', 1);
		if ($key != $last_item_key)
		{
			// Render all but last item - along with separator
			if (!empty($item->link))
			{
				echo '<a href="' . $item->link . '" class="pathway">' . html_entity_decode($item->name) . '</a>';
			}
			else
			{
				echo '<span>' . $item->name . '</span>';
			}

			if (($key != $penult_item_key) || $show_last)
			{
				echo ' <span class="sep">' . $separator . '</span> ';
			}

		}
		elseif ($show_last)
		{
			// Render last item if reqd.
			echo '<span aria-current="page">' . $item->name . '</span>';
		}
	endforeach;
	?>
</span>
