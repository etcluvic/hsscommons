<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('index.css');
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="section">
	<div class="tagline">
		<p><?php echo Lang::txt('COM_SUPPORT_TAGLINE'); ?></p>
	</div>

	<?php if (Component::isEnabled('com_kb')) { ?>
	<div class="about odd kb">
		<h3><a href="<?php echo Route::url('index.php?option=com_kb'); ?>"><?php echo Lang::txt('COM_SUPPORT_KNOWLEDGE_BASE'); ?></a></h3>
		<p><a href="<?php echo Route::url('index.php?option=com_kb'); ?>"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?></a> <?php echo Lang::txt('COM_SUPPORT_KNOWLEDGE_BASE_TAGLINE'); ?></p>
	</div>
	<?php } ?>

	<div class="about even report">
		<h3><a href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>"><?php echo Lang::txt('COM_SUPPORT_REPORT_PROBLEMS'); ?></a></h3>
		<p><a href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>"><?php echo Lang::txt('COM_SUPPORT_REPORT_PROBLEMS'); ?></a> <?php echo Lang::txt('COM_SUPPORT_REPORT_PROBLEMS_TAGLINE'); ?> <a href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>"><?php echo Lang::txt('COM_SUPPORT_TICKET_TRACKING_SYSTEM'); ?></a>. <?php echo Lang::txt('COM_SUPPORT_REPORT_PROBLEMS_GUARANTEE_RESPONSE'); ?></p>
	</div>

	<div class="about odd tickets">
		<h3><a href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>"><?php echo Lang::txt('COM_SUPPORT_TRACK_TICKETS'); ?></a></h3>
		<p><?php echo Lang::txt('COM_SUPPORT_TRACK_TICKETS_PART_ONE'); ?> <a href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>"><?php echo Lang::txt('COM_SUPPORT_TICKET_TRACKING_SYSTEM'); ?></a>? <?php echo Lang::txt('COM_SUPPORT_TRACK_TICKETS_PART_TWO'); ?></p>
	</div>
</section>
