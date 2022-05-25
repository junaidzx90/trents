jQuery(document).ready(function ($) {
	function loadLogo(inp, prev) {
		var imgfile, selectedFile;
		// If the frame already exists, re-open it.
		if (imgfile) {
			imgfile.open();
			return;
		}
		//Extend the wp.media object
		imgfile = wp.media.frames.file_frame = wp.media({
			title: 'Choose Logo',
			button: {
				text: 'Choose Logo'
			},
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		imgfile.on('select', function () {
			selectedFile = imgfile.state().get('selection').first().toJSON();
			inp.val(selectedFile.url);
			prev.attr('src', selectedFile.url);
		});

		//Open the uploader dialog
		imgfile.open();
	}
	$('#emaillogo').on("click", function (e) {
		e.preventDefault();
		loadLogo($('#profile_logo'), $('#profile_logo_show'));
	});

	$('#removeLogo').on("click", function (e) {
		e.preventDefault();
		$('#profile_logo').val("");
		$('#profile_logo_show').attr("src", "");
	});

	$("#reject_driver_profile_docs").on("click", function(){
		let driver_id = $(this).data("id");
		let data_type = $(this).data("type");

		if(confirm("The documents will be deleted permanently.")){
			$.ajax({
				type: "post",
				url: trents.ajaxurl,
				data: {
					action: "reject_driver_profile_docs",
					nonce: trents.nonce,
					driver_id: driver_id,
					data_type: data_type
				},
				beforeSend: function(){
					$("#reject_driver_profile_docs").text("Processing...");
					$("#reject_driver_profile_docs").prop("disabled", true);
				},
				dataType: "json",
				success: function (response) {
					setTimeout(() => {
						location.reload();
					}, 2000);
				}
			});
		}
	});

	$("#payment_paid").on("click", function(){
		if(confirm("Are you sure the payment is correct?")){
			let payment_id = $(this).data("id");
			$.ajax({
				type: "post",
				url: trents.ajaxurl,
				data: {
					action: "approve_payment",
					payment_id: payment_id,
					nonce: trents.nonce
				},
				beforeSend: function(){
					$("#payment_paid").text("Processing...");
					$("#payment_paid").prop("disabled", true);
				},
				dataType: "json",
				success: function (response) {
					location.reload();
				}
			});
		}
	});
});