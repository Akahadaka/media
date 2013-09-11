/*
 * jQuery Modal Window
 * @version 1.0 (2011-01-05)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
(function($)
{
	$.fn.modal = function(user_options)
	{
		var options = $.extend({
	        'width'  : 800,
	        'height' : 600,
	        'action' : 'open',
	        'remote' : false,
	        'content': false,
	        'modal'  : true,
	        'cancel' : '.cancel'
	    }, user_options);
		
		var scrollPosition;

		var close = function()
		{
			
			var dom = (options.remote) ? window.parent.document : window.document;
			
			//Refresh the page without hash data
			//dom.location.href = dom.location.href.replace(dom.location.hash.replace(/.*\//, ''), '');
			
			//Remove the iframe
			$('.modal-background', dom).remove();
			$('.modal-content', dom).remove();
			
			//@todo Find a way to enable
			//      again for IE
			//Enable scrolling again
			/*$('html', dom).css({
				'overflow-y' : '',
				'overflow-x' : ''
			});*/
			
			//document.body.scroll="yes";
			
			$(window).scrollTop(scrollPosition);
		}//close

		var open = function(parent)
		{
			scrollPosition = $(window).scrollTop();
			var marginTop = ($(window).height() - options.height)/2;
			var marginLeft = ($(window).width() - options.width)/2;
			
			var IFRAME = $('<iframe class="modal-content"/>').css({
				'position'    : 'fixed',
				'top'         : 0,
				'left'        : 0,
				'width'       : options.width,
				'height'      : options.height,
				'margin-top'  : marginTop,
				'margin-left' : marginLeft,
				'background'  : '#FFF',
				'z-index'     : 9999999
			})
			.attr('src', options.remote)
			.attr('frameborder', 0)
			.attr('scrolling', 'no')
			.attr('allowtransparency', 'false');
			
			var CONTENT = $('<div class="modal-content"/>').css({
				'position': 'fixed',
				'top'         : 0,
				'left'        : 0,
				'width'       : options.width,
				'height'      : options.height,
				'margin-top'  : marginTop,
				'margin-left' : marginLeft,
				'background'  : '#FFF',
				'z-index'     :9999999
			})
			.html(options.content);
			
			var WINDOW = $('<div class="modal-background"/>').css({
				'position': 'fixed',
				'top'     : 0,
				'left'    : 0,
				'width'   : '100%',
				'height'  : '100%',
				'opacity' : 0.8,
				'z-index' : 9999998
			});
			
			// Attach iframe to window if specified
			// or fill content html
			if (options.remote) {
				parent.append(IFRAME);
			} else if (options.content) {
				parent.append(CONTENT);
			}
			
			parent.append(WINDOW);
			
			// Close iframe 
			// when clicking outside of iframe
			// but only if it is not in modal mode
			if (!options.modal) {
				WINDOW.click(function() {
					close();
				});
			}
			
			transparent = 'rgba(0, 0, 0, 0), transparent';
			background_color = (transparent.indexOf(WINDOW.css('background-color')) > -1) ? '#FFF' : WINDOW.css('background-color');
			WINDOW.css({'background-color' : background_color});
			
			//Disable scrolling
			/*$('html').css({
				'overflow-y':'hidden',
				'overflow-x':'hidden'
			});*/
			
			

		}//open
		
		// Setup the escape key 
		// so that users can't get stuck with a faulty window
		// or in a process they can't back out of
		$(window).bind('keyup', function(e)
		{
			switch(e.which)
			{
				case 27:
					e.preventDefault();
					close();
					break;
					
			}
			
		});
		
		switch(options.action)
		{
			case 'open':
				open($(this));
				break;
			case 'close':
				close();
				break;
		}//switch
		
		
	}
})(jQuery);