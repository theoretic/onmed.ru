<?
/*
class AbstractThumb

php8
path/to/file.jpg -> path/to/100x100/file.jpg.webp
Other than original output formats (webp) added

AT
16.11.23
*/

abstract class AbstractThumb {

	function __construct($_file,$_config) {

		$this->config = $_config;
		$this->Benchmark = new Benchmark();
		$this->Batch = new Batch($_config['batch']);
		$this->file = $_file;
		if( $this->config['imagesPath'] ) $this->file = "{$this->config['imagesPath']}/{$this->file}";
		if( !$this->parse() ) return false;
		return true;
	}

	//

	function parseIPTC() {
		require_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/classes/IPTC.class.php";
		return new IPTC($this->data['srcFile']);
	}

	function parse() {

		$this->file = str_replace('//','/',$this->file);

		//dirs and paths
		$dir = $srcDir = dirname($this->file);
		$parts = explode('/',$this->file);
		$filename = array_pop($parts);
		$lastDir = array_pop($parts);
		if($dir == '') $dir='.';//here

		//name and extensions
		$parts = explode('.',$filename);
		$extension = array_pop($parts);
		$srcExtension = array_pop($parts);
		$name = $srcName = implode('.',$parts);

		$cropResizeFlag = preg_match('/^[0-9]{0,}x[0-9]{0,}$/',$lastDir);

		//looking for source file of type different from requested, like path/to/image.jpg.webp <- path/to/image.jpg
		$srcFile = "$dir/$name.$srcExtension";

		//trying to find src file located above the crop directory, like path/100x100/image.jpg <- path/image.jpg
		if( !is_file($srcFile) && $cropResizeFlag ){
			$srcDir = str_replace("/$lastDir",'',$srcDir);
			$srcFile = str_replace("/$lastDir",'',$srcFile);
		}

		if ( !is_file($srcFile) ) {
			header("HTTP/1.0 404 Not found");
			die();
		}

		$srcFile = str_replace('//','/',$srcFile);
		$properties = getimagesize($srcFile);

//echo '$properties: ', var_dump($properties);

		if(!$properties) {
			header("HTTP/1.0 404 Not found");
			die();
		}

		//setting width and height equal to original ones, will apply if everything else fails
		$width = $properties[0];
		$height = $properties[1];

		$srcAspect = round( $properties[0]/$properties[1], 10 );

		//defining new image geometry
		//path like /path/to/file/NxM/file.jpg, resize/crop needed
		if( $cropResizeFlag ) {
			list( $width, $height ) = explode( 'x', $lastDir );
			$width = (int)$width;
			$height = (int)$height;
			$aspect = $height && $width? round( $width/$height, 10 ) : false;
		}


		//handling the case when width or height are exceeding allowed max value

		if( $width > $this->config['widthMax'] ){
			$width = $this->config['widthMax'];
			$height = $aspect? ceil( $width / $aspect ) : ceil( $width / $srcAspect );
			if( $height > $this->config['heightMax'] ){
				$height = $this->config['heightMax'];
				$width = $aspect? ceil( $height * $aspect ) : ceil( $height * $srcAspect );
			}
		}

		if( $height > $this->config['heightMax'] ){
			$height = $this->config['heightMax'];
			$width = $aspect? ceil( $height * $aspect ) : ceil( $height * $srcAspect );
			if( $width > $this->config['widthMax'] ){
				$width = $this->config['widthMax'];
				$height = $aspect? ceil( $width / $aspect ) : ceil( $width / $srcAspect );
			}
		}

		//handling the case when width or height are exceeding src value

		if( $width > $properties[0] ){
			$width = $properties[0];
			$height = $aspect? ceil( $width / $aspect ) : ceil( $width / $srcAspect );
			if( $height > $properties[1] ){
				$height = $properties[1];
				$width = $aspect? ceil( $height * $aspect ) : ceil( $height * $srcAspect );
			}
		}

		if( $height > $properties[1] ){
			$height = $properties[1];
			$width = $aspect? ceil( $height * $aspect ) : ceil( $height * $srcAspect );
			if( $width > $properties[0] ){
				$width = $properties[0];
				$height = $aspect? ceil( $width / $aspect ) : ceil( $width / $srcAspect );
			}
		}

		//forcing /src/src.jpg.avif instead of /src/10000x10000/src.jpg.avif if requested image is too big
		//it's enough to store a single /src/src.jpg.avif instead of its copies in 10000x10000/ and other folders like this

		if( $width >= $properties[0] && $height >= $properties[1] && (!$aspect || ($aspect && abs(($aspect-$srcAspect)/$srcAspect)<.01 )) )
			$this->file = "$srcDir/$filename";

		//setting empty width and/or height if necessary
		$width=($width==0)? '' : $width;
		$height=($height==0)? '' : $height;

		//data
		$this->data = [
			'file'=>$this->file,
			'filename'=>$filename,
			'name'=>$name,
			'extension'=>$extension,
			'dir'=>$dir,

			'width'=>$width,
			'height'=>$height,
			'aspect'=>$aspect,

			'srcFile'=>$srcFile,
			'srcName'=>$srcName,
			'srcExtension'=>$srcExtension,
			'srcDir'=>dirname($srcFile),
			'srcWidth'=>$properties[0],
			'srcHeight'=>$properties[1],
			'srcAspect' => $srcAspect,

			'focalPoint' => [ "x"=>"50%", "y"=>"50%"], //default value
		];


		//trying to get "focal point" (crop center) from IPTC
		$IPTCObj = $this->parseIPTC();

		$special_instructions = $IPTCObj->getValue(IPTC_SPECIAL_INSTRUCTIONS);

		if ($special_instructions != "") {
			$IPTCFocalPoint = json_decode( $IPTCObj->getValue(IPTC_SPECIAL_INSTRUCTIONS) )->focalPoint;
		}

		//$IPTCFocalPoint = json_decode( $IPTCObj->getValue(IPTC_SPECIAL_INSTRUCTIONS) )->focalPoint;

		if(isset($IPTCFocalPoint)) {
			//expected format: {"x":"39.4%","y":"48.6%"}
			$this->data['focalPoint'] = (Array)$IPTCFocalPoint;
		}

		if( $_GET['debug'] == 'data' ){
			echo 'data: ', var_dump($this->data);
			die;
		}

		return true;
	}

	//defines what to do with image
	function prepareActions() {

		extract($this->data);
		extract($this->config);

		if( ($width || $height) && ($width>=$srcWidth && $height>=$srcHeight) )
			{
			//no action needed?
			return;
			}

		$targetWidth=$width;
		$targetHeight=$height;

		if(!$width && !$height)
			{
			$targetWidth=$this->config['defaults']['width'];
			$targetHeight=$this->config['defaults']['height'];
			}

		if(!$targetWidth)
			{
			$targetWidth=ceil($targetHeight * $srcAspect);
			$targetAspect=$srcAspect;
			}

		if(!$targetHeight)
			{
			$targetHeight=ceil($targetWidth / $srcAspect);
			$targetAspect=$srcAspect;
			}

		$targetAspect = $targetAspect? : $targetWidth/$targetHeight;

		if( $targetAspect != $srcAspect )//proportional scale to nearest size + crop
			{

			if( $targetAspect >= $srcAspect )//proportionnaly resize to target width then crop to target height
				{
				$resizeWidth=$targetWidth;
				$resizeHeight=ceil($targetWidth / $srcAspect);

				$cropWidth=$targetWidth;
				$cropHeight=ceil($targetWidth / $targetAspect);
				}

			else//proportionnaly resize to target height then crop to target width
				{
				$resizeWidth=ceil($targetHeight * $srcAspect);
				$resizeHeight=$targetHeight;

				$cropWidth=ceil($targetHeight * $targetAspect);
				$cropHeight=$targetHeight;
				}

			$cropSrcWidth=$resizeWidth;
			$cropSrcHeight=$resizeHeight;

			}

		else//proportional scale, no crop
			{
			$resizeWidth=$targetWidth;
			$resizeHeight=$targetHeight;
			}

		//setting actions
		if(isset($resizeWidth))
			{
			$this->actions[] = [
				'action' => 'resize',
				'arguments' => [
					'width'=>$resizeWidth,
					'height'=>$resizeHeight,
					'postfix'=>''
					],
				];
			}

		if(isset($cropWidth))
			{
			$this->actions[] = [
				'action'=>'crop',
				'arguments' => [
					'width'=>$cropWidth,
					'height'=>$cropHeight,
					'srcWidth'=>$cropSrcWidth,
					'srcHeight'=>$cropSrcHeight,

					'focalPoint' => $this->data['focalPoint'],
					],
				];
			}

		return true;
	}

	function performActions() {
		foreach($this->actions as $i=>$action)
			{
			$fname = $action['action'];//
			$this->$fname($action['arguments']);
			}
		return true;
	}

	//postprocess

	function create() {

		if(!isset($this->data)) return false;
		if( is_file($this->data['file']) ) return true;

		//output image bigger than source image: output the source image
		if(
			( $this->data['width'] >= $this->data['srcWidth'] || $this->data['height'] >= $this->data['srcHeight'] ) &&
			$this->data['aspect'] == $this->data['srcAspect'] &&
			$this->data['extension'] == $this->data['srcExtension']
			)
			{
			$this->output($this->data['srcFile']);
			die();
			}

		//filesystem
		if(!is_dir($this->data['dir']))
			{
			$permission = ($this->config['permission'])? : 0755;
			mkdir($this->data['dir'],$permission,true);
			//chmod($this->data['dir'],$permission);
			}

		$this->prepareActions();
		$this->performActions();

		if(isset($this->config['watermark'])) $this->watermark();
		$this->perform('postprocess');
		$this->perform('finalize');

		if($this->config['mode']!='batch') $this->Batch->remove($this->data['file']);

		return true;
	}

	function perform($_method,$_args=[]) {
		if(method_exists($this,$_method)) return $this->$_method($_args);
		return false;
	}

	function output($_file = false) {

		$_file = isset($_file) && $_file != false ? $_file : null;

		if (!isset($_file)) {
			$_file = isset($this->data['file']) ? $this->data['file'] : null;
		}

		if (file_exists($_file)) {

			//getimagesize may not work for webp and avif at certain hostings!
/*
			$properties = getimagesize($_file);
echo '$_file: ', var_dump($_file);//
echo '$properties: ', var_dump($properties);//
			if(!$properties) $_file = $this->config['defaults']['file'];
*/
			$stat = stat($_file);

			//$now = gmdate("D, d M Y H:i:s",time());
			//header("Expires: $now GMT");
			//header("Last-Modified: $now");
			//header("Cache-Control: no-store, no-cache, must-revalidate");
			//header("Cache-Control: post-check=0, pre-check=0",false);
			//header("Pragma: no-cache");

//echo var_dump(mime_content_type($_file));

			header("Keep-Alive: timeout=15, max=99");
			header('ETag: '.md5_file($_file));
			//header("Content-Type: {$properties['mime']}");
			header("Content-Type: ".mime_content_type($_file));
			header("Content-Length: {$stat['size']}");
			readfile($_file);

		}
		else {
			header('HTTP/1.0 404 Not found');
			//throw new Exception('File ' . $_file . ' is not found');
		}
	}

	//abstracts

	abstract function resize($_args);

	abstract function crop($_args);

	abstract function watermark($_args);

	abstract function rotate($angle);

	function copy($_args) {
		extract($_args);

		$file = $file? : $this->data['file'];
		$srcFile = $srcFile? : $this->data['srcFile'];

		if(!$file || !$srcFile) return false;
		return copy($srcFile,$file);
	}

}