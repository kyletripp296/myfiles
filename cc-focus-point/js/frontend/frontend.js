(function($){
	$(document).on('ready',function(){
		var img = $('img.focused-image');
		$.each(img,function(){
			$(this).wrap('<div class="focused-image-container"></div>');
			var h = $(this).parent().parent().outerHeight();
			var w = $(this).parent().parent().width();
			if(!h){
				h = $(this).parent().width();
			}
			if(!w){
				w = $(this).parent().height();
			}
			$(this).parent().css('padding-bottom',(100*(h/w))+'%');
		});
	});
}(jQuery));