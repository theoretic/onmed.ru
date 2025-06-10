<?
/*
AT
11.03.19
*/

class Less extends Parser
	{
	var
		$includes = [
			'_include/less/lessc.php',
			]
		;

	function parse()
		{
		$parser = new lessc;
		$this->css = $parser->parse($this->css);
		}
	}