/*
 * jQuery Dependant Dropdown Combobox
 * @version 1.0 (2011-01-28)
 * @requires jQuery 1.0+
 * @author andrew.nash
 *
 * @change 2011-08-23 andrew.nash changed id to reflect old id, rather than risk an unconventional name
 * @change 2011-02-06 andrew.nash made it compatible with class calling by placing content in EACH loop
 */
(function($)
{
	$.fn.otherbox = function(param)
	{
	    $(this).each(function()
		{
			//@todo make key:value json pairs for params
			
			if (param === undefined)
			{
				action = 'append';
			}
			else if ((param !== 'append') && (param !== 'remove'))
			{
				action = 'append';
				value = param;
			}
			else
			{
				action = param;
			}
			
			//Identify this object to act on
			SELECT = $(this);
			if (SELECT.data('name') === undefined) SELECT.data('name', SELECT.attr('name'));
			if (SELECT.data('id') === undefined) SELECT.data('id', SELECT.attr('id'));
			
			switch(action)
			{
				case 'append':
					if (!SELECT.data('otherbox'))
					{
						width = parseInt(SELECT.css('width')) - 27;
						height = 18;

						SELECT.css({height:25});

						DIV = $('<div/>').css({position:'relative'});
						SELECT.append($('<option/>').val('other').text('-- Other --'));
						INPUT = $('<div/>').append(
							$('<input/>').attr('id', SELECT.attr('id') + '_other').attr('type', 'text').css(
							{
								'width':width, 
								'height':height, 
								'border':'none', 
								'outline':'none', 
								'paddingLeft':2
							})
						).css({position:'absolute', top:3, left:3});
						
						
						//Place in a DIV and append to page
						SELECT.after(DIV);
						DIV.append(SELECT);
						DIV.append(INPUT.hide());
			
						
						//Check to see if our defined value is in the options
						
						if ((value != '') && (value != null) && (value !== undefined))
						{
							found = false;
							$('option', SELECT).each(function()
							{
								found = ($(this).val() == value) ? true : found;
							});
							
							if (!found)
							{
								SELECT.val('other');
								INPUT.show();
								$('input', INPUT).focus().attr('name', SELECT.data('name')).val(value);
								SELECT.attr('name', '');
							}
						}
						
			
						SELECT.data('otherbox', true);
								
						SELECT.change(function()
						{
							id = $(this).data('id');
							name = $(this).data('name');
							INPUT = $('#' + id + '_other');
							
							if ($(this).val() == 'other')
							{
								INPUT.parent().show();
								INPUT.focus().attr('name', name);
								$(this).attr('name', '');
								
							}
							else
							{
								INPUT.parent().hide();
								$(this).attr('name',name);
								INPUT.attr('name', '');
							}
						});
					}
					
					break;
				
				case 'remove':
					if (SELECT.data('otherbox'))
					{
						DIV = SELECT.parent();
						INPUT = $('div', SELECT.parent());
					   
						INPUT.remove();
						DIV.after(SELECT);
						DIV.remove();
						
						$('option[value=other]', SELECT).remove();
						
						SELECT.data('otherbox', false);    
					}
					break;
			}//switch    
	    });//each
	}
})(jQuery);