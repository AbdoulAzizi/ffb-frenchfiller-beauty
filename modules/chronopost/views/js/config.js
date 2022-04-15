/**
	* MODULE PRESTASHOP OFFICIEL CHRONOPOST
	* 
	* LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
	* LICENCE : Tous droits réservés, le droit d'auteur s'applique - COPIE ET REDISTRIBUTION INTERDITES
* SANS ACCORD EXPRES D'OXILEO
	*
	* @author    Oxileo SAS <contact@oxileo.eu>
	* @copyright 2001-2018 Oxileo SAS
	* @license   Proprietary - no redistribution without authorization
	*/

$( document ).ready(function() {
	$(".createCarrier").on('click', function(e) {
		e.preventDefault();
		var carrier = $(this).val();
		var contract = $('select[name=chronoparams\\['+ carrier +'\\]\\[account\\]]').val();

		$.ajax({
			url: path+'/async/createCarrier.php?shared_secret='+chronopost_secret+'&code='+carrier+'&contract='+contract,
			dataType: 'json'
		}).done( function( data ) { 
			if(!data['success']) {
				if (data['error']) {
					alert(data['error']);
					return
				}
				alert(failure_msg);
				return;
			}

            $('#createnewcarrier').attr('value', 'oui');

			alert(success_msg);
            window.location.href = window.location.href;
			return;
		});   
		return false;
	});
});
