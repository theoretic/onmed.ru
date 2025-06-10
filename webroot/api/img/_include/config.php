<?
/*
AT
20.01.22
*/

$config = [

	//autoconfig
	'os' => strstr(PHP_OS, 'WIN') ? 'windows' : 'unix',
	'imagesPath' => "{$_SERVER['DOCUMENT_ROOT']}/site/assets/files",

	'engine'=>'imagemagick',
	'mode'=>'cli',

	'permission'=>0755,

	'defaults' => [
		'file'=>'.wait.png',
		'width'=>100,
		'height'=>80
	],

	'widthMax'=>2400,
	'heightMax'=>1280,

	'quality'=>1,

	'jpeg' => [
		'quality'=>70,
	],

	'png' => [
		'quality'=>9,
	],

	'webp' => [
		'quality'=>70,
		'method'=>4,//affects compression, 6=max
	],

	'avif' => [
		'quality'=>55,
	],

/*
'watermark'=>[
	'file'=>$_SERVER['DOCUMENT_ROOT'].'/site/assets/images/_watermark.png',
	//'file'=>$_SERVER['DOCUMENT_ROOT'].'/site/assets/images/_watermark.svg',
	'minWatermarkableDiagonal'=>200,
	'random' => [
		'steps'=>10, //toggles random watermarking
		'minScale'=>.1,
		'maxScale'=>1,
		],
	'scale'=>.1 //for non-random watermarking
	],
*/

//'logfile'=>'.log.csv',

	'windows'=>[
		'imagemagickPath' => 'D:/portable/imagemagick/',
		//'batchExt'=>'bat',
	],

	'unix' => [
		//'batchExt'=>'sh',
	],

	'batch' => [
		'dir'=>'.batch',
		'perFile'=>10,
	]
];