jQuery(function($){

/*-----------------------------------------------------------------------------------*/
/*	Sortable
/*-----------------------------------------------------------------------------------*/

	$( '.wd-repeatable-add' ).click( function() {
		field = $(this).closest('td').find('.wd-custom-repeatable li:last').clone(true);
		fieldLocation = $(this).closest('td').find('.wd-custom-repeatable li:last');
		$('input', field).val('').attr('name', function(index, name) {
			return name.replace(/(\d+)/, function(fullMatch, n) {
				return Number(n) + 1;
			});
		})
		field.insertAfter(fieldLocation, $(this).closest('td'))
		return false;
	} );
	
	$('.wd-repeatable-remove').click(function(){
		field = $('.wd-custom-repeatable li');
		if(field.length>1){
  			$(this).parent().remove();
  		}
		return false;
	} );
		
	$('.wd-custom-repeatable').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.sort'
	} );

} );