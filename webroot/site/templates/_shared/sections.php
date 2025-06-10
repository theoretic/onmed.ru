<?
/*
page sections
AT
26.10.23
*/
?>

<? foreach($page->sections as $i=>$section): ?>
	<?
	$imgClass = $i%2 == 0? 'left-floated' : 'right-floated'
	?>
	<section class="narrative">

		<h2><?=$section->title?></h2>

		<? if($section->summary): ?>
			<p class="L light">
				<?=$section->summary?>
			</p>
		<? endif ?>

		<? if($section->image): ?>
			<img
				data-src="<?=$section->image->url?>"
				alt="<?=$section->title?>"
				class="<?=$imgClass?> section-image"
				>
		<? endif ?>

		<?=$section->body?>

		<? if($section->images) { $images = $section->images; include '_shared/thumbs.php'; } ?>
		<? if($section->videos) { $videos = $section->videos; include '_shared/videos.php'; } ?>

	</section>

<? endforeach ?>
