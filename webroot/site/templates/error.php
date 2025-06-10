<?
/*
Error template
AT
20.11.24
*/

$css[] = '/site/assets/css/error.css';

?>

<? include '_shared/_prolog.php' ?>

<section class="centered padded container block">
	<h1>
		<?=$page->longtitle? : $page->title?>
	</h1>
	<div class="XL padded body">
		<?=strip_tags($page->body)?>
	</div>
	<? if( count($page->images)>0 ): ?>
		<?
		$image = $page->images->getRandom(1)->url;
		?>
		<a href="<?=$homePage->url?>" id="image" class="block">
			<img id="image" data-src="<?=$image?>" />
		</a>
	<? endif ?>
</section>

<? include '_shared/_epilog.php' ?>