<?php
/*
Display all products
*/
$baselink = strtok($_SERVER['REQUEST_URI'], '?');

$this->css();
// $this->js();
?>

<div class="modal-background d-none"></div>
<div class="modal d-none" id="delete-modal">
    <div class="modal-title">
        <h3>Confirm product deletion</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to delete this item?</p>
        <div class="btn-case">
            <div class="btn modal-close-btn">No</div>
            <a href="#" class="btn modal-submit-btn">Yes</a>
        </div>
    </div>
</div>
<a class="btn create-btn" href="<?php echo $baselink . "?plugintask=manageproduct" ?>">Create a product</a>
<div class="display-case">
    <?php foreach($this->products as $product) 
    { ?>
    <div class="product-container">
        <div class="menu">
            <a href="<?php echo $baselink . "?plugintask=manageproduct&id=" . $product->id; ?>" class="manage-btn">Edit</a>
            <div class="manage-btn modal-btn" data-modal="delete-modal" data-submit-link="<?php echo $baselink . "?plugintask=deleteproduct&id=" . $product->id; ?>" style="float: right; margin-left: 5px;">Delete</div>
        </div>
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
<script src="/app/plugins/groups/commerce/assets/js/commerce.js" type="text/javascript"></script>