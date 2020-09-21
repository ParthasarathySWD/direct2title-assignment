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
	
	$(window).resize(function() {
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
	});
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
	});
	$('.card-options-fullscreen').on('click', function(e){
		$($.fn.dataTable.tables( true ) ).css('width', '100%');
		$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
	});

});
