

(function($){

	// http://odyniec.net/projects/imgareaselect/usage.html
	
	$.fn.extend({
		'init_imgvariants': function ( opt ) {
			
			var opt = $.extend({
			}, opt);
			
			return this.each(function () {
			
				var $me = $(this);
			
				var $imgArr = $me.find('.fal-imgvariant .fal-img');
				var $focalPointArr = $me.find('.fal-focalpoint .fal-img');
				
				var $formField = $me.find('textarea');
				var str = $.trim($formField.val());
				
				try {
					var variants = $.parseJSON(str);
				} catch(e) {
					var variants = {crop:{},focalpoint:{}};					
				}
								
				
				$focalPointArr.each( function () {
					var data = $(this).data();
					var $img = $(this).find('img');
					
					var w = $img.width();
					var h = $img.height();
					
					if (!variants.focalpoint[data.id]) {
						variants.focalpoint[data.id] = {x1:0.5,y1:0.5};
					}
					
					var obj = variants.focalpoint[data.id];
					obj.imgWidth = w;
					obj.imgHeight = h;
	
					obj.fp = $img.focalPoint({
						instance:		true,
						parent: 		$(this),
						onSelectEnd: 	update_selection,
					});
					
					obj.fp.setSelection(
						w*obj.x1, 
						h*obj.y1
					);
					
					//obj.fp.update();
					
					variants.focalpoint[data.id] = obj;
				});
				
				
				$imgArr.each( function () {
				
					var data = $(this).data();
					var $img = $(this).find('img');
					
					var w = $img.width();
					var h = $img.height();

					var aspectratio = data.aspectratio;
					if (!aspectratio) aspectratio = h/w;
					aspectratio = (''+aspectratio).split(':');
					if (aspectratio.length > 1) aspectratio = aspectratio[1]/aspectratio[0];
					var f = w*aspectratio > h ? 1/(w*aspectratio)*h : 1;
					
					if (!variants.crop[data.id]) {
					
						var ww = 1*f;
						var hh = w/h*aspectratio*f;
						
						variants.crop[data.id] = {
							x1: (1-ww)/2,
							y1: (1-hh)/2,
							x2: (1-ww)/2+ww,
							y2: (1-hh)/2+hh
						};
					}

					var obj = variants.crop[data.id];
					
					
					obj.imgWidth = w;
					obj.imgHeight = h;
					
					obj.ias = $img.imgAreaSelect({
						instance:		true,
						show:			true,
						handles: 		true,
						parent: 		$(this),
						aspectRatio:	data.aspectratio,
						onSelectEnd: 	update_selection,
					});
					
					obj.ias.setSelection(
						w*obj.x1, 
						h*obj.y1,
						w*obj.x2,
						h*obj.y2
					);
					
					obj.ias.update();
					variants.crop[data.id] = obj;
					
				});
				
				
				function update_selection () {
					var data = {crop:{},focalpoint:{}};
					$.each(variants.crop, function (k,v) {
						var obj = this;
						var p = obj.ias.getSelection(true);
						var w = obj.imgWidth;
						var h = obj.imgHeight;
						
						data.crop[k] = {
							x1:	rnd(1/w*p.x1),
							x2: rnd(1/w*p.x2),
							y1: rnd(1/h*p.y1),
							y2: rnd(1/h*p.y2)}
						;
						
					});
					
					$.each(variants.focalpoint, function (k,v) {
						var obj = this;
						var p = obj.fp.getSelection();
						var w = obj.imgWidth;
						var h = obj.imgHeight;
						
						data.focalpoint[k] = {
							x1:	rnd(1/w*p.x1),
							y1: rnd(1/h*p.y1)
						};
					});
					
					$formField.val(JSON.stringify(data));
				}
				
				update_selection();
				
				function rnd( v ) {
					return Math.floor(v*100)/100;
				}
				
			});
		}
	});

})(TYPO3.jQuery);




(function($){
	TYPO3.jQuery(function () {
	
		//$('.t3-form-field-item').bind("DOMSubtreeModified", update_fal_croppers);
		setInterval(update_fal_croppers, 500);
		
		function update_fal_croppers () {
		
			$('.fal-imgvariant-all').not('.active').each( function () {
				var $me = $(this);
				var $imgArr = $me.find('.fal-imgvariant .fal-img');
				
				if ($($imgArr[0]).width() < 10) return;
				
				$me.addClass('active');
				setTimeout(function () {
					$me.init_imgvariants();
				}, 100);
				
			});
		}
		
	});
})(TYPO3.jQuery);


