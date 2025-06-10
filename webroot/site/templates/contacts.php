<?
/*
Contacts template
AT
18.02.25
*/

$settings->contacts->schedule = str_replace('<br />','',$settings->contacts->schedule);

$css[] = '/site/assets/css/contacts.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<section class="padded container block">
<div class="padded card">
	<div id="details" class="flex flex-top">
		<div id="details-address" class="padded">
			<h5>Адрес</h5>
			<?=$settings->contacts->address?>
			<h5>Ближайшее метро</h5>
			<?=$settings->contacts->metro?>
		</div>
		<div id="details-contacts" class="padded">
			<h5>Тел.</h5>
			<a href="tel:<?=$phoneCleaned?>">
				<?=$settings->contacts->phone?>
			</a>
			<h5>Email</h5>
			<?//=obfuscateMailtoHref($settings->contacts->email)?>
			<a href="mailto:<?=$settings->contacts->email?>">
				<?=$settings->contacts->email?>
			</a>
		</div>
		<div id="details-schedule" class="padded">
			<h5>Режим работы</h5>
			<?=$settings->contacts->schedule?>
		</div>
	</div>

	<div id="map" class="padded card block">
		<?=$settings->contacts->map_html?>
	</div>

	<div class="padded block">
		<h5>Как добраться</h5>
		<p>
			<?=$settings->contacts->getting_to?>
		</p>
		<h5>Реквизиты</h5>
<?
		$settingName = 'requisites';
		include '_shared/settings-table.php';
?>
	</div>

	<br>

<!--
	<div class="padded card block">
		<h4>
			Напишите главврачу
		</h4>
		<? include '_shared/email-feedback-form.php' ?>
	</div>
-->
	<? if($page->body): ?>
		<div class="body">
			<?=$page->body?>
		</div>
	<? endif ?>
	<? if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>

	<br><br>
</div>
</section>


<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>