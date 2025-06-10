<?
/*
AT
php8
30.06.21
*/

class Batch {

	function __construct($_config) {
		$this->config=$_config;
		//$this->Log=new Log('.log');
		return true;
	}

	function find($_search) {
		if(!$_search || $_search=='') return false;

		$found=false;
		$lines=array();
		foreach(glob("{$this->config[dir]}/*.batch") as $file)
			{
			$lines=file($file);
			foreach($lines as $i=>$line)
				{
				if(trim($line)==$_search)
					{
					$found=true;
					break;
					}
				}
			}
		return $found;
	}

	function remove($_search) {
		if(!$_search || $_search=='') return false;

		foreach(glob("{$this->config['dir']}/*.batch") as $file)
			{
			$lines=file($file);

			$match=false;
			foreach($lines as $j=>$line)
				{
				if(!strstr($line,$_search)) continue;
				$match=$j;
				break;
				}

			if($match===false) continue;

			unset($lines[$match]);

			//removing batch file if no line
			if(count($lines)==0)
				{
				unlink($file);
				}
			else
				{
				$fp=fopen($file,'w+');
				fwrite($fp,implode('',$lines));
				fclose($fp);
				}
			//break; //yes the same command could possibly reside in several batch files
			}
		return true;
	}

	function getNewFile() {
		return "{$this->config[dir]}/".Benchmark::microtime2float().".batch";
	}

	function getFile($_mode) {
		if($_mode=='new') return $this->getNewFile();

		$batchFiles=glob("{$this->config[dir]}/*.batch");
		if(!$batchFiles) return $this->getNewFile();

		//finding last (current) batch file
		sort($batchFiles);
		return array_pop($batchFiles);
	}

	function add($_command) {
		if($this->find($_command)) return true;

		$batchFile=$this->getFile('current');

		$lines=file($batchFile);
		$linesTotal=($lines[0]!='')? count($lines) : 0;

		$batchFile=($linesTotal < $this->config['perFile'])? $batchFile : $this->getFile('new');
		$fp=fopen($batchFile,'a+');
		fwrite($fp,$_command.PHP_EOL);
		fclose($fp);

		return true;
	}

}