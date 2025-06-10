<?
/*
Discounts
AT
21.11.24
*/

$now = $now? : time();

?>

<? foreach( $discountPages as $discountPage ): ?>
	<?
	$class = ( $discountPage->date_start < $now && $discountPage->date_end > $now)? 'current' : '';
	$for = $discountPage->discount? 'на' : 'за';
	?>
	<div class="<?=$class?>	discount card">

		<a href=<?=$discountPage->url?> class="centered padded discount-discount flex flex-center flex-middle"
			<? if( $discountPage->image->url ): ?>
				data-back="<?=$discountPage->image->url?>"
				data-min-height=200
			<? endif ?>
			>
			<h4 class="half-padded discount-dates">
				с <?=Datetime\dateRu($discountPage->date_start)?>
				по <?=Datetime\dateRu($discountPage->date_end)?>
			</h4>
			<h2>
				<?=$discountPage->title?>
			</h2>
			<!--
			<? if( count($discountPage->offers)>0 ): ?>
				<h6 class="half-v-padded">
					<?//=$for?>
					услуги
				</h6>
				<div class="centered">
					<? foreach( $discountPage->offers as $offerPage ): ?>
						<a href="<?=$offerPage->url?>" class="L half-padded nowrap">
							<?=$offerPage->title?>
						</a>
					<? endforeach ?>
				</div>
			<? endif ?>
			-->
		</a>

		<a href=<?=$discountPage->url?> class="centered padded discount-offers">
			<h3 class="discount-amount flex flex-center flex-wrap">
				<? if($discountPage->discount): ?>
					-<?=$discountPage->discount?>%
				<? elseif($discountPage->price): ?>
					<? if($discountPage->oldprice): ?>
						<span class="oldprice">
							<?=$discountPage->oldprice?>
						</span>
					<? endif ?>
					<?=$discountPage->price?> ₽
				<? endif ?>
			</h3>
		</a>

	</div>
<? endforeach ?>