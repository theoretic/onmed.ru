<?
/*
class Log
AT
18.11.13
*/

class Log
	{

	function __construct($_file)
		{
		$this->file=$_file;
		$this->fp=fopen($_file,'a+');
		}

	function add($_string)
		{
		if($_string=='') return true;
		if(!$this->fp) return false;

		$_string=date('Y-m-d H:i:s ',time()).$_string.PHP_EOL;
		fwrite($this->fp,$_string);

		return true;
		}
	}

?>