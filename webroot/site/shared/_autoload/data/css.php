<?
/*
default css files
AT
13.05.26
*/

$css = [
	'/site/assets/css/_core.css'							=> ['region'=>'head'],
	'/site/assets/css/bvi.css'								=> ['region'=>'head'],
	'/site/assets/css/appointment-specialists-all.css',
];

if( IS_WINTER_HOLIDAYS ) $css[] = '/site/assets/css/snow.css';