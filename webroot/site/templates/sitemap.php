<?
/*
XML sitemap
https://processwire.com/talk/topic/3846-how-do-i-create-a-sitemapxml/
AT
01.01.24
*/

namespace ProcessWire;

//include("{$_SERVER['DOCUMENT_ROOT']}/site/templates/_shared/__prepend.php");

$paths = [];

$patterns = (Object)[

	'excluded' => [
		'admin',
		'data',
		'payment',
		'orders',
		'checkout',
		'sitemap',
		'sitemap.xml',
		'http404',
		'backend',
		'trash',
	],

	'lowPriority' => [
		'about',
		'size',
		'contacts',
		'feedbacks',
	],
];


////

function isRenderable( Page $page, $patterns ) {
	$return = true;

	foreach( $patterns->excluded as $sample ){

		if( strpos($page->name,$sample) !== false ) {
			$return = false;
			break;
		}
		if( strpos($page->url,$sample) !== false ) {
			$return = false;
			break;
		}

	}

	return $return;
}

function isLowPriority( Page $page, $patterns ) {
	$return = false;

	foreach( $patterns->lowPriority as $sample ){

		if( strpos($page->name,$sample) !== false ) {
			$return = true;
			break;
		}
		if( strpos($page->url,$sample) !== false ) {
			$return = true;
			break;
		}

	}

	return $return;
}

function imageData( Page $page, $image ){
	return (Object)[
		'url'					=> "https://{$_SERVER['SERVER_NAME']}{$image->url}",
		'title'					=> $image->description? : $page->longtitle? : $page->title,
		'caption'				=> $image->description? : $page->longtitle? : $page->title,
	];
}

function renderSitemapPage( Page $page, $patterns ) {
	$isLowPriority = isLowPriority( $page, $patterns );

	$lastmod = $page->modified;
	if( $lastmod < time()-86400*30 ){
		//older than ~1 month
		$lastmod = strtotime("last Monday");
	}

	$lastmod = date("Y-m-d", $lastmod);

	$data = [
		'httpUrl'				=> $page->httpUrl,
		//'lastmod'				=> date("Y-m-d", $page->modified),
		'lastmod'				=> $lastmod,
		'changefreq'			=> $isLowPriority? 'monthly' : 'weekly',
		'priority'				=> $isLowPriority? '0.5' : '1.0',
		'images'				=> [],
	];

	if($page->image) $data['images'][] = imageData($page, $page->image);
	if( $page->images ){
		if( count($page->images)>0 ){
			foreach( $page->images as $image ) $data['images'][] = imageData($page,$image);
		}
	}

	return wire('files')->render( 'sitemap/page.php', $data );
}

function renderSitemapChildren(Page $page , $patterns ) {

	$out = '';
	$newParentPages = new PageArray();
	//$childPages = $page->children('include=all');
	$childPages = $page->children();

	foreach($childPages as $childPage) {

		if ( !isRenderable($childPage,$patterns) ) continue;

		$out .= renderSitemapPage($childPage,$patterns);

		if($childPage->numChildren)
			$newParentPages->add($childPage);
		else
			wire('pages')->uncache($childPage);

	}

	foreach($newParentPages as $newParentPage) {
		$out .= renderSitemapChildren($newParentPage,$patterns);
		wire('pages')->uncache($newParentPage);
	}

	return cleanup($out);
}

function renderSitemapXML(array $paths = [], $patterns) {

	$out ='
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
';

	array_unshift($paths, '/'); // prepend homepage

	foreach($paths as $path) {
		$page = wire('pages')->get($path); 

		if(!$page->id) continue;
		if ( !isRenderable($page,$patterns) ) continue;

		$out .= renderSitemapPage( $page, $patterns );
		if($page->numChildren) $out .= renderSitemapChildren( $page, $patterns );
	}
	
	$out .= '
</urlset>
';
	return cleanup($out);
}

function cleanup($string){
	$replacements = [
		"\r",
		"\n",
		"\t",
	];

	return str_replace( $replacements, '', $string );
}

////

header("Content-Type: text/xml");
echo renderSitemapXML( $paths, $patterns );