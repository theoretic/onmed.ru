<?
/*
Contacts
details
AT
03.09.25
*/


?>

<div id="details" class="flex flex-middle flex-wrap flex-between">
	<div class="padded">
		<h5>Адрес</h5>
		<?=$settings->contacts->address?>
		<br><br>
		<h5>Ближайшее метро</h5>
		<?=$settings->contacts->metro?>
	</div>
	<div class="padded centered">
		<h5>Тел.</h5>
		<a href="tel:<?=$phoneCleaned?>">
			<?=$settings->contacts->phone?>
		</a>
		<br><br>
		<h5>Email</h5>
		<?//=obfuscateMailtoHref($settings->contacts->email)?>
		<a href="mailto:<?=$settings->contacts->email?>">
			<?=$settings->contacts->email?>
		</a>
	</div>
	<div class="padded right-aligned">
		<?=$settings->contacts->schedule?>
	</div>
</div>