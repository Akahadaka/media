<?php
$htm = Ashtree_Html_Page::instance();
$tpl = Ashtree_Common_Template::instance();

$htm->css = 'reset';
$htm->css = 'text';
$htm->css = '960';
$htm->css = 'sorter';

$htm->jss = 'scripts';

$tpl->region_sidebar_first_grid = 'grid-3';
$tpl->region_content_grid = 'grid-12';
$tpl->region_sidebar_second_grid = 'grid-3';