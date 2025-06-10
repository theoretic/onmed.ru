<?
/*
Admin page tool page
AT
29.05.23
*/

namespace ProcessWire;

class AdminPageTool extends Process {

////

	public static function getModuleInfo() {

		return [
			'title' => 'Custom admin page', 
			'version' => 003,
			'summary' => 'Custom admin page',
			'href' => 'http://atis.pro',
			//'singular' => true,
			//'autoload' => true,
		];
	}

	public function __construct(){
		//i18n constants
		//include DOCUMENT_ROOT."/site/shared/data/i18n.php";
	}

	public function ___execute() {
		$page = $this->wire()->page;
		$parts = explode('/', $page->url);
		//removing empty element before first slash if present
		if( current($parts)=='' ) array_shift($parts);
		//removing the root page path
		array_shift($parts);
		//removing empty element after last slash if present
		if( end($parts)=='' ) array_pop($parts);

		$controller = implode('/', $parts);
		$controllerPath = DOCUMENT_ROOT."/site/shared/modules/AdminPageTool/controllers/$controller";
		$controllerFile = "$controllerPath/$controller.php";
		$templatesPath = DOCUMENT_ROOT."/site/templates/_adminPageTool/$controller";
		$cssFile = "/site/assets/css/_adminPageTool/$controller.css";
		$jsPath = "/site/assets/js/_adminPageTool/$controller";

		foreach( glob("{$_SERVER['DOCUMENT_ROOT']}$jsPath/*.js") as $jsFile ){
			$jsFile = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $jsFile );
			$jsFile = str_replace( '//', '/', $jsFile );
			$this->config->scripts->add($jsFile);
		}

		$this->config->styles->add($cssFile);

		$output = include $controllerFile;
		return $output;
	}

}