<?
/*
default css files
AT
27.08.25
*/

$css = [
	'/site/assets/css/_core.css'				=> ['region'=>'head'],
	'/site/assets/css/bvi.css'					=> ['region'=>'head'],
];

if( IS_WINTER_HOLIDAYS ) $css[] = '/site/assets/css/snow.css';