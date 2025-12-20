<?
/*
footer
AT
01.09.25
*/

?>

<footer>
	<div id="footer-flex" class="flex flex-middle container">

		<div id="footer-first" class="half-padded">
			<div>
				<strong class="L">
					<?=$settings->requisites->company_name?>
				</strong>
				<br>
				<?=$settings->contacts->address?>
			</div>
			<div id="footer-map-button" class="half-v-padded flex">
				<? include '_shared/buttons/map-button.php' ?>
			</div>
		</div>

		<div id="footer-center" class="padded centered">
			<? if(!$pageHasFeedbackForm) include '_shared/buttons/feedback-button.php' ?>
			<p class="padded centered">
				<a href="/sitemap">
					Карта сайта
				</a>
			</p>
<!--
			<div class="flatmenu flex flex-center">
				<br>
				<br>
				<? foreach($aboutPage->children as $child): ?>
					<a href="<?=$child->url?>"><?=$child->title?></a>
				<? endforeach ?>
			</div>
-->

<!--
			<div id="footer-paycards" class="centered padded flex flex-center">
				<img src="/site/assets/files/images/amex.png"/>
				<img src="/site/assets/files/images/maestro.png"/>
				<img src="/site/assets/files/images/mc.png"/>
				<img src="/site/assets/files/images/visa.png"/>
			</div>
-->
		</div>

		<div id="footer-last" class="padded">
			<? //include '_shared/socials.php' ?>
			<div id="footer-phone-buttons">
				<a href="tel:<?=$phoneCleaned?>" class="L half-padded phone flex flex-right flex-middle flex-nowrap">
					<? $svgSprite=(Object)[ 'symbol'=>'phone', 'title'=>'тел.', 'css'=>'XL icon' ]; include '_shared/svg-sprite.php' ?>
					<?=$settings->contacts->phone?>
				</a>
				<div id="footer-buttons" class="flex">
					<? include '_shared/buttons/reg-button.php' ?>
					<? //include '_shared/buttons/call-doctor-button.php' ?>
				</div>
				<? include '_shared/icons-links.php' ?>
			</div>
			<div id="footer-email-vendor" class="half-padded flex">
<?/*
				<div id="footer-email" class="half-padded flex flex-middle">
					<a href="mailto:<?=$settings->email?>" class="flex flex-middle">
						<? $svgSprite=(Object)['symbol'=>'mail', 'css'=>'XL padded icon']; include '_shared/svg-sprite.php' ?>
						<span role="caption">
							<?=$settings->email?>
						</span>
					</a>
				</div>
*/?>
				<div id="footer-vendor">
					<a href="//atis.pro/" target="_blank" title="Made in atis.pro">
						<img src="//atis.pro/logo/atispro-white.svg" width=60>
					</a>
				</div>
			</div>
		</div>

	</div>

	<div id="contraindications" class="padded centered XL uppercase">
		Имеются противопоказания. Необходима консультация специалиста.
	</div>

</footer>