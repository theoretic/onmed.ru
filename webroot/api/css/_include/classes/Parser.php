<?
/*
AT
11.03.19
*/


class Parser
	{
	var
		$includes,
		$css
		;

	function __construct($_css)
		{
		$this->includeFiles();
		$this->css=$_css;
		return true;
		}

	function includeFiles()
		{
		foreach($this->includes as $i=>$file)
			include_once $file;
		return true;
		}
	}