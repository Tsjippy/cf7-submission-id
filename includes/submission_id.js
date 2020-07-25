jQuery(document).ready(function($) {
    document.addEventListener( 'wpcf7submit', function( event ) {
		// if validation is not failed
		if(event.detail.status != 'validation_failed'){
			//Form is submitted and validated ok, make AJAX call to update the ID
			var formid = event.detail.contactFormId;
			var options   = {
				url :cf7_submission_id_object.ajax_url,
				method: "POST",
				data: {
					"action"	: "update_cf7_submission_id",
					"formid"	: formid,
				},
			}
			//Actual AJAX call
			$.ajax(options).done(function(success) {
				//update the id field for visible fields
				$(".wpcf7-submission_id").each( function() {
					$(this).val(success);
				});
				//update the id field for invisible fields
				$(".wpcf7-submission_id_hidden").each( function() {
					$(this).val(success);
				});
			}).fail(function(err) {
				console.log(arguments);
			});
		}
	})
});