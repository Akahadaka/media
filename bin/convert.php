<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();

include(ASH_ROOTPATH . 'bin/models/imdb.class.php');

$form = new Ashtree_Form();
$form->method = 'get';

$source = $form->createField('text');
$source->name  = 'source';
$source->label = 'Media Source Folder';
$source->value = 'C:\Temp';

$dest = $form->createField('text');
$dest->name  = 'destination';
$dest->label = 'Media Destination Folder';
$dest->value = 'C:\Media';

$submit = $form->createField('submit');
$submit->name  = 'action';
$submit->value = 'Apply';

$form->addField($source);
$form->addField($dest);

$form->addControl($submit);

$tpl->form = $form();

echo dump($_POST, 1);

$tpl->title = 'Where is your media?';
if (isset($_POST['action'])) {
	$tpl->title = 'List of Media files to convert';
}