<?
/*
branch discounts slider
AT
02.11.23
*/

$now = time();
$discountPages = $discountPages? : $pages->find("template=discount,date_start<$now,date_end>$now,sort=random,limit=5");
//var_dump($discountPages);

?>

<? foreach( $discountPages as $discountPage ): ?>
		<?
			$current = ++$i == 1? 'current' : '';
			$class = ( $discountPage->date_start < $now && $discountPage->date_end > $now)? 'current' : '';
			$for = $discountPage->discount? 'на' : 'за';
			$backgroundImageUrl = false;
			switch(true){
				case $discountPage->background->url:
					$backgroundImageUrl = $discountPage->background->url;
				break;

				default:
					$backgroundImageUrl = $discountsPage->background->url;
			}
		?>

		<a href="<?=$discountPage->url?>" class="<?=$current?> absolute padded centered discount flex flex-col flex-center flex-middle slide block" data-back="<?=$backgroundImageUrl?>">
			<!--
			<h1>
				<?=$discountPage->title?>
			</h1>
			-->

			<? if( $discountPage->date_start && $discountPage->date_end ): ?>
				<h2 class="half-v-padded discount-dates">
					с <?=Datetime\dateRu($discountPage->date_start)?>
					по <?=Datetime\dateRu($discountPage->date_end)?>
				</h2>
			<? endif ?>

			<? if( $discountPage->discount || $discountPage->price ): ?>
				<h1 class="discount-amount">
					<? if($discountPage->discount): ?>
						-<?=$discountPage->discount?>%
						<? elseif($discountPage->price): ?>
						<?=$discountPage->price?> ₽
					<? endif ?>
				</h1>
			<? endif ?>

			<!-- <h2 class="half-v-padded"><?=$for?> услуги</h2> -->

			<? if( count($discountPage->offers)>0 ): ?>
				<h2>
				<? foreach( $discountPage->offers as $offerPage ): ?>
					<span class="">
						<?=$offerPage->title?>
					</span>
				<? endforeach ?>
				</h2>
			<? endif ?>
		</a>
<? endforeach ?>


<div class="absolute dots">
<? foreach( $discountPages as $discountPage ): ?>
	<? $current = ++$i == 1? 'current' : '' ?>
	<a class="<?=$current?> dot"></a>
<? endforeach ?>
</div>

<a class="absolute prev flex flex-middle">
	<? $svgSymbol='arrow'; include '_shared/svg-sprite.php' ?>
</a>

<a class="absolute next flex flex-middle">
	<? $svgSymbol='arrow'; include '_shared/svg-sprite.php' ?>
</a>

<? $i=0 ?>