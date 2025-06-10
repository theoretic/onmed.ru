<?
/*
Style config
AT
15.12.23
*/

return [

	//for development
	//'save'				=> false,
	//'minify'			=> false,

	'cssPath' => "{$_SERVER['DOCUMENT_ROOT']}/site/assets/css",

	'removeUnusedCss'	=> false,

	'htmlPath' => "{$_SERVER['DOCUMENT_ROOT']}/site/templates",
	'saveRemovedCSS'	=> true,
	'protectedSelectorSamples' => [
		'[data-back]',
		':',
		'+',
		'(',
		')',
		'~',
		'@',

		'alert',
		'blockquote',
		'carousel',
		'font',
		'notification',
		'svg',

		'dt-',//dynamic tables
		'dz-',//dropzone
		'//fc',//calendar
		'expand',
		'hamburger',
		'menu',
		'is-',
		'li',
		'#pw-content-body table a',
		'icon',
		'input',
		'lg-',//light gallery
		'offer',
		'overflow',
		//'program',
		'rounded',
		'search',
		'select',
		'slide',
		'submenu',
		'video',
		'wa-',//wa-mediabox
	],

	'useBrowserPrefixes' => true,

	'hashFile' => '.hash',
	'dirPermissions' => 0755,

	'comment' => '//',
	'include' => '-include',
	'noprocess' => [
		'@import',
		],
	'prefixes' => [
		'Chrome' => ['wk','webkit',],
		'Firefox' => ['ff','moz','firefox'],
		'Internet Explorer' => ['ms','ie',],
		'IE' => ['ms','ie',],
		'Opera' => ['o','op','opera'],
		'Safari' => ['wk','sf','safari'],
	],
	'computedPrefixes' => [
		'can',
		'has',
		'is'
	],

	'parsers' => [
		'less' => 'Less',
		'scss' => 'Scss',
	],

	'cacheDir' => '.cache',
	'cacheFiles' => [
		'Chrome' => 'wk',
		'Firefox' => 'ff',
		'Explorer' => 'ie',
		'IE' => 'ie',
		'Internet Explorer' => 'ie',
		'Opera' => 'op',
		'Safari' => 'sf',
		'unknown' => '_default',
	],
];