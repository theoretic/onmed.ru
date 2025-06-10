<?
/*
Default banner
AT
28.12.23
*/

namespace ProcessWire;
?>

<? if( $page->background->url ): ?>
<div class="padded">
	<div class="centered padded banner" data-back="<?=$page->background->url?>">
		<? include '_shared/breadcrumbs.php' ?>
		<h1>
			<?=$page->title?>
		</h1>
	</div>
</div>
<? endif ?>