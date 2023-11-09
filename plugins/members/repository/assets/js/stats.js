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
        // Show ORCID publications modal
        $('a#show-orcid-pub-btn').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	false
		});

        // Select/deselect publications for importation on the modal
        let selectedPutCodes = [];
        $('.pub-modal .pub-modal-item').on("click", function() {
            const dataPutCode = $(this).data('putcode');
            if ($(this).hasClass('selected')) {
                selectedPutCodes = selectedPutCodes.filter(putCode => putCode != dataPutCode);
            } else {
                selectedPutCodes.push(dataPutCode);
            }
            $(this).toggleClass('selected');
            $(this).find('.pub-modal-item-selected-text').toggleClass('hidden');
            $(this).closest('form.pub-modal-item-container').find('input.selected-putcodes-input').val(selectedPutCodes.join(','))
        })
    }
}

//-------------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Members.Repository.initialize();
});