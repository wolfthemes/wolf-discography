;( function( $ ) {

	'use strict';

	$('.wd-repeatable-add').on( 'click', function() {
		var field = $(this).closest('td').find('.wd-custom_repeatable li:last').clone(true),
			fieldLocation = $(this).closest('td').find('.wd-custom_repeatable li:last');
		$('input', field).val('').attr('name', function(index, name) {
			return name.replace(/(\d+)/, function(fullMatch, n) {
				return Number(n) + 1;
			});
		})
		field.insertAfter(fieldLocation, $(this).closest('td'))
		return false;
	});
	
	$('.wd-repeatable-remove').on( 'click', function(){
		var field = $('.wd-custom_repeatable li');
		if(field.length>1){
  			$(this).parent().remove();
  		}
		return false;
	});
		
	$('.wd-custom_repeatable').sortable( {
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.sort'
	} );

} )( jQuery );