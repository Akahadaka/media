<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();

include(ASH_INC . 'person.model.php');

Ashtree_Common_Debug::debugbar('OFF');

$mdl = new Person_Model();

if (isset($_POST['submit'])) {
	if ($mdl->sendmail_forgot($_POST['email'])) {
		Ashtree_Common_Message::message('success', 'Your new pasword has been sent to your email ' . $_POST['email']);
		$_SESSION['forgot'] = $_POST['email'];
		redirect(ASH_BASENAME . 'login');
	} else {
		Ashtree_Common_Message::message('failure', 'Your username can not be found on our system. Please try again');
	}
}

// User security levels
// set to 0 for this page
$usr = Ashtree_Common_Secure::instance();
$usr->security   = SECURE_NONE;
$usr->success = Ashtree_Common_Secure::gotopath();

$form = new Ashtree_Form();
$form->method = 'post';
$form->action = '';

// USERNAME
$username = $form->createField('text');
$username->name        = 'email';
$username->label       = 'Username';
$username->placeholder = 'email@example.com';
$username->value       = @$_POST['email'];
$username->help        = 'Use your email address as your username';

// FORGOT
$forgot = $form->createControl('submit');
$forgot->name   = 'submit';
$forgot->value  = 'Send new password';

// Build the form
// and add to page template
$form->addField($username);

$form->addControl($forgot);

$tpl->form = $form();