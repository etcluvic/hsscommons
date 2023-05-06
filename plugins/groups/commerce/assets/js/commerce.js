console.log("Load commerce.js");

$('form.product-form').on("submit", function(e) {
    e.preventDefault();
    console.log("Creating a new product");
});