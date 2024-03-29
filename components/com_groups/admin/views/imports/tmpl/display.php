<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Groups\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'), 'import');

if ($canDo->get('core.admin'))
{
	Toolbar::custom('sample', 'sample', 'sample', 'COM_GROUPS_IMPORT_SAMPLE', false);
	Toolbar::spacer();
	Toolbar::custom('run', 'script', 'script', 'COM_GROUPS_RUN');
	Toolbar::custom('runtest', 'runtest', 'script', 'COM_GROUPS_TEST_RUN');
	Toolbar::spacer();
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

$this->css('import');
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'imports') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'importhooks') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_HOOKS'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-6"><?php echo Html::grid('sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_GROUPS_IMPORT_DISPLAY_FIELD_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_NUMRECORDS'); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_GROUPS_IMPORT_DISPLAY_FIELD_CREATED', 'created_at', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_GROUPS_IMPORT_DISPLAY_FIELD_LASTRUN', 'ran_at', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php
					// Initiate paging
					echo $this->imports->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->imports->count() > 0) :
				$i = 0;
				?>
				<?php foreach ($this->imports as $import) : ?>
					<tr>
						<td>
							<?php if ($canDo->get('core.admin')) { ?>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $import->get('id'); ?>" class="checkbox-toggle" />
								<label for="cb<?php echo $i;?>" class="sr-only visually-hidden"><?php echo $import->get('id'); ?></label>
							<?php } ?>
						</td>
						<td class="priority-6">
							<?php echo $import->get('id'); ?>
						</td>
						<td>
							<?php if ($canDo->get('core.admin')) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $import->get('id')); ?>">
									<?php echo $this->escape($import->get('name')); ?>
								</a>
							<?php } else { ?>
								<?php echo $this->escape($import->get('name')); ?>
							<?php } ?>
							<br />
							<span class="hint">
								<?php echo nl2br($this->escape($import->get('notes'))); ?>
							</span>
						</td>
						<td class="priority-4">
							<?php echo $this->escape($import->get('count', 0)); ?>
						</td>
						<td class="priority-3">
							<strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON'); ?></strong>
							<time datetime="<?php echo $import->get('created_at'); ?>"><?php echo Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a'); ?></time><br />
							<strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY'); ?></strong>
							<?php
								if ($created_by = User::getInstance($import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
						<td>
							<?php
								$lastRun = $import->runs()
									->whereEquals('import_id', $import->get('id'))
									->whereEquals('dry_run', 0)
									->ordered()
									->row();
							?>
							<?php if ($lastRun->get('id')) : ?>
								<strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON'); ?></strong>
								<time datetime="<?php echo $import->get('ran_at'); ?>"><?php echo Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a'); ?></time><br />
								<strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY'); ?></strong>
								<?php
									if ($created_by = User::getInstance($lastRun->get('ran_by')))
									{
										echo $created_by->get('name');
									}
								?>
							<?php else: ?>
								n/a
							<?php endif; ?>
						</td>
						<td class="priority-4">
							<?php
								$runs = $import->runs()
									->whereEquals('import_id', $import->get('id'))
									->whereEquals('dry_run', 0)
									->total();

								echo $runs;
							?>
						</td>
					</tr>
				<?php
					$i++;
				endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6"><?php echo Lang::txt('COM_GROUPS_IMPORT_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>