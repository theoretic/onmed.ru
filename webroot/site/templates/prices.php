<?
/*
Prices template
AT
25.11.24
*/

$pagesWithPrices = $branchPage->find('prices!=,sort=title');

$css[] = '/site/assets/css/prices.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<section class="padded container">
	<? if($page->summary): ?>
		<div class="XL padded">
			<?=$page->summary?>
		</div>
	<? endif ?>

	<? if($page->body): ?>
		<div class="padded body">
			<?=$page->body?>
		</div>
	<? endif ?>	

	<div class="flex flex-col flex-gap" data-accordion='{"mode":"single"}'>
		<? foreach( $pagesWithPrices as $pageWithPrices): ?>
			<div class="half-h-padded card" data-accordion-item>
				<a class="strong p2 flex flex-middle flex-space-between" data-accordion-toggle>
					<?=$pageWithPrices->longtitle? : $pageWithPrices->title ?>
					<div class="XL strong" data-accordion-toggle-icon>
						+
					</div>
				</a>
				<div class="hidden" data-accordion-content>
					<a href="<?=$pageWithPrices->url?>" class="S right-floated button">
						подробнее...
					</a>
					<br>
					<? $priceItems = $pageWithPrices->prices; $config=(Object)['disclaimer'=>false]; include 'prices/prices-table.php' ?>
				</div>
			</div>
		<? endforeach ?>
	</div>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>