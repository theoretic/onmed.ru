<?
/*
Default template
AT
08.09.25
*/

$page->hasContent = $page->summary || $page->body || count($page->images)>0 || count($page->files)>0;

//$css[] = '/site/assets/css/default.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include $page->background->url? 'default/banner.php' : '_shared/title.php' ?>
<? if( $page->shouldHaveSubmenu ) include '_shared/submenu.php' ?>


<section class="padded container" >
	<? if($page->hasContent): ?>
	<div class="padded card" >

		<? if( $page->summary ): ?>
			<p class="XL">
				<?=$page->summary?>
			</p>
		<? endif ?>

	<?/*
		<div class="padded centered">
			<? $regButtonConfig=(Object)['css'=>'simple L margin-l button']; include '_shared/buttons/reg-button.php' ?>
		</div>
	*/?>

		<? if( $page->body ): ?>
			<div class="body">
				<?=$page->body?>
			</div>
		<? endif ?>

		<? if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
		<? if($page->files) { include '_shared/files.php'; } ?>

		<? include '_shared/sections.php' ?>

	</div>
	<? endif ?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>