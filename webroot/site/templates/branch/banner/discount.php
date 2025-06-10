<?
/*
branch
banner with template : discount
AT
29.10.24
*/
?>

<? if( $bannerPage->date_start && $bannerPage->date_end ): ?>
	<h2 class="half-v-padded discount-dates">
		с <?=\Datetime\dateRu($bannerPage->date_start)?>
		по <?=\Datetime\dateRu($bannerPage->date_end)?>
	</h2>
<? endif ?>

<h1>
	<?=$bannerPage->title?>
</h1>

<? if( $bannerPage->discount || $bannerPage->price ): ?>
	<h1 class="discount-amount">
		<? if($bannerPage->discount): ?>
			-<?=$bannerPage->discount?>%
			<? elseif($bannerPage->price): ?>
			<? if($bannerPage->oldprice): ?>
				<span class="oldprice">
					<?=$bannerPage->oldprice?>
				</span>
			<? endif ?>
			<?=$bannerPage->price?> ₽
		<? endif ?>
	</h1>
<? endif ?>

<!-- <h2 class="half-v-padded"><?=$for?> услуги</h2> -->

<h2>
<? if( count($bannerPage->offers)>0 ): ?>
	<? foreach( $bannerPage->offers as $offerPage ): ?>
		<span class="p1">
			<?=$offerPage->title?>
		</span>
	<? endforeach ?>
<? endif ?>
</h2>
