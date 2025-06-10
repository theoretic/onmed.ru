<?
/*
Head
AT
04.04.25
*/
?>

<? include '_shared/head/metas.php' ?>
<? include '_shared/head/favicons.php' ?>
<?=$settings->seo->head?>

<? if(!strstr(PHP_OS,'WIN')) include '_shared/head/3rd-party.php' ?>

<style>
:root {
--color: <?=$currentBranch['color']?>;
<? foreach($branches as $name=>$data): ?>
	--color<?=ucfirst($name)?>: <?=$data['color']?>;
<? endforeach ?>
}
</style>

<? include '_shared/css.php' ?>
<? include '_shared/js.php' ?>