(function($){
	var ptext = '<div class="primary-text"><span>'+ktpc.primary_text+'</span><a href="#">'+ktpc.make_text+'</a></div>';

	$(document).on('ready',function(){
		var $c = $('#categorydiv');
		if(!$c.length){
			return false;
		}
		$c.append('<input type="hidden" name="primary_category" value="'+ktpc.primary+'" />');
		var $i = $c.find('input[type="checkbox"]:checked');
		$.each($i,function(){
			var $l = $(this).parents('li');
			$l.append(ptext);
		});
		setPrimaryCategory();
	});

	$(document).on('click','#categorydiv input[type="checkbox"]',function(){
		var $l = $(this).parents('li');
		var $c = $('#categorydiv input[type="checkbox"]:checked');
		var c = $(this).is(':checked');
		if(c){
			$l.append(ptext);
			//if this is the only checked category, it becomes primary
			if($c.length==1){
				var id = $c[0].getAttribute(value);
				updatePrimaryCategory(id);
			}
		} else {
			var $p = $l.find('.primary-text');
			var p = $p.hasClass('is-primary');
			$p.remove();
			//primary category gets unchecked, set first active category to primary
			if(p && $c.length){
				var id = $c[0].getAttribute('value');
				updatePrimaryCategory(id);
			}
		}
	});

	$(document).on('click','#categorydiv .primary-text a',function(e){
		e.preventDefault();
		var category_id = $(this).parents('li').find('input').val();
		updatePrimaryCategory(category_id);
	});

	function updatePrimaryCategory(category_id){
		$('input[name="primary_category"]').val(category_id);
		setPrimaryCategory();
	}

	function setPrimaryCategory(){
		var id = $('input[name="primary_category"]').val();
		var $i = $('#categorydiv input[type="checkbox"][value="'+id+'"]');
		$('#categorydiv .is-primary').removeClass('is-primary');
		$.each($i,function(){
			var $l = $(this).parents('li');
			$l.find('.primary-text').addClass('is-primary');
		});
	}

}(jQuery));