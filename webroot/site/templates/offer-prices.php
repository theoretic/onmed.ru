<?
/*
Offer prices template
AT
29.05.25
*/

namespace ProcessWire;

$specialistPages = $branchPage->find("has_parent=" . $branchPage->get("name=doctors")->url. ",template=specialist,offers={$page},sort=title");
//echo '$specialistPages: ', var_dump($specialistPages);//

if( "$specialistPages" )
	$feedbackPages = $pages->find("template=feedback,has_parent=$specialistPages,,sort=-date,sort=-created");

//$selector = "template=feedback,specialist.offers.id={$page->id},sort=-date,sort=-created";
//echo '$selector: ', var_dump($selector);//
//$feedbackPages = $pages->find($selector);
if( $feedbackPages!==null && count($feedbackPages)>0 ){
	$totalFeedbackPages = count($feedbackPages);
	$perPage = 10;
	$currentPage = $input->urlSegments[1]? : 1;
	$start = $perPage * ($currentPage-1);
	$totalPages = ceil( count($feedbackPages)/$perPage );
	$feedbackPages->filter("start=$start,limit=$perPage");
}

$pagesWithPrices = new PageArray();
$pagesWithPrices->add($page);
$pagesWithPrices->add( $page->children("template=offer,prices.count>0,sort=title") );
if( $page->pages_with_prices !== null ) $pagesWithPrices->add( $page->pages_with_prices );

//echo '$pagesWithPrices: ', var_dump($pagesWithPrices);

$css[] = '/site/assets/css/offer.css';
?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<? //include count($pagesWithPrices)>0? 'offer/offers.php' : 'offer/offer.php' ?>
<? include 'offer/offer.php' ?>

<!--
<div class="padded centered container">
<? $regButtonConfig=(Object)['css'=>'simple L margin-l button']; include '_shared/buttons/reg-button.php' ?>
</div>
-->


<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>