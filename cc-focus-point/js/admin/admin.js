(function($){
	var interval = false;
	var currentMousePos = { x: 0, y: 0 };
	var focusCoords = { x: "0", y: "0"};
	var dragging = false;

	/* callback to run smartcrop on initial file upload */
    $.extend( wp.Uploader.prototype, {
        success : function( file ){
            if(file.attributes.mime.match('image/')){
            	if(!$('#smartcrop').length){
            		$('<div id="smartcrop" style="display:none;"></div>').appendTo('body');
            	}
            	$('<img src="'+file.attributes.url+'" width="'+file.attributes.width+'" height="'+file.attributes.height+'" data-id="'+file.id+'" />').appendTo('#smartcrop');
            	interval = setInterval(function(){
            		var img = $('#smartcrop img')[0];
            		if(img.complete && img.naturalWidth !== 0){
            			clearInterval(interval);
            			doSmartcrop();
            		}
            	},100);
            }
        }
    });


    function doSmartcrop(){
    	$('#smartcrop img').each(function(){
    		var img = this;
			var id = img.getAttribute('data-id');
			var w = parseInt(img.getAttribute('width'));
			var h = parseInt(img.getAttribute('height'));
			var x = (w<h) ? w : h;
			var options = { width: x, height: x };
			smartcrop.crop(img, options, function(result) {
				var x = (100 * (result.topCrop.x + (result.topCrop.width/2))/w).toFixed(2);
				var y = (100 * (result.topCrop.y + (result.topCrop.height/2))/h).toFixed(2);
				saveCoords(id, x, y);
				$('#smartcrop img[data-id="'+id+'"]').remove();
			});
    	});
    }

	/* this fires any time we click to edit the image */
	$(document).on('click','input[id^="imgedit-open-btn"], .button.edit-attachment, a.edit-attachment',function(){
		startPolling();
	});

	/* this fires when we click Set Focus Point in the media library sidebar */
	$(document).on('click','.set-focus-point',function(e){
		e.preventDefault();
		var $f = $('.media-frame, .edit-attachment-frame');
		$f.addClass('hide-edit');
		var $e = $('.edit-attachment');
		if($e.length){
			$e[0].click();
		}
	});

	/* this fires when we click Set Focus Point in the acf gallery block */
	$(document).on('click','.acf-gallery-side .set-focus-point',function(e){
		e.preventDefault();
		//clicking the acf edit opens the media library edit
		$('.acf-gallery-edit')[0].click();
		//wait until .media-sidebar .set-focus-point exists then trigger click on it
		interval = setInterval(function(){
			if($('.media-sidebar .set-focus-point').length){
				clearInterval(interval);
				$('.media-sidebar .set-focus-point')[0].click();
			}
		},100);
	});

	/* this fires when we hit cancel on the edit image screen */
	$(document).on('click','.imgedit-cancel-btn',function(){
		$('.hide-edit').removeClass('hide-edit');
	});

	/* this fires when we click the set focus button on edit screens */
	$(document).on('click','.imgedit-focus',function(){
		$('.imgedit-panel-content').addClass('setfocus');
	});

	/* this fires when we click any edit image button that isnt set focus */
	$(document).on('click','.imgedit-menu > button:not(.imgedit-focus)',function(){
		$('.imgedit-panel-content').removeClass('setfocus');
	});

	/* this fires when we hit the save button on image edit screens */
	$(document).on('click','.imgedit-focus-submit-btn',function(){
		var post_id = $('.imgedit-wrap > div').attr('id').replace('imgedit-panel-','');
		saveCoords(post_id, focusCoords.x, focusCoords.y);
	});

	/* captures all mouse movement, if drag event is active update x/y coords */
	$(document).on('mousemove',function(event) {
		currentMousePos.x = event.pageX;
		currentMousePos.y = event.pageY;
		if(dragging){
			setCoords();
		}
	});

	/* captures drag event on focus point edit screen */
	$(document).on('mousedown','.focus-wrapper',function(e){
		if(e.which==1){ //only left mouse click
			setCoords();
			dragging = true;
		}
	});

	/* captures end of drag event on focus point edit screen */
	$(document).on('mouseup','.focus-wrapper',function(e){
		if(e.which==1){ //only left mouse click
			dragging = false;
		}
	});

	/* waits until a certain element is done loading then continues the script */
	function startPolling(){
		interval = setInterval(function(){
			if($('.imgedit-menu').length){
				stopPolling();
			}
		},100);
	}

	/* fires when certain element is done loading */
	function stopPolling(){
		clearInterval(interval);
		addFrontEndButton();
		getCoords();
	}

	/* adds a set focus button to the top of the edit screen */
	function addFrontEndButton(){
		if(!$('.imgedit-focus').length){
			var btn = '<button type="button" class="imgedit-focus button">Set Focus Point</button>';
			$(btn).prependTo('.imgedit-menu');
			$('<div class="focus-wrapper">').prependTo('.imgedit-crop-wrap');
			$('.imgedit-crop-wrap img').appendTo('.focus-wrapper');
			$('<div class="focus"></div>').appendTo('.focus-wrapper');
			$('<input type="button" class="button button-primary imgedit-focus-submit-btn" disabled="disabled" value="Save">').appendTo('.imgedit-panel-content .imgedit-submit');
			$('.focus-wrapper img').addClass('focused-image');
		}
	}

	/* gets x and y of current focus point on edit screens */
	function getCoords(){
		var post_id = $('.imgedit-wrap > div').attr('id').replace('imgedit-panel-','');
		var data = {
			action: 'get_coords',
			post_id: post_id,
		};
		$.post(ajaxURL,data,function(res){
			var response = JSON.parse(res);
			focusCoords.x = response.x;
			focusCoords.y = response.y;
			updateCoords();
			if($('.hide-edit').length){
				$('.imgedit-focus')[0].click();
			}
		});
	}

	/* sends ajax to save current x and y coords as image meta */
	function saveCoords(post_id, x, y){
		var data = {
			action: 'save_coords',
			post_id: post_id,
			x: x,
			y: y,
		}
		$.post(ajaxURL,data,function(res){
			var response = JSON.parse(res);
			disableSubmitButton();
		});
	}

	/* adds disable attribute to image edit submit button if exists */
	function disableSubmitButton(){
		var $b = $('.imgedit-focus-submit-btn');
		if($b.length){
			$b.attr('disabled','disabled');
		}
	}
	
	/* updates js variable with current x and y position */
	function setCoords(){
		var x = currentMousePos.x;
		var y = currentMousePos.y;
		var l = $('.focus-wrapper').offset().left;
		var t = $('.focus-wrapper').offset().top;
		var w = $('.focus-wrapper').width();
		var h = $('.focus-wrapper').height();

		focusCoords.x = (100*((x-l)/w)).toFixed(2);
		focusCoords.y = (100*((y-t)/h)).toFixed(2);
		
		if(focusCoords.x>100){
			focusCoords.x = 100;
		}
		if(focusCoords.x<0){
			focusCoords.x = 0;
		}
		if(focusCoords.y>100){
			focusCoords.y = 100;
		}
		if(focusCoords.y<0){
			focusCoords.y= 0;
		}

		$('.imgedit-focus-submit-btn').removeAttr('disabled');
		updateCoords();
	}

	/* updates css to show the x y coords on edit screens */
	function updateCoords(){
		$('.focus-wrapper .focus').css({
			left: focusCoords.x+'%',
			top: focusCoords.y+'%',
		});
	}
}(jQuery));