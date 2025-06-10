<?
/*
IS_WINTER_HOLIDAYS
AT
27.12.23
*/

define( 'IS_WINTER_HOLIDAYS', ( date('m')==12 && date('d')>=15 ) || ( date('m')==1 && date('d')<=15 ) );