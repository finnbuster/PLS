// remap jQuery to $
(function($){})(window.jQuery);


/* trigger when page is ready */
$(document).ready(function (){
	
	$('.carousel-inner').children('.item').first().addClass('active');
	
	$('.carousel').carousel({
	
		interval: 4000,
		pause: 'hover'
	
	});

});


/* optional triggers

$(window).load(function() {
	
});

$(window).resize(function() {
	
});

*/