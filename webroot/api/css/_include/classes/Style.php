<?
/*
AT
30.05.23

extra css removal

cache-control headers

PHP8

can handle 'static' addresses:

path/to/default.css -> path/to/default/

include syntax:

-include path/to/file

prefix syntax:

-webkit-
-webkit7-
-!o5-
-moz6>-
-ms>6-
-ie<8-
-ie8>-
-!ie8>-

-is_mobile-
-has_hover-
-!can_swipe-

include command can be prefixed:

-is_mobile- -include path/to/file.ext

cache file path example:

/path/to/.cache/wk39.a763.css

*/

class Style
	{

	function __construct( $_path, $_config )
		{
		$this->config = $_config;

		$_path = str_replace('.css','',$_path);
		if(substr($_path,-1)=='/') $_path=substr($_path,0,strlen($_path)-1);
		if( $this->config['cssPath'] ) $_path = "{$this->config['cssPath']}/$_path";
		$this->cssPath=$_path;

		if( $this->config['removeUnusedCss'] !== false ){
			$this->htmlPath = $this->config['htmlPath'];
			$this->setHTMLHashFile();
			$this->getHTMLFiles();
			$this->makeHTMLHash();
			$this->getSavedHTMLHash();
		}

		//injections
		$this->Browser = new Browser();

		$this->touchCacheDir();
		$this->getCSSFiles();

		//hash
		$this->makeCSSHash();
		$this->makeComputedPrefixesHash();
		$this->setCSSHashFile();
		$this->getSavedCSSHash();
		}

//

	public function make(){
		$this->chunkify();
		$this->parseChunks();
		if( $this->config['removeUnusedCss'] !== false ) $this->removeUnusedCss();
		if( $this->config['minify'] !== false ) $this->minify();
	}

//

	public function save(){
		$this->setNewCacheFile();
		$this->saveCSSCache();
		$this->saveCSSHash();
	}

//files

	private function getCSSFiles(){
		//pass 1
		$this->CSSFiles=glob("{$this->cssPath}/*.*ss");

		//pass2: looking for included files
		$this->includeCSSFiles();
	}

	private function getHTMLFiles(){
		include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/rglob.php";
		$this->HTMLFiles = rglob("{$this->htmlPath}/*.php");
	}

//includes

	private function includeCSSFiles(){
		// any file may contain line like -include path/to/another/file.ext . These files should be placed into $this->CSSFiles array

		$files=$this->CSSFiles;
		foreach($files as $i=>$file)
			{
			$lines=file($file);
			unset($includeCSSFiles);
			foreach($lines as $j=>$line)
				{
				if(!$includeFile=$this->findInclude($line)) continue;
				if( $this->config['cssPath'] ) $includeFile = "{$this->config['cssPath']}/$includeFile";
				$includeCSSFiles[]=$includeFile;
				}
			$files=$this->mergeIncludeFiles($files,$includeCSSFiles,$i);
			}
		$this->CSSFiles=$files;
	}

	private function findInclude($_line){
		//includes can be controlled by computed prefixes
		//example: -is_mobile- -include path/to/mobile.css

		//if(strpos($_line,$this->config['include'])!==0) return false;
		if( !strstr($_line,$this->config['include']) ) return false;

		//rejecting if prefix exists but does not fit
		if( $prefix = $this->findPrefix($_line) )
			{
			$parsedPrefix = $this->parsePrefix($prefix);
			}

		if( $parsedPrefix && !$this->fits($parsedPrefix) ) return false;

		$_line=preg_replace('/\s+/s',' ',$_line);
		$_line=$this->stripSingleLineComment($_line);
		if($prefix) $_line=$this->stripPrefix($_line);
		list($tmp,$includeFile)=explode(' ',$_line);
		return $includeFile;
	}

	private function mergeIncludeFiles($_files,$_includeCSSFiles,$_i){
		if( !is_array($_includeCSSFiles) ) return $_files;
		if( count($_includeCSSFiles)<1 ) return $_files;
		$filesBegin=array_slice($_files,0,$_i);
		$filesEnd=array_slice($_files,$_i);
		$files=array_merge($filesBegin,$_includeCSSFiles,$filesEnd);
		return $files;
	}

//hash

	public function isActual(){
		if( !$this->cssHash ) return false;
		if( !$this->savedCSSHash ) return false;

		if( $this->config['removeUnusedCss'] !== false ){
			if( !$this->htmlHash ) return false;
			if( !$this->savedHTMLHash ) return false;
	
		}

		$isActual = $this->cssHash == $this->savedCSSHash;
		if( $this->config['removeUnusedCss'] !== false ){
			$isActual = $isActual && ( $this->htmlHash == $this->savedHTMLHash );
		}

		return $isActual;
	}

	private function setCSSHashFile(){
		$this->cssHashFile="{$this->cssPath}/{$this->config['cacheDir']}/{$this->config['hashFile']}";
	}

	private function setHTMLHashFile(){
		$this->htmlHashFile="{$this->htmlPath}/{$this->config['hashFile']}";
	}

	private function makeComputedPrefixesHash(){
		//finds all computed prefixes and makes a short hash for later use in cache filename
		//hash is built using only the prefixes valid for current browser
		$foundPrefixes = [];

		foreach($this->CSSFiles as $file){
			$lines = file($file);
			foreach($lines as $line){
				if( !$computedPrefix = $this->findComputedPrefix($line) ) continue;
				$foundPrefixes[] = $computedPrefix;
			}
		}

		$foundPrefixes = array_unique($foundPrefixes);

		//checking which prefixes fits current browser
		foreach($foundPrefixes as $i=>$foundPrefix_){
			$foundPrefix = $this->parseComputedPrefix($foundPrefix_);
			if( !$this->fitsComputed($foundPrefix) )
				unset($foundPrefixes[$i]);
		}

		if(!sizeof($foundPrefixes)){
			$this->computedPrefixesHash = '';
		}
		else{
			$this->computedPrefixesHash = implode('',$foundPrefixes);
			$this->computedPrefixesHash = md5($this->computedPrefixesHash);
			$this->computedPrefixesHash = substr($this->computedPrefixesHash,0,4);
		}
	}

	private function makeCSSHash(){
		foreach($this->CSSFiles as $file) $css.=file_get_contents($file);
		$this->cssHash = md5($css);
	}


	private function makeHTMLHash(){
		foreach($this->HTMLFiles as $file) $html.=file_get_contents($file);
		$this->htmlHash = md5($html);
	}

	private function getSavedCSSHash(){
		$this->savedCSSHash=file_get_contents($this->cssHashFile);
	}

	private function getSavedHTMLHash(){
		$this->savedHTMLHash=file_get_contents($this->htmlHashFile);
	}

	public function saveCSSHash(){
		file_put_contents( $this->cssHashFile, $this->cssHash );
	}

	public function saveHTMLHash(){
		file_put_contents( $this->htmlHashFile, $this->htmlHash );
	}

	//prefixes

	private function findPrefix($_line){
		//prefix is a line part like -something_with_or_without_digits_1234- sitting at the beginning of the line
		// -include path/to/file.ext : -include is NOT a prefix!
		if( strstr($_line,$this->config['include']) ) return false;

		$_line=trim($_line);
		$result = preg_match('/^-(\S+)-\s/',$_line,$matches);
		if( $result==0 ) return false;

		return $matches[1];
	}

	private function findComputedPrefix($_prefix){
		//computed prefixes are like can_, has_, is_
		// if finds a computed prefix, returns it in form 'is_mobile' or '!can_swipe'
		//here we suppose the the argument is a prefix without - signs, like has_swipe, NOT -has_swipe-

		//checking whether the prefix is a computed one

		foreach( $this->config['computedPrefixes'] as $computedPrefix)
			{
			$match = strpos($_prefix,"{$computedPrefix}_");
			if( $match===false ) continue;
			$match = $_prefix;
			}
		return $match;
	}

	private function parseNonComputedPrefix($_line){
		//for prefixes like wk5, !ie9
		$prefix['negation']=strstr($_line,"!");

		$prefix['abbr']=$_line;
		$prefix['abbr']=preg_replace('/([\!0-9\<>])+/','',$prefix['abbr']);

		$prefix['version']=$_line;
		$prefix['version']=preg_replace('/([\!a-z<>])+/','',$prefix['version']);
		$prefix['version']=strlen($prefix['version'])? $prefix['version']:false;

		$prefix['postfix']=$_line;
		$prefix['postfix']=preg_replace('/([\!a-z0-9])+/','',$prefix['postfix']);
		$prefix['postfix']=strlen($prefix['postfix'])? $prefix['postfix']:false;

		return $prefix;
	}

	private function parseComputedPrefix($_line){
		//for prefixes like can_, has_, is_ and their negations like !can_
		$prefix['computed'] = true;
		$prefix['negation']=strstr($_line,"!");
		list( $prefix['method'], $prefix['argument'] ) = explode('_', $_line);

		//some cleanup
		$prefix['method'] = str_replace( ['/', '\\', '!', '-', ' ', '	'],'',$prefix['method']);
		$prefix['argument'] = str_replace('-','',$prefix['argument']);
		if(strstr($prefix['argument'],' '))
			{
			$parts = explode($prefix['argument'],' ');
			$prefix['argument'] = $parts[0];
			}

		return $prefix;
	}

	private function parsePrefix($_line){
		$result = $this->findComputedPrefix($_line);
		$funcName = $result? 'parseComputedPrefix' : 'parseNonComputedPrefix';
		return $this->$funcName($_line);
	}

	private function abbr2names($_abbr){
		$names=[];
		foreach($this->config['prefixes'] as $name=>$abbrs)
			{
			if(in_array($_abbr,$abbrs))
				$names[]=$name;
			}
		return $names;
	}

	private function stripPrefix($_line){
		//-someprefix-   \t  \t  some-css-code -> some-css-code

		$_line=trim($_line);
		$_line=str_replace("\t",' ',$_line);
		$parts=explode(' ',$_line);
		$possiblePrefix=$parts[0];
		if(substr($possiblePrefix,0,1)!='-' || substr($possiblePrefix,-1)!='-') return $_line;

		unset($parts[0]);
		$result=implode(' ',$parts);
		return implode(' ',$parts);
	}

	//parse

	private function getParserName($_file){
		$extension=array_pop(explode('.',$_file));
		//ignoring the 'x' part of extension, i.e. xless should be parsed as less
		if(substr($extension,0,1)=='x') $extension=substr($extension,1);
		return $this->config['parsers'][$extension];
	}

	private function chunkify(){
		// (file01.css, file02.xless, file03.css, file04.xless) => chunks ('Less'=>(file02.xless, file04.xless), 'css'=>(file01.css, file03.css))

		$this->chunks=[];
		foreach($this->CSSFiles as $i=>$file){
			$chunkName=$this->getParserName($file);
			if(!$chunkName) $chunkName='css';//no need to use specific parser

			$lines=[];
			foreach(file($file) as $i=>$line){
				$line=$this->parseLine($line);
				if($line=='')
					{
					unset($lines[$i]);
					continue;
					}
				$lines[$i]=$line;
			}

			//$this->chunks[$chunkName].=implode('',$lines); //possible parsing errors when single-line comments like // are used not at the beginning of the line!
			$this->chunks[$chunkName].=implode(PHP_EOL,$lines);
		}
	}

	private function parseChunks(){
		$this->css='';
		foreach($this->chunks as $name=>$chunk){
			if($name=='css') //no need to parse
				{
				$this->css.=$chunk;
				continue;
				}

			//not pure css: using specific parser
			unset($Parser);
			$Parser=new $name($chunk);
			$Parser->parse();
			$this->css.=$Parser->css;
		}
	}

	private function isProcessable($_line){
		$isProcessable = true;
		foreach($this->config['noprocess'] as $noprocess)
			{
			//if(strstr($_line,$noprocess))
			if( strpos($_line,$noprocess) !== false )
				{
				$isProcessable = false;
				break;
				}
			}
		return $isProcessable;
	}

	private function parseLine($_line){
		if(!$_line || $_line=='') return '';

		//skipping @import command and other staff which is not to be processed by parser(s)
		if(!$this->isProcessable($_line)) return $_line;

		$_line=trim($_line);

		//pass 1: unsetting commented lines
		$_line=$this->stripSingleLineComment($_line);

		//pass 2: unsetting lines with -include command
		if(strpos($_line,$this->config['include'])===0) return '';

		//pass 3: checking whether the line fits current browser
		if( $this->config['useBrowserPrefixes'] !== false )
			{
			$prefix = $this->findPrefix($_line);
			if($prefix) 
				{
				$parsedPrefix=$this->parsePrefix( $prefix );
				if(!$this->fits($parsedPrefix)) return '';
//				//echo "parseLine($_line): prefix fits: -$prefix-<br>";//ok
				}
			}

		//pass4: stripping browser prefix if placed at the beginning of the line
		$_line=$this->stripPrefix($_line);
//		//echo "parseLine($_line): line without prefix: -$_line-<br>";//fails!

		return $_line;
	}

	private function stripSingleLineComment($_line){
		if(strpos($_line,$this->config['comment'])===false) return $_line;
		if($this->config['comment']=='//'){
			if(strpos($_line,'http://')) return $_line;
			if(strpos($_line,'https://')) return $_line;
			if(strpos($_line,'base64')) return $_line;
		}

		$parts=explode($this->config['comment'],$_line);
		return $parts[0];
	}

	function minify(){
		//stripping unused spaces and newlines
		$this->css=preg_replace('/\s+/s',' ',$this->css);

		//stripping multiline comments
		$this->css=preg_replace('!/\*.*?\*/!s','',$this->css);

		$this->css=str_replace("\r",'',$this->css);
	
		$this->css=str_replace(' >','>',$this->css);
		$this->css=str_replace('> ','>',$this->css);

		$this->css=str_replace(', ',',',$this->css);
		$this->css=str_replace(': ',':',$this->css);
		$this->css=str_replace('; ',';',$this->css);
		//$this->css=str_replace('+ ','+',$this->css);
		$this->css=str_replace('~ ','~',$this->css);
		$this->css=str_replace(' ~','~',$this->css);

		$this->css=str_replace(') ',')',$this->css);
		$this->css=str_replace(' (','(',$this->css);

		$this->css=str_replace('} ','}',$this->css);
		$this->css=str_replace(' {','{',$this->css);
		$this->css=str_replace(';}','}',$this->css);

		//very important patch! if not applied, we have 'element :hover' affecting EVERY element child!
		$this->css=str_replace(' :hover',':hover',$this->css);
	}

	private function fitsNonComputed($_prefix){
		if ( $_prefix['computed'] ) return false;

		$fits=true;

		if( !in_array($this->Browser->name,$this->abbr2names($_prefix['abbr'])) )
			$fits=false;

		else{
			//browser name match
			if( $_prefix['version'] && $_prefix['version']!=$this->Browser->version ){
				if(!$_prefix['postfix'])
					$fits=false;
				else{
					if( $_prefix['postfix']=='>' && $this->Browser->version >= $_prefix['version'] )
						$fits=true;
					if( $_prefix['postfix']=='<' && $this->Browser->version <= $_prefix['version'] )
						$fits=true;
				}
			}
		}

		if($_prefix['negation']!='') $fits=!$fits;

		return $fits;
		}

	private function fitsComputed($_prefix){
		//var_dump($_prefix);//
		
		if ( !$_prefix['computed'] ) return false;
		if ( !$_prefix['method'] || !$_prefix['argument'] ) return false;

		if( strstr($_prefix['method'],'!' ) ) $_prefix['method'] = str_replace('!','',$_prefix['method']);

		$_prefix['method'] = (string)$_prefix['method'];
		$fits = $this->Browser->{$_prefix['method']}($_prefix['argument']);

		if($_prefix['negation']!='') $fits=!$fits;

		return $fits;
	}

	private function fits($_prefix){
		if( !$_prefix['computed'] && !$_prefix['abbr'] ) return true;
		if( $this->config['useBrowserPrefixes'] === false ) return true;

		//selecting prefix type
		$funcName = $_prefix['computed']? 'fitsComputed' : 'fitsNonComputed';
		$result = $this->$funcName($_prefix);

		return $this->$funcName($_prefix);
	}

//remove unused css

	private function isProtectedSelector($selector){
		foreach( $this->config['protectedSelectorSamples'] as $sample ){
			if( strpos( $selector, $sample ) === false ) continue;
			//if( $selector != $sample ) continue;
			return true;
		}
		return false;
	}

	private function removeUnusedCss(){

//echo '$this->css: ', var_dump($this->css);

		$css = [];
		$removedCSS = [];

		$html = '';
		$htmlObj = new DiDom\Document;

		foreach( $this->HTMLFiles as $HTMLFile ) $html .= file_get_contents($HTMLFile);

//echo '$html: ', var_dump($html);

		//$htmlObj->loadStr( $html );
		$htmlObj->loadHtml( $html );

		$cssSettings = Sabberworm\CSS\Settings::create()->withMultibyteSupport(false);
		$oCssParser = new Sabberworm\CSS\Parser($this->css, $cssSettings);
		//$cssRenderMode = Sabberworm\CSS\OutputFormat::createCompact();
		$cssRenderMode = Sabberworm\CSS\OutputFormat::create();
		$cssParsed = $oCssParser->parse();

///*
		foreach ($cssParsed->getContents() as $oItem) {

			switch(true){
				case $oItem instanceof Sabberworm\CSS\RuleSet\DeclarationBlock:
					$oBlock = $oItem;
					$selectors = $oBlock->getSelectors();
					$remove = true;
					foreach( $selectors as $selector){
						if( $this->isProtectedSelector($selector) ){
							$remove = false;
							break;
						}
						if( count ($htmlObj->find($selector)) > 0 ){
							$remove = false;
							break;
						}
					}

					if( $remove && $this->config['saveRemovedCSS'] )
						$removedCSS[] = $oBlock->render($cssRenderMode);
					else
						$css[] = $oBlock->render($cssRenderMode);
				break;

/*
				case $oItem instanceof Sabberworm\CSS\CSSList\AtRuleBlockList:
					$selectors = $oBlock->getSelectors();
					$remove = true;
					foreach($oItem->getContents() as $oBlock) {
						if( $oBlock instanceof Sabberworm\CSS\RuleSet\AtRuleSet ){
							$css[] = $oBlock->render($cssRenderMode);
							continue;
						}

						$selector = $oBlock->getSelectors()[0];
						if( $this->isProtectedSelector($selector) ){
							$css[] = $oBlock->render($cssRenderMode);
							continue;
						}

						if( count( $htmlObj->find($selector) ) == 0 ){
//echo 'AtRuleBlockList: selector not found: ', var_dump($selector);
							if( $this->config['saveRemovedCSS'] )
								$removedCSS[] = $oBlock->render($cssRenderMode);
							continue;
						}
						$css[] = $oItem->render($cssRenderMode);

					}
				break;
*/
				//case $oItem instanceof Sabberworm\CSS\RuleSet\AtRuleSet:
				//case $oItem instanceof Sabberworm\CSS\CSSList\KeyFrame:
				default:
					$css[] = $oItem->render($cssRenderMode);

			}

		}
//*/
	//
//echo 'strlen($this->css)', strlen($this->css);
		$css = array_unique($css);
		$this->css = implode( "\n", $css );
//echo 'strlen($this->css)', strlen($this->css);

		if( $this->config['saveRemovedCSS'] ){
			$removedCSS = implode( "", $removedCSS );
			$file = "{$this->cssPath}/{$this->config['cacheDir']}/_removed.css";
			file_put_contents( $file, $removedCSS );
		}

	}

//cache

	public function isCached(){
		return $this->getActualCacheFile();
	}

	private function touchCacheDir(){
		//creates the local cache dir if not exists
		$dir=$this->cssPath.'/'.$this->config['cacheDir'];
		if( is_dir($dir) ) return;
		mkdir($dir,$this->config['dirPermissions']);
	}

	private function defineCacheFileName(){
		$fileName='_default';

		if( $this->config['useBrowserPrefixes'] !== false ){
			if($this->Browser->name){
				$fileName=$this->config['cacheFiles'][$this->Browser->name];
				if($this->Browser->version){
					$version = str_replace('.','',$this->Browser->version);
					$fileName.=$version;
				}
			}
			if($this->computedPrefixesHash)
				$fileName .= ".{$this->computedPrefixesHash}";
		}

		return $fileName;
		}

	private function setNewCacheFile(){
		$dir=$this->cssPath.'/'.$this->config['cacheDir'];
		$file = $this->defineCacheFileName();

		$file="$dir/$file.css";
		$this->cacheFile=$file;

		return $file;
	}

	private function getActualCacheFile(){
		$dir=$this->cssPath.'/'.$this->config['cacheDir'];
		$file = $this->defineCacheFileName();

		$cached = [];
		$cached += glob("$dir/$file*.css");
		$cached += glob("$dir/$file*.*.css");

		if(sizeof($cached))
			{
			arsort($cached);
			$file = $cached[0];
			$this->cacheFile=$file;
			return $file;
			}
		return false;
	}

	public function wipeCache( $_pattern='*.css' ){
		//$_pattern = $_pattern? : '*.css';
		$dir=$this->cssPath.'/'.$this->config['cacheDir'];
		//unlink($this->cssHashFile);//very important! Forces update for every cached css file

		foreach(glob("$dir/$_pattern") as $file)
			{
			if(!is_file($file)) continue;
			unlink($file);
			}
	}

	private function saveCSSCache(){
		$fp=fopen($this->cacheFile,'w+');
		fwrite($fp,$this->css);
		fclose($fp);
	}

//output

	function output(){
		header('Content-Type: text/css; charset=UTF-8');
		header('Cache-Control: public,max-age=86400');
		if( is_file($this->cacheFile) ){
			//header('last-modified:Fri, 27 Jan 2017 18:32:10 GMT');
			header('Content-Length: '.filesize($this->cacheFile));
			header('ETag: '.md5_file($this->cacheFile));
			readfile($this->cacheFile);
			return;
		}
	echo $this->css;
	}
}