<?
/*
Sitemap
AT
01.09.25
*/

$tree = renderNavTree(
	$homePage,
	[
	'maxDepth' => 1000,
	'onlyNonempty' => false,
	//'deniedItemTemplates' => false,
	//'allowedItemTemplates' => ['products']
	]
	);

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include $page->background->url? 'default/banner.php' : '_shared/title.php' ?>

<section class="padded expandable-tree card flex container" >
	<?=$tree?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>