<?
/*
breadcrumbs
excludes hidden pages
➜ / 
AT
27.11.23
*/

$parents = $parents? : $page->parents;

?>

<div id="breadcrumbs" class="half-v-padded">
	<? foreach( $parents as $parent ): ?>
		<? if( $parent->isHidden() ) continue ?>
		<a href="<?=$parent->url?>"><?=$parent->title?></a>
		<span class="p1">
		/
		</span>
	<? endforeach ?>
</div>

