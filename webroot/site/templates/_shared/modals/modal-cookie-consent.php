<?
/*
Cookie consent modal
AT
28.08.25
*/
?>

<div class="fixed modal" id="modal-cookie-consent">
	<div class="padded container">
		<p>
			<?=$settings->privacy->cookie_consent_text?>
		</p>
		<a
			class="float-right button"
			data-modal-close
			data-trigger-event="cookieConcentAccepted"
			>
			понятно
		</a>
		<br>
		<br>
	</div>
</div>