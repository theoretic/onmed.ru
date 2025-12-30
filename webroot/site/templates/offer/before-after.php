<?
/*
Offer
before and after
AT
29.12.25
*/
?>

<div class="beforeafter centered flex flex-wrap flex-center gap">
	<div class="before half-padded flex-1">
		<h5>
			До оказания услуги
		</h5>
		<? $images=$page->images_before; include '_shared/thumbs.php' ?>
	</div>

	<div class="performer half-padded flex-1">
		<h5>
			Услугу оказал специалист
		</h5>
		<div>
			<? $specialistPage = $page->specialist; $specialistCSS='margin-auto'; include 'specialists/specialistCard.php' ?>
		</div>
	</div>

	<div class="after half-padded flex-1">
		<h5>
			После оказания услуги
		</h5>
		<? $images=$page->images_after; include '_shared/thumbs.php' ?>
	</div>

</div>