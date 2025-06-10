<?
/*
Coupon template
AT
12.12.23
*/

//$css[] = '/site/assets/css/default.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<section class="container" >
	<div class="padded centered card">
		<div class="padded">
			<? if($page->summary): ?>
				<p class="XL centered">
					<?=$page->summary?>
				</p>
			<? endif ?>
			<? if($page->body): ?>
				<?=strip_tags($page->body)?>
			<? endif ?>
		</div>
		<? if($page->image->url): ?>
			<div class="double-padded">
				<img
					data-src="<?=$page->image->url?>"
					alt="<?=$page->genericTitle?>"
					style="max-width:100%"
				>
			</div>
		<? endif ?>
		<div class="padded centered">
			<? $regButtonConfig=(Object)['css'=>'simple L margin-l button']; include '_shared/buttons/reg-button.php' ?>
		</div>
	</div>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>