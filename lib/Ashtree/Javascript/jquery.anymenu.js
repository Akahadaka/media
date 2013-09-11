/*
 * jQuery Any Menu
 * @version 1.0 (2012-20-20)
 * @requires jQuery 1.4+
 * @author andrew.nash
 */
(function($)
{
	$.fn.anymenu = function(user_options)
	{
		var options = $.extend({
			'data'  : false,
			'type'  : 'dropdown'
	    }, user_options);
		
		$(this).each(function(){
			var UL = $(this);
			
			// Prepare the top level for children
			UL.addClass('anymenu topmenu')
			.css({
				'position' : 'relative'
			});
			
			// Hide all submenus
			$('ul', UL).addClass('anymenu submenu');
			$('ul > li', UL).hide();
			
			// Position the second level items
			$('> li > ul', UL)
			.css({
				'position' : 'absolute'
			});
			
			// Show/hide the submenu being hovered over
			$('body').on('mouseenter.anymenu', '.anymenu.topmenu li', function(){
				$('> ul > li', this).slideDown();
				$('> a', this).addClass('hover');
			});
			
			$('body').on('mouseenter.anymenu', '.anymenu.submenu li', function(){
				$('> ul > li', this).show('slide', 'left', 1000);
			});
			
			$('body').on('mouseleave.anymenu', '.anymenu li', function(){
				$('> ul > li', this).slideUp();
				$('> a', this).removeClass('hover');
			});
			
		});
		
		return $(this);
	}
})(jQuery);