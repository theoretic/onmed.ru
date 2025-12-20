<?
/*
specialist title
AT
03.09.25
*/

?>
<section id="title" class="padded container">
	<? include '_shared/breadcrumbs.php' ?>
	<h1>
		<?=$page->lastname?> <?=$page->firstname?> <?=$page->patronymic?>
	</h1>
	<? if($page->job_title->title): ?>
	<p class='half-v-padded L comment'>
		<?=$page->job_title->title?>
	</p>
	<? endif ?>
</section>