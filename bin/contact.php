<?php 
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();

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

// EMAIL THE FORM
if (isset($_POST['submit'])) {
	$mailer = new Ashtree_Common_Sendmail();
	$mailer->to           = "beta+media@ashtree.co.za";
	$mailer->from         = "{$_POST['name']} <{$_POST['email']}>";
	$mailer->subject      = $mailer->title = "Media Form";
	$mailer->message      = <<<MAIL
<p>
{$_POST['message']}
</p>
<p>
--<br/>
<strong>{$_POST['name']}</strong>
<br/>
{$_POST['email']}
</p>
MAIL;
	$mailer->isHTML();
	if ($mailer->invoke()) {
		Ashtree_Common_Message::message('success', 'Thank you. Your message has been sent successfully');
		redirect(ASH_ROOTNAME . 'contact');
	} else {
		Ashtree_Common_Message::message('failure', 'There was an error. Please try again');
	}
}


$form = new Ashtree_Form();
$form->method = 'post';
$form->action = ASH_ROOTNAME . 'contact';

// NAME
$name = $form->createField('text');
$name->name        = 'name';
$name->label       = 'Full Name';
$name->placeholder = 'name & surname';
$name->value       = @$_POST['name'];
$name->validate->required    = true;

// EMAIL
$email = $form->createField('text');
$email->name        = 'email';
$email->label       = 'Email Address';
$email->placeholder = 'email@example.com';
$email->value       = @$_POST['email'];
$email->validate->required    = true;
$email->validate->email       = true;

// MESSAGE
$message = $form->createField('textarea');
$message->name        = 'message';
$message->label       = 'Message';
$message->placeholder = 'message here...';
$message->value       = @$_POST['message'];
$message->validate->required    = true;

$captcha = $form->createField('custom');
$captcha->id      = 'captcha';
$captcha->class   = 'QapTcha';


// SUBMIT
$submit = $form->createControl('submit');
$submit->name = 'submit';
$submit->value = 'Send Message';

// Build the form
// and add to page template
$form->addField($name);
$form->addField($email);
$form->addField($message);
$form->addField($captcha);

$form->addControl($submit);

$tpl->form = $form();