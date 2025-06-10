<?
/*
AT
01.07.21
*/

class Sass extends Parser
	{
	var
		$includes=array(
			'_include/sass/SassFile.php',
			'_include/sass/SassException.php',
			'_include/sass/tree/SassNode.php',
			'_include/sass/SassParser.php',
			)
		;

	function parse()
		{
		/*
		$options = array(
		'style' => 'nested',
		'cache' => FALSE,
		'syntax' => 'sass',
		'debug' => TRUE,
		//'extensions' => array('Compass','Own')
		);
		*/
		$SassParser=new SassParser($options);
		$SassParser->toCss($this->css,false);
		return $this->css;
		}
	}