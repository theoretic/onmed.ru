<?
/*
Specialist prices
AT
03.03.25
*/

$specialistPage = $specialistPage? : $page;

?>

<? if( $specialistPage->oldprice && $specialistPage->oldprice > $specialistPage->price ): ?>
	<span class="oldprice">
		<?=$specialistPage->oldprice?>
	</span>
<? endif ?>
<span class="price">
	<?=$specialistPage->price?> ₽<sup>*</sup>
</span>
<br>
<span class="p1 comment">
* 
для первичных посетителей<br>
медицинского центра
</span>