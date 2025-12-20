<?
/*
AT
02.12.24
*/

?>

		</main>

		<? include '_shared/footer.php' ?>

		<div id="header2MenuToggle" data-toggle-class='{ "html":"menu-visible", "#header2MenuToggle":"is-active" }' class="hamburger hamburger-3dx">
			<div class="hamburger-box">
				<div class="hamburger-inner"></div>
			</div>
		</div>

		<? if(!$pageHasFeedbackForm) include '_shared/modals/modal-feedback.php' ?>
		<? include '_shared/modals/modal-map.php' ?>
		<? include '_shared/modals/modal-onleave.php' ?>


		<? if( !$_COOKIE['cookieConcentAccepted'] ) include '_shared/modals/modal-cookie-consent.php' ?>
		<? include '_shared/body/3rd-party-end.php' ?>
	</body>

	<? if(IS_WINTER_HOLIDAYS) include '_shared/snow.php' ?>

</html>

<? $MARKUP_REGION = 'end' ?>
<? include '_shared/css.php' ?>
<? include '_shared/js.php' ?>