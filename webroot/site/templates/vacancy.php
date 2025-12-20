<?
/*
Vacancy template
AT
03.09.25
*/

//$css[] = '/site/assets/css/default.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include 'vacancy/title.php' ?>

<section class="padded container" >
	<div class="padded card" >

		<div class="flex flex-middle flex-between">

			<div class="half-padded col">
				<label>график работы</label>
				<span class="L">
					<?=$page->schedule?>
				</span>
			</div>

			<div class="half-padded right-aligned col">
				<label>зарплата от</label>
				<span class="XL price">
					<?=$page->salary?> ₽
				</span>
			</div>

		</div>

		<hr/>

		<div class="half-padded date comment">
			<?//=$vacancуPage->date?>
			<?=date('d.m.Y', $vacancуPage->genericTimestamp)?>
		</div>

		<div class="half-padded">
			<? if( $page->summary ): ?>
				<p class="XL">
					<?=$page->summary?>
				</p>
			<? endif ?>

		<?/*
			<div class="padded centered">
				<? $regButtonConfig=(Object)['css'=>'simple L margin-l button']; include '_shared/buttons/reg-button.php' ?>
			</div>
		*/?>

			<? if( $page->body ): ?>
				<div class="body">
					<?=$page->body?>
				</div>
			<? endif ?>

			<? if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
			<? if($page->files) { include '_shared/files.php'; } ?>

		</div>

	</div>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>