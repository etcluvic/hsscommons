<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = array('publication');

switch ($this->line->get('master_access')):
	case 1:
		$cls[] = 'registered';
		break;
	case 2:
		$cls[] = 'protected';
		break;
	case 3:
		$cls[] = 'private';
		break;
	case 0:
	default:
		$cls[] = 'public';
		break;
endswitch;

$info = array();
if ($this->thedate):
	$info[] = $this->thedate;
endif;

if (($this->line->category && !intval($this->filters['category']))):
	$info[] = $this->line->cat_name;
endif;

if ($this->authors && $this->params->get('show_authors')):
	$info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': ' . \Components\Publications\Helpers\Html::showContributors($this->authors, false, true);
endif;

if ($this->line->doi):
	$info[] = 'https://doi.org/' . $this->line->doi;
endif;

$moreClasses = '';
if (!$this->line->hasImage()):
	$moreClasses = ' generic';
endif;

$title = html_entity_decode($this->line->title);

$extras = Event::trigger('publications.onPublicationsList', array($this->line));
?>
<li class="<?php echo implode(' ', $cls); ?>">
	<div class="pub-thumb<?php echo $moreClasses; ?>">
		<img src="<?php echo Route::url($this->line->link('thumb')); ?>" alt="<?php echo $this->escape($this->line->title); ?>" />
	</div>
	<div class="pub-details">
		<p class="title">
			<a href="<?php echo Route::url($this->line->link()); ?>"><?php echo $this->escape($title); ?></a>
		</p>

		<?php if (!empty($extras)): ?>
			<?php echo implode("\n", $extras); ?>
		<?php endif; ?>

		<?php
		if ($this->params->get('show_ranking') && $this->config->get('show_ranking')):
			$ranking = round($this->line->get('master_ranking'), 1);

			$r = (10 * $ranking);

			$this->css('
				#rank-' . $this->line->get('id') . ' {
					width: ' . $r . '%;
				}
			');
			?>
			<div class="metadata">
				<dl class="rankinfo">
					<dt class="ranking">
						<span class="rank">
							<span class="rank-<?php echo $r; ?>" id="rank-<?php echo $this->line->get('id'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_THIS_HAS'); ?></span>
						</span><?php echo number_format($ranking, 1) . ' ' . Lang::txt('COM_PUBLICATIONS_RANKING'); ?>
					</dt>
					<dd>
						<p><?php echo Lang::txt('COM_PUBLICATIONS_RANKING_EXPLANATION'); ?></p>
						<div></div>
					</dd>
				</dl>
			</div>
			<?php
		elseif ($this->params->get('show_rating') && $this->config->get('show_rating')):
			switch ($this->line->get('master_rating')):
				case 0.5:
					$class = ' half-stars';
					break;
				case 1:
					$class = ' one-stars';
					break;
				case 1.5:
					$class = ' onehalf-stars';
					break;
				case 2:
					$class = ' two-stars';
					break;
				case 2.5:
					$class = ' twohalf-stars';
					break;
				case 3:
					$class = ' three-stars';
					break;
				case 3.5:
					$class = ' threehalf-stars';
					break;
				case 4:
					$class = ' four-stars';
					break;
				case 4.5:
					$class = ' fourhalf-stars';
					break;
				case 5:
					$class = ' five-stars';
					break;
				case 0:
				default:
					$class = ' no-stars';
					break;
			endswitch;

			if ($this->line->get('master_rating') > 5):
				$class = ' five-stars';
			endif;
			?>
			<div class="metadata">
				<p class="rating"><span title="<?php echo Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')); ?>" class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')); ?></span>&nbsp;</span></p>
			</div>
			<?php
		endif;
		?>

		<p class="details">
			<?php echo implode(' <span class="separator">|</span> ', $info); ?>
		</p>

		<p class="result-description">
			<?php
			$content = '';

			if ($this->line->get('abstract')):
				$content = $this->line->get('abstract');
			elseif ($this->line->get('description')):
				$content = $this->line->get('description');
			endif;

			echo \Hubzero\Utility\Str::truncate(stripslashes($content), 300);
			?>
		</p>
	</div>
</li>
