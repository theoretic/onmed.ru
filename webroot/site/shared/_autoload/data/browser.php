<?
/*
client checks
AT
24.07.23
*/

//Browser check
$browser = new Browser();
define( 'IS_IE', $browser->is('ie') );
define( 'IS_MOBILE', $browser->is('mobile') );