<?
/*
offers
AT
27.11.19
*/

$now = $now? : time();
$offerPages = $offerPages? : $pages->find("template=offer,sort=title");

?>

<? foreach( $offerPages as $offerPage ): ?>
	<?
	$image = $offerPage->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

	$discountPage = $pages->findOne("template=discount, date_start<$now, date_end>$now, offers.id={$offerPage->id}, discount|price>0, sort=discount");
	$discountedPrice = $discountPage->discount? round( $offerPage->price * (1-$discountPage->discount/100), 2 ) : false;

	?>
	<div class="padded centered offer">
		<div class="card">
			<a href="<?=$offerPage->url?>" >
				<div class="offer-image">
					<img data-src="<?=$image?>" data-aspect="3:2">
				</div>
				<div class="padded offer-title">
					<h5>
						<?=$offerPage->title?>
					</h5>
				</div>
				<div class="XL padded offer-price">
					<? if($offerPage->price) include '_shared/price.php' ?>
				</div>
			</a>
		</div>
	</div>
<? endforeach ?>