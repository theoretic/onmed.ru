<?
/*
$browser->can('swipe')
$browser->has('touch')
$browser->is('mobile')

AT
28.11.18
*/

class Browser extends \Wolfcast\BrowserDetection
	{
	
	public
		$name,
		$userAgent,
		$version,
		$platform,
		$platformName,
		$platformVersion
		;

	public function __construct()
		{
		parent::__construct();
		$this->name = $this->getName();
		$this->userAgent = $this->getUserAgent();
		$this->version = $this->getVersion();
	
		$this->platform = $this->getPlatform();
		$this->platformName = $this->getPlatformVersion();
		$this->platformVersion = $this->getPlatformVersion(true);

		}

	public function is($property)
		{
		switch($property)
			{
			case 'mobile':
				return $this->isMobile();
			break;

			case 'desktop':
				//var_dump(!$this->isMobile());
				return !$this->isMobile();
			break;

			case 'robot':
				return $this->isRobot();
			break;

			case 'ie':
				return $this->getName() == Wolfcast\BrowserDetection::BROWSER_IE;
			break;

			default:
				return false;
			}
		}

	public function has($property)
		{
		switch($property)
			{
			case 'touch':
				return $this->isMobile();
			break;

			case 'swipe':
				return $this->isMobile();
			break;

			case 'hover':
				return !$this->isMobile();
			break;

			case 'clip-path':
				return $this->getName() == Wolfcast\BrowserDetection::CHROME;
			break;

			default:
				return false;
			}
		}

	public function can($property)
		{
		switch($property)
			{
			case 'swipe':
				//just an example
				return $this->is('phone') || $this->is('mobile');
			break;

			default:
				return false;
			}
		}

	}

?>