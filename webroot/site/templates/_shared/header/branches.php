<?
/*
branches
AT
19.12.23
*/
?>

<? foreach( $branchPages as $branchPage_ ): ?>
	<?
		$classes = [];
		$classes[] = $branchPage_->name;
		if( $branchPage == $branchPage_ ) $classes[] = 'current';
		$classes = implode(' ',$classes);
	?>
	<a href="<?=$branchPage_->url?>" class="<?=$classes?>">
		<?=$branchPage_->title?>
	</a>
<? endforeach ?>