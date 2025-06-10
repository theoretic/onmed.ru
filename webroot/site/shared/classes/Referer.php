<?
/*
Referer
AT
14.08.23
*/

class Referer{

	public
		$url,
		$path,
		$language,
		$page
		;

	function __construct(){

//echo '$_GET[referer]: ', var_dump($_GET['referer']);
//echo '$_SERVER[HTTP_REFERER]: ', var_dump($_SERVER['HTTP_REFERER']);

		$this->url = $_GET['referer'] !== null ? : $_SERVER['HTTP_REFERER'];
		$this->getPath();
		$this->getLanguage();
//echo '$this->language: ', var_dump($this->language);//
		$this->getPage();
		}

//

	private function getPath(){
		if( !$this->url ) return null;
		if( !strstr($this->url,$_SERVER['HTTP_HOST']) ) return null;

		$path = $this->url;
		$path = str_replace($_SERVER['HTTP_HOST'],'',$path);
		$path = str_replace( [ 'http://', 'https://' ],'',$path);

		$this->path = $path;
		return $this->path;

	}

	private function getLanguage(){
		if( !$this->path ) return null;

		$parts = explode('/',$this->path);
		$candidate = $parts[1];
		$languagePage = \ProcessWire\wire('languages')->getDefault();
		foreach( \ProcessWire\wire('languages') as $languagePageCandidate ) {
			if( $languagePageCandidate->name != $candidate) continue;
			$languagePage = $languagePageCandidate;
			break;
		}

		$this->language = $languagePage;
		return $this->language;
	}

	private function getPage(){
		if( !$this->path ) return null;

		$path = $this->path;
		if( $this->language->name !== 'default' ){
			//we need to strip language name off the $path
			$parts = explode( '/', $path );
			array_splice( $parts, 1, 1 );
			$path = implode( '/', $parts );
			if( $path=='' ) $path = '/'; //home
		}

		$refererPage = \ProcessWire\wire('pages')->get("path=$path");

		if( $refererPage->id ){
			$this->page = $refererPage;
			return $this->page;
		}

		//url segments used: stepping one level up until a referer page is found

		$parts = explode('/',$this->path);

		while( count($parts)>0 ) {
			array_pop($parts);
			$path = implode('/',$parts);
			$refererPage = \ProcessWire\wire('pages')->get("path=$path");
			if( !$refererPage->id ) continue;

			$this->page = $refererPage;
			break;
		}

		//var_dump($refererPage);
		return $this->page;
	}

}