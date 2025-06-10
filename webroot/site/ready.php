<?
/*
hooks
AT
27.06.23
*/

foreach( glob( DOCUMENT_ROOT."/site/shared/hooks/*.php") as $hook )
	include_once $hook;