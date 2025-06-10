<?
/*
Field utilities
*/

namespace Field;

function implodePagesFields( $pages, $field, $separator=', ')
	{
	if( !count($pages) ) return;

	$values = [];
	foreach( $pages as $page )
		{
		if(!$page->$field) continue;
		$values[] = $page->$field;
		}

	$result = implode( $separator, $values );
	$result = (string)$result;

	//$result = strtolower($result); //!!!
	$result = mb_strtolower($result);
	return $result;
	}
