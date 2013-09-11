/*
 * jQuery Massive Select
 * @version 1.0 (2011-10-31)
 * @requires jQuery 1.4+
 * @author andrew.nash
 */
(function($)
{
	$.fn.massiveselect = function(user_options)
	{
		var options = $.extend({
			'options'  : '',
			'selected' : '',
			'value' : 'value' 
	    }, user_options);
		
		function radioify(data, name)
		{
			
			var UL = $('<ul style="list-style:none; padding:0"/>').hide();
			var id = name.replace('[', '_').replace(']', '');
			var nm = name.replace(/\[.*/, '');
			
			//alert($.dump(data));
		
			$.each(data, function(key, val)
			{
				if (val.name !== undefined)
				{
					var LI = $('<li/>');
					//alert(val.name);
					
					id_key  = id+'_'+key;
					val_key = (options.value == 'key') ? key : val.name;
					LABEL = $('<label for="'+id_key+'" style="cursor:pointer" />').text(val.name);
					RADIO = $('<input type="radio" id="'+id_key+'" value="'+val_key+'" style="cursor:pointer" />');
					
					RADIO.bind('click', function(){
						RADIO = $(this);
						LABEL = $(this).siblings('label');
						LI = $(this).closest('li');
						
						id    = 'check_'+RADIO.attr('id');
						name  = RADIO.attr('name');
						value = RADIO.attr('value');
						
						if ($('#'+id).length) {
							CHECKBOX = $('#'+id);
							CHECKBOX.attr('checked', true).show();
						}
						else 
						{
							CHECKBOX = $('<input type="checkbox" checked="checked" id="'+id+'" name="'+nm+'[]" value="'+value+'" style="cursor:pointer" />');
							RADIO.after(CHECKBOX);
						}
						
						$(this).hide();
						LI.addClass('selected');
						LI.siblings().slideUp('slow').fadeOut('slow');
						LI.children('ul:first').slideDown('slow');
						LABEL.attr('for', id);
						RADIO.removeAttr('name');
						
						CHECKBOX.bind('click', function(){
							id   = $(this).attr('id').replace('check_', '');
							name = $(this).attr('name');
							
							RADIO = $('#'+id);
							LABEL = $(this).siblings('label');
							LI    = $(this).closest('li');
							
							LI.removeClass('selected');
							RADIO.attr('checked', false).show();
							LI.siblings().slideDown().fadeIn('slow');
							LI.children('ul:first').slideUp('slow');
							LABEL.attr('for', id);
							RADIO.attr('name', name);
							$(this).hide();
						});
					});
					
					
					
					LI.append(RADIO).append(LABEL);
					
					if (val[0] !== undefined) LI.append(radioify(val, name+'['+key+']'));
					
					UL.append(LI);
				}
			});
			
			return UL;
			
		}
		
		$(this).each(function()
		{
			var INPUT  = $(this);
			var id     = (INPUT.attr('id')) ? 'id="' + INPUT.attr('id') + '"' : '';
			var SELECT = $('<div '+id+' class="massive-select"/>');
			
			//alert($.dump(options.options[0]));
			var UL = radioify(options.options, INPUT.attr('name'));
			
			// Display the massive select
			UL.show();
			SELECT.append(UL);
			
			
			
			INPUT.parent().append(SELECT);
			
			
			INPUT.remove();
		});

	}
})(jQuery);