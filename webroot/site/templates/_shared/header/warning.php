<?
/*
warning
AT
16.11.23
*/

//checking whether current user has disabled the current warning

//echo 'disabled warnings: ', var_dump( $_SESSION['disabledWarnings'] );//

if( $warningPage->id && !$_SESSION['disabledWarnings'][$warningPage->id] ) include '_shared/header/warning-template.php';