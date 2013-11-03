<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();
$usr = Ashtree_Common_Secure::instance();
#$sql = Ashtree_Database_Connection::instance(ASH_SITE_NAME);

// MAIN MENU
$mainmenu = new Plugin_Jquery_Anymenu('mainmenu');
$home    = $mainmenu->createItem('Browse');
$watch   = $mainmenu->createItem('Watch', 'watch');
$convert = $mainmenu->createItem('Convert', 'convert');
$about   = $mainmenu->createItem('About', 'about');
$contact = $mainmenu->createItem('Contact', 'contact');

$mainmenu->addItem($home);
$mainmenu->addItem($watch);
$mainmenu->addItem($convert);
$mainmenu->addItem($about);
$mainmenu->addItem($contact);

$tpl->region_menu_inner = $mainmenu();

// Before we build the page we apply security
// By specifying the conditions
// And the redirects
$usr->connection = ASH_SITE_NAME;
$usr->security   = SECURE_NONE;
$usr->failure    = ASH_ROOTNAME . 'login';
$usr->denied     = ASH_ROOTNAME . 'login';


// USER MENU
$usermenu = new Plugin_Jquery_Anymenu('usermenu');
if (!$usr->userinfo) {
	$login    = $usermenu->createItem('Login', 'login');
	$register = $usermenu->createItem('Register', 'register');
	$usermenu->addItem($login);
	$usermenu->addItem($register);
} else {
	$account = $usermenu->createItem('Account', 'user/', 'user/home');
	$logout = $usermenu->createItem('Logout', 'login?action=LOGOUT');
	$usermenu->addItem($account);
	$usermenu->addItem($logout);
}
$tpl->region_user_second_inner = $usermenu();

// SECONDARY MENU
$tpl->region_page_top_inner = '';

$adminmenu = new Plugin_Jquery_Anymenu('adminmenu');
$people = $adminmenu->createItem('People', 'admin/people', '', '');

if ($usr->userinfo['security'] >= SECURE_ADMIN) {
	// Output hello message to index
	$dom = new DOMDocument();
	$hello = $dom->createElement('div');
	$hello->setAttribute('style', 'float:right');
	$hello->setAttribute('class', 'welcome-message');
	$hello->nodeValue = "Hello {$usr->userinfo['dispname']}";
	$tpl->region_page_top_inner .= $dom->saveXML($hello);
}

if ($usr->userinfo['security'] >= SECURE_FULLADMIN) {
	$adminmenu->addItem($people);
}
$tpl->region_page_top_inner .= $adminmenu();

// FOOTER
$tpl->region_footer_first_inner = "&copy; " . $htm->set_year(2013);
