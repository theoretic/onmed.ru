<?
/*
Contacts template
AT
05.10.25
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

	<? include 'contacts/details.php' ?>
	<br><br>

	<? include 'contacts/map.php' ?>
	<br>

	<?=$settings->contacts->getting_to?>
	<br><br>

	<? $settingName = 'requisites'; include '_shared/settings-table.php' ?>

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
</div>
</section>


<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>