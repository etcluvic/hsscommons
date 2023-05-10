<?php
// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

$this->css();
?>
<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <div class="file-uploader">
                <p><?php if (isset($this->fileName)) {
                    echo $this->fileName;
                }?></p>
                <p><?php if (isset($this->fileSize)) {
                    echo $this->fileSize;
                }?></p>
                <form action="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;task=upload" method="post" enctype="multipart/form-data">
                    <p><input type="file" name="upload"></p>
                    <p><input type="submit" class="btn" value="<?php echo Lang::txt('COM_FILE_UPLOAD'); ?>"></p>
                    <?php echo Html::input('token'); ?>
                </form>
            </div>
        </div>
    </div>

    <?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</section>