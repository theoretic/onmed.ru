<?
/*
Offer: single offer
AT
27.11.25

$now = time();
//$discountPage = $pages->findOne("template=discount, date_start<$now, date_end>$now, offers.id={$page->id}, discount>0, sort=-discount");
$discountPage = $pages->findOne("template=discount, date_start<$now, date_end>$now, offers.id={$page->id}, sort=-discount");
$discountedPrice = $discountPage->discount? round( $page->price * (1-$discountPage->discount/100), 2 ) : false;
$offerPage = $page;
*/

namespace ProcessWire;

$offerPricesPages = $page->children("template=offer-prices");

//echo '$feedbackPages: ', var_dump($feedbackPages);//

if( "$feedbackPages" )
	$css[] = '/site/assets/css/feedbacks.css';

?>

<section role="offer" class="padded container block">
	<div class="card">
		<? if( !$page->hasImagesInBody && $page->image ): ?>
			<div id="offer-top" class="flex flex-middle">
				<? if($page->image): ?>
					<div id="offer-top-image">
						<img data-src="<?=$page->image->url?>">
					</div>
				<? endif ?>
				<? if($page->summary): ?>
					<div id="offer-top-summary" class="XL padded">
						<?=$page->summary?>
					</div>
				<? $summaryIsDisplayed=1 ?>
				<? endif ?>
			</div>
		<? endif ?>

		<div id="texts">

			<? if(!$summaryIsDisplayed && $page->summary): ?>
				<p class="XL padded">
					<?=$page->summary?>
				</p>
			<? endif ?>

			<? if($page->body): ?>
				<div class="padded body">
					<?=$page->body?>
				</div>
			<? endif ?>

			<? include '_shared/sections.php' ?>
			<? if( $page->images !== null ) { $images = $page->images; include '_shared/thumbs.php'; } ?>

			<? if( count($page->images_before) && count($page->images_after) && $page->specialist ): ?>
				<? include 'offer/before-after.php' ?>
			<? endif ?>

			<? if( count($specialistPages)>0 ): ?>
				<div id="specialists">
					<? include 'specialists/specialists.php' ?>
				</div>
			<? endif ?>

		</div>

	</div>

	<? if( count($page->prices)>0 ): ?>
		<section class="v-padded container table-container" data-accordion='{"mode":"single"}'>
			<? include 'prices/prices-table.php' ?>
		</section>
	<? endif ?>

	<? if( count($offerPricesPages)>0 ): ?>
		<? $pagesWithPrices=$offerPricesPages; $pricesAccordionConfig=(Object)['skipItemLinkButton'=>1]; include 'prices/prices-accordion.php' ?>
	<? endif ?>

	<div class="double-v-padded">
		<div class="card">
			<?=$settings->contacts->map_html?>
		</div>
	</div>

	<? include '_shared/achievements.php' ?>

	<h2>Оставьте свой отзыв</h2>
	<div class="padded card">
		<? include 'feedbacks/feedback-form.php' ?>
	</div>

	<? if( "$feedbackPages" ): ?>
		<h2>Отзывы</h2>
		<? include 'feedbacks/feedbacks.php' ?>
	<? endif ?>

</section>
