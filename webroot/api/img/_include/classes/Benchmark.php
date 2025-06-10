<?
/*
class Benchmark
php8
AT
20.01.22
*/

class Benchmark
	{
	var
		$marks
		;

	function __construct()
		{
		$this->mark('start');
		}

	static function microtime2float()
		{
		list($msec,$sec)=explode(' ', microtime());
		return (float)( (float)$msec + (float)$sec );
		}

	function mark($_mark)
		{
		$this->marks[$_mark]=$this->microtime2float();
		}

	}


?>