<?php
/**
 * Using Ashtree CMS
 * @author andrew.nash
 * @license FAN (Free for All for Now)
 */

// Bootstrap is required to kickstart the CMS
// Default location is /lib/Ashtree/Common/Bootstrap.php
// All pages make use of the index so this is required only once
// Note: Plugins, etc using the Ashtree libraries but not this index will need the include
require('lib/Ashtree/Common/Bootstrap.php');

// Manually disbale the debugbar
#Ashtree_Common_Debug::debugbar('off');

// The page class ensures that the entire site is to WH3C Schools standards
// Multiple instances can be created to branch page structure
// By default it loads jQuery
$htm = Ashtree_Html_Page::instance();

// Choose the default theme the site shoudl start with
// Internal default is wireframe
$htm->set_theme('wireframe');

// Set the copyright year
$htm->set_year(2012);

// Set the default stylesheet to use
// Also set any page specific stylesheet
$htm->css = 'styles';
$htm->css = (isset($_GET['modal'])) ? 'modal' : '';
$htm->css = $htm->template;

// Similarly set the default javascript include
// And and page specific javascript includes
$htm->jss = 'scripts';
$htm->jss = $htm->template;

// Manually include any common files required
// Note that /inc/*.inc.php file are automatically included
#include("inc/{$htm->template}.php");
#include(ASH_ROOTPATH . 'inc/scripts.js.php');

// Create all the template data
// for populating the template
$tpl = Ashtree_Common_Template::instance();

// Load the template file
$tpl->template = TRUE;

// Decide how to use the index
// to check if it should be wrapped in the index template
// by looking for the "modal" flag in the URL
$tpl->modal = isset($_GET['modal']);

// Generate a token
// to validate that the login attempt
// is genuinely from Ashtree
$tpl->login_token = Ashtree_Common_Secure::token();

// Before including the templates we include any binaries that may still modify the page
// Default location is in the /bin folder
$htm->include_binaries('index');
$htm->include_binaries($htm->template);

// Keep some user variables for template use
// The most obvious being the user display name
// and user security level
$tpl->user = isset($usr->userinfo) ? $usr->userinfo : array();

// Now the page is ready to be created
// Anything below this point will appear in the body tag
$htm->invoke();

// Write a list of messages to the screen
// directed at the user from the software
$msg = new Ashtree_Common_Message();
$tpl->region_preface_first_inner = $msg->list_messages();

// Append it to the index frame
$tpl->region_content_inner = $tpl->include_template($htm->template);
$tpl->region_content_inner .= $tpl->body_from_binaries;
// Finally we will output our tempate content inside the main template
// By default this is the index found in themes/themename/bin/templates/index.tpl.php
$htm->print_template('index');
