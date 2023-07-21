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

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Are we in draft flow?
$move = ($this->showControls) ? 'continue' : '';

$title 	 = $move && $this->manifest->draftHeading ? $this->manifest->draftHeading : $this->manifest->title;
$tagline = isset($this->manifest->draftTagline) ? $this->manifest->draftTagline : null;

$activeEl = isset($this->master->props['showElement'])
			? $this->master->props['showElement'] : 0;
$element =  Request::getInt( 'el', $activeEl );

$isFirst = $this->pub->curation()->getFirstBlock() == $this->step ? true : false;

?>
<div id="pub-editor" class="pane-desc">
	<form action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post" id="plg-form" enctype="multipart/form-data">
	 	 <fieldset>
			<input type="hidden" name="id" value="<?php echo $this->pub->_project->get('id'); ?>" id="projectid" />
			<input type="hidden" name="version" id="version" value="<?php echo $this->pub->versionAlias; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" id="action" value="save" />
			<input type="hidden" name="complete" id="complete" value="<?php echo $complete; ?>" />
			<input type="hidden" name="required" id="required" value="<?php echo $required; ?>" />
			<input type="hidden" name="selections" id="selections" value="" />
			<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
			<input type="hidden" name="element" id="element" value="<?php echo $element; ?>" />
			<input type="hidden" name="next" id="next" value="" />
			<input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
			<input type="hidden" name="move" id="move" value="<?php echo $move; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->get('id'); ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->get('version_id'); ?>" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->pub->_project->isProvisioned() ? 1 : 0; ?>" />
		 </fieldset>
  		<div id="c-pane" class="columns">
			 <div class="c-inner draftflow">
			 			<a href="<?php echo Route::url($this->pub->link('editversion') . '&action=cancel'); ?>" class="icon-cancel" style="position: absolute; top: 50px; right: 70px; cursor: pointer;">Cancel draft</a>
						<h4><?php echo $title; ?></h4>
						<?php
							if ($tagline && $move)
							{ ?>
							<h5><?php echo $tagline; ?> <?php if ($this->manifest->about && !$this->pub->_project->isProvisioned()) { ?><a class="pub-info-pop more-content" href="#info-panel" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CLICK_TO_LEARN_MORE'); ?>">&nbsp;</a> <?php } ?></h5>
						<?php }
						?>
						<!-- Archie writes this block to handle pre-published publications -->
						<?php if ($this->active === 'content') { ?>
							<div class="prev-published-block">
								<p style="font-size: 110%;">Has this item been published previously?</p>
								<div style="display: flex; flex-direction: row; margin-top: 5px;">
									<input id="yes-prev-published" type="radio" class="prev-published-btn" name="published_previously" value="1" <?php if ($this->pub->version->forked_from == $this->pub->version->id) { ?>checked="checked"<?php } ?>>
									<label>Yes</label>
									<input id="no-prev-published" type="radio" class="prev-published-btn" name="published_previously" value="0" style="margin-left: 30px;" <?php if (!$this->pub->version->forked_from == $this->pub->version->id) { ?>checked="checked"<?php } else if ($this->pub->doi) { ?>disabled<?php } ?>>
									<label>No</label>
								</div>
								<div class="has-doi-block <?php echo ($this->pub->version->forked_from == $this->pub->version->id) ? "" : "hidden" ?>">
									<p style="font-size: 110%;">Does this item have a DOI (Digital Object Identifier)?<span style="margin-left: 15px;">(Not sure? <a href="https://hsscommons.ca/kb/resources/what-is-doi" style="text-decoration: underline;" target="_blank">Learn more</a>.)</span></p>
									<div style="display: flex; flex-direction: row; margin-top: 5px;">
										<input id="yes-doi" type="radio" class="prev-published-btn" name="has_doi" value="1" <?php if ($this->pub->doi) { ?>checked="checked"<?php } ?>>
										<label>Yes</label>
										<input id="no-doi" type="radio" class="prev-published-btn" name="has_doi" value="0" style="margin-left: 30px;" <?php if (!$this->pub->doi) { ?>checked="checked"<?php } else { ?>disabled<?php } ?>>
										<label>No</label>
									</div>
									<div class="prev-published-hint-block <?php echo (!$this->pub->doi) ? "" : "hidden" ?>" style="font-size: 90%;">
										<div style="padding: 10px; margin-top: 20px; margin-bottom: 20px;">
											<p>Please enter more information about your publication here and on the following pages to re-publish it in the Canadian HSS Commons Repository. On the “Notes” page, you can enter the publication’s original citation information.</p>
											<p>Would you like to give your previously published item a DOI? You can do that, too! On the final review page, simply select “Publish with DOI".</p>
										</div>
									</div>
								</div>
								<div class="retrieve-block <?php echo ($this->pub->doi) ? "" : "hidden" ?>" style="font-size: 90%;">
									<!-- <div style="margin-top: 15px; margin-bottom: 10px;">Retrieve information</div>
									<div>Graham: Add text to warn users about the disabling of the retrieval button after clicking it</div>
									<div>Enter this text later (for Alyssa and Ray)</div>
									<div>Enter a publication DOI.</div> -->
									<div style="padding: 10px; margin-top: 20px; margin-bottom: 20px;">
										<p>If your publication already has a Digital Object Identifier (DOI), please enter it below to automatically retrieve information about your publication.</p>
										<p>Note: once you click the "Retrieve" button below, the button will be disabled. If you entered the DOI incorrectly or need to start over, please click on the "Cancel draft" link in the top-right corner of this page.</p>
										<strong>Enter the publication's DOI in the following format: 10.12345/ABDC-1001</strong>
									</div>
									<input id="retrieve-doi" type="text" style="width: 50%;" placeholder="10.12345/ABDC-1001" <?php if ($this->pub->doi) { ?>value="<?php echo $this->pub->doi ?>" disabled<?php } ?>>
									<div style="margin-top: 10px;">
										<button id="retrieve-btn" class="btn" data-vid="<?php echo $this->pub->get('version_id'); ?>" <?php if ($this->pub->doi) { ?>disabled<?php } ?>>Retrieve</button>
									</div>
									<div id="retrieve-msg" class="hidden" style="font-weight: 500; margin-top: 10px;"></div>
								</div>
							</div>
						<?php } ?>
						<!---->
						<?php echo $this->content; ?>
						<div class="hidden">
							<div id="info-panel" class="full-content"><?php echo $this->manifest->about; ?></div>
						</div>

						<?php
						if ($this->active != 'review') { ?>
						<div class="submit-area <?php echo ($this->showControls == 2 || $this->showControls == 4) ? ' extended' : ''; ?>" id="submit-area">
							<?php if (!$isFirst && $this->showControls && $this->showControls != 3) { ?>
								<span class="button-wrapper bw-previous icon-prev">
									<input type="button" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_PREVIOUS'); ?>" id="c-previous" class="submitbutton btn icon-prev" />
								</span>
							<?php } ?>
							<?php if ($this->showControls == 4 || $this->showControls == 1) { ?>
							<span class="button-wrapper icon-apply">
								<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_APPLY_CHANGES'); ?>" id="c-apply" class="submitbutton btn icon-apply" />
							</span>
							<?php } ?>
							<?php if ($this->showControls && $this->showControls != 3) { ?>
							<span class="button-wrapper icon-next">
								<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="c-next" class="submitbutton btn icon-next" />
							</span>
							<?php } ?>
						</div>
					<?php } ?>
			 </div>
		</div>
	</form>
</div>

<div class="hidden">
	<div id="addnotice" class="addnotice">
		<form id="notice-form" name="noticeForm" action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post">
		 <fieldset>
			<input type="hidden" name="pid" value="<?php echo $this->pub->get('id'); ?>" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version->get('version_number'); ?>" />
			<input type="hidden" name="p" id="props" value="" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="dispute" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_TITLE'); ?></h5>
			<label>
				<span class="block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_LABEL'); ?></span>
				<textarea name="review" id="notice-review" rows="5" cols="10"></textarea>
			</label>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="notice-submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			</p>
		</form>
	</div>
</div>

<div class="hidden">
	<div id="skip-notice" class="addnotice">
		<form id="skip-notice-form" name="skipForm" action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post">
		 <fieldset>
			<input type="hidden" name="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
			<input type="hidden" name="p" id="skip-props" value="" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="skip" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_SKIP_TITLE'); ?></h5>
			<label>
				<span class="block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_SKIP_LABEL'); ?></span>
				<textarea name="review" id="skip-notice-review" rows="5" cols="10"></textarea>
			</label>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="skip-notice-submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			</p>
		</form>
	</div>
</div>
