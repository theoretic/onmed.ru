<?
/*
Advanced pagintaion
URL segments based
AT
11.02.25
*/

?>

<div class="padded pagination flex flex-center flex-middle">

	<? if($currentPage > 1): ?>
		<a href="<?=$page->url?>" class="page">
			❮❮
		</a>
		<a href="<?=$page->url?><?=$currentPage-1?>" class="page">
			❮
		</a>
		<div class="page">
			...
		</div>
	<? endif ?>

<!--
	<? for( $i=1; $i<=$totalPages; $i++ ): ?>
		<?
		$current = $i==$currentPage? 'current ' : '';
		?>
		<a href="<?=$page->url?><?=$i?>" class="<?=$current?>page">
			<?=$i?>
		</a>
	<? endfor ?>
-->

<!--
		<a href="<?=$page->url?><?=$currentPage-1?>" class="page">
			<?=$currentPage-1?>
		</a>

-->		<a class="current page">
			<?=$currentPage?>
		</a>
<!--
		<a href="<?=$page->url?><?=$currentPage+1?>" class="page">
			<?=$currentPage+1?>
		</a>
-->

	<? if($currentPage < $totalPages): ?>
		<div class="page">
			...
		</div>
		<a href="<?=$page->url?><?=$currentPage+1?>" class="page">
			❯
		</a>
		<a href="<?=$page->url?><?=$totalPages?>" class="page">
			❯❯
		</a>
	<? endif ?>

</div>
