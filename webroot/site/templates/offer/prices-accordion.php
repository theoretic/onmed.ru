<?
/*
prices accordion
AT
31.10.23
*/
?>

<section id="prices-accordion" class="padded container block">
	<? foreach($pagesWithPrices as $pageWithPrices): ?>
		<?
		?>
		<h4>
			<?=$pageWithPrices->title?>
		</h4>
		<? include 'prices/prices-table.php' ?>
	<? endforeach ?>
</section>