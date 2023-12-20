/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 Electronic Textural Cultural Lab
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!HUB.Members) {
	HUB.Members = {};
}

//-------------------------------------------------------------
//	Members Repository
//-------------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Members.Repository = {
    jQuery: jq,

    initialize: function()
	{
        // ORCID publications modal functionalities
        HUB.Members.Repository.orcidPublicationsModal();
    },

    // ORCID publications modal functionalities
    orcidPublicationsModal: function()
    {
        // Only show the first 5 publications initially
        $('.pub-modal-item').hide();
        $('.orcid-pub-1').show();

        // Show ORCID publications import modal
        $('a#show-orcid-pub-btn').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	false
		});

        // Show ORCID publications export modal
        $('a.show-orcid-export-modal-btn').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	false
		});

        // Select/deselect an individual publication for importation on the modal
        let selectedPutCodes = [];
        $('.pub-modal .pub-modal-item').on("click", function(e) {
            const dataPutCode = $(this).data('putcode');
            if ($(this).hasClass('selected')) {
                selectedPutCodes = selectedPutCodes.filter(putCode => putCode != dataPutCode);
            } else {
                selectedPutCodes.push(dataPutCode);
            }
            $(this).toggleClass('selected');
            if (!$(e.target).is('.selected-checkbox')) {
                $(this).find('.selected-checkbox').prop('checked', (i, value) => !value);
            }
            $(this).closest('form.pub-modal-item-container').find('input.selected-putcodes-input').val(selectedPutCodes.join(','));
            
            // Enable/disable import button depeding on if there is any selected publication
            if (selectedPutCodes.length === 0) {
                $('#orcid-pub-modal-submit-btn').addClass('disabled');
            } else {
                $('#orcid-pub-modal-submit-btn').removeClass('disabled');
            }
        })

        // Select all publications for importation on the modal
        $('.pub-modal .select-all-btn').on("click", function() {

            selectedPutCodes = [];
            $('.pub-modal .pub-modal-item').each(function() {
                const dataPutCode = $(this).data('putcode');
                selectedPutCodes.push(dataPutCode);
                $(this).addClass('selected');
                $(this).find('.selected-checkbox').prop('checked', true);
            })
            $(this).closest('form.pub-modal-item-container').find('input.selected-putcodes-input').val(selectedPutCodes.join(','));
            $('#orcid-pub-modal-submit-btn').removeClass('disabled');
        })

        // Deselect all publications for importation on the modal
        $('.pub-modal .deselect-all-btn').on("click", function() {

            selectedPutCodes = [];
            $('.pub-modal .pub-modal-item').each(function() {
                $(this).removeClass('selected');
                $(this).find('.selected-checkbox').prop('checked', false);
            })
            $(this).closest('form.pub-modal-item-container').find('input.selected-putcodes-input').val('');
            $('#orcid-pub-modal-submit-btn').addClass('disabled');
        })

        // Show publications on a page when clicking on a page number on the paginator
        // $('.pub-modal-paginator-item').on("click", function() {
        //     if (!$(this).hasClass('selected')) {
        //         const pageNum = $(this).data('item');
            
        //         // Show the publications
        //         $('.pub-modal-item').hide();
        //         $('.orcid-pub-' + pageNum).show();

        //         // Display the selected page number differently
        //         $('.pub-modal-paginator-item.selected').removeClass('selected');
        //         $(this).addClass('selected');
        //     }
        // })

        // Navigate through pages when clicking on the page navigation arrows
        $('.pub-modal-paginator .page-navigator').on('click', function() {
            // Doesn't do anything if the button is disabled
            if ($(this).hasClass('disabled')) {
                return;
            }
            
            const paginator = $(this).closest('.pub-modal-paginator');
            const totalPages = paginator.data('total-pages');
            const currentPage = parseInt(paginator.find('#current-page').text());
            let nextPage = 0;

            // Move to the next page
            if ($(this).hasClass('next')) {
                nextPage = currentPage + 1;
                paginator.find('#current-page').text(nextPage);
                
                // Disable next button if reach the last page
                if (nextPage === parseInt(totalPages)) {
                    $(this).addClass('disabled');
                }

                // Enable the previous button if it is disabled
                const prevBtn = paginator.find('.previous');
                if (prevBtn.hasClass('disabled')) {
                    prevBtn.removeClass('disabled');
                }
            }

            // Move to the previous page
            if ($(this).hasClass('previous')) {
                nextPage = currentPage - 1;
                paginator.find('#current-page').text(nextPage);
                
                // Disable previous button if reach the first page
                if (nextPage === 1) {
                    $(this).addClass('disabled');
                }

                // Enable the next button if it is disabled
                const nextBtn = paginator.find('.next');
                if (nextBtn.hasClass('disabled')) {
                    nextBtn.removeClass('disabled');
                }
            }

            // Show the publications
            $('.pub-modal-item').hide();
            $('.orcid-pub-' + nextPage).show();
        })

        // Display information on ORCID export modal
        $('.show-orcid-export-modal-btn').on('click', function() {
            const pubId = $(this).data('pubid');
            
            // Send an ajax request to get the publication information
            $.ajax({
                url: window.location.href + '/getPubInfo?pubId=' + pubId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Get publication information successfully');
                    console.log(data);
                    const pubData = data.work;
                    // Display publication information on export modal
                    const container = $('#orcid-export-modal .information-container');
                    container.empty();
                    container.append('<h3>Work details</h3>');
                    container.append('<p><strong>Work type</strong>: ' + pubData.type + '</p>');
                    container.append('<p><strong>Work title</strong>: ' + pubData.title.title + '</p>');
                    container.append('<p><strong>Work subtitle</strong>: ' + pubData.title.subtitle + '</p>');
                    container.append('<p><strong>Work translated title</strong>: ' + pubData.title['translated-title'] + '</p>');
                    container.append('<p><strong>Journal title</strong>: ' + pubData['journal-title'] + '</p>');

                    const pubDate = pubData['publication-date'];
                    container.append('<p><strong>Publication date</strong>: ' + pubDate.year + '-' + pubDate.month + '-' + pubDate.day + '</p>');
                    container.append('<p><strong>Link</strong>: ' + pubData.url + '</p>');
                    
                    container.append('<h3>Citation</h3>');
                    container.append('<p><strong>Citation type</strong>: ' + pubData.citation['citation-type'] + '</p>');
                    container.append('<p><strong>Citation</strong>: ' + pubData.citation['citation-value'] + '</p>');
                    container.append('<p><strong>Citation description</strong>: ' + pubData['short-description'] + '</p>');
                
                    container.append('<h3>Work identifiers</h3>');
                    for(let i = 0; i < pubData['external-ids']['external-id'].length; i++) {
                        const externalId = pubData['external-ids']['external-id'][i];
                        container.append('<p><strong>Type</strong>: ' + externalId['external-id-type'] + '</p>');
                        container.append('<p><strong>Value</strong>: ' + externalId['external-id-value'] + '</p>');
                        container.append('<p><strong>Url</strong>: ' + externalId['external-id-url'] + '</p>');
                        container.append('<p><strong>Relationship</strong>: ' + externalId['external-id-relationship'] + '</p>');
                    }

                    container.append('<h3>Contributors</h3>');
                    for(let i = 0; i < pubData['contributors']['contributor'].length; i++) {
                        const contributor = pubData['contributors']['contributor'][i];
                        container.append('<p><strong>ORCID</strong>: ' + contributor['contributor-orcid'].path + '</p>');
                        container.append('<p><strong>Name</strong>: ' + contributor['credit-name'] + '</p>');
                        container.append('<p><strong>Role</strong>: ' + contributor['contributor-attributes']['contributor-role'] + '</p>');
                    }

                    container.append('<h3>Other information</h3>');
                    container.append('<p><strong>Language code</strong>: ' + pubData['language-code'] + '</p>');
                    container.append('<p><strong>Country</strong>: ' + pubData.country + '</p>');

                    container.append('<a href="' + window.location.href + '/getPubInfo?pubId=' + pubId + '&confirm=1' + '" class="btn">Export this publication to ORCID</a>')
                },
                error: function(error) {
                    console.log('Failed to get publication information');
                    console.log(error);
                }
            });
        });
    }
}

//-------------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Members.Repository.initialize();
});