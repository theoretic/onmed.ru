<?
/*
AT
14.02.17
Basic functionality of defining user-agent by get_browser()
*/

abstract class AbstractBrowser
	{

	public function __construct()
		{
		//browscap file download each week
		$iniUrl = 'http://browscap.org/stream?q=PHP_BrowsCapINI';
		$iniFile = DOCUMENT_ROOT."/site/shared/php_browscap.ini";
		ini_set('browscap',$iniFile);

		$modifiedTime = filemtime($iniFile);		
		if( !$modifiedTime || time()-$modifiedTime > 7 * 86400 )
			{
			file_put_contents($iniFile,file_get_contents($iniUrl));			
			}

		//browser
		$browser=get_browser(null, true);
		foreach($browser as $name=>$value) $this->$name=$value;

		if(!$this->browser || $this->browser=='Default Browser')
			{
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome'))
				{
				$this->browser='Chrome';
				$parts=explode(' ',$_SERVER['HTTP_USER_AGENT']);
				foreach($parts as $part)
					{
					if(!strstr($part,'Chrome')) continue;
					$part=trim($part);
					list($tmp,$fullversion)=explode('/',$part);
					$subparts=explode('.',$fullversion);
					$this->version=$subparts[0];
					break;
					}
				}
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Firefox'))
				{
				$parts=explode(' ',$_SERVER['HTTP_USER_AGENT']);
				$nameversion=array_pop($parts);
				list($this->browser,$this->version)=explode('/',$nameversion);
				}
			if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE'))
				{
				$this->browser='IE';
				$parts=explode(';',$_SERVER['HTTP_USER_AGENT']);
				foreach($parts as $part)
					{
					if(!strstr($part,'MSIE')) continue;
					$part=trim($part);
					list($tmp,$this->version)=explode(' ',$part);
					break;
					}
				}
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Trident'))//MSIE again!
				{
				$this->browser='IE';
				$parts=explode(';',$_SERVER['HTTP_USER_AGENT']);
				$last=array_pop($parts);
				$last=substr($last,0,strpos($last,')'));
				list($revision,$this->version)=explode(':',$last);
				$this->version=(int)$this->version;
				}
			}

		$this->name=$this->browser;
		$this->version=(int)$this->version;
		}

	public function __call($name,$args)
		{
		//wrapper for functions like ->has(), ->is(), or ->can()
		return $this->$args[0];
		}
	}

?>