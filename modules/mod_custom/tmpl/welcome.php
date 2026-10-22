<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

if ($params->get('backgroundimage'))
{
	$this->css('
	.custom' . $moduleclass_sfx . ' {
		background-image: url(' . $params->get('backgroundimage') . ');
	}
	');
}
?>
<div class="custom<?php echo $moduleclass_sfx ?>">
	<?php echo $module->content; ?>
    <?php if (!User::isGuest()): ?>
        <div id="welcome-button" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <a href="/about" class="btn">
                <?php echo Lang::txt('MOD_LEARN_MORE_ABOUT_HSS') ?>
            </a>
        </div>
    <?php else: ?>
        <div id="welcome-button" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <a href="/register" class="btn">
                <?php echo Lang::txt('MOD_REGISTER') ?>
            </a>
            <a href="/about" class="btn">
                <?php echo Lang::txt('MOD_LEARN_MORE_ABOUT_HSS') ?>
            </a>
        </div>
    <?php endif; ?>
</div>
