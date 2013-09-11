/*
 * jQuery List box to add and remove items
 * @version 1.0 (2011-02-06)
 * @requires jQuery 1.7.1+
 * @author andrew.nash
 *
 * @change 2011-02-24 andrew.nash added support for names that are already arrays. e.g. name="info[0][]" id="info_0"
 * @change 2011-02-09 andrew.nash found trim is only supported as $.trim(text)
 */
(function($)
{
	$.fn.addonbox = function(user_options)
	{	
		//Merge default options with the users
		var options = $.extend({
			'values'     : '',
			'numbered'   : false,
			'onComplete' : false,
			'onDelete'   : false
		}, user_options);
	
		/**
		 *
		 */
		function create_addonbox(INPUT, val, numbers)
		{
			INPUT.addClass('addonbox');
			name = INPUT.data('name');
			index = INPUT.data('index');
			id = name.replace('[', '_').replace(']', '');
			
			var INPUT_2 = INPUT.clone().attr('name', name).data('index', (index+1)).val('');
			$('#' + id + '_rem_' + index).after(INPUT_2);

			if (numbers) {
				$('#' + id + '_rem_' + index).prev().before($('<span id="'+id+'_num_'+index+'" class="number"/>').text(index+1+'. '));
			}
			
			if (typeof options.onComplete == 'function') {
				options.onComplete(INPUT);
			}
			//$('#' + id + '_add_' + index).hide();
			$('#' + id + '_rem_' + index).css('display', '');
			
			//alert($.dump($('#' + id + '_rem_' + index).css('display')));
			
			INPUT.off('focus.addonbox');
			$('#' + id + '_box_' + (index-1)).off('blur.addonbox');
			
			if (val !== undefined) {
				
				if (typeof(val[0]) != 'string') {
					res = {};
					i = 0;
					for(key in val){
						if (i++) {
							res[key] = val[key];
						} else {
							INPUT.val(key);
							for (x in val[key]){
								INPUT.after($('<input type="hidden" value="'+val[key][x]+'" class="'+x+'" />'));
							}
						}
					}
					val = ($.isEmptyObject(res)) ? undefined : res;
					console.log($.dump(val));
				} else {
					INPUT.val(val[0]);
					val.shift();
					
				}
				
				INPUT_2.addonbox({
					'values'     : val,
					'numbered'   : numbers, 
					'onComplete' : options.onComplete, 
					'onDelete'   : options.onDelete
				});
			}
			else
			{
				INPUT_2.addonbox({
					'onComplete' : options.onComplete, 
					'onDelete'   : options.onDelete
				});
			}
			
			INPUT.focus();
		}//create_addonbox
		
	
		/**
		 *
		 */
		function delete_addonbox(INPUT)
		{
			if (INPUT.length)
			{
				if (typeof options.onDelete == 'function') {
					options.onDelete(INPUT);
				}
			
				id = INPUT.data('name').replace('[', '_').replace(']', '');
				$('#' + id + '_div_' + INPUT.data('index')).remove();
			}
		}//delete_addonbox
		
		
		/**
		 *
		 */
		function remove_addonbox(INPUT)
		{
			name = INPUT.data('name');
			index = INPUT.data('index');
			id = name.replace('[', '_').replace(']', '');
			var INPUT_2 = $('#' + id + '_box_' + (index+1));
			
			$('#' + id + '_rem_' + index).hide();
			//$('#' + name + '_add_' + index).show();
			
			if ($.trim(INPUT_2.val()) == '')
			{
				delete_addonbox(INPUT_2);
			}
			
			INPUT.on('focus.addonbox', function()
			{
				create_addonbox($(this));
			});
			
			
		}//remove_addonbox
		

		/**
		 * MAIN
		 */
		$(this).each(function()
		{
			INPUT = $(this);
			name = (INPUT.data('name') === undefined) ? INPUT.attr('name') : INPUT.data('name');
			index = (INPUT.data('index') !== undefined) ? INPUT.data('index') : 0;
			id = name.replace('[', '_').replace(']', '');
			
			//Make the select input submit as an array
			INPUT.data({'index':index, 'name':name}).attr('name', name + '[]').attr('id', id + '_box_' + index);
			
			DIV = $('<div/>').attr('id', id + '_div_' + index);
			//ADD = $('<input/>').attr('type', 'button').val('+').attr('id', id + '_add_' + index).data({'index':index, 'name':name}).addClass('snow addonbox-button add');
			REM = $('<input/>').attr('type', 'button').val('-').attr('id', id + '_rem_' + index).data({'index':index, 'name':name}).addClass('snow addonbox-button rem').hide();
			
			INPUT.wrap('<div id="' + id + '_div_' + index + '"/>');
			$('#' + id + '_div_' + index).append(REM).addClass(INPUT.attr('class')+'_div');
			
			if (index != 0) $('#' + id + '_div_' + index).parent().parent().append($('#' + id + '_div_' + index));
				
			
			/**
			 *
			 */
			INPUT.on('focus.addonbox', function()
			{
				create_addonbox($(this));
				
			});//focus
			
			
			/**
			 *
			 */
			INPUT.on('blur.addonbox', function()
			{
				if ($.trim($(this).val()) == '')
				{
					remove_addonbox($(this));
				}
			});//blur
			
			
			/**
			 *
			
			$('#' + id + '_add_' + index).click(function()
			{
				name = $(this).data('name');
				index = $(this).data('index');
				
				//$(this).hide();
				//$('#' + id + '_rem_' + index).show();
				
				create_addonbox(INPUT);
			});//click
			 */
			
			/**
			 *
			 */ 
			$('#' + id + '_rem_' + index).click(function()
			{
				name = $(this).data('name');
				index = $(this).data('index');
				id = name.replace('[', '_').replace(']', '');
				
				//$(this).hide();
				//$('#' + id + '_add_' + index).show();
				delete_addonbox($('#' + id + '_box_' + index));
			});//click
			
			
			//Check the values of the box and populate if necessary
			if ((options.values != '') && (options.values != null) && (options.values !== undefined))
			{
				create_addonbox(INPUT, options.values, options.numbered);
			}
			
		});//each
	}//fn
})(jQuery);