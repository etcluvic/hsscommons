<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$route = 'index.php?option=com_publications&task=submit';
$url   = Route::url($route . '&pid=' . $this->pub->id);

$this->css()
	->js()
	->css('provisioned')
	->js('setup');

?>
<div id="project-wrap">
	<section class="main section">
		<header id="content-header">
			<h2 style="color: #000;"><?php echo $this->title; ?></h2>
		</header>

		<h3 class="prov-header"><a href="<?php echo $route; ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo $url; ?>"> "<?php echo \Hubzero\Utility\Str::truncate($this->pub->title, 65); ?>"</a> &raquo; <?php echo Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT'); ?></h3>

		<?php
			// Display status message
			$this->view('_statusmsg', 'projects')
			     ->set('error', $this->getError())
			     ->set('msg', $this->msg)
			     ->display();
		?>

		<div id="activate-intro">
			<div class="grid">
				<div class="col span6 first">
					<h3><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_WHAT_YOU_GET'); ?></h3>
					<ul id="activate-features">
						<li id="feature-files">
							<span class="ima">&nbsp;</span>
							<span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_REPOSITORY'); ?></span>
						</li>
						<li id="feature-todo">
							<span class="ima">&nbsp;</span>
							<span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_TODO'); ?></span>
						</li>
						<li id="feature-wiki">
							<span class="ima">&nbsp;</span>
							<span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_WIKI'); ?></span>
						</li>
						<li id="andmore">
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=features'); ?>"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_AND_MORE'); ?></a>
						</li>
					</ul>
				</div>
				<div class="col span6 omega">
					<div id="activate-body">
						<h3><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_YOUR_NEW_PROJECT'); ?></h3>
						<form action="<?php echo Route::url('index.php?option=com_projects&alias=' . $this->model->get('alias') . '&task=activate'); ?>" method="post" id="activate-form" enctype="multipart/form-data">
							<fieldset>
								<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" id="projectid" />
								<input type="hidden" name="task" value="activate" />
								<input type="hidden" name="confirm" value="1" />
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="verified" id="verified" value="<?php echo $this->verified; ?>" />
								<input type="hidden" name="pubid" value="<?php echo $this->pub->id; ?>" />
							</fieldset>
							<div id="activate-summary">
								<p>
									<span class="activate-label">Publication:</span>
									<span class="prominent"><?php echo $this->pub->title; ?></span>
								</p>
								<p>
									<span class="activate-label"><?php echo Lang::txt('COM_PROJECTS_TEAM'); ?>:</span> <?php echo $this->team; ?>
								</p>
							</div>
							<fieldset>
								<label for="field-title">
									<span class="pub-info-pop tooltips" title="<?php echo Lang::txt('COM_PROJECTS_PROJECT_TITLE') . ' :: ' . Lang::txt('COM_PROJECTS_HINTS_TITLE'); ?>">&nbsp;</span>
									<?php echo Lang::txt('COM_PROJECTS_PROJECT_TITLE'); ?>
									<input name="title" id="field-title" maxlength="250" type="text" value="<?php echo $this->pub->title; ?>" class="verifyme long" />
								</label>

								<label for="field-alias">
									<span class="pub-info-pop tooltips" title="<?php echo Lang::txt('COM_PROJECTS_CHOOSE_ALIAS') . '::' . Lang::txt('COM_PROJECTS_HINTS_NAME'); ?>">&nbsp;</span>
									<?php echo Lang::txt('COM_PROJECTS_ALIAS_NAME'); ?>
									<span class="verification"></span>
									<input name="new-alias" id="field-alias" maxlength="30" type="text" value="<?php echo $this->suggested; ?>" class="verifyme long" />
								</label>
								<p class="submitarea">
									<input type="submit" id="b-continue" class="btn btn-primary active" value="<?php echo Lang::txt('COM_PROJECTS_ACTIVATE_CREATE_A_PROJECT'); ?>" />
									<span style="margin-top: 0;" class="btn btn-cancel"><a href="<?php echo $url; ?>"><?php echo "Cancel"; ?></a></span>
								</p>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div><!-- / #introduction.section -->
		<div class="clear"></div>
	</section><!-- / .main section -->
</div>