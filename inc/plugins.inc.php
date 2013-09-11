<?php

// Allow modal windows
// Use a link with the class "modal"
Plugin_Jquery_Modal::Activate();

// Make pretty menus
Plugin_Jquery_Anymenu::Activate();

// Uses the TinyMCE Rich Text Editer
// Use a textarea with the class "rte"
Plugin_Jquery_Wysiwyg::Activate('tiny_mce');

// Insert Google Analytics
#Plugin_Google_Analytics::Activate('123');

// Google MAPS
#Plugin_Google_Maps::Activate('');

// ========================
// For Development Use ONLY
// ========================
// Short circuit default page security
#Plugin_Wireframe_Security::Activate(array(
#	'anonymous',
#	'registered',
#	'administrator'		
#));