console.log("Load commerce.js");

/* Modals */
// Close modal
$('.modal-close-btn').on("click", function() {
    const modal = $(this).closest('.modal');
    modal.addClass('d-none');
    $('.modal-background').addClass('d-none');
})

// Open modal
$('.modal-btn').on("click", function() {
    const modalId = $(this).data("modal");
    const submitLink = $(this).data("submit-link");
    $('.modal-background').removeClass('d-none');
    $('#' + modalId).removeClass('d-none');
    if (submitLink) {
        $('#' + modalId + ' a.modal-submit-btn').attr("href", submitLink);
    }
})