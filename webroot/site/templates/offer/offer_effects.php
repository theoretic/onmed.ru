<?
/*
Offer effects
Each image_before should have a corresponding image_after!
AT
22.01.26
*/
?>

<? foreach( $page->offer_effects as $offerEffectItem ): ?>

	<div class="margin-auto performer">
		<? $specialistPage = $offerEffectItem->specialist; $specialistCSS='margin-auto'; include 'specialists/specialistBlock.php' ?>
	</div>

	<? $i=0 ?>
	<? foreach( $offerEffectItem->images_before as $imageBefore ): ?>
		<?
		$imageAfter = $offerEffectItem->images_after->eq($i);
		?>
		<div class="margin-auto padded comparatorContainer">
			<div class="relative comparator">
				<div class="absolute comparatorLeft" data-back="<?=$imageBefore->url?>" ></div>
				<div class="absolute comparatorRight" data-back="<?=$imageAfter->url?>" ></div>
				<input type="range" min="0" step="0.5" max="100" value="50">
			</div>
		</div>
		<? $i++ ?>
	<? endforeach ?>

	<? unset($i) ?>

<? endforeach ?>