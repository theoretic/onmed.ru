<?
/*
Output formatter for images inside text
https://processwire.com/blog/posts/output-formatting/
AT
14.12.23
*/

$wire->addHookAfter('FieldtypeTextarea::formatValue', function(HookEvent $e) {
	$field = $e->arguments(1);
	if($field->name != 'body') return;//

	$html = $e->return;


preg_match_all(
	'/<img[^>]*src=([\'"])(?<src>.+?)\1[^>]*>/i',
	$html,
	$matches,
);

	//echo '$matches: ', var_dump($matches);//

	foreach( $matches as $match ){
		$updatedMatch = str_replace( 'src=', "class='adaptive rounded' data-src=", $match );
		$html = str_replace( $match, $updatedMatch, $html );
	}

	$e->return = $html;

});