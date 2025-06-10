<?
/*
Posts
AT
08.12.21
*/

$postPages = $postPages? : $page->children;

?>

<? foreach( $postPages as $postPage ): ?>
	<div class="padded post">
		<div class="flex flex-middle card">
			<? if( $postPage->image->url ): ?>
				<a href=<?=$postPage->url?> class="post-image">
					<img data-src="<?=$postPage->image->url?>">
				</a>
			<? endif ?>
			<div class="padded post-texts">
				<div class="S date">
					<?=date('d.m.Y',$postPage->created)?>
				</div>
				<a href="<?=$postPage->url?>">
					<h3 class="half-v-padded">
						<?=$postPage->title?>
					</h3>
					<?=$postPage->summary?>
				</a>
			</div>
		</div>
	</div>
<? endforeach ?>