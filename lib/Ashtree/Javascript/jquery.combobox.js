/*
 * jQuery Dependant Dropdown Combobox
 * @version 1.0 (2011-01-28)
 * @requires jQuery 1.0+
 * @author andrew.nash
 */
(function($)
{
	$.fn.combobox = function(json, param, param2)
	{
		//Additional paramters
		if (param2 !== undefined)
		{
			$(this).data('other', param2);
			selected = param;
		}
		else if (param !== undefined)
		{
			selected = param;
		}
		else
		{
			selected = null;
		}
		
		
		/**
		 * CREATE
		 */
		function create_combobox(SELECT, data)
		{        	     			
			//if another level exists
		    if (data[0][0] !== undefined)
		    {
		        //Create a new combobox
		        var SELECT_2 = $('<select/>').attr('class', SELECT.attr('class')).attr('name', SELECT.attr('name')).data('other', SELECT.data('other'));
		        
		        //Add it after the current combobox
		        SELECT.after(SELECT_2);
		        SELECT.data('child', SELECT_2);
				
		        //Fill the new combobox
		        create_combobox(SELECT_2, data[0]);
		    }
			else if (SELECT.data('other') == 'multibox')
		    {
				SELECT.multibox();
		    }
		    
		    SELECT.attr('name', SELECT.attr('name')+'[]');
		    
		}//function create_combobox
		
		
		/**
		 * CLEAR
		 */
		function clear_combobox(SELECT, data)
		{
		    var choose = ((SELECT.data('other') == 'multibox') && (data[0][0] === undefined)) ? '-- Select Multiple --' : '-- Select One --';
			//var choose = SELECT.data('default');
			
			SELECT.show();
		    
		    $('option', SELECT).remove();
		    SELECT.html($("<option/>").val(-1).text(choose));
		    SELECT.otherbox('remove');
			//SELECT.attr('size', 1);
		    
		    //clear the child box
			if (data[0][0] !== undefined)
		    {  
		        clear_combobox(SELECT.data('child'), data[0]);
		    }
		}//function clear_combobox
		
		
		/**
		 * POPULATE
		 */
		function populate_combobox(SELECT, data, selected, level)
		{	    		    
			if (data[0] !== undefined)
			{
				//clear the child box
				clear_combobox(SELECT, data);
				
				//populate the options
				var is_selected = false;
				
				$.each(data, function(key, value)
				{
					if ((selected !== null) && (level !== undefined))
					{
						if (key == selected[level])
						{	
							is_selected = key;
						}
					}
					
					SELECT.append($("<option/>").val(key).text(value.name));
				});
				
				if (is_selected !== false)
				{
					SELECT.val(is_selected);
					if (data[0][0] !== undefined)
					{ 
						populate_combobox(SELECT.data('child'), data[is_selected], selected, ++level);
					}
				}
				
				
			}
			
			//debug
			/*
			if (selected[level] !== undefined)
			{
				SELECT.append($("<option/>").text('-- Selected Debug --'));
				$.each(selected, function(key, value)
				{
					is_selected = (key == selected[level]) ? true : false;
					
					SELECT.append($("<option/>").val(key).text(key + ':' + value + ':' + is_selected)).attr('selected', is_selected);
					
				});
			}
			*/
			//if ((selected[0] !== undefined) && (selected[level] !== undefined))
			//{
			//	populate_combobox(SELECT.data('child'), data[SELECT.val()], selected, ++level);
			//}
			
		    
		    //check which was selected
		    if (data[0][0] !== undefined)
		    {   
		        SELECT.change(function()
		        {
		            //if (data[SELECT.val()][0] !== undefined)
					//{
						populate_combobox(SELECT.data('child'), data[SELECT.val()]);
					//}
					//hide the box if no options
					//else
					//{		
					//	$('option', SELECT.data('child')).remove();
					//	$(SELECT.data('child')).hide();
					//}
					
		        });
		    }
		    else if (SELECT.data('other') == 'otherbox')
		    {
					SELECT.otherbox();
		    }
			else if (SELECT.data('other') == 'multibox')
		    {
					SELECT.multibox();
					//$('option:first', SELECT).val('-- Select Multiple --');
		    }
		}//function populate_combobox
		
		
		/**
		 * MAIN
		 */
		//Fill the first combobox
		create_combobox($(this), json);
		populate_combobox($(this), json, selected, 0);
		
	}
})(jQuery);

