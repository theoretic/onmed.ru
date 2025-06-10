<? if ($discountedPrice): ?>
	<span class="oldprice">
		<?=$offerPage->price?>
	</span>
	<?=$discountedPrice?>
<? else: ?>
	<?=$offerPage->price?>
<? endif ?>
 ₽ 

<? if( $discountPage->price > 0 ): ?>
	<a href="<?=$discountPage->url?>" class="green XS light badge">
		спецпредложение
	</a>
<? endif ?>

<? if( $offerPage->template == 'program' ): ?>
	<span class="grey XS light badge">
		программа
	</span>
<? endif ?>