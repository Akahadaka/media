<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();
#$sql = Ashtree_Database_Connection::instance(ASH_SITE_NAME);

include(ASH_ROOTPATH . 'bin/models/person.model.php');

Ashtree_Common_Debug::debugbar('OFF');

// Apply CAPTCHA
$htm->jss = ASH_PLUGINS . "jquery.qaptcha/jquery/jquery.ui.touch.js";
$htm->jss = ASH_PLUGINS . "jquery.qaptcha/jquery/QapTcha.jquery.js";
$htm->css = ASH_PLUGINS . "jquery.qaptcha/jquery/QapTcha.jquery.css";

$message_form_lock   = "Please SLIDE from left to right to unlock";
$message_form_unlock = "The form is unlocked";
$qaptcha_php_file    = ASH_PLUGINS . "jquery.qaptcha/php/Qaptcha.jquery.php";
$form_submit_qaptcha = "Please enable javascript on your browser to unlock this form";

$htm->jquery = <<<JQUERY
    // Apply Captcha
    $('#captcha').QapTcha({
        autoSubmit : false,
        autoRevert : true,
        PHPfile : '{$qaptcha_php_file}',
        txtLock :'{$message_form_lock}',
        txtUnlock :'{$message_form_unlock}'

    });

JQUERY;

$htm->style = "
.QapTcha {
	width:90%;
}
";

$mdl = new Person_Model();

// User security levels
// set to 0 for this page
$usr = Ashtree_Common_Secure::instance();
$usr->security   = SECURE_NONE;
$usr->success = Ashtree_Common_Secure::gotopath();

//Saving the data
if (isset($_POST['submit'])) {
	// check if $_SESSION['qaptcha_key'] created with AJAX exists
	if(isset($_SESSION['qaptcha_key']) && !empty($_SESSION['qaptcha_key'])) {
	
		$qaptcha_key = $_SESSION['qaptcha_key'];
	
		// check if the random input created exists and is empty
		if(isset($_POST[''.$qaptcha_key.'']) && empty($_POST[''.$qaptcha_key.''])) {
			$mdl->setVars($_POST);
			if ($mdl->insert($sql)) {
				$mdl->sendmail_welcome();
				$mdl->sendmail_notify();
				Ashtree_Common_Message::message('success', 'Your registration has been successful.');
				redirect(($usr->success != '') ? $usr->success : ASH_ROOTNAME . 'user/');
			} else {
				Ashtree_Common_Message::message('failure', 'There was an error. Please try again');
			}
		}
	}  else {
		Ashtree_Common_Message::message("failure", $form_submit_qaptcha);
	}
	unset($_SESSION['qaptcha_key']);
}

Ashtree_Common_Message::message('info', 'You can login as soon as you complete this form. Your login details will also be sent to you by email for your records.');

// Login form
// built with form widgets
// confirming to Ashtree_Common_Secure
// @TODO decouple Ashtree_Common_Secure
$form = new Ashtree_Form();
$form->method = 'post';
$form->action = '';
$form->autocomplete = false;

// EMAIL
$email = $form->createField('text');
$email->name        = 'email';
$email->label       = 'Email Address';
$email->placeholder = 'email@example.com';
$email->value       = @$_POST['email'];
$email->help        = 'Your email address will also be your username for logging into the system';
$email->validate->required    = true;
$email->validate->email       = true;
$email->validate->remote      = ASH_ROOTHTTP . 'get/checkemail.php';
$email->validate->setMessage('email', 'This needs to be a valid email address');
$email->validate->setMessage('required', 'This field is required');
$email->validate->setMessage('remote', 'This user already exists on our system');

// PASSWORD
$password = $form->createField('password');
$password->name        = 'password';
$password->label       = 'Password';
$password->placeholder = 'password';
$password->value       = @$_POST['password'];
$password->validate->required    = true;

// CONFIRM PASSWORD
$confirm_password = $form->createField('password');
$confirm_password->name        = 'confirm_password';
$confirm_password->label       = 'Confirm Password';
$confirm_password->placeholder = 'confirm password';
$confirm_password->value       = @$_POST['confirm_password'];
$confirm_password->validate->required    = true;
$confirm_password->validate->equalTo  = '#password';
$confirm_password->validate->setMessage('equalTo', 'Please enter the same password again to confirm');

// NAME
$firstname = $form->createField('text');
$firstname->name        = 'firstname';
$firstname->label       = 'Name';
$firstname->value       = @$_POST['firstname'];
$firstname->validate->required    = true;

// SURNAME
$lastname = $form->createField('text');
$lastname->name        = 'lastname';
$lastname->label       = 'Surname';
$lastname->value       = @$_POST['lastname'];
$lastname->validate->required    = true;

// CONTACT NUMBER
$contact_number = $form->createField('text');
$contact_number->name        = 'phone';
$contact_number->label       = 'Contact Number';
$contact_number->value       = @$_POST['phone'];

// SPECIAL REQUEST
$special = $form->createField('checkbox');
$special->name    = 'special';
$special->label   = 'Request special access privileges (requires verification with DEA)';
$special->value   = 1;
$special->checked = @$_POST['special'];

$captcha = $form->createField('custom');
$captcha->id      = 'captcha';
$captcha->class   = 'QapTcha';

// SUBMIT
$submit = $form->createControl('submit');
$submit->name  = 'submit';
$submit->value = 'Register';

// Build the form
// and add to page template
$form->addField($email);
$form->addField($password);
$form->addField($confirm_password);
$form->addField($firstname);
$form->addField($lastname);
$form->addField($contact_number);
$form->addField($special);
$form->addField($captcha);


$form->addControl($submit);

$tpl->form = $form();