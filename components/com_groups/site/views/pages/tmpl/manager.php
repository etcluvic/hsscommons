<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.fancyselect.css', 'system')
     ->js('jquery.fancyselect', 'system')
     ->js('jquery.nestedsortable', 'system');

// has home override
$hasHomeOverride = false;
if (file_exists(PATH_APP . DS . $this->group->getBasePath() . DS . 'pages' . DS . 'overview.php'))
{
	$hasHomeOverride = true;
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-group group btn popup 1200x600" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component&path=/uploads'); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_UPLOAD_MANAGER'); ?>
				</a>
				<a class="icon-group group btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>

	<?php if ($this->group->isSuperGroup() && $hasHomeOverride) : ?>
		<p class="info"><?php echo Lang::txt('COM_GROUPS_PAGES_SUPER_GROUP_HAS_HOME_OVERRIDE'); ?></p>
	<?php endif; ?>

	<div class="group-page-manager">
		<ul class="tabs clearfix">
			<li><a data-tab="pages" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#pages'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGES'); ?></a></li>
			<li><a data-tab="categories" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#categories'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE_CATEGORIES'); ?></a></li>
			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<li><a data-tab="modules" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#modules'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_MODULES'); ?></a></li>
			<?php endif;?>
		</ul>

		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&task=pages'); ?>" method="post" id="hubForm" class="full">
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="pages" />

			<fieldset data-tab-content="pages">
				<?php
					$this->view('display')
					     ->set('group', $this->group)
					     ->set('categories', $this->categories)
					     ->set('pages', $this->pages)
					     ->set('config', $this->config)
					     ->set('search', isset($this->search) ? $this->search : '')
					     ->display();
				?>
			</fieldset>

			<fieldset data-tab-content="categories">
				<?php
					$this->view('display', 'categories')
					     ->set('group', $this->group)
					     ->set('categories', $this->categories)
					     ->display();
				?>
			</fieldset>

			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<fieldset data-tab-content="modules">
					<?php
						$this->view('display', 'modules')
						     ->set('group', $this->group)
						     ->set('modules', $this->modules)
						     ->display();
					?>
				</fieldset>
			<?php endif; ?>
		</form>
	</div>
</section>