<?php

class Plugin_Jquery_Wysiwyg {

	/**
	 * Convert textareas into Tiny MCE Rich Text Areas
	 */
	public static function Activate($wysiwyg) {
	
		switch(strtolower($wysiwyg)) {
			case 'tinymce':
			case 'tiny_mce':
			case 'tiny mce':
				self::_Activate_Tiny_MCE();
		}
	
	}
	
	private function _Activate_Tiny_MCE($settings=NULL) {
		$htm = Ashtree_Html_Page::instance();
		
		$htm->jss = ASH_LIB . 'tiny_mce/jquery.tinymce.js';
		
		switch(strtolower($settings)) {
			case 'default':
			default:
				$retrieved_settings = self::_Tiny_MCE_Default();
		}
		
		$htm->jquery = "
			$('.rte').tinymce({
				script_url : '" . ASH_LIB . "tiny_mce/tiny_mce.js',
				{$retrieved_settings},
				content_css : 'css/content.css'
			});
		";

	}
	
	private function _Tiny_MCE_Full() {
		return "
			//theme : 'advanced',
			//plugins : 'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',
			// Theme options
			//theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
			//theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
			//theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
			//theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
			//theme_advanced_toolbar_location : 'top',
			//theme_advanced_toolbar_align : 'left',
			//theme_advanced_statusbar_location : 'bottom',
			//theme_advanced_resizing : true,
			// Drop lists for link/image/media/template dialogs
			//template_external_list_url : 'lists/template_list.js',
			//external_link_list_url : 'lists/link_list.js',
			//external_image_list_url : 'lists/image_list.js',
			//media_external_list_url : 'lists/media_list.js',
			// Replace values for the template plugin
			//template_replace_values : {
			//	username : 'Some User',
			//	staffid : '991234'
			//}";
	}
	
	private function _Tiny_MCE_Default() {
		return "
			width : '100%',
			theme : 'advanced',
			theme_advanced_buttons1 : 'cleanup,|,undo,redo,|,bold,italic,underline,|,link,|,bullist,numlist,outdent,indent,|,sub,sup,|,charmap',
			theme_advanced_buttons2 : '',
			theme_advanced_buttons3 : '',
			theme_advanced_buttons4 : '',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left',
			theme_advanced_statusbar : false,
			plugins : 'autoresize'";
	}
}
