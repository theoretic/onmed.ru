<?
/*
Media template
AT
20.11.24
*/

//$css[] = '/site/assets/css/default.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/title.php' ?>

<section class="flex flex-center container block">
	<? if($page->html) echo $page->html ?>
	<?//=$page->body?>
	<? if($page->images) { include '_shared/thumbs.php' } ?>
	<? if($page->videos) { include '_shared/videos.php' } ?>
	<? include '_shared/sections.php' ?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>