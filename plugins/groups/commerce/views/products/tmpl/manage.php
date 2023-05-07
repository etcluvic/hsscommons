<?php
/**
 * Template for managing (add and edit) a single product
 */
$baselink = strtok($_SERVER['REQUEST_URI'], '?');

$this->css();
$this->js();
?>
<div class="page-container">
    <h1><?php echo ($this->product) ? "Edit a product" : "Add a product"; ?></h1>
    <form class="product-form" method="post" action="<?php  echo $baselink; ?>">
        <input type="text" name="title" placeholder="Short title" id="title" value="<?php echo ($this->product) ? $this->product->title : ""; ?>" required>
        <input type="number" name="price" placeholder="Price" id="price" value="<?php echo ($this->product) ? $this->product->price : ""; ?>" required>
        <textarea maxlength="500" name="description" rows="10" placeholder="Short description of the product" id="description" required><?php echo ($this->product) ? $this->product->description : ""; ?></textarea>
        <fieldset>
            <input type="hidden" name="plugintask" value="manageproduct">
            <input type="hidden" name="method" value="post">
            <input type="hidden" name="id" value="<?php echo ($this->product) ? $this->product->id : count($this->products) + 1; ?>">
        </fieldset>
        <div class="btn-case">
            <input type="submit" value="<?php echo ($this->product) ? "Submit" : "Create"; ?>" class="btn">
            <a href="<?php echo $baselink ?>" class="btn">Cancel</a>
        </div>
    </form>
</div>