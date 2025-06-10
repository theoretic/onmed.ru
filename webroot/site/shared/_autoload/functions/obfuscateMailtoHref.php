<?
/*
AT
25.07.22
*/

function obfuscateMailtoHref( $email ){
	return "<a href=\"javascript:window.location.href=atob('" . base64_encode("mailto:$email") . "')\" style=\"unicode-bidi: bidi-override; direction: rtl;\">" . strrev($email) . "</a>";
}