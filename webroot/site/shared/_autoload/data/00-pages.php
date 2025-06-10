<?
/*
pages loaded by default
AT
19.12.23
*/

$homePage = $pages->get('/');
$aboutPage = $pages->get('name=o-nas');
$discountsPage = $pages->get('template=discounts');
$branchPages = $pages->find('template=branch');

if( $page->template == 'branch')
	$branchPage = $page;
else
	$branchPage = $page->parents('template=branch')->last();

//echo '$branchPage: ', var_dump($branchPage);//

$specializationPages = $pages->find("template=specialization,sort=title");
$offersPage = $branchPage->get("template=offers,include=all");
$offerPages = $pages->find("template=offer,sort=title");
$specialistPages = $pages->find("template=specialist,sort=title");

$warningPage = $pages->get("template=warning,status!=unpublished");
//echo '$warningPage: ', var_dump($warningPage);