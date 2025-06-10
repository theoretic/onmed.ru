<?
/*
Warning disable
AT
15.11.23
*/

//echo '$warningPage: ', var_dump($warningPage);

if( $warningPage->id )
	$_SESSION['disabledWarnings'][$warningPage->id] = 1;

return[
	'success' => 1,
];