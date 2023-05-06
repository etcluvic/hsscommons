<?php
/**
 * Template for managing (add and edit) a single product
 */
$baselink = strtok($_SERVER['REQUEST_URI'], '?');

$this->css();
$this->js();
?>
<h1>Add a product</h1>
<form class="product-form" method="post" action="<?php  echo $baselink; ?>">
    <input type="text" name="title" placeholder="Short title" id="title">
    <input type="number" name="price" placeholder="Price" id="price">
    <textarea maxlength="500" name="description" rows="10" placeholder="Short description of the product" id="description"></textarea>
    <fieldset>
        <input type="hidden" name="plugintask" value="addproduct">
        <input type="hidden" name="method" value="post">
        <input type="hidden" name="id" value="<?php echo count($this->products) + 1 ?>">
    </fieldset>
    <input type="submit" value="Create">
    <a href="<?php echo $baselink ?>">Cancel</a>
</form>