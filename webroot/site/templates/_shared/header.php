<?
/*
header
AT
07.12.23
*/

$isHome = $page->url == $homePage->url;
$homeHref = ($isHome)? '' : "<a href={$homePage->url}>";
$homeUnhref = ($isHome)? '' : '</a>';

?>

<header id="header1">
	<div id="header1-flex" class="flex flex-middle container">

		<div id="header1-first">
			<?=$homeHref?>
				<div id="header1LogoWrapper">
					<img src="/site/assets/svg/onmed-white.svg">
				</div>
			<?=$homeUnhref?>
			<div id="branches" class="relative">
				<? include '_shared/header/branches.php' ?>
			</div>
		</div>

		<div id="header1-middle" class="centered">
			<div class="ML flex flex-middle">
				<?=$settings->contacts->address?>
			</div>
			<div class="half-padded centered flex flex-center">
				<? include '_shared/buttons/map-button.php' ?>
			</div>
		</div>

		<div id="header1-last" class="flex flex-middle">
			<a id="header1-phone" href="tel:<?=$phoneCleaned?>" class="ML half-padded phone flex flex-right flex-middle flex-nowrap">
				<? $svgSprite=(Object)[ 'symbol'=>'phone', 'title'=>'тел.', 'css'=>'L icon' ]; include '_shared/svg-sprite.php' ?>
				<?=$settings->contacts->phone?>
			</a>
			<div id="header1-buttons" class="flex flex-middle">
				<? include '_shared/buttons/reg-button.php' ?>
				<? include '_shared/icons-links.php' ?>
				<? //include '_shared/buttons/call-doctor-button.php' ?>
			</div>
		</div>

	</div>

</header>

<? include '_shared/header/warning.php' ?>

<header id="header2">
	<div id="header2-flex" class="flex flex-middle container">
		<? if( $page!=$offersPage): ?>
			<button id="offers-menu-show" class="flex flex-center flex-middle" data-toggle-class='{ "html":"offers-menu-visible" }'>
				<? //$svgSymbol='first-aid-kit'; $svgClass='XL padded icon'; include '_shared/svg-sprite.php' ?>
				Наши услуги
			</button>
		<? endif ?>
		<? include '_shared/header/header2-menu.php' ?>
		<div id="header1-search" class="flex">
			<div id="search">
				<form class="flex flex-middle">
					<input id="search-input" type="text" name="search" class="opaque" placeholder="Что Вы ищете?" onkeyup="window.search(this.value)"/>
					<div id="search-icon">
						<? $svgSprite=(Object)[ 'symbol'=>'search', 'title'=>'тел.', 'css'=>'L icon' ]; include '_shared/svg-sprite.php' ?>
					</div>
				</form>
			</div>
			<div id="search-results" class="hidden absolute half-padded card"></div>
		</div>
	</div>
</header>