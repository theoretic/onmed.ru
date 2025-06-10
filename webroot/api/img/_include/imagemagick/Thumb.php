<?
/*
class Thumb
using imagemagick
cli only

avif support added
webp support added
progressive JPEG

AT
02.02.23
*/

class Thumb extends AbstractThumb {

	function __construct($_file,$_config) {
		if( !parent::__construct($_file,$_config) ) return false;
		$this->imagemagickPath = $this->config[$this->config['os']]['imagemagickPath'];
	}

	function resize($_arguments) {
		extract($_arguments);
		if(!$width || !$height) return false;

		$x=($width)? 'x':'';
		$this->imagemagickArgs[]="-resize {$width}{$x}{$height}{$postfix}";

		return true;
	}

	function crop($_arguments) {

// arguments=> { ["width"]=> float(220) ["height"]=> int(220) ["srcWidth"]=> float(417) ["srcHeight"]=> int(220) ["focalPoint"]=> array(2) { ["x"]=> string(5) "37.4%" ["y"]=> string(5) "47.5%" } } 

//echo '$_arguments:', var_dump($_arguments);//

/*
xf = srcWidth * focalPoint[x]/100
x = xf - width/2 = ( srcWidth * focalPoint[x]/100 ) - width/2
*/
		extract($_arguments);
		if(!$width && !$height) return false;
		if(!$srcWidth && !$srcHeight) return false;

		$x=0; $y=0; //default

//["focalPoint"]=> array(2) { ["x"]=> string(5) "37.4%" ["y"]=> string(5) "47.5%" } } 
		if( $focalPoint ) {

//echo '$focalPoint:', var_dump($focalPoint);//

			$x = (float)str_replace('%','',$focalPoint['x']);
			$x = ( $srcWidth * $x / 100 ) - round($width/2);
			$x = round($x);
//echo '$srcWidth, $width:', var_dump($srcWidth, $width);//
			$x = $x <= $srcWidth-$width? $x : $srcWidth-$width;
			$x = $x>=0? $x : 0;
//echo '$x:', var_dump($x);//

			$y = (float)str_replace('%','',$focalPoint['y']);
			$y = ( $srcHeight * $y / 100 ) - round($height/2);
			$y = round($y);
			$y = $y <= $srcHeight-$height? $y : $srcHeight-$height;
			$y = $y>=0? $y : 0;
		}

//echo "-crop {$width}x{$height}+$x+$y";//

		$this->imagemagickArgs[]="-crop {$width}x{$height}+$x+$y";

		return true;
	}

	//watermarking

	function watermark($_args) {
		//2do
		return true;
	}

	//postprocess

	function defineUnsharp() {
		/*
manual
radius: The radius of the Gaussian, in pixels, not counting the center pixel (default 0).
sigma: The standard deviation of the Gaussian, in pixels (default 1.0).
gain: The fraction of the difference between the original and the blur image that is added back into the original (default 1.0).
threshold: The threshold, as a fraction of QuantumRange, needed to apply the difference amount (default 0.05).

tests
radius: can be kept as default
sigma: ! oreol radius around edges, auto values are good, could be smaller than defaults
gain: !!! the intensivity of effect, defaults may need to be trimmed down
threshold: !! the LOWER it is the SMALLER details are subject to be sharped
		*/

		if(!$this->data) return false;
		extract($this->data);

		$unsharpMatrix=array(
			'gain'=>array(
				'area'=>array(
					1000000 => .7,
					100000 => .5,
					10000 => .3,
					1000 => .1
					),
				),
			'threshold'=>array(
				'area'=>array(
					1000000 => .04,
					100000 => .03,
					10000 => .02,
					1000 => .01
					),
				),
			);

		$srcArea = $srcWidth * $srcHeight;

		$width = (int)$width;
		$height = (int)$height;
		$area = $width * $height? : $width * $width? : $height * $height;

		$areaScale = $area / $srcArea;

		$this->unsharpParams=array(
			'radius'=>0,
			'sigma'=>.7,
			'gain'=>.7,
			'threshold'=>.04,
			);

		foreach($unsharpMatrix as $parameter=>$values)
			{
			foreach($values['area'] as $areastep => $amount)
				{
				if($area>$areastep) continue;

				$areastepnext=next($values['area']);
				prev($values['area']);

				if($area<=$areastep && $area>$areastepnext)
					$this->unsharpParams[$parameter]=$amount;
				}
			}

		return true;
	}

	function rotate($angle){
		
	}

	function postprocess() {
		$this->defineUnsharp();
		extract($this->unsharpParams);

		switch ($this->data['extension']){
			case 'webp':
				//$this->imagemagickArgs[]='-emulate-jpeg-size true';//??
				if( $this->config['webp']['quality'] ) $this->imagemagickArgs[] = "-quality {$this->config['webp']['quality']}";
				if( $this->config['webp']['method'] ) $this->imagemagickArgs[] = "-define webp:method={$this->config['webp']['method']}";
			break;

			case 'avif':
				//as of 2022-01-17, may have no effect because imagemagick avif support is still not very well implemented
				if( $this->config['avif']['quality'] ) $this->imagemagickArgs[] = "-quality {$this->config['avif']['quality']}";
			break;
		}

		$this->imagemagickArgs[]="-strip +profile \"*\" -unsharp {$radius}x{$sigma}+{$gain}+{$threshold} ";

		return true;
	}

	function finalize() {
		$srcFile = $this->data['srcFile'];
		$file = $this->data['file'];
//		echo "-$srcFile- -$file-";//srcFile may have wrong extension like non-existent 'src.webp' (src.jpg is existent)

		$command="\"{$this->imagemagickPath}convert\" \"$srcFile\" ";
		$command.=implode(' ', $this->imagemagickArgs);
		$command.=" \"$file\"";

		switch($this->config['mode']) {
			case 'batch':
				$this->Batch->add($command);
			break;

			default://cli
				// ` - this is a shortcut to shell_exec()
				//$result=`$command`;
				
				$cmd_result = null;
				exec($command);
				
				//var_dump($command); die();
				$this->Batch->remove($file);
		}

		return true;
	}

}