<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

// $content = $this->row->description('parsed');
// $content = ($content ?: $item->description('parsed'));
$content = "<strong>Repository item description:</strong>";
$content .= $item->description('parsed');
$extraNote = $this->row->description('parsed');
$content .= ($extraNote ? "<strong>Notes:</strong> " . $extraNote : "");
?>
		<p class="link">
			<a href="<?php echo stripslashes($item->get('url')); ?>" rel="external nofollow noreferrer">
				<?php echo $this->escape(stripslashes($item->get('title', $item->get('url')))); ?>
			</a>
		</p>
<?php if ($content): ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php endif;
