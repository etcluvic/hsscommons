<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('changepassword.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="member btn" href="<?php echo Route::url('index.php?option='.$this->option.'&id='.$this->profile->get('id')); ?>"><?php echo Lang::txt('COM_MEMBERS_MYACCOUNT'); ?></a></li>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="error" id="errors"><?php echo $this->getError(); ?> </p>
		<?php } else { ?>
			<p id="errors"></p>
		<?php } ?>

		<form action="<?php echo Route::url($this->profile->link() . '&task=changepassword', true); ?>" method="post" id="hubForm">
			<!--div class="explaination">
				<p><?php echo Lang::txt('COM_MEMBERS_CHANGEPASSWORD_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_MEMBERS_PASSWORD_IF_FORGOTTEN_RESET', Route::url('index.php?option=com_users&view=login&task=logout&return=' . base64_encode('/members/reset'))); ?></p>
			</div-->
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_CHANGEPASSWORD_CHOOSE'); ?></legend>

				<label<?php echo ($this->change && $this->oldpass && !\Hubzero\User\Password::passwordMatches($this->profile->get('id'), $this->oldpass, true)) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo Lang::txt('COM_MEMBERS_FIELD_CURRENT_PASS'); ?>
					<input name="oldpass" id="oldpass" type="password" value="" />
				</label>
				<?php
				if ($this->change && !$this->oldpass)
				{
					echo '<p class="error">' . Lang::txt('COM_MEMBERS_PASS_BLANK') . '</p>';
				}
				if ($this->change && $this->oldpass && !\Hubzero\User\Password::passwordMatches($this->profile->get('id'), $this->oldpass, true))
				{
					echo '<p class="error">' . Lang::txt('COM_MEMBERS_PASS_INCORRECT') . '</p>';
				}
				?>

				<div class="grid">
					<div class="col span6">
						<label<?php echo ($this->change && (!$this->newpass || $this->newpass != $this->newpass2)) ? ' class="fieldWithErrors"' : ''; ?>>
							<?php echo Lang::txt('COM_MEMBERS_FIELD_NEW_PASS'); ?>
							<input name="newpass" id="newpass" type="password" value="" />
							<?php
							if ($this->change && !$this->newpass)
							{
								echo '<span class="error">' . Lang::txt('COM_MEMBERS_PASS_BLANK') . '</span>';
							}
							?>
						</label>
					</div>
					<div class="col span6 omega">
						<label<?php echo ($this->change && (!$this->newpass2 || $this->newpass != $this->newpass2)) ? ' class="fieldWithErrors"' : ''; ?>>
							<?php echo Lang::txt('COM_MEMBERS_FIELD_PASS_CONFIRM'); ?>
							<input name="newpass2" id="newpass2" type="password" value="" />
							<?php
							if ($this->change && !$this->newpass2)
							{
								echo '<span class="error">' . Lang::txt('COM_MEMBERS_PASS_MUST_CONFIRM') . '</span>';
							}
							if ($this->change && $this->newpass && $this->newpass2 && ($this->newpass != $this->newpass2))
							{
								echo '<span class="error">' . Lang::txt('COM_MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH') . '</span>';
							}
							?>
						</label>
					</div>
				</div>
				<?php
				if (count($this->password_rules) > 0)
				{
					echo "\t\t<ul id=\"passrules\">\n";
					foreach ($this->password_rules as $rule)
					{
						if (!empty($rule))
						{
							if (is_array($this->validated))
							{
								$err = in_array($rule, $this->validated);
							}
							else
							{
								$err = '';
							}

							$mclass = ($err)  ? ' class="error"' : ' class="empty"';
							echo "\t\t\t<li $mclass>".$rule."</li>\n";
						}
					}
					if (is_array($this->validated))
					{
						foreach ($this->validated as $msg)
						{
							if (!in_array($msg, $this->password_rules))
							{
								echo "\t\t\t".'<li class="error">'.$msg."</li>\n";
							}
						}
					}
					echo "\t\t\t</ul>\n";
				}
			?>
			</fieldset><div class="clear"></div>

			<p class="submit">
				<?php echo Html::input('token'); ?>
				<input type="hidden" id="pass_no_html" name="no_html" value="0" />
				<input type="hidden" name="change" value="1" />
				<input class="btn btn-success" name="submit" id="password-change-save" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_CHANGEPASSWORD'); ?>" />
			</p>
		</form>
	</div>
</section><!-- / .main section -->
