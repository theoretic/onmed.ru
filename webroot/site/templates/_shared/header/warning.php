<?
/*
warning
AT
16.11.23
*/

//The warning is always rendered: pages are cached by StaticPages and served without PHP,
//so a per-visitor (session) check here would either be ignored or baked into the cache for everyone.
//Whether the current visitor has closed it is decided in the browser by the disabledWarning cookie
//(see warning-template.php and frontend/src/js/_core/handleWarningDisabled.js).

if( $warningPage->id ) include '_shared/header/warning-template.php';