<?php

class Plugin_Jquery_Modal {

	/**
	 * Turn all links with the class modal into modal windows when opened
	 */
	public static function Activate() {
		$htm = Ashtree_Html_Page::instance();
		$tpl = Ashtree_Common_Template::instance();
		
		$tpl->modal = isset($_GET['modal']);
		
		// Modify all modal links
		// to open in a modal window
		// append ?modal so the page knows to strip the index template
		$htm->jss = ASH_PLUGINS . 'jquery.modal/jquery.modal.js';
		$htm->css = ASH_PLUGINS . 'jquery.modal/jquery.modal.css';
		$htm->jquery = <<<JQUERY
			$('body').on('click.modal', '.modal', function(e)
			{
				href = $(this).attr('href');
				hash = (href.indexOf('#') > -1) ? href.replace(/.*#(.*)/, '$1') : '';
				data = (href.indexOf('?') > -1) ? href.replace(/.*\?(.*)/, '$1').replace('#'+hash, '').replace(/\?modal|&modal/, '') : '';
				link = href.replace('?'+data, '').replace('#'+hash, '');
				href = link + '?modal' + ((data != '') ? '&'+data : '') + ((hash != '') ? '#'+hash : '');
				e.preventDefault();	
				$('body').modal({
					'remote': href,
					'width': 960,
					'height': 550
				});
			});
JQUERY;
		
		// Add ability to close the modal window
		// without completing the task
		$htm->jquery = <<<JQUERY
			$('body').on('click.cancel', '.cancel', function(){
				$('body', window.parent.document).modal({remote:true, action:'close'});
			});
JQUERY;
	}
}
