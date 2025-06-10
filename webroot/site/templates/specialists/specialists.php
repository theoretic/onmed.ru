<?
/*
specialists
$specialistPages should be defined outside this file
AT
29.01.25
*/

include_once '_shared/functions/Field.php';
//$i = 0;
?>

<div class="v-padded flex flex-center flex-gap">
<? foreach( $specialistPages as $specialistPage ): ?>
	<?
	$specialistPage->lastname = $specialistPage->title;
	$image = $specialistPage->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';
	//$current = (++$i == 1) ? 'current' : '';
	?>
	<div class="centered specialist card flex flex-center flex-between">

		<a href="<?=$specialistPage->url?>">
			<div class="specialist-image">
				<img data-src="<?=$image?>" data-aspect="3:4">
			</div>
			<div class="half-h-padded">
				<h3 class="specialist-title">
					<?=$specialistPage->lastname?><br>
					<?=$specialistPage->firstname?><br>
					<?=$specialistPage->patronymic?>
				</h3>
				<div class="specialist-description">
					<? if($specialistPage->summary): ?>
						<?=$specialistPage->summary?>
					<? elseif($specialistPage->specializations): ?>
						<?=Field\implodePagesFields( $specialistPage->specializations, 'title' )?>
					<? endif ?>
				</div>
			</div>
		</a>

		<div class="half-padded specialist-bottom flex flex-center flex-bottom">
			<? if($specialistPage->price): ?>
				<div class="w100 half-padded">
					Стоимость приёма от<br>
					<? include 'specialist/prices.php' ?>
				</div>
			<? endif ?>
			<a href="<?=$specialistPage->archimedURL?>" class="small button" target=_blank>
				записаться
			</a>
		</div>

	</div>
<? endforeach ?>
</div>