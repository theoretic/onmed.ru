<?
/*
Vacancies
AT
03.09.25
*/

?>

<? foreach( $vacancуPages as $vacancуPage ): ?>
	<div class="padded post">
		<div class="flex flex-middle flex-between card">
			<? if( $vacancуPage->image->url ): ?>
				<a href=<?=$vacancуPage->url?> class="post-image col">
					<img data-src="<?=$vacancуPage->image->url?>">
				</a>
			<? endif ?>
			<div class="padded post-texts col">
				<div class="date comment">
					<?//=$vacancуPage->date?>
					<?=date('d.m.Y', $vacancуPage->genericTimestamp)?>
				</div>
				<a href="<?=$vacancуPage->url?>">
					<h3 class="half-v-padded">
						<?=$vacancуPage->title?>
					</h3>
					<?=$vacancуPage->summary?>
				</a>
			</div>

			<div class="padded centered col">
				<label>график работы</label>
				<?=$vacancуPage->schedule?>
			</div>

			<div class="padded right-aligned col">
				<label>зарплата от</label>
				<span class="XL price">
					<?=$vacancуPage->salary?> ₽
				</span>
			</div>

		</div>
	</div>
<? endforeach ?>