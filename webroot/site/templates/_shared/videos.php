<?
/*
videos
AT
12.04.19
*/

$videos = $videos? : $page->videos;

?>

<div class="thumbs lg-videos flex flex-center flex-middle">
<? foreach($videos as $video): ?>
	<?
	//video ID
	switch(true)
		{
		//youtube page address of type //www.youtube.com/watch?v=Q2SCde_Dy-8
		case strstr( $video->link, 'watch?v=' ):
			$videoID = explode('watch?v=',$video->link)[1];
			$videoHosting = 'youtube';
		break;
		//youtube share link of type //youtu.be/Q2SCde_Dy-8
		case strstr( $video->link, 'youtu.be' ):
			$videoID = explode('youtu.be/',$video->link)[1];
			$videoHosting = 'youtube';
		break;
		}

	//video thumb
	switch($videoHosting)
		{
		default: //youtube
			////img.youtube.com/vi/Q2SCde_Dy-8/maxresdefault.jpg
			//$videoThumb = "//img.youtube.com/vi/$videoID/mqdefault.jpg";
			$videoThumb = "//img.youtube.com/vi/$videoID/maxresdefault.jpg";
		}
	?>
	<a data-src="<?=$video->link?>" data-sub-html="<?=$video->title?>" class="flex flex-center flex-middle" style="background: url(<?=$videoThumb?>)" >
		<div class="centered play">
			<? $svgSymbol = 'play'; $svgClass = 'X3L icon'; include '_shared/svg-sprite.php' ?>
		</div>
		<?//=$video->title?>
	</a>
<? endforeach ?>
</div>