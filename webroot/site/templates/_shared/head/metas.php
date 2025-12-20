<?
/*
metas
OpenGraph
AT
08.09.25
*/
?>

<no-typo>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>
		<?=$page->genericMetaTitle?>
	</title>

	<meta name="description" content="<?=$page->genericMetaDescription?>">
	<? if($page->seo_keywords): ?>
		<meta name="keywords" content="<?=$page->seo_keywords?>">
	<? endif ?>

	<meta http-equiv='last-modified' content='<?=date('D, d M Y H:i:s T',$page->modified)?>'>

	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<!-- <meta name='format-detection' content='telephone=no'> -->

	<meta property="og:title" content="<?=$page->genericMetaTitle?>"/>
	<meta property="og:description" content="<?=$page->genericMetaDescription?>"/>
	<meta property="og:image" content="<?=$page->image->url? : '/site/assets/files/images/favicons/android-icon-192x192.png' ?>">
	<meta property="og:type" content="article"/>
	<meta property="og:url" content= "<?=$page->url?>" />

	<? @include "{$page->template}/json-ld.php" ?>

</no-typo>