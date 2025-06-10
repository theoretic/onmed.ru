<?
/*
Helper
Returns rendered html
AT
18.10.23
*/

class Helper{

	public static function __callStatic( $name, $args ){

		$path = '_shared/helpers';
		$PWFiles = \ProcessWire\wire('files');

		//guessing template like $path/$name.php
		$templateFileCandidate = "$path/{$name}.php";
		if( $PWFiles->exists($templateFileCandidate) ) return $PWFiles->render( $templateFileCandidate, $args );

		//guessing template like $path/$name/$args[0].php
		$templateFileCandidate = "$path/$name/{$args[0]}.php";
		if( $PWFiles->exists($templateFileCandidate) ) return $PWFiles->render( $templateFileCandidate, [ 'data'=>(Object)$args[1] ] );

		//guessing template like $args[0]
		$templateFileCandidate = $args[0];
		if( $PWFiles->exists($templateFileCandidate) ) return $PWFiles->render( $templateFileCandidate, [ 'data'=>(Object)$args[1] ] );

	}

}