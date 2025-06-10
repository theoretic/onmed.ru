<?
/*
Offers template
AT
20.11.24
*/

//echo '$branchPage: ', var_dump($branchPage);

$offerPages = $branchPage->find('parent.name=offers,template=offer,sort=title');

$css[] = '/site/assets/css/offers.css';

?>

<? include '_shared/_prolog.php' ?>
<? //include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<section class="padded flex container">
	<? if( $page->summary ): ?>
		<p class="L">
			<?=$page->summary?>
		</p>
	<? endif ?>
	<? if( $page->body ): ?>
		<div class="body">
			<?=$page->body?>
		</div>
	<? endif ?>

	<div id="offers" class="guttered flex">
		<? foreach( $offerPages as $offerPage ): ?>
			<?
			$subofferPages = $offerPage->children("template=offer,sort=title");
			?>
			<div class="padded flex-1 offer card">
				<a href="<?=$offerPage->url?>">
					<h2>
						<?=$offerPage->title?>
					</h2>
				</a>
				<? if( count($subofferPages)>0 ): ?>
				<div class="scrollable">
					<? foreach( $subofferPages as $subofferPage ): ?>
						<a href="<?=$subofferPage->url?>">
						<h3>
							<?=$subofferPage->title?>
						</h3>
						</a>
					<? endforeach ?>
				</div>
				<? endif ?>
			</div>
		<? endforeach ?>
	</div>
</section>

<section id="directions" class="flex flex-wrap container">
	<? include '_shared/directions.php' ?>
</section>

<? //include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>