<?
/*
api: products search
AT
17.10.23
*/

$skipCSRF = 1;

$search = $input->get->search;

if( !$matches = $pages->find("title|longtitle|summary*=$search,sort=title") )
	return [];

foreach($matches as $match)
	{
	$results[] = [
		'title' => $match->title,
		'url' => $match->url,
		//'image' => $match->images->first()->url,
		];
	}

return $results;