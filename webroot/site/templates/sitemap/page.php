<?
/*
sitemap page
AT
02.02.22
*/
?>
<url>
	<loc><?=$httpUrl?></loc>
	<lastmod><?=$lastmod?></lastmod>
	<changefreq><?=$changefreq?></changefreq>
	<priority><?=$priority?></priority>
	<? if( count($images)>0 ): ?>
		<? foreach( $images as $image ): ?>
		<image:image>
			<image:loc>
				<?=$image->url?>
			</image:loc>
			<image:title>
				<?=$image->title?>
			</image:title>
			<image:caption>
				<?=$image->caption?>
			</image:caption>
		</image:image>
		<? endforeach ?>
	<? endif ?>
</url>
