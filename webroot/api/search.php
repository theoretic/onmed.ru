<?
/*
api: products search
AT
22.09.25
*/

$skipCSRF = 1;

$search = $input->get->search;

if( !$matches = $pages->find("title|longtitle|summary*=$search,template!=feedback,sort=title") )
	return [];

foreach($matches as $match)
	{
	$results[] = [
		'title'			=> $match->title,
		'url'			=> $match->url,
		'template'		=> $match->template,
		//'image'		=> $match->images->first()->url,
		];
	}

return $results;