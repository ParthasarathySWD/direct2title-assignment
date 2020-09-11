$(document).ready(function() {
	$('.select2').select2();
	
	$('.upload-image-btn').on('click', function(){
		$('.upload-image-div').slideToggle();
	})
	$('.btn-preview-success').on('click', function(){
		$('.upload-preview').slideToggle();
	})

	$('.custom-datatable').DataTable({
		scrollX:true,
	});
});
