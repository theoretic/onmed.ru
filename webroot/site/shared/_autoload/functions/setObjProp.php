<?
/*
Set object property
AT
06.06.22
*/

function setObjProp( &$obj, $keys, $value ){
	$obj = (Object)$obj;
	//$keys is supposed to be an array or a string like key.subkey.subsubkey
	if( !is_array($keys) ) $keys = explode('.',$keys);

//echo '$keys: ', var_dump($keys);

	$current = $obj;
	foreach( $keys as $key ){
		if( !isset($current->$key) ) $current->$key = (Object)[];
		$current = &$current->$key;
	}
	$current = $value;
//echo '$obj after: ', var_dump($obj);//
	return $obj;
}