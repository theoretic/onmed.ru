<?
/*
Sanitizes all get/post input automatically
AT
12.02.25
*/

namespace Processwire;

function sanitizeAll($getOrPost) {
	foreach( $getOrPost as $name => $value) {
		switch(true) {
			case strpos( $name,'email' ):
				$value = wire('sanitizer')->email($value);
			break;

			default:
				$value = wire('sanitizer')->text($value,['maxLength'=>0]);
			}
		$getOrPost[$name] = $value;
		}
	}

if( count($input->post) ) sanitizeAll($input->post);
if( count($input->get) ) sanitizeAll($input->get);

//var_dump($input->post);