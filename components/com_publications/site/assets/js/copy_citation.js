//-----------------------------
// Copy citation to clipboard
//-----------------------------

jQuery(document).ready(function ($) {
    $('.copy-citation').on('click', function () {
        // Get the target citation container
        var targetId = $(this).data('target');

        // Clone the container
        var fullContent = $('#' + targetId).clone();
        
        // Remove unwanted parts
        fullContent.find('.details').remove();
        var filteredContent = fullContent.text()
            .replace('Researchers should cite this work as follows:', '')
            .trim();

        // Use Clipboard API to copy the filtered content
        navigator.clipboard.writeText(filteredContent)
            .then(function () {
                alert('Citation copied to clipboard!');
            })
            .catch(function (err) {
                console.error('Error copying citation: ', err);
            });
    });
});
