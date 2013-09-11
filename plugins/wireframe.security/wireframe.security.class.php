<?php 

class Plugin_Wireframe_Security {
	
	/**
	 * 
	 */
	public static function Activate($accesslevels=NULL) {
		$htm = Ashtree_Html_Page::instance();
		$tpl = Ashtree_Common_Template::instance();
		
		$levels = isset($accesslevels)
			? $accesslevels
			: array(
				'anonymous',
				'registered',
				'administrator',
				'super-administrator'
			);
		
		$tpl->security = $_SESSION['security'] = isset($_GET['security']) ? $_GET['security'] : (isset($_SESSION['security']) ? $_SESSION['security'] : 0);
		
		$wireframe_security = <<<HEREDOC
			<div id="wireframe-security">
				<div class="access-level alpha">Views as:</div> 
HEREDOC;
		foreach($levels as $level=>$name) {
			$position = '';
			#if ($level == 0) $position = ' alpha';
			if ($level == count($levels)-1) $position = ' omega';
			$active = ($_SESSION['security'] == $level) ? ' active' : '';
			$qs = new Ashtree_Common_Querystring($_SERVER['REQUEST_URI']);
			$qs->security = $level;
			$wireframe_security .= <<<HEREDOC
			<div class="access-level{$position}{$active}"><a href="{$qs}">{$name}</a></div>
HEREDOC;
		}
		
		$wireframe_security .= <<<HEREDOC
			</div>
HEREDOC;
		
		$htm->css = ASH_PLUGINS . 'wireframe.security/wireframe.security.css';
		if (!$tpl->modal || !$tpl->template) {
			$tpl->body_from_binaries .= $wireframe_security;
		}
	}
}