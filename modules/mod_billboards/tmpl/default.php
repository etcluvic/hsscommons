<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>

<div class="slider">
	<div class="banner" id="<?php echo $this->collection; ?>">
		<?php foreach ($this->slides as $slide) : ?>
			<div class="slide" id="<?php echo $slide->alias; ?>">
				<div class="slide-overlay"></div>
				<div class="slide-content">
					<h3><?php echo $slide->header; ?></h3>
					<?php echo $slide->text; ?>
					<div class="<?php echo $slide->learn_more_location; ?>">
						<a class="<?php echo $slide->learn_more_class; ?>" href="<?php echo $slide->learn_more_target; ?>">
							<?php echo $slide->learn_more_text; ?>
						</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div <?php echo ($this->pager == 'null') ? '' : 'class="pager"'; ?> id="<?php echo $this->pager; ?>"></div>
</div>