/*
 * jQuery Quick Filter
 * @version 1.0 (2010-08-20)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
(function($)
{
	$.fn.quickfilter = function(list_identifier, num_headers, delayon)
	{

		function _filter(TR, criteria)
		{				
			found = false;
			for (var i in criteria.slice(0, criteria.length))
			{
				found += (TR.stripTags().toLowerCase().search(criteria[i].toLowerCase()) != -1) ? 1 : 0;
			}//for
								
			if (found != criteria.length)
			{
				TR.hide();
			}
			else
			{
				TR.show();
			}
		}//function _filter
		
		//if its a table
		var list = (($(list_identifier).length > 1) ? $(list_identifier) : ($('tr', $(list_identifier)).length) ? $('tr', $(list_identifier)) : (($('li', $(list_identifier)).length) ? $('li', $(list_identifier)) : $('div', $(list_identifier))));

		//use 'num_headers' to skip header check (e.g. first two rows (header|filter))
		var num_headers = (num_headers === undefined) ? 2 : num_headers;
		
		var delayit = (delayon > 50) ? delayon : 10;
			
		
		//bind the keypress event for a users typed text
		$(this).keyup(function()
		{
			pattern = $(this).val().split(/\s/);
			
			if (delayon)
			{
				list.slice(num_headers).eachDelay(function()
				{
					_filter($(this), pattern)
				}, delayit);//each
			}
			else
			{
				list.slice(num_headers).each(function()
				{
					_filter($(this), pattern)
				});//each
			}
		});//event keyup
	}
})(jQuery);


/*
 * jQuery Strip Tags
 * @version 1.0 (2010-08-20)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
jQuery.fn.stripTags = function() 
{
	return this.html().replace(/<\/?[^>]+>/gi, '');
};


/*
 * 
 * jQuery Delay Plugin
 * version: 0.0.1 (14-Jan-2010)
 * @requires jQuery v1.3.0 or later
 * Author: drew (drew.wells@claytonhomes.com)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.fn.eachDelay = function(callback, speed)
{
	return jQuery.eachDelay( this, callback, speed)
}

jQuery.extend(
{
	eachDelay: function(object,callback, speed)
	{ 
		var name, i = -1, length = object.length, $div = $('<div>'), id;
		//not an array process as object
		if (length === undefined) 
		{ 
			var arr = [], x = -1;
			for (name in object) 
			{
				arr[++x] = name; 
			}//for
			
			id = window.setInterval(function()
			{
				if( ++i === arr.length || callback.call(object[ arr[i] ], arr[i], object[ arr[i] ]) === false)
				{
			 		clearInterval(id);
				}
			}, speed);	
		}
		//array-compatible element ie. [], jQuery Object
		else 
		{
			id = window.setInterval(function()
			{ 
				if (++i === object.length || callback.call(object[i], i, object[i]) === false)
				{
					clearInterval(id);
				}
			}, speed);
		}
		return object;
	}
});
	