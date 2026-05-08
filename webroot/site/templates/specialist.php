<?
/*
Specialist template
AT
08.05.26
*/

namespace ProcessWire;

$image = $page->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

//include "specialist/views/{$input->urlSegments[1]}.php" if file exists, otherwise include default.php
//if $input->urlSegments[1] == 'reg' include 'specialist/views/reg.php' only if $page->id_medflex is set, otherwise include default.php

switch(true) {
	case $input->urlSegments[1] == 'reg' && $page->id_medflex:
		include 'specialist/views/reg.php';
	break;

	default:
		include 'specialist/views/default.php';
}