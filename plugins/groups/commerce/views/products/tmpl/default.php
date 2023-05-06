<?php
/*
Display all products
*/
$baselink = strtok($_SERVER['REQUEST_URI'], '?');

$this->css();
$this->js();
?>

<a class="btn create-btn" href="<?php echo $baselink . "?plugintask=addproduct" ?>">Create a product</a>
<div class="display-case">
    <?php foreach($this->products as $product) 
    { ?>
    <div class="product-container">
        <p class="product-title"><?php echo $product->title; ?></p>
        <h2 class="product-price"><?php echo "$" . $product->price ?></h2>
        <p class="product-description"><?php 
        if ($product->description) {
            echo $product->description; 
        } else {
            echo "No description provided";
        }
        ?></p>
    </div>
    <?php } ?>
</div>