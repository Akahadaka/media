/*
 * jQuery Auto suggest
 * @version 1.0 (2011-02-06)
 * @requires jQuery 1.7.1+
 * @author andrew.nash
 */
(function($)
{
	$.fn.autosuggest = function(user_options)
	{
		var options = $.extend(
		{
			'remote'     : '',
			'params'     : '',
			'data'       : '',
			'busyimage'  : '',
			'children'   : false,
			'hook'       : false
	    }, user_options);
		
		
		// Convert a JSON struct to HTML UL
		// Vist as deep as necessary
		function json_to_list(json)
		{
			//alert($.dump(json));
			if (json !== undefined)
			{	
				
				var UL = $('<ul/>');
				$.each(json, function(key, val)
				{
					hasSub = (typeof val == 'object');
					name = hasSub ? key : val;
					var LI = $('<li/>').css({'white-space':'nowrap'}).html(name);
					if (hasSub) LI.append(json_to_list(val));
					
					UL.append(LI);
				});

				return UL;
			}
			
			return false;
		}
		
		
		// Tranverse through a json object
		// And filter on a specific term
		function filter_json(json, term)
		{
			return json;
		}
		
		
		// Loop through each specified element
		// and prepare it for auto-suggest
		$(this).each(function()
		{
			

			var INPUT = $(this);
			var SUGGEST;
			var BUSY;
			
			if (!INPUT.parent('.autosuggest').length) 
			{
				SUGGEST = $('<div class="autosuggest-suggest"/>').css({
					'position'         : 'absolute',
					'left'             : 0,
					'min-width'        : INPUT.width()+'px',
					'z-index'		   : 99
				});
				
				BUSY = $('<div class="autosuggest-progress"/>').css({
					'position' : 'absolute', 
					'top'      : '0', 
					'right'    : '5px'
				});
				
				INPUT.wrap('<span style="position:relative"/>').parent().addClass('autosuggest').append(SUGGEST.hide());		
				INPUT.attr('autocomplete', 'off');
				
				transparent = 'rgba(0, 0, 0, 0), transparent';
				background_color = (transparent.indexOf(SUGGEST.css('background-color')) > -1) ? '#FFF' : SUGGEST.css('background-color');
				padding_top      = (SUGGEST.css('padding-top') != '') ? SUGGEST.css('padding-top') : '4px';
				padding_right    = (SUGGEST.css('padding-right') != '') ? SUGGEST.css('padding-right') : '4px';
				padding_bottom   = (SUGGEST.css('padding-bottom') != '') ? SUGGEST.css('padding-bottom') : '4px';
				padding_left     = (SUGGEST.css('padding-left') != '') ? SUGGEST.css('padding-left') : '4px';
				
				SUGGEST.css({'background-color' : background_color});
				SUGGEST.css({'padding-top' : padding_top});
				SUGGEST.css({'padding-right' : padding_right});
				SUGGEST.css({'padding-bottom' : padding_bottom});
				SUGGEST.css({'padding-left' : padding_left});
			}
			else
			{
				SUGGEST = $('.autosuggest-suggest', INPUT.closest('.autosuggest'));
				BUSY    = $('.autosuggest-progress', INPUT.closest('.autosuggest'));
				
			}
			
			// Write the found contents to the page
			function show_dropdown(json)
			{
				BUSY.remove();
				//alert(SUGGEST.html());
				SUGGEST.html(json_to_list($.parseJSON(json))).slideDown();
				
				$('li', SUGGEST).not(':has(a.addnew)').bind('click.autosuggest', function(e)
				{
					if (options.children) e.stopPropagation();
					INPUT.val($(this).clone().children('ul').remove().end().text());
					hide_dropdown();
					if (typeof(options.hook) == 'function')
					{
						options.hook(this).call();
					}
					INPUT.focus();
				}).css({'cursor':'pointer'});
			}
			
			// Do another 
			function hide_dropdown()
			{
				SUGGEST.slideUp();
			}
			
			INPUT.on('keyup.autosuggest', function()
			{
				// Set a progress indicator 
				// so users are aware that something will happen
				if (options.busyimage) BUSY.append($('<img src="'+options.busyimage+'"/>'));
				else BUSY.text('...');
				
				$(this).parent().append(BUSY);
				
				// Filter the dataset, 
				// either from a json dataset
				// or a remote call
				if (options.remote)
				{
					$.get(
						options.remote, 
						$.extend({'value':INPUT.val()}, options.params),
						function(data)
						{
							if (data) show_dropdown(data);
							else hide_dropdown();
						}
					);
					
				}
				else if (options.data)
				{
					data = filter_json(data, INPUT.val());
					if (data) show_dropdown(data);
					else hide_dropdown();
				}

			});
			
			$(window).on('click.autosuggest keyup.autosuggest', function(e)
			{
				switch(e.which)
				{
					case 27:
					case 13:
						e.preventDefault();
					case 9:
					case 1:
						hide_dropdown();
						
				}
				
			});

		});
		
	}
})(jQuery);