<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();

Ashtree_Common_Debug::debugbar('OFF');

// User security levels
// set to 0 for this page
$usr = Ashtree_Common_Secure::instance();
$usr->security   = SECURE_NONE;
$usr->success = Ashtree_Common_Secure::gotopath();


// Log the user out
// and destroy any session/cookie info
// belonging to that user
if (isset($_GET['action']) && ($_GET['action'] == 'LOGOUT'))
{
	$userinfo = Ashtree_Common_Secure::userinfo();
	Ashtree_Common_Secure::deauth(ASH_SITE_NAME);

	$msg->message = "SUCCESS:: '{$userinfo['username']}' has been logged out successfully";
	redirect(ASH_ROOTNAME);
}

// Login form
// built with form widgets
// conforming to Ashtree_Common_Secure
// @TODO decouple Ashtree_Common_Secure
$gotopath = Ashtree_Common_Secure::gotopath();

// @BUG for login redirect on login/register/forgot pages
if ((strpos($gotopath, 'login') !== false) || (strpos($gotopath, 'register') !== false) || (strpos($gotopath, 'forgot') !== false)) $gotopath = false;

// Pre-poulate the username
if (isset($_SESSION['forgot'])) {
	$_POST['login']['username'] = $_SESSION['forgot'];
	unset($_SESSION['forgot']);
}

$form = new Ashtree_Form();
$form->method = 'post';
$form->action = ($gotopath) ? $gotopath : ASH_ROOTNAME . 'user/';

// USERNAME
$username = $form->createField('text');
$username->name        = 'login[username]';
$username->label       = 'Username';
$username->placeholder = 'email@example.com';
$username->value       = @$_POST['login']['username'];
$username->help        = 'Use your email address as your username';

// PASSWORD
$password = $form->createField('password');
$password->name        = 'login[password]';
$password->label       = 'Password';
$password->placeholder = 'password';
$password->value       = @$_POST['login']['password'];

// REMEMBER
$remember = $form->createField('checkbox');
$remember->name    = 'login[remember]';
$remember->label   = 'Remember Me?';
$remember->value   = @$_POST['login']['remember'];

// LOGIN
$login = $form->createControl('submit');
$login->value = 'Login';

// REGISTER
$register = $form->createControl('button');
$register->href    = ASH_ROOTNAME . 'user/register';
$register->value = 'Register';

// FORGOT
$forgot = $form->createField('custom');
$forgot->id    = "forgot";
$forgot->value = '<a href="' . ASH_BASENAME . 'forgot">I have forgotten my username and/or password</a>';

// Build the form
// and add to page template
$form->addField($username);
$form->addField($password);
$form->addField($remember);

$form->addControl($login);
$form->addControl($register);

#$form->addField($forgot);

$tpl->form = $form();