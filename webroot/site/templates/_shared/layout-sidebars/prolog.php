<?
/*
sticky class removed
AT
05.11.24
*/
?>

<div id="layout-sidebars" class="flex container">

	<? if( $page!=$offersPage): ?>
		<aside role="primary">
			<div class="">
				<? include 'offers/offers-menu.php' ?>
			</div>
		</aside>
	<? endif ?>

	<article>