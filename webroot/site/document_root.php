<?
/*
DOCUMENT_ROOT global constant
AT
09.10.23
*/

if( isset($_SERVER['DOCUMENT_ROOT']) ) define( 'DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] );

else{
	//CLI
	$dir = __DIR__;
	while( !is_file("$dir/index.php") ){
		$dir = str_replace('\\','/',$dir);
		$parts = explode('/',$dir);
		array_pop($parts);
		$dir = implode('/',$parts);
	}
	define( 'DOCUMENT_ROOT', $dir );
}