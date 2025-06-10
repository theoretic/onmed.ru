<?
/*
News
AT
19.11.24
*/

$newsPages = $newsPages? : $page->children();
$newsPages->sort("-genericTimestamp");

?>

<? foreach( $newsPages as $newsPage ): ?>
	<div class="padded post">
		<div class="flex flex-middle card">
			<? if( $newsPage->image->url ): ?>
				<a href=<?=$newsPage->url?> class="post-image col">
					<img data-src="<?=$newsPage->image->url?>">
				</a>
			<? endif ?>
			<div class="padded post-texts col">
				<div class="S date">
					<?=$newsPage->date?>
					<?//=$newsPage->genericTimestamp?>
				</div>
				<a href="<?=$newsPage->url?>">
					<h3 class="half-v-padded">
						<?=$newsPage->title?>
					</h3>
					<?=$newsPage->summary?>
				</a>
			</div>
		</div>
	</div>
<? endforeach ?>