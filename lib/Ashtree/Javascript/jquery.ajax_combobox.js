/*
 * jQuery Dependant Slideout Combobox
 * @version 1.0 (2011-01-28)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
(function($)
{
	$.fn.ajax_combobox = function(user_options)
	{
		var SELECT = $(this);
		
		//Merge default options with the users
		var options = $.extend({
			engine: '../../inc/engine.php',
			action: 'select',
			field: SELECT.attr('id'),
			selected: '',
			children: '',
			dependon: '',
			otherbox: false
		}, user_options);
		
		//Create event to update
		var SELECT_2 = $('#'+options.children[0]);
		SELECT.data('child', options.children[0]);
		
		//alert('ID:'+$.dump(SELECT.attr('id'))+'\n\nOPTIONS:'+$.dump(options)+'\n\nDATA:'+$.dump(SELECT.data('child')));

		SELECT.change(function()
		{
			//console.log("change");
			SELECT_2.ajax_combobox({
				engine: options.engine,
				field: SELECT_2.attr('id'),
				children: [SELECT_2.data('child')],
				dependon: $(this).val(),
				otherbox: options.otherbox
			});
			
		});//change
		
		
		var selected = '';
		
		//Update selected if any
		if (options.selected !== undefined)
		{
			selected = options.selected;
			//Array of many selected
			if (options.selected[0] !== undefined)	
			{
				selected = options.selected;
				options.selected = options.selected[0];
				selected.shift();
				
			}
		}
		
		//AJAX call to get the values of the combobox
		$.get(
			options.engine,
			{
				action: options.action,
				field: options.field,
				selected: options.selected,
				dependon: options.dependon
			},
			function(data)
			{
				//console.log('engine.php?action='+options.action+'&field='+options.field+'&selected='+options.selected+'&dependon='+options.dependon);
				//console.log(data);
				if (options.otherbox) 
				{
					SELECT.html(data).data('otherbox', false).otherbox(options.selected);
				}
				else 
				{
					SELECT.html(data);
				}
				
				//Update dependent children if any
				if (options.field !== undefined && options.children !== undefined)
				{
					var children = '';
					
					//Array of many children
					if (options.children[0] !== undefined)	
					{
						children = options.children;
						options.children = options.children[0];
						children.shift();
					}

					$('#'+options.children).ajax_combobox({
						'engine': options.engine,
						'selected': selected,
						'children': children,
						'dependon': SELECT.val(),
						'otherbox': options.otherbox
					});
				}
				
				
				
			}//function
		);//get
	}
})(jQuery);