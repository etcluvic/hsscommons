<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

/**
 *
 * Modified by CANARIE Inc. for the HSSCommons project.
 *
 * Summary of changes: Written by CANARIE Inc. Based on HUBzero's Plugin of plg_members_impact, with implicit permission under original MIT licence.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('publications.css', 'plg_projects_publications');

function getStatus($status = null)
{
		switch ($status)
		{
			case 0:
				$name = 'unpublished';
				break;
			case 1:
				$name = 'published';
				break;
			case 3:
			default:
				$name = 'draft';
				break;
			case 4:
				$name = 'ready';
				break;
			case 5:
				$name = 'pending';
				break;
			case 7:
				$name = 'wip';
				break;
		}
		return $name;
}
?>
<div class="mypubs">

<?php if ($this->pubstats) {
?>

	<h2><?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_TITLE'); ?></h2>
	<table>
		<thead>
		<tr>
		        <th>
                                <?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_PUBTITLE'); ?>
                        </th>
                        <th>
                               <?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_CREATED'); ?>
                        </th>
			<th>
				<?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_VERSION'); ?>	
			</th>
	                <th>
                                <?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_STATUS'); ?>
                        </th>	
		</tr>
		</thead>
	<?php foreach ($this->pubstats as $stat) { ?>
		<tr>
		<td>
			<span><a href="<?php echo Route::url('index.php?option=com_publications' . '&id=' . $stat->publication_id) . '?version=' . $stat->version_number; ?>"><?php echo $stat->title; ?></a></span>
		</td>
                <td>
                         <span class="block mini faded"><?php echo Date::of($stat->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
               </td>
               <td>
			 <span> <?php echo $stat->version_label; ?> </span></span>
               </td>	      
               <td>
                       	<span class="<?php echo getStatus($stat->state); ?> major_status"><?php echo getStatus($stat->state); ?></span>
               </td> 
	       </tr>	
	<?php } ?>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_MEMBERS_REPOSITORY_STATS_NO_INFO'); ?></p>
<?php } ?>

</div>
