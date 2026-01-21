<?
/*
specialist block
$specialistPages should be defined outside this file
AT
29.12.25
*/

include_once '_shared/functions/Field.php';

$specialistPage->lastname = $specialistPage->title;
$image = $specialistPage->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

?>

<div class="flex flex-center flex-middle <?=$specialistCSS?>">
	<a href="<?=$specialistPage->url?>" class="flex flex-middle">
		<div class="specialist-avatar">
			<img data-src="<?=$image?>" data-aspect="1:1">
		</div>
		<h6 class="half-padded">
			<?=$specialistPage->lastname?><br>
			<?=$specialistPage->firstname?><br>
			<?=$specialistPage->patronymic?>
		</h6>
	</a>

	<div class="half-padded flex flex-center flex-middle">
		<? if($specialistPage->price): ?>
			<div class="centered half-padded">
				Стоимость приёма от<br>
				<? include 'specialist/prices.php' ?>
			</div>
		<? endif ?>
		<a href="<?=$specialistPage->archimedURL?>" class="small button" target=_blank>
			записаться
		</a>
	</div>

</div>