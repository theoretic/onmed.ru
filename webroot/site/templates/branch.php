<?
/*
Branch template
AT
21.11.23
*/

include '_shared/seo-url.php';

$css[] = '/site/assets/css/branch.css';
//$js = '/site/assets/js/home.js';

//if(!IS_MOBILE) $js[] = '/site/assets/js/home.pc.js' ;

?>

<? include '_shared/_prolog.php' ?>

<section id="discounts">
	<div id="slider-discounts" role="slider" class="relative wide container">
		<? include 'branch/banners.php' ?>
	</div>
</section>

<? include '_shared/layout-sidebars/prolog.php' ?>


		<section id="intro" class="padded">
			<div class="L light top-padded">
				<?=$page->summary?>
			</div>
		</section>

		<section id="sections" class="padded">
			<? include '_shared/sections.php' ?>
		</section>
		<br><br>

<?/*
<section id="offers" class="container">
	<h2 class="padded centered">Наши услуги</h2>
	<div class="flex flex-center flex-middle">
		<? include 'offers/offers-grid.php' ?>
	</div>
	<div class="centered v-padded">
		<a href="/offers" class="XL button">
			все услуги
		</a>
	</div>
</section>

<section id="specialists" class="container">
	<h2 class="padded centered">Мы в лицах</h2>
	<div class="">
		<? include 'specialists/specialists.php' ?>
	</div>
	<div class="centered v-padded">
		<a href="/specialists" class="XL button">
			все специалисты
		</a>
	</div>
</section>
*/?>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>