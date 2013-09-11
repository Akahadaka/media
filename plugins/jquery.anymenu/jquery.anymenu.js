/*
 * jQuery Any Menu
 * @version 1.0 (2012-20-20)
 * @requires jQuery 1.4+
 * @author andrew.nash
 */
(function($)
{
	$.fn.anymenu = function(user_options)
	{
		var options = $.extend({
			'data'    : false,
			'type'    : 'dropdown',
			'effects' : 'slide' 
	    }, user_options);
		
		$(this).each(function(){
			var UL = $(this);
			
			// Prepare the top level for children
			UL.addClass('anymenu topmenu')
			.css({
				'position' : 'relative'
			});
			
			// Hide all submenus
			$('ul', UL).addClass('anymenu submenu');
			$('ul > li', UL).hide();
			
			// Position the second level items
			$('> li > ul', UL)
			.css({
				'position' : 'absolute',
				'z-index'  : '99'
			});
			
			// Show/hide the submenu being hovered over
			$('body').on('mouseenter.anymenu', '.anymenu.topmenu li', function(){
				if (options.effects == 'slide') $('> ul > li', this).slideDown(options.duration);
				else $('> ul > li', this).show();
				$('> a', this).addClass('hover');
			});
			
			$('body').on('mouseenter.anymenu', '.anymenu.submenu li', function(){
				$('> ul > li', this).show('slide', 'left', options.duration);
			});
			
			$('body').on('mouseleave.anymenu', '.anymenu li', function(){
				if (options.effects == 'slide') $('> ul > li', this).slideUp(options.duration);
				else $('> ul > li', this).hide();
				$('> a', this).removeClass('hover');
			});
			
		});
		
		return $(this);
	}
})(jQuery);


/**
* hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
* 
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne brian(at)cherne(dot)net
*/
(function($){if(!$.isFunction('hoverIntent'))$.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev])}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev])};var handleHover=function(e){var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t)}if(e.type=="mouseenter"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob)},cfg.timeout)}}};return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover)}})(jQuery);