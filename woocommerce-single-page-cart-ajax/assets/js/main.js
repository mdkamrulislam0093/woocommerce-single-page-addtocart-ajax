jQuery(document).ready(function($){
	$('body').on('click keyup keypress blur change', '.cart .qty', function(){
		console.log($(this));
		$(this).parents('.cart').find('.the_custom_atc').attr('data-quantity', $(this).val());
	});
});