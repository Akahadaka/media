/*
 * jQuery Keep an initial value on an input value
 * @version 1.0 (2011-02-06)
 * @requires jQuery 1.0+
 * @author andrew.nash
 *
 * @todo Add visible password option
 */
(function($)
{
	$.fn.defaultval = function(user_options, focus_callback, blur_callback)
	{
		var options = $.extend(
		{
	        'value' : 'Your text here...',
			'color'   : '#AAA',
			'focus'   : function(){},
			'blur'    : function(){}
	    }, user_options);
		
		if (typeof(user_options)   == 'string')   options.value   = user_options;
		if (typeof(blur_callback)  == 'function') options.blur    = blur_callback;
		if (typeof(focus_callback) == 'function') options.focus   = focus_callback;
		
		$(this).each(function()
		{
			var INPUT = $(this);
			
			INPUT.val(options.value).addClass('defaultval');
			
			if (options.color !== false) INPUT.css({'color': options.color});
			
			INPUT.unbind('focus').bind('focus', function()
			{
				if ($(this).val() == options.value) 
				{
					$(this).val('').css({'color': 'inherit'}).removeClass('defaultval');
				}
				
				if(typeof(options.focus) == 'function')
				{
					options.focus.call($(this));
			    }
				
			});

			INPUT.unbind('blur').bind('blur', function()
			{
				if ($(this).val() == "") 
				{
					$(this).val(options.value).addClass('defaultval');
					if (options.color !== false) $(this).css({'color': options.color});
				}
				
				if(typeof(options.blur) == 'function')
				{
					options.blur.call($(this));
			    }
			});
		});
	}
})(jQuery);