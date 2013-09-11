/*
 * jQuery Accordion wizard style form
 * @version 1.0 (2011-10-18)
 * @requires jQuery 1.0+
 * @author andrew.nash
 *
 */
(function($)
{
	$.fn.wizard = function(user_options)
	{
		var options = $.extend(
		{
			'tab'   : '.tab',
			'row'	: '.row',
			'help'  : '.help',
			'error' : '.error',
			'next'  : '.next',
			'back'  : '.back',
			'done'  : '.done',
			'hook'	: false
	    }, user_options);
		
		$(this).each(function()
		{
			var FORM = $(this);
			var UL = $('> ul:first', FORM);
			var LI = $('> li:first', UL);
			
			var end = $('> li:last', UL).index();
			
			// Hide all the wizard pages
			// and disable unecessary buttons
			UL.css({
				'list-style':'none',
				'margin':0,
				'padding':0
			});
			$('> li', UL).hide();
			$('.back', FORM).attr('disabled', true);
			if (end == 0) $('.next', FORM).attr('disabled', true);
			
			// If tabs have been specified
			// Then link to the tabs
			// Use hash to keep track with page refresh
			if ($(options.tab).length)
			{
				$(options.tab).removeClass('active').css({'cursor':'pointer'});
				$(options.tab+':first').addClass('active');
				
				// Handle a tab as button
				$(options.tab).bind('click', function(e)
				{
					var A = $('a:first', $(this));
					
					e.preventDefault();
					if (A.length) window.location.hash = '#'+A.attr('href').replace(/.*#/, '');
					
					
					pos = $(this).index();
					$('> li', UL).hide();
					$('> li:eq('+pos+')', UL).show();
					
					$(options.tab).removeClass('active');
					$(this).addClass('active');
					
					$('.next', FORM).attr('disabled', (pos == end));
					$('.back', FORM).attr('disabled', (pos == 0));
					
					FORM.data('pos', pos);
					
					if ((pos == end) && $(options.done).length) 
					{
						$(options.done).attr('disabled', false);
					}
					
					// Hook in user defined actions
					if (typeof(options.hook[pos]) == 'function')
					{
						options.hook[pos].call();
					}
				});
				
			}
			
			// Hide the help text
			if ($(options.help).length && $(options.row).length)
			{
				$(options.help).hide();
				
				$('input, select, textarea', options.row).focus(function()
				{
					var ROW = $(this).closest(options.row);
					var HELP = $(options.help, ROW);
					if (HELP.text()) HELP.show();
				});
				
				$('input, select, textarea', options.row).blur(function()
				{
					var ROW = $(this).closest(options.row);
					$(options.help, ROW).hide();
				});
			}
			
			// Hide the error text
			if ($(options.error).length)
			{
				$(options.error).hide();
			}
			
			
			FORM.data('pos', 0);
			
			LI.show();
			
			// Handle for the next button
			if ($(options.next).length)
			{
				
				$(options.next, FORM).bind('click', function(){
					pos = FORM.data('pos');
					LI = $('> li:eq('+pos+')', UL);
					LI.hide();
					LI.next().show();
					
					if ($(options.tab).length)
					{
						TAB = $(options.tab+':eq('+pos+')');
						TAB.removeClass('active');
						TAB.next().addClass('active');
						window.location.hash = $('a', TAB.next()).attr('href').replace(/.*#/, '');
					}
					
					FORM.data('pos', ++pos);
					$('.back', FORM).attr('disabled', false);
					if (pos == end) 
					{
						$(this).attr('disabled', true);
						if ($(options.done).length) 
						{
							$(options.done).attr('disabled', false);
						}
					}
					
					// Hook in user defined actions
					if (typeof(options.hook[pos]) == 'function')
					{
						options.hook[pos].call();
					}
					
				});
			}
			
			// Handle for the back button
			if ($(options.back).length)
			{
				$(options.back, FORM).bind('click', function(){
					pos = FORM.data('pos');
					LI = $('> li:eq('+pos+')', UL);
					LI.hide();
					LI.prev().show();
					
					if ($(options.tab).length)
					{
						TAB = $(options.tab+':eq('+pos+')');
						TAB.removeClass('active');
						TAB.prev().addClass('active');
						window.location.hash = $('a', TAB.prev()).attr('href').replace(/.*#/, '');
					}
					
					FORM.data('pos', --pos);
					$('.next', FORM).attr('disabled', false);
					if (pos == 0) $(this).attr('disabled', true);
					
					// Hook in user defined actions
					if (typeof(options.hook[pos]) == 'function')
					{
						options.hook[pos].call();
					}
					
				});
			}
			
			// Disable the finish until at least the final tab
			if ($(options.done).length)
			{
				$(options.done).attr('disabled', true);
			}
			
			// Jump directly to the required tab
			var hash = window.location.hash;
			if (hash) $('a[href$="'+hash+'"]').closest(options.tab).trigger('click');
			
			// Dim all fields on submit
			FORM.submit(function(){
				//@todo There is a problem here with validate
				//      where it cancels the submit on error
				//      but only after all fields have been disabled
				//$('input, select, textarea').attr('readonly', true);
			});
			
		});
	}
})(jQuery);