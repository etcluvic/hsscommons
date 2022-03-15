<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = 'even';

$this->css('usage', 'com_usage');
?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_USAGE'); ?></h3>

<p class="info"><?php echo Lang::txt('PLG_MEMBERS_USAGE_EXPLANATION'); ?></p>

<div id="statistics">
	<table class="data">
		<caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS'); ?>:</th>
				<td><?php echo $this->contribution['contribs']; ?></td>
			</tr>
		<?php /*if ($this->total_tool_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_TOOLS'); ?>:</th>
				<td><?php echo number_format($this->total_tool_users); ?></td>
			</tr>
		<?php } ?>
		<?php if ($this->total_andmore_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_ANDMORE'); ?>:</th>
				<td><?php echo number_format($this->total_andmore_users); ?></td>
			</tr>
		<?php }*/ ?>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK'); ?>:</th>
				<td><?php echo $this->rank; ?></td>
			</tr>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST'); ?>:</th>
				<td><?php echo $this->contribution['first']; ?></td>
			</tr>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST'); ?>:</th>
				<td><?php echo $this->contribution['last']; ?></td>
			</tr>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CITATIONS'); ?>:</th>
				<td><?php echo $this->citation_count; ?></td>
			</tr>
		<?php if ($this->cluster_users) { ?>
			<tr class="<?php
				$cls = ($cls == 'even') ? 'odd' : 'even';
				echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CLUSTERS'); ?>:</th>
				<td><?php echo Lang::txt('PLG_MEMBERS_USAGE_USERS_IN_COURSES_SERVED', number_format($this->cluster_users), number_format($this->cluster_classes), number_format($this->cluster_schools)); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p><?php echo Lang::txt('PLG_MEMBERS_USAGE_FOOTNOTE'); ?></p>
</div>
