<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined('_HZEXEC_') or die();

$versionLabel 	= $this->pub->get('version_label', '1.0');
$versionAlias 	= $this->pub->versionAlias();
$status 	  	= $this->pub->getStatusName();
$active 		= $this->active ? $this->active : 'status';
$activenum 		= $this->activenum ? $this->activenum : null;

// Are we in draft flow?
$move = ($this->pub->isDev() || $this->pub->isReady()) ? '&move=continue' : '';

$i = 1;

?>
<?php if ($this->pub->id) { ?>
	<p id="version-label" class="version-label<?php if ($active == 'status') { echo ' active'; } ?><?php if ($this->pub->state == 5 || $this->pub->state == 0) { echo ' nobar'; } ?>">
		<a href="<?php echo Route::url( $this->pub->link('edit') . '&action=versions'); ?>" class="versions" id="v-picker"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS'); ?></a> &raquo;
		<a href="<?php echo Route::url($this->pub->link('editversion')); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $versionLabel . ' (' . $status . ')'; ?></a>
		<?php if (($this->pub->state == 3 || $this->pub->state == 7) && $this->pub->curation('complete') && $this->pub->project()->access('content')) { ?>
		- <a href="<?php echo Route::url( $this->pub->link('editversion') . '&action=review'); ?>" class="readytosubmit"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DRAFT_READY_TO_SUBMIT'); ?></a>
		<?php } ?>
    </p>
<?php } ?>

<?php
	// No bar when unpublished or pending review (or no editing access)
	if ($this->pub->state == 5 || $this->pub->state == 0 || !$this->pub->project()->access('content'))
	{
		return false;
	}
?>
	<ul id="status-bar" <?php if ($move) { echo 'class="moving"'; } ?>>
		<?php foreach ($this->pub->curation('blocks') as $blockId => $block ) {

			$blockname = $block->name;
			$status    = $block->review && $block->review->status != 2
						? $block->review->status : $block->status->status;
			$updated   = $block->review && $this->pub->state != 1 ? $block->review->lastupdate : null;

			if ($updated && $block->review->status != 2)
			{
				$class = 'c_wip';
			}
			elseif ($status == 2 || $status == 3)
			{
				$class = 'c_incomplete';
			}
			else
			{
				$class = ($status > 0 || $this->pub->state == 1) ? 'c_passed' : 'c_failed';
			}

			$isComing = $this->pub->_curationModel->isBlockComing($blockname, $blockId, $activenum);
			if (($move && $isComing) && $this->active)
			{
				$class = 'c_pending';
			}

			if ($blockId == $activenum)
			{
				$class = '';
			}
			$i++;

			// Hide review block until in review
			if ($blockname == 'review' && ($blockId != $activenum))
			{
				continue;
			}
		?>
		<li<?php if ($blockId == $activenum) { echo ' class="active"'; } ?>>
			<a href="<?php echo Route::url( $this->pub->link('editversion') . '&section=' . $blockname . '&step=' . $blockId . '&move=continue'); ?>" <?php echo $class ? 'class="' . $class . '" onclick="clickNextBtn(event, this.href);"' : 'onclick="clickNextBtn(event, this.href);"'; ?>><?php echo $block->manifest->label; ?></a>
		</li>
	<?php } ?>
	</ul>

<script>

// Function to make the status bar in publishing save form
function clickNextBtn(e, href) {
    e.preventDefault();
    // Serialize the form data
    var formData = $('#plg-form').serialize();
    // Make an AJAX request to submit the form data
    $.ajax({
        type: 'POST', // or 'GET' depending on your form method
        url: $('#plg-form').attr('action'),
        data: formData,
        success: function(response) {
            window.location.href = href;
        },
        error: function(xhr, status, error) {
            // Handle errors if needed
            console.error(error);
        }
    });
}
</script>