<?
/*
Basic pagintaion
AT
11.02.25
*/

?>

<div class="padded pagination flex flex-center flex-middle">
	<? for( $i=1; $i<=$totalPages; $i++ ): ?>
		<?
		$current = $i==$currentPage? 'current ' : '';
		?>
		<a href="<?=$page->url?><?=$i?>" class="<?=$current?>page">
			<?=$i?>
		</a>
	<? endfor ?>
</div>
