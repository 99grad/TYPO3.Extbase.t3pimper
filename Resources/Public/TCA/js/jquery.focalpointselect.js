
(function($) {

	$.extend({
		'focalPoint': function ( image, opt ) {

			var opt = $.extend({
				instance: 		false,
				parent: 		false,
				onSelectEnd: 	false
			}, opt);


			var $image = image;
			var $me = $(this);
			var $parent = opt.parent;
			var $point = $('<div />');
			var offX = 0;
			var offY = 0;
			
			$point.addClass('fp-select');
			$parent.find('.fp-select').remove();
			$parent.append( $point );

			function setSelection ( x1, y1 ) {
				$point.css({top:y1, left: x1});
			}
			
			function getSelection ( opt ) {
				return {x1:$point.position().left, y1:$point.position().top};
			}
			
			function update () {
			
			}
			
			$point.unbind().mousedown( function (e) {
				$(document).unbind('mousemove').mousemove(movingMouseMove).mouseup(docMouseUp);
				offX = $parent.offset().left;
				offY = $parent.offset().top;
			});
			
			function movingMouseMove ( e ) {
				var x = Math.max(Math.min(e.pageX - offX, $parent.width()-5),5);
				var y = Math.max(Math.min(e.pageY - offY, $parent.height()-10),5);
				$point.css({top:y, left:x});
			}
			
			function docMouseUp ( e ) {
				$(document).unbind('mousemove').unbind('mouseup');
				if (opt.onSelectEnd) opt.onSelectEnd( $image, getSelection() );
			}

			
			this.getSelection = getSelection;			
			this.setSelection = setSelection;
			this.update = update;
			
		}
	});
	
	$.fn.extend({
	
		'focalPoint': function ( opt ) {
			opt = opt || {};

			this.each( function () {
				$(this).data('imgFocalPoint', new $.focalPoint(this, opt));			
			});
			
			if (opt.instance) return $(this).data('imgFocalPoint');
			
			return this;
		}
	});

})(TYPO3.jQuery);
