<?
/*
Specialist template
AT
05.05.26
*/

namespace ProcessWire;

$image = $page->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

switch( true ) {
	case $input->urlSegments[1] == 'appointment' && $page->id_medflex:
		include 'specialist/views/appointment.php';
	break;
	default:
		include 'specialist/views/default.php';
}