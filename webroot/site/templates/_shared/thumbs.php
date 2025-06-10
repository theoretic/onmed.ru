<?
/*
thumbs
uses wa-mediabox
AT
29.11.19
*/

$images = $images? : $page->images;

?>

<div class="thumbs flex flex-center">
<? foreach($images as $image): ?>
		<a href="<?=$image->url?>" title="<?=$image->description?>" data-mediabox="wa-images" data-title="<?=$image->description?>">
			<img data-src="<?=$image->url?>" alt="<?=$image->description?>" class="adaptive">
		</a>
<? endforeach ?>
</div>