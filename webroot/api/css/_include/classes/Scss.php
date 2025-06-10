<?
/*
AT
11.03.19
*/

class Scss extends Parser
	{
	var
		$includes=[
			'_include/scss/scss.inc.php',
			]
		;

	function parse()
		{
		$parser = new scssc;
		$this->css = $parser->compile($this->css);
		}
	}