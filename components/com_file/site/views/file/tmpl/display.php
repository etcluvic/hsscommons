<?php
// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

$this->css();
?>
<div class="file-uploader">
    <p><?php if (isset($this->fileName)) {
        echo $this->fileName;
    }?></p>
    <p><?php if (isset($this->fileSize)) {
        echo $this->fileSize;
    }?></p>
    <div class="uploaded-files">
        <h2>Uploaded files:</h2>
        <?php foreach ($this->files as $file) {
            echo "<p>$file</p>";
        }?>
    </div>
    <form action="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;task=upload&amp;tmpl=component" method="post" enctype="multipart/form-data">
        <p><input type="file" name="upload"></p>
        <p><input type="submit" class="btn" value="<?php echo Lang::txt('COM_FILE_UPLOAD'); ?>"></p>
        <?php echo Html::input('token'); ?>
    </form>
    <?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>