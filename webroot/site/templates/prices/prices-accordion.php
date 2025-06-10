<?
/*
Prices accordion
AT
10.06.25
*/

$pricesAccordionConfig = $pricesAccordionConfig? : (Object)[];

?>

<section class="v-padded flex flex-col flex-gap" data-accordion='{"mode":"single"}'>
	<? foreach( $pagesWithPrices as $pageWithPrices): ?>
		<div class="half-h-padded card" data-accordion-item>
			<a class="strong p2 flex flex-middle flex-space-between" data-accordion-toggle>
				<?=$pageWithPrices->longtitle? : $pageWithPrices->title ?>
				<div class="XL strong" data-accordion-toggle-icon>
					+
				</div>
			</a>
			<div class="hidden" data-accordion-content>
<?
//echo '$pricesAccordionConfig: ', var_dump($pricesAccordionConfig);
?>
				<? if( !$pricesAccordionConfig->skipItemLinkButton ): ?>
					<a href="<?=$pageWithPrices->url?>" class="S right-floated button">
						подробнее...
					</a>
				<? endif ?>
				<br>
				<? $priceItems = $pageWithPrices->prices; $config=(Object)['disclaimer'=>false]; include 'prices/prices-table.php' ?>
			</div>
		</div>
	<? endforeach ?>
</section>

<? unset($pricesAccordionConfig) ?>