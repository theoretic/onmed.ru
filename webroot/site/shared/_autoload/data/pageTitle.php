<?
/*
Page title
AT
06.12.23
*/

$pageTitle = isset($page->longtitle) && strlen($page->longtitle) > 0 ? $page->longtitle : null;
$pageTitle = !isset($pageTitle) && isset($page->title) && strlen($page->title) > 0 ? $page->title : $pageTitle;