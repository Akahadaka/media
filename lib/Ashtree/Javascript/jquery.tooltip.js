/*
 * jQuery Tooltip
 * @version 1.0 (2011-10-05)
 * @requires jQuery 1.4+
 * @author andrew.nash
 */
(function($)
{
	$.fn.tooltip = function(user_options)
	{
		var options = $.extend({
			'target' : '',
			'html'   : 'Tooltip goes here',
			'event'  : 'focus',
			'revent' : ''
	    }, user_options);
		
		
		// Every event needs a reverse/release event
		// Automatically determine the revent if none was specified
		if (!options.revent)
		{

			$.each(options.event.split(' '), function(k, v){
				
				switch(v)
				{
					case 'focus':
						options.revent += 'blur ';
						break;
					case 'mouseover':
						options.revent += 'mouseout ';
						break;
					case 'mouseenter':
						options.revent += 'mouseleave ';
						break;
				}
			});
		}

		// Take each element matching and apply the tooltip
		// The tooltip can be invoked on any event
		$(this).each(function()
		{
			var INPUT = $(this);
			var TIP   = $('<div class="tooltip" style="position:absolute"/>').html(options.html);
	
			if (INPUT.attr('disabled')) INPUT = INPUT.wrap('<span style="display:'+INPUT.css('display')+'"/>').parent();
			
			INPUT.bind(options.event, function()
			{
				if (options.target) $(options.target).html(options.html).show();
				else $(this).wrap('<span style="position:relative"/>').parent().append(TIP);
			});
			
			INPUT.bind(options.revent, function()
			{
				if (options.target) $(options.target, $(this).parent().parent()).hide();
				else { TIP.remove(); $(this).unwrap(); }
			});
			
		});

	}
})(jQuery);