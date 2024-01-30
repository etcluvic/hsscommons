<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<?php if ($this->name === 'copy-url') { ?>
	<?php if ($this->publication->version->doi) { ?>
		<span id="copy-url-btn" data-doi="<?php echo $this->publication->version->doi; ?>"><?php echo Lang::txt("PLG_PUBLICATION_SHARE_COPY_DOI"); ?></span>
	<?php } else { ?>
		<span id="copy-url-btn" data-doi=""><?php echo Lang::txt("PLG_PUBLICATION_SHARE_COPY_URL"); ?></span>
	<?php } ?>
<?php } else { ?>
	<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=share&v=' . $this->publication->version_number . '&sharewith=' . strtolower($this->name)); ?>" title="<?php echo Lang::txt('PLG_PUBLICATION_SHARE_ON', ucfirst($this->name)); ?>" class="popup" rel="external"><span class="share_<?php echo strtolower($this->name);  ?>"><span><?php echo Lang::txt('PLG_PUBLICATIONS_SHARE_' . strtoupper($this->name)); ?></span></span></a>
<?php } ?>