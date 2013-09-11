/*
 * jQuery Dependant Dropdown Combobox
 * @version 1.0 (2011-01-28)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
(function($)
{
	$.fn.multibox = function()
	{
        var SELECT = $(this);
        
        SELECT.attr('name', SELECT.attr('name')+'[]');
        SELECT.attr('multiple', true);            
	}
})(jQuery);

